<?php

namespace App\Http\Controllers\Game;

use App\Http\Controllers\Controller;

use App\Events\MessageSent;
use App\Models\Room;
use App\Models\Game;
use App\Events\MoveMade;
use App\Events\GameFinished;
use App\Events\PlayerReady;
use App\Models\Move;
use App\Models\PlayerStats;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class GameController extends Controller
{
    public function show(Room $room)
    {
        // Check if user is in the room
        if (!$room->players()->where('user_id', Auth::id())->exists()) {
            return redirect()->route('rooms.index');
        }

        // Load relationships
        $room->load(['players', 'games' => function ($query) {
            $query->latest()->first();
        }]);

        $game = $room->games()->where('status', 'playing')->first()
            ?? Game::initializeGame($room);

        return Inertia::render('Game/Show', [
            'room' => [
                'id' => $room->id,
                'name' => $room->name,
                'status' => $room->status,
                'created_by' => $room->created_by,
                'players' => $room->players->map(function ($player) {
                    return [
                        'id' => $player->id,
                        'name' => $player->name,
                        'pivot' => [
                            'is_ready' => (bool) $player->pivot->is_ready
                        ]
                    ];
                }),
                'moves' => $game->moves ?? []
            ],
            'currentPlayer' => $game->current_player, // Use stored current player
            'user' => Auth::user(),
            'game' => [
                'id' => $game->id,
                'status' => $game->status,
                'board_state' => $game->board_state
            ]
        ]);
    }

    public function toggleReady(Room $room)
    {
        $player = $room->players()->where('user_id', Auth::id())->first();
        if (!$player) {
            return response()->json(['error' => 'Player not in room'], 403);
        }

        // Toggle ready state
        $room->players()->updateExistingPivot(Auth::id(), [
            'is_ready' => !$player->pivot->is_ready
        ]);

        // Check if all players are ready
        $allPlayersReady = ($room->players()->where('is_ready', true)->count() === $room->players()->count()) && $room->players()->count() === 2;
        if ($allPlayersReady) {
            $room->update(['status' => 'playing']);
        }

        // Broadcast event
        broadcast(new PlayerReady(
            $room->id,
            Auth::id(),
            !$player->pivot->is_ready,
            $allPlayersReady ? 'playing' : 'waiting'
        ));

        return response()->json([
            'status' => 'success',
            'is_ready' => !$player->pivot->is_ready,
            'game_started' => $allPlayersReady
        ]);
    }

    public function makeMove(Request $request, Room $room)
    {
        $request->validate([
            'x' => 'required|integer|min:0|max:14',
            'y' => 'required|integer|min:0|max:14',
        ]);

        $game = $room->games()->latest()->first();

        if (!$game || $game->status === 'finished') {
            return response()->json(['error' => 'Game not active'], 400);
        }

        // Get last move to verify turn
        $lastMove = $game->moves()->latest()->first();
        if ($lastMove && $lastMove->user_id === Auth::id()) {
            return response()->json(['error' => 'Not your turn'], 400);
        }

        // Create the move
        $move = new Move([
            'user_id' => Auth::id(),
            'x' => $request->x,
            'y' => $request->y,
            'order' => $game->moves()->count() + 1
        ]);

        $game->moves()->save($move);

        $game->switchTurn();

        // Update game state
        $boardState = $game->board_state ?? array_fill(0, 15, array_fill(0, 15, null));
        $boardState[$request->y][$request->x] = Auth::id();
        $game->board_state = $boardState;
        $game->save();

        // Broadcast move to other players
        broadcast(new MoveMade($room->id, $move->toArray()))->toOthers();

        // Check for win condition
        if ($this->checkWin($boardState, $request->x, $request->y, Auth::id())) {
            // Update game status
            $game->update([
                'status' => 'finished',
                'winner_id' => Auth::id()
            ]);

            // Update room status
            $room->update(['status' => 'finished']);

            // Update player stats
            $winner = Auth::user();
            $loser = $room->players->where('id', '!=', Auth::id())->first();

            // Update winner stats
            $winnerStats = PlayerStats::firstOrCreate(['user_id' => $winner->id]);
            $winnerStats->increment('wins');
            $winnerStats->updateRating(true);

            // Update loser stats
            if ($loser) {
                $loserStats = PlayerStats::firstOrCreate(['user_id' => $loser->id]);
                $loserStats->increment('losses');
                $loserStats->updateRating(false);
            }

            // Broadcast the game finished event
            broadcast(new GameFinished($room->id, Auth::id()));

            return response()->json(['status' => 'win']);
        }

        return response()->json(['status' => 'success']);
    }

    public function sendMessage(Request $request, Room $room)
    {
        $request->validate([
            'message' => 'required|string|max:255'
        ]);

        $message = $room->messages()->create([
            'user_id' => Auth::id(),
            'message' => $request->message
        ]);


        broadcast(new MessageSent(
            $room->id,
            [
                'id' => $message->id,
                'user_id' => $message->user_id,
                'message' => $message->message,
                'created_at' => $message->created_at
            ]
        ));

        Log::info('Message sent' . $message);
        return response()->json(['status' => 'success', 'message' => '$message']);
    }

    private function checkWin($board, $x, $y, $playerId): bool
    {
        $directions = [
            [0, 1],  // horizontal
            [1, 0],  // vertical
            [1, 1],  // diagonal right
            [1, -1]  // diagonal left
        ];

        foreach ($directions as $dir) {
            $count = 1;

            // Check forward
            $i = 1;
            while (
                $i <= 4 &&
                isset($board[$y + $dir[0] * $i][$x + $dir[1] * $i]) &&
                $board[$y + $dir[0] * $i][$x + $dir[1] * $i] === $playerId
            ) {
                $count++;
                $i++;
            }

            // Check backward
            $i = 1;
            while (
                $i <= 4 &&
                isset($board[$y - $dir[0] * $i][$x - $dir[1] * $i]) &&
                $board[$y - $dir[0] * $i][$x - $dir[1] * $i] === $playerId
            ) {
                $count++;
                $i++;
            }

            if ($count >= 5) return true;
        }

        return false;
    }
}

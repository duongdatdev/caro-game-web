<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Game;
use App\Events\MoveMade;
use App\Events\GameFinished;
use App\Events\PlayerReady;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Pusher\Pusher;

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

        // Get current game or create new one
        $game = $room->games()->firstOrCreate(
            ['status' => 'playing'],
            [
                'board_state' => array_fill(0, 15, array_fill(0, 15, null)),
                'status' => 'playing'
            ]
        );

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
            'currentPlayer' => Auth::id(),
            'game' => [
                'id' => $game->id,
                'status' => $game->status,
                'board_state' => $game->board_state
            ]
        ]);
    }

    public function toggleReady(Room $room)
    {
        // Check if user is in the room
        $player = $room->players()->where('user_id', Auth::id())->first();
        if (!$player) {
            return response()->json(['error' => 'Player not in room'], 403);
        }

        // Toggle ready state
        $room->players()->updateExistingPivot(Auth::id(), [
            'is_ready' => !$player->pivot->is_ready
        ]);

        // Check if all players are ready
        $allPlayersReady = $room->players()->where('is_ready', true)->count() === $room->players()->count();

        if ($allPlayersReady) {
            $room->update(['status' => 'playing']);

            // Create a new game if one doesn't exist
            $game = $room->games()->firstOrCreate(
                ['status' => 'playing'],
                [
                    'board_state' => array_fill(0, 15, array_fill(0, 15, null)),
                    'status' => 'playing'
                ]
            );

            // Convert room to array with necessary data
            $roomData = [
                'id' => $room->id,
                'status' => $room->status,
                'players' => $room->players->map(function ($player) {
                    return [
                        'id' => $player->id,
                        'name' => $player->name,
                        'pivot' => [
                            'is_ready' => (bool)$player->pivot->is_ready
                        ]
                    ];
                })->toArray()
            ];

            //Config
            $options = array(
                'cluster' => 'ap1',
                'encrypted' => true
            );

            $pusher = new Pusher(
                env('PUSHER_APP_KEY'),
                env('PUSHER_APP_SECRET'),
                env('PUSHER_APP_ID'),
                $options
            );

            $pusher->trigger('room.' . $room->id, 'player.ready', [
                'room' => $roomData,
                'game_id' => $game->id,
                'player_id' => $player->id
            ]);
            // broadcast(new PlayerReady($room->id, $room, $game->id, $player->id))->toOthers();
        }

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

        // Verify it's player's turn
        $lastMove = $game->moves()->latest()->first();
        if ($lastMove && $lastMove->user_id === Auth::id()) {
            return response()->json(['error' => 'Not your turn'], 400);
        }

        // Make move
        $move = $game->moves()->create([
            'user_id' => Auth::id(),
            'x' => $request->x,
            'y' => $request->y,
            'order' => $game->moves()->count() + 1
        ]);

        // Update board state
        $boardState = $game->board_state ?? array_fill(0, 15, array_fill(0, 15, null));
        $boardState[$request->y][$request->x] = Auth::id();
        $game->board_state = $boardState;
        $game->save();

        broadcast(new MoveMade($room->id, $move->toArray()))->toOthers();

        if ($this->checkWin($boardState, $request->x, $request->y, Auth::id())) {
            $game->update([
                'status' => 'finished',
                'winner_id' => Auth::id()
            ]);
            broadcast(new GameFinished($room->id, Auth::id()))->toOthers();
            return response()->json(['status' => 'win']);
        }

        return response()->json(['status' => 'success']);
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

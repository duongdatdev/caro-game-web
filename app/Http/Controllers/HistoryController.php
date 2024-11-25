<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use PgSql\Lob;

class HistoryController extends Controller
{
    public function index()
    {
        DB::enableQueryLog();
        $games = Game::with(['room.players', 'winner'])
            ->whereHas('room.players', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($game) {
                Log::debug('Game Details', [
                    'game_id' => $game->id,
                    'player_id' => Auth::id(),
                    'player_id_check' => $game->room->players->first()->id,
                    'status' => $game->status,
                    'winner_id' => $game->winner_id,
                    'room_id' => $game->room_id,
                    'board_state' => $game->board_state,
                    'created_at' => $game->created_at
                ]);
                return [
                    'id' => $game->id,
                    'opponent' => $this->getOpponentName($game),
                    'result' => $this->getGameResult($game),
                    'rating_change' => $this->getRatingChange($game),
                    'played_at' => $game->created_at->format('Y-m-d H:i')
                ];
            });

        Log::debug('Query Log', ['queries' => DB::getQueryLog()]);
        return Inertia::render('History/Index', [
            'games' => $games,
            'stats' => $this->getPlayerStats()
        ]);
    }

    private function getOpponentName($game)
    {
        try {
            // Ensure the room and players are loaded
            if (!$game->room || !$game->room->players) {
                Log::error('Game room or players not loaded', ['game' => $game->id]);
                return 'Unknown 1';
            }

            // Use the players collection to find the opponent
            $opponent = $game->room->players->firstWhere('id', '!=', Auth::id());

            return $opponent ? $opponent->name : 'Unknown 3';
        } catch (\Exception $e) {
            return 'Unknown 2';
        }
    }

    private function getGameResult($game)
    {
        Log::debug('Game result', ['game' => $game->id, 'winner' => $game->winner_id, 'player' => Auth::id()]);
        if ($game->winner_id === Auth::id()) return 'win';
        if ($game->winner_id) return 'loss';
        return 'draw';
    }

    private function getRatingChange($game)
    {
        if ($game->winner_id === Auth::id()) return 15;
        if ($game->winner_id) return -15;
        return 0;
    }

    private function getPlayerStats()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $stats = $user->playerStats()->firstOrCreate([]);

        return [
            'total_games' => $stats->wins + $stats->losses,
            'wins' => $stats->wins,
            'losses' => $stats->losses,
            'win_rate' => $this->calculateWinRate($stats),
            'rating' => $stats->rating
        ];
    }

    private function calculateWinRate($stats)
    {
        $total = $stats->wins + $stats->losses;
        if ($total === 0) return 0;
        return round(($stats->wins * 100) / $total, 1);
    }
}

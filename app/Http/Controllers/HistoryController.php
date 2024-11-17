<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class HistoryController extends Controller
{
    public function index()
    {
        $games = Game::with(['room.players', 'winner'])
            ->whereHas('room', function ($query) {
                $query->whereHas('players', function ($q) {
                    $q->where('user_id', Auth::id());
                });
            })
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($game) {
                return [
                    'id' => $game->id,
                    'opponent' => $this->getOpponentName($game),
                    'result' => $this->getGameResult($game),
                    'rating_change' => $this->getRatingChange($game),
                    'played_at' => $game->created_at->format('Y-m-d H:i')
                ];
            });

        return Inertia::render('History/Index', [
            'games' => $games,
            'stats' => $this->getPlayerStats()
        ]);
    }

    private function getOpponentName($game)
    {
        try {
            // Check if game and room exist
            if (!$game->room || !$game->room->players) {
                return 'Unknown';
            }

            $opponent = $game->room->players()
                ->where('user_id', '!=', Auth::id())
                ->first();

            return $opponent ? $opponent->name : 'Unknown';
        } catch (\Exception $e) {
            return 'Unknown';
        }
    }

    private function getGameResult($game)
    {
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

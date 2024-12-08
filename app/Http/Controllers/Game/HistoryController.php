<?php

namespace App\Http\Controllers\Game;

use App\Http\Controllers\Controller;

use App\Models\Game;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use PgSql\Lob;
use App\Models\GameHistory;

use function Pest\Laravel\get;

class HistoryController extends Controller
{
    public function index()
    {
        $histories = GameHistory::with(['game', 'user1', 'user2', 'winner'])
        ->where('user1_id', Auth::user()->id)
        ->orWhere('user2_id', Auth::user()->id)
        ->latest()
        ->get()
        ->map(function($history) {
            $currentUser = Auth::user();
            $opponent = $history->user1_id === $currentUser->id 
                ? $history->user2 
                : $history->user1;
            
            $result = 'draw';
            if ($history->winner_id) {
                $result = $history->winner_id === $currentUser->id ? 'win' : 'loss';
            }
            
            return [
                'id' => $history->id,
                'played_at' => $history->created_at->format('Y-m-d H:i'),
                'opponent' => $opponent->name ?? 'Unknown',
                'result' => $result,
                'game_type' => $history->game->type ?? 'Unknown'
            ];
        });

        $stats = $this->getPlayerStats();

        return Inertia::render('History/Index', [
            'histories' => $histories,
            'stats' => $stats
        ]);
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

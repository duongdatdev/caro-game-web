<?php
namespace App\Http\Controllers\Game;

use App\Http\Controllers\Controller;

use App\Models\PlayerStats;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LeaderboardController extends Controller
{
    public function index()
    {
        $players = PlayerStats::with('user')
            ->join('users', 'player_stats.user_id', '=', 'users.id')
            ->select(
                'users.name',
                'player_stats.*',
                DB::raw('(CASE WHEN (wins + losses) > 0 THEN (wins * 100.0 / (wins + losses)) ELSE 0 END) as win_rate')
            )
            ->orderBy('rating', 'desc')
            ->get()
            ->map(function ($player, $index) {
                return [
                    'rank' => $index + 1,
                    'name' => $player->name,
                    'rating' => $player->rating,
                    'wins' => $player->wins,
                    'losses' => $player->losses,
                    'winRate' => round($player->win_rate, 1),
                    'isCurrentUser' => $player->user_id === Auth::id()
                ];
            });

        return Inertia::render('Leaderboard/Index', [
            'players' => $players
        ]);
    }
}
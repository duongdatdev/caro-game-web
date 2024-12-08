<?php

namespace App\Http\Controllers\Game;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;


class DashboardController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Retrieve or create the player's stats
        $statsModel = $user->playerStats()->firstOrCreate([]);

        // Fetch all players ordered by rating in descending order
        $allPlayers = \App\Models\PlayerStats::orderBy('rating', 'desc')->get();

        // Calculate the user's rank based on rating
        $rank = $allPlayers->search(function ($playerStats) use ($user) {
            return $playerStats->user_id === $user->id;
        }) + 1;
        $recentGames = \App\Models\GameHistory::with(['game', 'user1', 'user2', 'winner'])
            ->where(function ($query) use ($user) {
                $query->where('user1_id', $user->id)
                    ->orWhere('user2_id', $user->id);
            })
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($history) use ($user) {
                $opponent = $history->user1_id === $user->id
                    ? $history->user2
                    : $history->user1;

                $result = 'draw';
                if ($history->winner_id) {
                    $result = $history->winner_id === $user->id ? 'win' : 'loss';
                }

                return [
                    'type' => $result,
                    'description' => 'Played against ' . ($opponent ? $opponent->name : 'Unknown'),
                    'time' => $history->created_at->diffForHumans(),
                    'rating_change' => $history->rating_change ?? 0
                ];
            });

        // Prepare the stats array
        $stats = [
            'wins' => $statsModel->wins,
            'losses' => $statsModel->losses,
            'draws' => $statsModel->draws,
            'rating' => $statsModel->rating,
            'rank' => $rank,
        ];

        // Fetch the user's rooms
        $rooms = $user->rooms()->with('players')->get();

        return Inertia::render('Dashboard', [
            'rooms' => $rooms,
            'stats' => $stats,
            'recentActivities' => $recentGames
        ]);
    }
}

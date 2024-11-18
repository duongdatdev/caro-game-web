<?php

namespace App\Http\Controllers;

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
        $recentGames = \App\Models\Game::with(['room.players', 'winner'])
        ->whereHas('room.players', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->orderBy('created_at', 'desc')
        ->take(5)
        ->get()
        ->map(function ($game) use ($user) {
            $opponent = $game->room->players->firstWhere('id', '!=', $user->id);
            $result = $game->winner_id === $user->id ? 'win' : 
                     ($game->winner_id === null ? 'draw' : 'loss');

            return [
                'type' => $result,
                'description' => 'Played against ' . ($opponent ? $opponent->name : 'Unknown'),
                'time' => $game->created_at->diffForHumans(),
                'rating_change' => $game->rating_change ?? 0
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

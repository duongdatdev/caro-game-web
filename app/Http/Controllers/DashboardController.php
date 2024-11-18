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
        ]);
    }
}

<?php

namespace App\Http\Controllers\Game;

use App\Models\Room;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Events\PlayerJoined;
use Illuminate\Support\Facades\Log;
use App\Events\PlayerLeft;
use Illuminate\Support\Facades\DB;
use App\Models\PlayerStats;
use App\Events\GameFinished;
use Illuminate\Routing\Controller;

class RoomController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $rooms = Room::with('creator', 'players')
            ->orderBy('created_at', 'desc')
            ->get();

        Log::info('Rooms: ' . $rooms);

        return Inertia::render('Room/Index', [
            'rooms' => $rooms
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'password' => 'nullable|string|min:6',
        ]);

        $room = Room::create([
            'name' => $request->name,
            'description' => $request->description,
            'password' => $request->password ? bcrypt($request->password) : null,
            'max_players' => 2,
            'created_by' => Auth::id(),
            'status' => 'waiting'
        ]);

        // Automatically join the creator to the room
        $room->players()->attach(Auth::id(), [
            'is_ready' => false
        ]);

        $player = [
            'id' => Auth::id(),
            'name' => Auth::user()->name,
            'pivot' => [
                'is_ready' => false
            ]
        ];

        // Broadcast player joined event 
        broadcast(new PlayerJoined($room->id, $player));

        // Redirect to game show page instead of rooms index
        return redirect()->route('game.show', ['room' => $room->id]);
    }

    public function join(Room $room, Request $request)
    {
        // Validate request if room has password
        if ($room->password) {
            $request->validate([
                'password' => 'required|string'
            ]);
        }

        try {
            // Check room status
            if ($room->status !== 'waiting') {
                return back()->withErrors(['general' => 'This room is no longer accepting players']);
            }

            // Check room capacity
            if ($room->players()->count() >= $room->max_players) {
                return back()->withErrors(['general' => 'Room is full']);
            }

            // Check if user is already in the room
            if ($room->players()->where('user_id', Auth::id())->exists()) {
                return redirect()->route('game.show', $room->id);
            }

            // Verify password if room is password protected
            if ($room->password && !Hash::check($request->password, $room->password)) {
                return back()->withErrors(['password' => 'Invalid password']);
            }

            // Join room within a transaction
            DB::transaction(function () use ($room) {
                // Join room
                $room->players()->attach(Auth::id(), [
                    'is_ready' => false
                ]);

                // Broadcast player joined event
                $player = [
                    'id' => Auth::id(),
                    'name' => Auth::user()->name,
                    'pivot' => [
                        'is_ready' => false
                    ]
                ];
                broadcast(new PlayerJoined($room->id, $player));
            });

            return redirect()->route('game.show', $room->id);
        } catch (\Exception $e) {
            Log::error('Failed to join room:', [
                'room_id' => $room->id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            return back()->withErrors(['general' => 'Failed to join room. Please try again.']);
        }
    }
    public function leave(Room $room)
    {
        // Check if player is in room
        if (!$room->players()->where('user_id', Auth::id())->exists()) {
            return back()->withErrors(['error' => 'You are not part of this room']);
        }

        // Handle ongoing game
        if ($room->status === 'playing') {
            $game = $room->games()->where('status', 'playing')->first();
            if ($game) {
                // Find opponent
                $opponent = $room->players->where('id', '!=', Auth::id())->first();

                // Update game status
                $game->update([
                    'status' => 'finished',
                    'winner_id' => $opponent ? $opponent->id : null
                ]);

                // Update room status to finished
                $room->update(['status' => 'finished']);

                // Update stats only if opponent exists
                if ($opponent) {
                    // Update winner (opponent) stats
                    $winnerStats = PlayerStats::firstOrCreate(['user_id' => $opponent->id]);
                    $winnerStats->increment('wins');
                    $winnerStats->updateRating(true);

                    // Update loser (leaving player) stats
                    $loserStats = PlayerStats::firstOrCreate(['user_id' => Auth::id()]);
                    $loserStats->increment('losses');
                    $loserStats->updateRating(false);

                    // Broadcast game finished event
                    broadcast(new GameFinished($room->id, $opponent->id));
                }

                // Broadcast player left event
                broadcast(new PlayerLeft($room->id, [
                    'id' => Auth::id(),
                    'name' => Auth::user()->name,
                ]));

                return redirect()->route('rooms.index')
                    ->with('success', 'You have left the room and forfeited the game');
            }
        } else {
            // For non-playing rooms, remove player
            $room->players()->detach(Auth::id());

            // Broadcast player left event
            broadcast(new PlayerLeft($room->id, [
                'id' => Auth::id(),
                'name' => Auth::user()->name,
            ]));
        }

        return redirect()->route('rooms.index')
            ->with('success', 'You have left the room');
    }
}

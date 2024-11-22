<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use App\Events\PlayerJoined;
use Illuminate\Support\Facades\Log;
use App\Events\PlayerLeft;

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
        if ($room->players()->count() >= 2) {
            return back()->withErrors(['error' => 'Room is full']);
        }

        // Check if player is already in the room
        if ($room->players()->where('user_id', Auth::id())->exists()) {
            return back()->withErrors(['error' => 'You are already in this room']);
        }

        // Check password if room has one
        if ($room->password) {
            if (!$request->password || !Hash::check($request->password, $room->password)) {
                return back()->withErrors(['password' => 'Invalid password']);
            }
        }

        // Join room
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

        broadcast(new PlayerJoined($room->id, $player));

        return redirect()->route('game.show', ['room' => $room->id]);
    }

    public function leave(Room $room)
{
    // Check if the player is part of the room
    if (!$room->players()->where('user_id', Auth::id())->exists()) {
        return back()->withErrors(['error' => 'You are not part of this room']);
    }

    // Remove the player from the room
    $room->players()->detach(Auth::id());

    // Broadcast the PlayerLeft event
    $player = [
        'id' => Auth::id(),
        'name' => Auth::user()->name,
    ];

    broadcast(new PlayerLeft($room->id, $player));

    // Optional: Update room status if necessary
    if ($room->players()->count() < 2 && $room->status === 'playing') {
        $room->update(['status' => 'waiting']);
        // You might also want to notify the remaining player(s) about the status change
    }

    return redirect()->route('rooms.index')->with('success', 'You have left the room');
}
}

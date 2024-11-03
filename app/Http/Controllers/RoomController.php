<?php
namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
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
            'max_players' => 2, // Fixed at 2 players
            'created_by' => Auth::id(),
            'status' => 'waiting'
        ]);

        return redirect()->route('rooms.index');
    }

    public function join(Room $room, Request $request)
    {
        $request->validate([
            'password' => 'required_if:has_password,true|string',
        ]);

        if ($room->password && !password_verify($request->password, $room->password)) {
            return back()->withErrors(['password' => 'Incorrect password']);
        }

        if ($room->players()->count() >= $room->max_players) {
            return back()->withErrors(['room' => 'Room is full']);
        }

        $room->players()->attach(Auth::id(), ['joined_at' => now()]);

        return redirect()->route('rooms.show', $room);
    }
}
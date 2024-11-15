<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Room;
use Illuminate\Support\Facades\Log;

// Broadcast::channel('user.{userId}', function ($user, $userId) {
//     return $user->id === $userId;
//   });

// Room channel authorization 
Broadcast::channel('room.{roomId}', function ($user, $roomId) {
    Log::debug('Room channel authorization called', [
        'room_id' => $roomId,
        'user' => $user ? $user->id : 'no user'
    ]);
    
    try {
        $room = Room::find($roomId);
        if (!$room) {
            Log::warning('Room not found', ['room_id' => $roomId]);
            return false;
        }
        
        $exists = $room->players()->where('user_id', $user->id)->exists();
        Log::info('Room authorization result', [
            'room_id' => $roomId,
            'user_id' => $user->id,
            'authorized' => $exists
        ]);
        
        return $exists;
    } catch (\Exception $e) {
        Log::error('Room authorization error', [
            'error' => $e->getMessage(),
            'room_id' => $roomId
        ]);
        return false;
    }
});

Broadcast::channel('test-channel', function() {
    Log::debug('Test channel authorization attempt');
    return true;
});
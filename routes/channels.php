<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Room;
use Illuminate\Support\Facades\Log;

// Add trace for file load
Log::debug('Loading channels.php');

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

// Test channel authorization with enhanced logging
// Broadcast::channel('test-channel', function ($user) {
//     Log::debug('Test channel callback triggered');
    
//     try {
//         // Force log write
//         Log::debug('Test authorization START');
//         Log::debug('Auth check status: ' . (auth()->check() ? 'true' : 'false'));
//         Log::debug('User present: ' . ($user ? 'yes' : 'no'));
        
//         $isAuthorized = auth()->check() && !is_null($user);
        
//         Log::info('Test channel result', [
//             'authorized' => $isAuthorized,
//             'user_id' => $user ? $user->id : null,
//             'time' => now()
//         ]);
        
//         // Force log write
//         Log::debug('Test authorization END');
        
//         return $isAuthorized;
//     } catch (\Exception $e) {
//         Log::error('Test channel error: ' . $e->getMessage());
//         return false;
//     }
// });

// // Verify routes are registered
// Log::info('Broadcasting routes registered', [
//     'channels' => ['room.{roomId}', 'test-channel'],
//     'time' => now()
// ]);
Broadcast::channel('test-channel', function() {
    return true; // Always allow access
  });
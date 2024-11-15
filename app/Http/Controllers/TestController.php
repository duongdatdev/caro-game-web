<?php
namespace App\Http\Controllers;

use App\Events\TestEvent;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Pusher\Pusher;

class TestController extends Controller
{
    public function triggerEvent(Request $request)
    {
        // $data = ['message' => 'This is a test event'];
        // event(new TestEvent($data));
        return Inertia::render('TestMessage/Index');
    }

    public function sendTestMessage(Request $request)
    {
        // $options = array(
        //     'cluster' => 'ap1',
        //     'encrypted' => true
        // );

        // $pusher = new Pusher(
        //     env('PUSHER_APP_KEY'),
        //     env('PUSHER_APP_SECRET'),
        //     env('PUSHER_APP_ID'),
        //     $options
        // );

        // $pusher->trigger('test-channel', 'test-event', $message);

        try {
            $message = $request->input('message');
            Log::info('Attempting to broadcast message', [
                'message' => $message,
                'channel' => 'test-channel',
                'event' => 'test-event'
            ]);
            
            broadcast(new TestEvent($message));
            
            Log::info('Broadcast successful');
            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error('Broadcasting error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}

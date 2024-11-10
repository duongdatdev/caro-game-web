<?php
namespace App\Http\Controllers;

use App\Events\TestEvent;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class TestController extends Controller
{
    public function triggerEvent(Request $request)
    {
        $data = ['message' => 'This is a test event'];
        event(new TestEvent($data));
        return Inertia::render('TestMessage/Index');
    }

    public function sendTestMessage(Request $request)
    {
        $message = $request->input('message');
        broadcast(new TestEvent(['message' => $message]));
        return response()->json(['status' => 'Message sent']);
    }
}

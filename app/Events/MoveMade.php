<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MoveMade implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $roomId,
        public array $move
    ) {}

    public function broadcastOn()
    {
        return new Channel('private-room.' . $this->roomId);
    }

    public function broadcastAs()
    {
        return 'move.made';
    }

    public function broadcastWith()
    {
        return [
            'move' => [
                'x' => $this->move['x'],
                'y' => $this->move['y'],
                'user_id' => $this->move['user_id'],
                'order' => $this->move['order']
            ]
        ];
    }
}
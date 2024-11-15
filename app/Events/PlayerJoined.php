<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PlayerJoined implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $roomId;
    public $player;

    public function __construct($roomId, $player)
    {
        $this->roomId = $roomId;
        $this->player = $player;
    }

    public function broadcastOn()
    {
        return new Channel('private-room.' . $this->roomId);
    }

    public function broadcastAs()
    {
        return 'player.joined';
    }
}
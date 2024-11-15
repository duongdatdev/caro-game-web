<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PlayerReady implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $roomId;
    public $playerId;
    public $isReady;
    public $roomStatus;

    public function __construct($roomId, $playerId, $isReady, $roomStatus)
    {
        $this->roomId = $roomId;
        $this->playerId = $playerId;
        $this->isReady = $isReady;
        $this->roomStatus = $roomStatus;
    }

    public function broadcastOn()
    {
        return new Channel('private-room.' . $this->roomId);
    }

    public function broadcastAs()
    {
        return 'player.ready';
    }
}
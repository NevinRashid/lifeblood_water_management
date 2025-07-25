<?php

namespace Modules\TicketsAndReforms\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\TicketsAndReforms\Models\Reform;

class ReformStatusChangedToInProgress
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $reform;
    /**
     * Create a new event instance.
     */
    public function __construct(Reform $reform)
    {
        $this->reform = $reform;
    }


    /**
     * Get the channels the event should be broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}

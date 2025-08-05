<?php

namespace Modules\TicketsAndReforms\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\TicketsAndReforms\Models\TroubleTicket;

class TroubleRejected
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $trouble;
    /**
     * Create a new event instance.
     */
    public function __construct(TroubleTicket $trouble)
    {
        $this->trouble = $trouble;
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

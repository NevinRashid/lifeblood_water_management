<?php

namespace Modules\WaterDistributionOperations\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Modules\DistributionNetwork\Models\Reservoir;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ReservoirCriticalLevelReached
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public Reservoir $reservoir;
    public float $currentLevel;

    public function __construct(Reservoir $reservoir, float $currentLevel)
    {
        $this->reservoir = $reservoir;
        $this->currentLevel = $currentLevel;
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

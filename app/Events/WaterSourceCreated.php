<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Modules\WaterSources\Models\WaterSource;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class WaterSourceCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

  /**
     * The water source instance.
     *
     * @var \Modules\WaterSources\Models\WaterSource
     */
    public $waterSource;

    /**
     * Create a new event instance.
     *
     * @param  \Modules\WaterSources\Models\WaterSource  $waterSource
     * @return void
     */
    public function __construct(WaterSource $waterSource)
    {
        $this->waterSource = $waterSource;
    }
    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}

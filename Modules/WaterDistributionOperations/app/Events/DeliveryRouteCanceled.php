<?php

namespace Modules\WaterDistributionOperations\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\WaterDistributionOperations\Models\DeliveryRoute;

class DeliveryRouteCanceled
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The canceled delivery route instance.
     *
     * @var DeliveryRoute
     */
    public DeliveryRoute $deliveryRoute;

    /**
     * Create a new event instance.
     *
     * @param DeliveryRoute $deliveryRoute
     * @return void
     */
    public function __construct(DeliveryRoute $deliveryRoute)
    {
        $this->deliveryRoute = $deliveryRoute;
    }
}

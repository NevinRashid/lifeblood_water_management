<?php

namespace Modules\WaterSources\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\WaterSources\Models\WaterExtraction;

class WaterExtracted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    public $waterExtraction;
    public $distributionNetworkId;

    public function __construct(WaterExtraction $waterExtraction, $distributionNetworkId)
    {
        $this->waterExtraction = $waterExtraction;
        $this->distributionNetworkId = $distributionNetworkId;
    }
}

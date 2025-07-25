<?php

namespace Modules\Sensors\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Sensors\Models\SensorReading;

class SensorReadingCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public SensorReading $reading,
        public bool $isAbnormal = false,
        public ?string $abnormalityType = null  // 'below_min' or 'above_max'
    ) {}

}

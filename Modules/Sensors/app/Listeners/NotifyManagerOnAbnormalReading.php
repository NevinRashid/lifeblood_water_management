<?php

namespace Modules\Sensors\Listeners;


use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Sensors\Events\SensorReadingCreated;
use Modules\Sensors\Jobs\SendAbnormalReading;

class NotifyManagerOnAbnormalReading
{
    /**
     * Create the event listener.
     */
    public function __construct() {}

    /**
     * Handle the event.
     */
    public function handle(SensorReadingCreated $event)
    {
        if (!$event->isAbnormal) return;

        // Dispatch to queue
        SendAbnormalReading::dispatch(
            reading: $event->reading,
            abnormalityType: $event->abnormalityType
        );
    }
}

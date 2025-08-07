<?php

namespace Modules\WaterSources\Listeners;


use Illuminate\Support\Facades\Log;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\WaterSources\Events\WaterSourceCreated;


class LogWaterSourceCreation
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * 
     * @param \Modules\WaterSources\Events\WaterSourceCreated $event
     * @return void
     */
    public function handle(WaterSourceCreated $event)
    {
        $waterSource = $event->waterSource;

        Log::info("A new water source has been created.", [
            'id' => $waterSource->id,
            'name' => $waterSource->name,
            'source' => $waterSource->source,
            'status' => $waterSource->status,
        ]);
    }
}

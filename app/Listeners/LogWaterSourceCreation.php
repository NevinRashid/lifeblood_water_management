<?php

namespace App\Listeners;


use App\Events\WaterSourceCreated;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;


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
     * @param \AppEvents\WaterSourceCreated $event
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

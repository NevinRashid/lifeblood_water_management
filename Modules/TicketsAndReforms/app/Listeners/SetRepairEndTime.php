<?php

namespace Modules\TicketsAndReforms\Listeners;

use Modules\TicketsAndReforms\app\Events\ReformStatusChangedToCompleted;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\TicketsAndReforms\Events\ReformStatusChangedToCompleted as EventsReformStatusChangedToCompleted;

class SetRepairEndTime
{
    /**
     * Create the event listener.
     */
    public function __construct() {}

    /**
     * Handle the event.
     */
    public function handle(EventsReformStatusChangedToCompleted $event)
    {
        $reform = $event->reform;
        if(!$reform->end_date){
            $reform->update([
                    'end_date' => now()
                    ]);
        }
    }
}

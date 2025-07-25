<?php

namespace Modules\TicketsAndReforms\Listeners;

use Modules\TicketsAndReforms\app\Events\ReformStatusChangedToInProgress;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\TicketsAndReforms\Events\ReformStatusChangedToInProgress as EventsReformStatusChangedToInProgress;

class SetRepairStartTime
{
    /**
     * Create the event listener.
     */
    public function __construct() {}

    /**
     * Handle the event.
     */
    public function handle(EventsReformStatusChangedToInProgress $event): void {
        $reform = $event->reform;
        if(!$reform->start_date){
            $reform->update([
                    'start_date' => now()
                    ]);
        }
    }
}

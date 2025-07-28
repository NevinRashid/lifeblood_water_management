<?php

namespace Modules\TicketsAndReforms\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\TicketsAndReforms\Events\ReformStatusChangedToInProgress;

class SetTroubleTicketStatusInProgress
{
    /**
     * Create the event listener.
     */
    public function __construct() {}

    /**
     * Handle the event.
     */
    public function handle(ReformStatusChangedToInProgress $event): void
    {
        $reform = $event->reform;
        $reform->ticket()->update(['status'=>'in_progress']);
    }
}

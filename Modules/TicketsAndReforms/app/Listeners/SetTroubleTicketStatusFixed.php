<?php

namespace Modules\TicketsAndReforms\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\TicketsAndReforms\Events\ReformStatusChangedToCompleted;

class SetTroubleTicketStatusFixed
{
    /**
     * Create the event listener.
     */
    public function __construct() {}

    /**
     * Handle the event.
     */
    public function handle(ReformStatusChangedToCompleted $event): void
    {
        $reform = $event->reform;
        $reform->ticket()->update(['status'=>'fixed']);
    }
}

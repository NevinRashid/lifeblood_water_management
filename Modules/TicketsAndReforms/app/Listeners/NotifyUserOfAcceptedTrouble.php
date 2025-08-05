<?php

namespace Modules\TicketsAndReforms\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\TicketsAndReforms\Events\CitizenReportOfTroubleAccepted;
use Modules\TicketsAndReforms\Notifications\TroubleAcceptedNotification;

class NotifyUserOfAcceptedTrouble
{
    /**
     * Create the event listener.
     */
    public function __construct() {}

    /**
     * Handle the event.
     */
    public function handle(CitizenReportOfTroubleAccepted $event): void
    {
        $trouble =$event->trouble;
        $reporter = $trouble->reporter;
        $reporter->notify(new TroubleAcceptedNotification($trouble));

    }
}

<?php

namespace Modules\TicketsAndReforms\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\TicketsAndReforms\Events\TroubleRejected;
use Modules\TicketsAndReforms\Notifications\TroubleRejectedNotification;

class NotifyUserOfRejectedTrouble
{
    /**
     * Create the event listener.
     */
    public function __construct() {}

    /**
     * Handle the event.
     */
    public function handle(TroubleRejected $event): void
    {
        $trouble = $event->trouble;
        $reporter = $trouble->reporter;
        $reporter->notify(new TroubleRejectedNotification($trouble));
    }
}

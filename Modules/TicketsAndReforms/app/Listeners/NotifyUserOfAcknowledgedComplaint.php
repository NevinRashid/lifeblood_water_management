<?php

namespace Modules\TicketsAndReforms\Listeners;

use Modules\TicketsAndReforms\app\Events\ComplaintAcknowledged;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyUserOfAcknowledgedComplaint
{
    /**
     * Create the event listener.
     */
    public function __construct() {}

    /**
     * Handle the event.
     */
    public function handle(ComplaintAcknowledged $event): void {}
}

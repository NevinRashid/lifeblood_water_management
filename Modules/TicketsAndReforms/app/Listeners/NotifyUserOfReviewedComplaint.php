<?php

namespace Modules\TicketsAndReforms\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\TicketsAndReforms\Events\ComplaintReviewed;
use Modules\TicketsAndReforms\Notifications\ComplaintReviewedNotification;

class NotifyUserOfReviewedComplaint
{
    /**
     * Create the event listener.
     */
    public function __construct() {}

    /**
     * Handle the event.
     */
    public function handle(ComplaintReviewed $event): void
    {
        $trouble = $event->trouble;
        $reporter = $trouble->reporter;
        $reporter->notify(new ComplaintReviewedNotification($trouble));
    }
}

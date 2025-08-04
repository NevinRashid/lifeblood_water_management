<?php

namespace Modules\TicketsAndReforms\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use Modules\TicketsAndReforms\Events\NewReformCreated;
use Modules\TicketsAndReforms\Notifications\NewReformNotification;

class SendReformNotification
{
    /**
     * Create the event listener.
     */
    public function __construct() {}

    /**
     * Handle the event.
     */
    public function handle(NewReformCreated $event): void
    {
        $reform = $event->reform;
        $members = $reform->team->members;
        Notification::send($members, new NewReformNotification($reform));
    }
}

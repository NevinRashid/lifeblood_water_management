<?php

namespace Modules\TicketsAndReforms\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use Modules\TicketsAndReforms\Events\ReformScheduleUpdated;
use Modules\TicketsAndReforms\Notifications\ReformScheduleChangedNotification;

class NotifyRepairTeamOfScheduleUpdate
{
    /**
     * Create the event listener.
     */
    public function __construct() {}

    /**
     * Handle the event.
     */
    public function handle(ReformScheduleUpdated $event): void {
        $reform = $event->reform;
        $changedFields = $event->changedFields;
        $members = $reform->team->members;
        Notification::send($members, new ReformScheduleChangedNotification($reform ,$changedFields));
    }
}

<?php

namespace Modules\TicketsAndReforms\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use Modules\TicketsAndReforms\Events\WaterSupplyRestored;
use Modules\TicketsAndReforms\Notifications\WaterRestorationNotification;
use Modules\UsersAndTeams\Models\User;

class NotifyCitizenAboutRestoration
{
    /**
     * Create the event listener.
     */
    public function __construct() {}

    /**
     * Handle the event.
     */
    public function handle(WaterSupplyRestored $event): void
    {
        $troubleTicket = $event->troubleTicket;
        $network = $troubleTicket->getNetwork();
        $citizen = User::ServedByNetwork($network->id)->get();
        Notification::send($citizen, new WaterRestorationNotification($troubleTicket));
    }
}

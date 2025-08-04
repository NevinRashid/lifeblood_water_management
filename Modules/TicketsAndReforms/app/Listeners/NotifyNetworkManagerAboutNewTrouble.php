<?php

namespace Modules\TicketsAndReforms\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\TicketsAndReforms\Events\NewTroubleTicketCreated;
use Modules\TicketsAndReforms\Notifications\NewTroubleTicketNotification;

class NotifyNetworkManagerAboutNewTrouble
{
    /**
     * Create the event listener.
     */
    public function __construct() {}

    /**
     * Handle the event.
     */
    public function handle(NewTroubleTicketCreated $event): void
    {
        $troubleTicket = $event->troubleTicket;
        $manager = $troubleTicket->getNetwork()?->manager;
        $manager->notify(new NewTroubleTicketNotification($troubleTicket));
    }
}

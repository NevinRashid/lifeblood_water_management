<?php

namespace Modules\TicketsAndReforms\Observers;

use Modules\TicketsAndReforms\Events\NewTroubleTicketCreated;
use Modules\TicketsAndReforms\Events\WaterSupplyInterrupted;
use Modules\TicketsAndReforms\Events\WaterSupplyRestored;
use Modules\TicketsAndReforms\Events\WaterSupplyStatusChanged;
use Modules\TicketsAndReforms\Models\TroubleTicket;

class TroubleTicketObserver
{
    /**
     * Handle the TroubleTicket "created" event.
     */
    public function created(TroubleTicket $troubleticket): void
    {
        //If there is a report confirming a trouble and its status is waiting_assignment
        if($troubleticket->wasRecentlyCreated && $troubleticket->status === 'waiting_assignment'){
            //A new TroubleTicket event will be triggered, which will alert the network manager of the presence of a malfunction,
            //in addition to alerting citizens of a water outage due to repairs.
            event(new NewTroubleTicketCreated($troubleticket));
        }
    }

    /**
     * Handle the TroubleTicket "updated" event.
     */
    public function updated(TroubleTicket $troubleticket): void
    {
        if($troubleticket->isDirty('status')){
            if($troubleticket->status === 'fixed'){
                event(new WaterSupplyRestored($troubleticket));
            }
        }
    }

    /**
     * Handle the TroubleTicket "deleted" event.
     */
    public function deleted(TroubleTicket $troubleticket): void {}

    /**
     * Handle the TroubleTicket "restored" event.
     */
    public function restored(TroubleTicket $troubleticket): void {}

    /**
     * Handle the TroubleTicket "force deleted" event.
     */
    public function forceDeleted(TroubleTicket $troubleticket): void {}
}

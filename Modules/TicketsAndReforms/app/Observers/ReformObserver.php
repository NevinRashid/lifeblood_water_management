<?php

namespace Modules\TicketsAndReforms\Observers;

use Modules\TicketsAndReforms\Events\ReformStatusChangedToCompleted;
use Modules\TicketsAndReforms\Events\ReformStatusChangedToInProgress;
use Modules\TicketsAndReforms\Models\Reform;

class ReformObserver
{
    /**
     * Handle the Reform "created" event.
     */
    public function created(Reform $reform): void {}

    /**
     * Handle the Reform "updated" event.
     */
    public function updated(Reform $reform): void
    {
        if($reform->isDirty('status')){
            if($reform->status === 'in_progress'){
                event(new ReformStatusChangedToInProgress($reform));
            }
            elseif($reform->status === 'completed'){
                event(new ReformStatusChangedToCompleted($reform));
            }

        }
    }

    /**
     * Handle the Reform "deleted" event.
     */
    public function deleted(Reform $reform): void {}

    /**
     * Handle the Reform "restored" event.
     */
    public function restored(Reform $reform): void {}

    /**
     * Handle the Reform "force deleted" event.
     */
    public function forceDeleted(Reform $reform): void {}
}

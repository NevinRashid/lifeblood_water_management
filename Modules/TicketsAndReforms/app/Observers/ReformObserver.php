<?php

namespace Modules\TicketsAndReforms\Observers;

use Modules\TicketsAndReforms\Events\NewReformCreated;
use Modules\TicketsAndReforms\Events\ReformScheduleUpdated;
use Modules\TicketsAndReforms\Events\ReformStatusChangedToCompleted;
use Modules\TicketsAndReforms\Events\ReformStatusChangedToInProgress;
use Modules\TicketsAndReforms\Models\Reform;

class ReformObserver
{
    /**
     * Handle the Reform "created" event.
     */
    public function created(Reform $reform): void
    {
        if($reform->wasRecentlyCreated){
            $reform->ticket()->update(['status' => 'assigned']);
            event(new NewReformCreated($reform));
        }
    }

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

        $dirty = $reform->getDirty();
        $changedFields = array_intersect(
        ['expected_start_date', 'expected_end_date'],
        array_keys($dirty)
        );

        if (!empty($changedFields)) {
            event(new ReformScheduleUpdated($reform, $changedFields));
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

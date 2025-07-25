<?php

namespace Modules\WaterDistributionOperations\Listeners;

use Modules\UsersAndTeams\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use Modules\WaterDistributionOperations\app\Events\ReservoirCriticalLevelReached;
use Modules\WaterDistributionOperations\Notifications\ReservoirCriticalLevelNotification;

class SendCriticalReservoirAlert
{
    /**
     * Create the event listener.
     */
    public function __construct() {}

    /**
     * Handle the event.
     */
    public function handle(ReservoirCriticalLevelReached $event)
    {

            $user = User::first(); 
            $user->notify(new ReservoirCriticalLevelNotification($event->reservoir, $event->currentLevel));

        
    }
}

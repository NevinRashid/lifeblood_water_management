<?php

namespace Modules\WaterDistributionOperations\Listeners;

use Modules\UsersAndTeams\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use Modules\WaterDistributionOperations\Events\ReservoirCriticalLevelReached;
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
        $reservoir = $event->reservoir;
        $currentLevel = $event->currentLevel;
        $manager = $event->reservoir->network->manager;
        $manager->notify(new ReservoirCriticalLevelNotification($reservoir, $currentLevel));
    }
}

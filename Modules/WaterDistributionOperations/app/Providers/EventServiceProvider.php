<?php

namespace Modules\WaterDistributionOperations\Providers;

use Modules\WaterDistributionOperations\Events\ReservoirCriticalLevelReached;
use Modules\WaterDistributionOperations\Listeners\SendCriticalReservoirAlert;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\WaterDistributionOperations\Events\DeliveryRouteCanceled;
use Modules\WaterDistributionOperations\Listeners\SendCanceledDeliveryRouteNotification;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array<string, array<int, string>>
     */
    protected $listen = [
        DeliveryRouteCanceled::class => [
            SendCanceledDeliveryRouteNotification::class,
            ReservoirCriticalLevelReached::class => [
                SendCriticalReservoirAlert::class,
            ],
        ]
    ];

    /**
     * Indicates if events should be discovered.
     *
     * @var bool
     */
    protected static $shouldDiscoverEvents = true;

    /**
     * Configure the proper event listeners for email verification.
     */
    protected function configureEmailVerification(): void {}
}

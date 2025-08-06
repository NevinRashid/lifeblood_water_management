<?php

namespace Modules\WaterSources\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\WaterSources\Events\WaterExtracted;
use Modules\WaterSources\Listeners\SendWaterExtractionNotification;
use Modules\WaterSources\Listeners\UpdateDistributionNetworkVolume;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array<string, array<int, string>>
     */
    protected $listen = [
        WaterExtracted::class => [
            UpdateDistributionNetworkVolume::class,
            SendWaterExtractionNotification::class,
        ],
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

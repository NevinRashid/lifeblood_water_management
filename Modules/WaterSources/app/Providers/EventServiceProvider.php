<?php

namespace Modules\WaterSources\Providers;

use Modules\WaterSources\Events\WaterTestFailed;
use Modules\WaterSources\Models\WaterQualityTest;
use Modules\WaterSources\Observers\WaterQualityTestObserver;
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

    protected $observers = [
        WaterQualityTest::class => [WaterQualityTestObserver::class],
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

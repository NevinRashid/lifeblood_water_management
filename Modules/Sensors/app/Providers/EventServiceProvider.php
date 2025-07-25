<?php

namespace Modules\Sensors\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Sensors\Events\SensorReadingCreated;
use Modules\Sensors\Listeners\CheckSensorLimits;
use Modules\Sensors\Listeners\NotifyManagerOnAbnormalReading;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array<string, array<int, string>>
     */
    protected $listen = [
        SensorReadingCreated::class => [
            CheckSensorLimits::class,
            NotifyManagerOnAbnormalReading::class,
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

<?php

namespace Modules\TicketsAndReforms\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\TicketsAndReforms\Events\ReformStatusChangedToCompleted;
use Modules\TicketsAndReforms\Events\ReformStatusChangedToInProgress;
use Modules\TicketsAndReforms\Listeners\SetRepairEndTime;
use Modules\TicketsAndReforms\Listeners\SetRepairStartTime;
use Modules\TicketsAndReforms\Listeners\SetTroubleTicketStatusFixed;
use Modules\TicketsAndReforms\Listeners\SetTroubleTicketStatusInProgress;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array<string, array<int, string>>
     */
    protected $listen = [
        ReformStatusChangedToInProgress::class => [
        SetRepairStartTime::class,
        SetTroubleTicketStatusInProgress::class
        ],

        ReformStatusChangedToCompleted::class => [
        SetRepairEndTime::class,
        SetTroubleTicketStatusFixed::class
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

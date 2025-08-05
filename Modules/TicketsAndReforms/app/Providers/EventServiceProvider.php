<?php

namespace Modules\TicketsAndReforms\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\TicketsAndReforms\Events\CitizenReportOfTroubleAccepted;
use Modules\TicketsAndReforms\Events\ComplaintAcknowledged;
use Modules\TicketsAndReforms\Events\ComplaintReviewed;
use Modules\TicketsAndReforms\Events\NewReformCreated;
use Modules\TicketsAndReforms\Events\NewTroubleTicketCreated;
use Modules\TicketsAndReforms\Events\ReformScheduleUpdated;
use Modules\TicketsAndReforms\Events\ReformStatusChangedToCompleted;
use Modules\TicketsAndReforms\Events\ReformStatusChangedToInProgress;
use Modules\TicketsAndReforms\Events\TroubleRejected;
use Modules\TicketsAndReforms\Events\WaterSupplyRestored;
use Modules\TicketsAndReforms\Listeners\NotifyCitizenAboutInterruption;
use Modules\TicketsAndReforms\Listeners\NotifyCitizenAboutRestoration;
use Modules\TicketsAndReforms\Listeners\NotifyNetworkManagerAboutNewTrouble;
use Modules\TicketsAndReforms\Listeners\NotifyRepairTeamOfScheduleUpdate;
use Modules\TicketsAndReforms\Listeners\NotifyUserOfAcceptedTrouble;
use Modules\TicketsAndReforms\Listeners\NotifyUserOfAcknowledgedComplaint;
use Modules\TicketsAndReforms\Listeners\NotifyUserOfRejectedTrouble;
use Modules\TicketsAndReforms\Listeners\NotifyUserOfReviewedComplaint;
use Modules\TicketsAndReforms\Listeners\SendReformNotification;
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

        NewTroubleTicketCreated::class => [
        NotifyNetworkManagerAboutNewTrouble::class,
        NotifyCitizenAboutInterruption::class,
        ],

        NewReformCreated::class => [
        SendReformNotification::class,
        ],

        WaterSupplyRestored::class => [
        NotifyCitizenAboutRestoration::class,
        ],

        ReformScheduleUpdated::class => [
        NotifyRepairTeamOfScheduleUpdate::class,
        ],

        CitizenReportOfTroubleAccepted::class => [
        NotifyUserOfAcceptedTrouble::class,
        ],

        ComplaintReviewed::class => [
        NotifyUserOfReviewedComplaint::class,
        ],

        TroubleRejected::class => [
        NotifyUserOfRejectedTrouble::class,
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

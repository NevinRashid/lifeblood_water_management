<?php

namespace Modules\WaterSources\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\DistributionNetwork\Models\DistributionNetwork;
use Modules\WaterSources\Notifications\WaterExtractionNotification;

class SendWaterExtractionNotification
{
    /**
     * Create the event listener.
     */
    public function __construct() {}

    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        $network = DistributionNetwork::find($event->distributionNetworkId);

        if ($network && $network->manager) {
            $manager = $network->manager;

            //prepare data
            $mailData = [
                'networkName' => $network->name,
                'source' => $event->waterExtraction->waterSource->name,
                'extractedAmount' => $event->waterExtraction->extracted,
                'deliveredAmount' => $event->waterExtraction->delivered_amount,
                'lostAmount' => $event->waterExtraction->lost_amount,
                'extractionDate' => $event->waterExtraction->created_at,
            ];

            // send email to manager of distribution network
            $manager->notify(new WaterExtractionNotification($mailData));
        }
    }
}

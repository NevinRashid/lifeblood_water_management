<?php

namespace Modules\WaterSources\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\DistributionNetwork\Models\DistributionNetwork;
use Modules\DistributionNetwork\Services\DistributionNetworkService;
use Modules\WaterSources\Events\WaterExtracted;

class UpdateDistributionNetworkVolume
{
    protected $networkService;

    public function __construct(DistributionNetworkService $networkService)
    {
        $this->networkService = $networkService;
    }

    public function handle(WaterExtracted $event)
    {
        $network = DistributionNetwork::find($event->distributionNetworkId);

        if ($network) {
            $lostAmount = ($network->loss_percentage / 100) * $event->waterExtraction->extracted;
            $deliveredAmount = $event->waterExtraction->extracted - $lostAmount;

            // Update network volume
            $this->networkService->updateCurrentVolume(
                ['extracted' => $deliveredAmount], // Now passing the delivered amount
                $network,
            );

            // Update extraction record
            $event->waterExtraction->update([
                'lost_amount' => $lostAmount,
                'delivered_amount' => $deliveredAmount
            ]);
        }
    }
}

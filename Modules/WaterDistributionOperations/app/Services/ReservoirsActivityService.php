<?php

namespace Modules\WaterDistributionOperations\Services;

use App\Services\Base\BaseService;
use Illuminate\Support\Facades\Log;
use Modules\DistributionNetwork\Models\Reservoir;
use Modules\WaterDistributionOperations\Models\ReservoirActivity;
use Modules\WaterDistributionOperations\Events\ReservoirCriticalLevelReached;

class ReservoirsActivityService extends BaseService
{
 

    public function __construct()
    {
        $this->model = new ReservoirActivity();
    }


    public function calculateCurrentLevel(int $reservoirId): float
    {
        $fillSum = ReservoirActivity::where('reservoir_id', $reservoirId)
            ->whereIn('activity_type', ['filling_ended', 'level_restored_above_critical'])
            ->sum('amount');

        $emptySum = ReservoirActivity::where('reservoir_id', $reservoirId)
            ->whereIn('activity_type', ['emptying_ended', 'critical_low_level'])
            ->sum('amount');

            Log::info("Reservoir $reservoirId - fillSum: $fillSum, emptySum: $emptySum");
        return $fillSum - $emptySum;
    }


    public function checkAndAlertCriticalLevel(int $reservoirId): ?array
    {
    $currentLevel = $this->calculateCurrentLevel($reservoirId);
    $reservoir = Reservoir::find($reservoirId);

    if ($reservoir && $currentLevel < $reservoir->minimum_critical_level) {
        Log::warning(" Critical level reached in Reservoir {$reservoir->name} (ID {$reservoirId}). Current: $currentLevel, Critical: {$reservoir->minimum_critical_level}");

        //event
        event(new ReservoirCriticalLevelReached($reservoir, $currentLevel));

        return [
            'reservoir_id' => $reservoir->id,
            'current_level' => $currentLevel,
            'critical_level' => $reservoir->minimum_critical_level,
        ];
        }

        return null;
    }
    }

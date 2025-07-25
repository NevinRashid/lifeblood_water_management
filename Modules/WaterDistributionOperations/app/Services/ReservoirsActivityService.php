<?php

namespace Modules\WaterDistributionOperations\Services;

use App\Services\BaseService;
use Modules\WaterDistributionOperations\Models\ReservoirActivity;

class ReservoirsActivityService extends BaseService
{
    public function handle() {}

    public function __construct()
    {
        $this->model = new ReservoirActivity();
    }


    public function calculateCurrentLevel(int $reservoirId): float
    {
        $fillSum = ReservoirsActivity::where('reservoir_id', $reservoirId)
            ->whereIn('activity_type', ['filling_ended', 'level_restored_above_critical'])
            ->sum('activity_level');

        $emptySum = ReservoirsActivity::where('reservoir_id', $reservoirId)
            ->whereIn('activity_type', ['emptying_ended', 'critical_low_level'])
            ->sum('activity_level');

        return $fillSum - $emptySum;
    }
}

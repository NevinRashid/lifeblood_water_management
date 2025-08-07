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

    /**
     * Create a new reservoirActivity
     *
     * @param array $data The data for the new reservoirActivity
     * @return mixed The newly created reservoirActivity model
     */
    public function store(array $data)
    {
        return $this->handle(function () use ($data) {
            $activity = parent::store($data);
            $this->checkAndAlertCriticalLevel($data['reservoir_id']);
            return $activity;
        });
    }

    /**
     * Update a reservoirActivity
     *
     * @param array $data The new data for the reservoirActivity
     * @param string|\Illuminate\Database\Eloquent\Model $modelOrId The model or ID to update
     * @return mixed The updated reservoirActivity model
     */
    public function update(array $data, string|\Illuminate\Database\Eloquent\Model $modelOrId)
    {
        return $this->handle(function () use ($data, $modelOrId) {
            $updatedReservoirActivity = parent::update($data, $modelOrId);
            return $updatedReservoirActivity;
        });
    }

    /**
     * Calculate the current water level of a specific reservoir.
     *
     * - Sums all recorded amounts for positive activities:
     *     'filling_ended' and 'level_restored_above_critical'.
     * - Sums all recorded amounts for negative activities:
     *     'emptying_ended' and 'critical_low_level'.
     * - The current level is computed as: total filled - total emptied.
     * - Logs the calculated sums for monitoring/debugging purposes.
     *
     * @param int $reservoirId  The ID of the reservoir to calculate the level for.
     *
     * @return float The resulting water level (can be negative if more water has been removed than added).
     */
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

    /**
     * Check if the reservoir's current level has reached a critical threshold and trigger an alert if necessary.
     * This method:
     *  - Calculates the current water level of the specified reservoir.
     *  - Compares it against the reservoir's defined minimum critical level.
     *  - If the level is below the critical threshold:
     *      - Logs a warning message.
     *      - Fires the ReservoirCriticalLevelReached event.
     *      - Returns an array with reservoir ID, current level, and critical threshold.
     *  - Returns null if the level is not critical or the reservoir is not found.
     *
     * @param int $reservoirId  The ID of the reservoir to monitor.
     *
     * @return array|null
     *         Returns data about the critical condition if triggered, otherwise null.
     */

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

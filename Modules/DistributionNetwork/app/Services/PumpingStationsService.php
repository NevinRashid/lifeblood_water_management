<?php

namespace Modules\DistributionNetwork\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Modules\DistributionNetwork\Models\PumpingStation;

class PumpingStationsService
{
    /**
     * Get all pumping stations with optional filtering
     */
    public function getAll(array $filters = [])
    {
        try {
            // Create a unique cache key based on the filters.
            $cacheKey = 'pumping_stations.all.' . md5(json_encode($filters));

            // Cache the paginated results for 3600 seconds.
            return Cache::remember($cacheKey, 3600, function () use ($filters) {
                $query = PumpingStation::query();

                if (isset($filters['status'])) {
                    $query->where('status', $filters['status']);
                }

                if (isset($filters['network_id'])) {
                    $query->where('distribution_network_id', $filters['network_id']);
                }

                return $query->paginate($filters['per_page'] ?? 15);
            });
        } catch (QueryException $e) {
            throw new \Exception('Database error while retrieving pumping stations', 500);
        }
    }

    /**
     * Get a single pumping station by ID
     */
    public function get(string $id): PumpingStation
    {
        try {
            // Cache the individual pumping station record for 3600 seconds.
            return Cache::remember('pumping_stations.' . $id, 3600, function () use ($id) {
                return PumpingStation::findOrFail($id);
            });
        } catch (ModelNotFoundException $e) {
            throw new \Exception('Pumping station not found', 404);
        }
    }

    /**
     * Create a new pumping station
     */
    public function store(array $data): PumpingStation
    {
        try {
            $station = PumpingStation::create($data);

            // Invalidate the cache for all pumping stations after a new one is created.
            Cache::tags('pumping_stations')->flush();

            return $station;
        } catch (QueryException $e) {
            throw new \Exception('Error creating pumping station: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update an existing pumping station
     */
    public function update(array $data, PumpingStation $pumpingStation): PumpingStation
    {
        try {

            $pumpingStation->update($data);

            // Invalidate the cache for the specific station and for the list of all stations.
            Cache::forget('pumping_stations.' . $pumpingStation->id);
            Cache::tags('pumping_stations')->flush();

            return $pumpingStation;
        } catch (ModelNotFoundException $e) {
            throw new \Exception('Pumping station not found', 404);
        } catch (QueryException $e) {
            throw new \Exception('Error updating pumping station: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Delete a pumping station
     */
    public function destroy(PumpingStation $pumpingStation): void
    {
        try {

            $pumpingStation->delete();

            // Invalidate the cache for the specific station that was deleted and for the list of all stations.
            Cache::forget('pumping_stations.' . $pumpingStation->id);
            Cache::tags('pumping_stations')->flush();
        } catch (ModelNotFoundException $e) {
            throw new \Exception('Pumping station not found', 404);
        } catch (QueryException $e) {
            throw new \Exception('Error deleting pumping station: ' . $e->getMessage(), 500);
        }
    }
}

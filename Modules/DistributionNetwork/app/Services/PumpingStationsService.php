<?php

namespace Modules\DistributionNetwork\Services;
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
            $query = PumpingStation::query();

            if (isset($filters['status'])) {
                $query->where('status', $filters['status']);
            }

            if (isset($filters['network_id'])) {
                $query->where('distribution_network_id', $filters['network_id']);
            }

            return $query->paginate($filters['per_page'] ?? 15);
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
            return PumpingStation::findOrFail($id);
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
            return PumpingStation::create($data);
        } catch (QueryException $e) {
            throw new \Exception('Error creating pumping station: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update an existing pumping station
     */
    public function update(array $data, string $id): PumpingStation
    {
        try {
            $station = PumpingStation::findOrFail($id);
            $station->update($data);
            return $station;
        } catch (ModelNotFoundException $e) {
            throw new \Exception('Pumping station not found', 404);
        } catch (QueryException $e) {
            throw new \Exception('Error updating pumping station: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Delete a pumping station
     */
    public function destroy(string $id): void
    {
        try {
            $station = PumpingStation::findOrFail($id);
            $station->delete();
        } catch (ModelNotFoundException $e) {
            throw new \Exception('Pumping station not found', 404);
        } catch (QueryException $e) {
            throw new \Exception('Error deleting pumping station: ' . $e->getMessage(), 500);
        }
    }
}

<?php

namespace Modules\DistributionNetwork\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use MatanYadaev\EloquentSpatial\Objects\Point;
use Modules\DistributionNetwork\Models\Reservoir;

class ReservoirService
{
    /**
     * Get all reservoirs with optional filters
     *
     * @param array $filters Array of filter parameters:
     *   - status: Filter by status
     *   - tank_type: Filter by tank type (main/sub)
     *   - network_id: Filter by distribution network ID
     *   - per_page: Number of items per page
     *   - lat/lng: Coordinates for proximity filtering
     * @return LengthAwarePaginator|Collection Paginated or collection of reservoirs
     */
    public function getAll(array $filters = []): LengthAwarePaginator|Collection
    {
        $query = Reservoir::with('distributionNetwork')
            ->when(isset($filters['status']), fn($q) => $q->where('status', $filters['status']))
            ->when(isset($filters['tank_type']), fn($q) => $q->where('tank_type', $filters['tank_type']))
            ->when(isset($filters['network_id']), fn($q) => $q->where('distribution_network_id', $filters['network_id']));

        // Apply location filter if coordinates provided
        if (isset($filters['lat']) && isset($filters['lng'])) {
            $point = new Point($filters['lat'], $filters['lng']);
            $query->orderByDistance('location', $point, 'asc');
        }

        return isset($filters['per_page'])
            ? $query->paginate($filters['per_page'])
            : $query->get();
    }

    /**
     * Get single reservoir by ID
     *
     * @param string $id Reservoir ID
     * @return Reservoir
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function get(string $id): Reservoir
    {
        try {
            return Reservoir::with('distributionNetwork')->findOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw new \Exception('Valve not found', 404);
        } catch (\Exception $e) {
            throw new \Exception('Error retrieving valve: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Create new reservoir
     *
     * @param array $data Validated reservoir data
     * @return Reservoir Newly created reservoir
     */
    public function store(array $data): Reservoir
    {
        try {

            DB::beginTransaction();
            $reservoir =  Reservoir::create($data);
            DB::commit();
            return $reservoir;
        } catch (QueryException $e) {
            DB::rollBack();
            throw new \Exception('Database error while creating valve', 500);
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception('Error creating valve: ' . $e->getMessage(), 400);
        }
    }

    /**
     * Update existing reservoir
     *
     * @param array $data Validated update data
     * @param string $id Reservoir ID to update
     * @return Reservoir Updated reservoir
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function update(array $data, string $id): Reservoir
    {
        try {

            DB::beginTransaction();
            $reservoir = Reservoir::findOrFail($id);

            // Only update location if it was explicitly provided
            if (array_key_exists('location', $data)) {
                $reservoir->location = $data['location'];
            }
            
            $reservoir->update($data);
            DB::commit();

            return $reservoir;
        } catch (QueryException $e) {
            DB::rollBack();
            throw new \Exception('Database error while creating valve', 500);
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception('Error creating valve: ' . $e->getMessage(), 400);
        }
    }

    /**
     * Delete reservoir
     *
     * @param string $id Reservoir ID to delete
     * @return void
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function destroy(string $id): void
    {
        try {

            DB::beginTransaction();
            $reservoir = Reservoir::findOrFail($id);
            $reservoir->delete();
            DB::commit();
        } catch (QueryException $e) {
            DB::rollBack();
            throw new \Exception('Database error while creating valve', 500);
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception('Error creating valve: ' . $e->getMessage(), 400);
        }
    }
}

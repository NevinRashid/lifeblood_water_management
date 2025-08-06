<?php

namespace Modules\DistributionNetwork\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Modules\DistributionNetwork\Models\Valve;

class ValvesService
{
    /**
     * Retrieve all valves with optional filtering and pagination
     *
     * @param array $filters Array of filter parameters
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     * @throws \Exception On database error
     */
    public function getAll(array $filters = [])
    {
        try {
            // Generate a unique cache key based on the filters.
            $cacheKey = 'valves.all.' . md5(json_encode($filters));

            // Use the remember function to cache the result for 3600 seconds.
            return Cache::remember($cacheKey, 3600, function () use ($filters) {
                $query = Valve::query();

                // Apply filters if provided
                if (!empty($filters)) {
                    $this->applyFilters($query, $filters);
                }

                // Apply sorting
                if (isset($filters['sort_by'])) {
                    $sortOrder = $filters['sort_order'] ?? 'asc';
                    $query->orderBy($filters['sort_by'], $sortOrder);
                }

                return $query->paginate($filters['per_page'] ?? 15);
            });
        } catch (QueryException $e) {
            throw new \Exception('Database error while retrieving valves', 500);
        } catch (\Exception $e) {
            throw new \Exception('Error retrieving valves: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get a single valve by ID
     *
     * @param string $id UUID of the valve
     * @return Valve
     * @throws ModelNotFoundException If valve not found
     * @throws \Exception On other errors
     */
    public function get(string $id)
    {
        try {
            // Use the remember function to cache the single valve for 3600 seconds.
            return Cache::remember('valves.' . $id, 3600, function () use ($id) {
                return Valve::findOrFail($id);
            });
        } catch (ModelNotFoundException $e) {
            throw new \Exception('Valve not found', 404);
        } catch (\Exception $e) {
            throw new \Exception('Error retrieving valve: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Create a new valve record
     *
     * @param array $data Validated valve data
     * @return Valve The created valve
     * @throws \Exception On creation error
     */
    public function store(array $data)
    {
        try {
            DB::beginTransaction();
            $valve = Valve::create($data);
            DB::commit();

            // Clear the cache for the 'getAll' method since the data has changed.
            Cache::tags('valves')->flush();

            return $valve;
        } catch (QueryException $e) {
            DB::rollBack();
            throw new \Exception('Database error while creating valve' . $e->getMessage(), 500);
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception('Error creating valve: ' . $e->getMessage(), 400);
        }
    }

    /**
     * Update an existing valve record
     *
     * @param array $data Validated update data
     * @param string $id UUID of the valve to update
     * @return Valve The updated valve
     * @throws ModelNotFoundException If valve not found
     * @throws \Exception On update error
     */
    public function update(array $data, string $id)
    {
        try {
            DB::beginTransaction();
            $valve = Valve::findOrFail($id);

            // Only update location if it was explicitly provided
            if (array_key_exists('location', $data)) {
                $valve->location = $data['location'];
            }
            
            $valve->update($data);
            DB::commit();

            // Clear the cache for the specific valve and all valves.
            Cache::forget('valves.' . $id);
            Cache::tags('valves')->flush();

            return $valve;
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            throw new \Exception('Valve not found', 404);
        } catch (QueryException $e) {
            DB::rollBack();
            throw new \Exception('Database error while updating valve', 500);
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception('Error updating valve: ' . $e->getMessage(), 400);
        }
    }

    /**
     * Delete a valve record
     *
     * @param string $id UUID of the valve to delete
     * @return void
     * @throws ModelNotFoundException If valve not found
     * @throws \Exception On deletion error
     */
    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();
            $valve = Valve::findOrFail($id);
            $valve->delete();
            DB::commit();

            // Clear the cache for the specific valve and all valves.
            Cache::forget('valves.' . $id);
            Cache::tags('valves')->flush();

        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            throw new \Exception('Valve not found', 404);
        } catch (QueryException $e) {
            DB::rollBack();
            throw new \Exception('Database error while deleting valve', 500);
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception('Error deleting valve: ' . $e->getMessage(), 400);
        }
    }

    /**
     * Apply filters to the valves query
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $filters
     * @return void
     */
    private function applyFilters($query, array $filters)
    {
        // Filter by status if provided
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Filter by type if provided
        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        // Add more filters as needed...
    }
}
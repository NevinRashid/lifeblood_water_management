<?php

namespace Modules\Beneficiaries\Services;

use App\Services\Base\BaseService;
use Illuminate\Support\Facades\Cache;
use Modules\Beneficiaries\Models\WaterQuota;

/**
 * Service for managing water quota records
 *
 * This class handles the business logic for creating, reading, updating,
 * and deleting water quotas. It also implements a caching layer to
 * speed up fetching the full list of quotas
 */
class WaterQuotaService extends BaseService
{
    /**
     * WaterQuotaService constructor
     *
     * @param \Modules\Beneficiaries\Models\WaterQuota $model The WaterQuota model instance
     */
    public function __construct(WaterQuota $model)
    {
        $this->model = $model;
    }

    /**
     * Get all water quotas, using a cache for unfiltered requests
     *
     * Caches the full list of water quotas for one day to reduce database queries
     * The cache is only used when no filters are applied
     *
     * @param array $filters An array of filters to apply to the query
     * @return mixed The result of the handle method, usually a collection or array
     */
    public function getAll(array $filters = [])
    {
        return $this->handle(
            function () use ($filters) {
                // If no filters are passed, we can use the cached version
                if (!$filters) {
                    return Cache::remember('waterQuotas', now()->addDay(), function () {
                        // If it's not in the cache, fetch from the DB and cache it
                        return parent::getAll();
                    });
                }

                // If we have filters, we need to hit the database directly
                return parent::getAll($filters);
            }
        );
    }

    /**
     * Store a new water quota record and clear the cache
     *
     * @param array $data The data for the new record
     * @return mixed The newly created model instance
     */
    public function store(array $data)
    {
        return $this->handle(function () use ($data) {
            $waterQuota = parent::store($data);

            // A new record was added, so we must clear the cache
            Cache::forget('waterQuotas');

            return $waterQuota;
        });
    }

    /**
     * Update a water quota record and clear the cache
     *
     * @param array $data The new data for the record
     * @param string|\Illuminate\Database\Eloquent\Model $modelOrId The model or ID to update
     * @return mixed The updated model instance
     */
    public function update(array $data, string|\Illuminate\Database\Eloquent\Model $modelOrId)
    {
        return $this->handle(function () use ($data, $modelOrId) {
            $updatedWaterQuota = parent::update($data, $modelOrId);

            // A record was changed, so clear the cache to avoid stale data
            Cache::forget('waterQuotas');

            return $updatedWaterQuota;
        });
    }

    /**
     * Delete a water quota record and clear the cache
     *
     * @param string $id The ID of the record to delete
     * @return mixed The result of the parent destroy method
     */
    public function destroy(string $id)
    {
        return $this->handle(function () use ($id) {
            $deletedResult = parent::destroy($id);

            // A record was removed, so clear the cache
            Cache::forget('waterQuotas');

            return $deletedResult;
        });
    }

    /**
     * Build a query for fetching water quotas with filtering and sorting
     *
     * @param array $filters An array of filters to apply to the query
     * @option float  $received_volume Filter by the exact volume received
     * @option string $allocation_date Filter by a specific allocation date (YYYY-MM-DD)
     * @option string $status Filter by status
     * @option string $sort_by Column to sort by (defaults to 'allocation_date')
     * @option string $sort_direction Sorting direction (defaults to 'asc')
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(array $filters = [])
    {
        // Start with the base query from the parent service
        $query = parent::query();

        // Apply filters conditionally
        $query->when(
            isset($filters['received_volume']),
            fn ($q) =>
            $q->where('received_volume', $filters['received_volume'])
        )
            ->when(
                isset($filters['allocation_date']),
                fn ($q) =>
                $q->whereDate('allocation_date', $filters['allocation_date'])
            )
            ->when(
                isset($filters['status']),
                fn ($q) =>
                $q->where('status', $filters['status'])
            );

        // Set default sorting parameters
        $sortBy = $filters['sort_by'] ?? 'allocation_date';
        $sortDirection = $filters['sort_direction'] ?? 'asc';

        return $query->orderBy($sortBy, $sortDirection);
    }
}
<?php

namespace Modules\WaterSources\Services;

use App\Services\Base\BaseService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Modules\WaterSources\Models\WaterExtraction;

/**
 * Handles business logic for water extraction records
 *
 * This service manages CRUD operations for water extractions,
 * provides advanced filtering, and includes a caching layer to
 * improve performance on read operations
 */
class WaterExtractionService extends BaseService
{
    /**
     * WaterExtractionService constructor
     *
     * @param \Modules\WaterSources\Models\WaterExtraction $model The WaterExtraction model instance
     */
    public function __construct(WaterExtraction $model)
    {
        $this->model = $model;
    }

    /**
     * Build a query for fetching water extraction records with filters
     *
     * @param array $filters An array of filters to apply to the query
     * @option int    $water_source_id Filter by a specific water source
     * @option string $start_date Filter for extractions on or after this date (YYYY-MM-DD)
     * @option string $end_date Filter for extractions on or before this date (YYYY-MM-DD)
     * @option float  $min_extracted Filter for records with at least this amount extracted
     * @option float  $max_extracted Filter for records with at most this amount extracted
     * @option string $sort_by Column to sort by (defaults to 'extraction_date')
     * @option string $sort_direction Sorting direction (defaults to 'desc')
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(array $filters = [])
    {
        // Wrap the logic in the base handler for consistent error management
        return $this->handle(function () use ($filters) {

            // Start with the base query builder
            $query = parent::query($filters);

            // Conditionally apply filters based on input
            $query->when(isset($filters['water_source_id']), function (Builder $q) use ($filters) {
                $q->where('water_source_id', $filters['water_source_id']);
            });

            $query->when(isset($filters['start_date']), function (Builder $q) use ($filters) {
                $q->where('extraction_date', '>=', $filters['start_date']);
            });

            $query->when(isset($filters['end_date']), function (Builder $q) use ($filters) {
                $q->where('extraction_date', '<=', $filters['end_date']);
            });

            $query->when(isset($filters['min_extracted']), function (Builder $q) use ($filters) {
                $q->where('extracted', '>=', $filters['min_extracted']);
            });

            $query->when(isset($filters['max_extracted']), function (Builder $q) use ($filters) {
                $q->where('extracted', '<=', $filters['max_extracted']);
            });

            // Set default sorting if not provided
            $sortBy = $filters['sort_by'] ?? 'extraction_date';
            $sortDirection = $filters['sort_direction'] ?? 'desc';

            return $query->orderBy($sortBy, $sortDirection);
        });
    }

    /**
     * Get all water extractions, with caching for unfiltered requests
     *
     * @param array $filters Filters to apply to the query
     * @return mixed A collection of water extractions
     */
    public function getAll(array $filters = [])
    {
        return $this->handle(
            function () use ($filters) {
                // If no filters are applied, use the cache
                if (!$filters) {

                    return Cache::remember('waterExtractions', now()->addDay(), function () {
                        return parent::getAll();
                    });
                }
                return parent::getAll($filters);
            }
        );
    }

    /**
     * Create a new water extraction record and clear the cache
     *
     * @param array $data The data for the new record
     * @return mixed The newly created model instance
     */
    public function store(array $data)
    {
        return $this->handle(function () use ($data) {
            $waterExtraction = parent::store($data);
            // Invalidate the cache since we've added new data
            Cache::forget('waterExtractions');
            return $waterExtraction;
        });
    }

    /**
     * Update a water extraction record and clear the cache
     *
     * @param array $data The new data for the record
     * @param string|\Illuminate\Database\Eloquent\Model $modelOrId The model or ID to update
     * @return mixed The updated model instance
     */
    public function update(array $data, string|\Illuminate\Database\Eloquent\Model $modelOrId)
    {
        return $this->handle(function () use ($data, $modelOrId) {
            $updatedWaterExtraction = parent::update($data, $modelOrId);
            // Invalidate the cache to reflect the changes
            Cache::forget('waterExtractions');
            return $updatedWaterExtraction;
        });
    }

    /**
     * Delete a water extraction record and clear the cache
     *
     * @param string $id The ID of the record to delete
     * @return mixed The result of the delete operation
     */
    public function destroy(string $id)
    {
        return $this->handle(function () use ($id) {
            $deletedWaterExtraction = parent::destroy($id);
            // Invalidate the cache since a record was removed
            Cache::forget('waterExtractions');
            return $deletedWaterExtraction;
        });
    }
}
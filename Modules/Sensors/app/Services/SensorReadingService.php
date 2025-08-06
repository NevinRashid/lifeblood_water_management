<?php

namespace Modules\Sensors\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Modules\Sensors\Events\SensorReadingCreated;
use Modules\Sensors\Models\SensorReading;

class SensorReadingService
{
    /**
     * Get all sensor readings with optional filters
     * * @param array $filters Array of filter parameters:
     * - sensor_id: Filter by specific sensor
     * - start_date: Filter readings after this date
     * - end_date: Filter readings before this date
     * - per_page: Pagination items per page
     * - sort_by: Field to sort by
     * - sort_order: Sort direction (asc/desc)
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     * @throws \Exception On database or other errors
     */
    public function getAll(array $filters = [])
    {
        try {
            // Generate a unique cache key based on the filters
            $cacheKey = 'sensor_readings.all.' . md5(json_encode($filters));

            // Cache the paginated results for 3600 seconds
            return Cache::remember($cacheKey, 3600, function () use ($filters) {
                $query = SensorReading::query();

                // Apply filters if provided
                if (!empty($filters)) {
                    $this->applyFilters($query, $filters);
                }

                // Apply sorting
                $sortBy = $filters['sort_by'] ?? 'recorded_at';
                $sortOrder = $filters['sort_order'] ?? 'desc';
                $query->orderBy($sortBy, $sortOrder);

                // Return paginated results
                return $query->paginate($filters['per_page'] ?? 15);
            });
        } catch (QueryException $e) {
            throw new \Exception(
                'Database error while retrieving sensor readings',
                500
            );
        } catch (\Exception $e) {
            throw new \Exception(
                'Error retrieving sensor readings: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Get single sensor reading by ID
     * * @param string $id Reading ID
     * @return SensorReading
     * @throws \Exception If reading not found or other errors
     */
    public function get(string $id)
    {
        try {
            // Cache the individual sensor reading for 3600 seconds
            return Cache::remember('sensor_readings.' . $id, 3600, function () use ($id) {
                return SensorReading::findOrFail($id);
            });
        } catch (ModelNotFoundException $e) {
            throw new \Exception('Sensor reading not found', 404);
        } catch (\Exception $e) {
            throw new \Exception(
                'Error retrieving sensor reading: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Create new sensor reading
     * * @param array $data Validated reading data
     * @return SensorReading The created reading
     * @throws \Exception On creation error
     */
    public function store(array $data)
    {
        try {
            DB::beginTransaction();
            $reading = SensorReading::create($data);
            SensorReadingCreated::dispatch($reading);
            DB::commit();

            // Invalidate the cache for all sensor readings as new data has been added.
            Cache::tags('sensor_readings')->flush();

            return $reading;
        } catch (QueryException $e) {
            DB::rollBack();
            throw new \Exception(
                'Database error while creating sensor reading: ' . $e->getMessage(),
                500
            );
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception(
                'Error creating sensor reading: ' . $e->getMessage(),
                400
            );
        }
    }

    /**
     * Update existing sensor reading
     * * @param array $data Validated update data
     * @param string $id Reading ID to update
     * @return SensorReading The updated reading
     * @throws \Exception On update error
     */
    public function update(array $data, string $id)
    {
        try {
            DB::beginTransaction();
            $reading = SensorReading::findOrFail($id);
            $reading->update($data);
            DB::commit();

            // Invalidate the cache for the specific reading and all readings.
            Cache::forget('sensor_readings.' . $id);
            Cache::tags('sensor_readings')->flush();

            return $reading;
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            throw new \Exception('Sensor reading not found', 404);
        } catch (QueryException $e) {
            DB::rollBack();
            throw new \Exception(
                'Database error while updating sensor reading: ' . $e->getMessage(),
                500
            );
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception(
                'Error updating sensor reading: ' . $e->getMessage(),
                400
            );
        }
    }

    /**
     * Delete sensor reading
     * * @param string $id Reading ID to delete
     * @throws \Exception On deletion error
     */
    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();
            $reading = SensorReading::findOrFail($id);
            $reading->delete();
            DB::commit();

            // Invalidate the cache for the specific reading and all readings.
            Cache::forget('sensor_readings.' . $id);
            Cache::tags('sensor_readings')->flush();

        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            throw new \Exception('Sensor reading not found', 404);
        } catch (QueryException $e) {
            DB::rollBack();
            throw new \Exception(
                'Database error while deleting sensor reading: ' . $e->getMessage(),
                500
            );
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception(
                'Error deleting sensor reading: ' . $e->getMessage(),
                400
            );
        }
    }

    /**
     * Apply filters to the query
     * * @param \Illuminate\Database\Eloquent\Builder $query Query builder instance
     * @param array $filters Array of filters to apply
     */
    private function applyFilters($query, array $filters)
    {
        // Filter by sensor ID if provided
        if (isset($filters['sensor_id'])) {
            $query->where('sensor_id', $filters['sensor_id']);
        }

        // Filter by start date (records after this date)
        if (isset($filters['start_date'])) {
            $query->where('recorded_at', '>=', $filters['start_date']);
        }

        // Filter by end date (records before this date)
        if (isset($filters['end_date'])) {
            $query->where('recorded_at', '<=', $filters['end_date']);
        }
    }
}
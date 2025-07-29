<?php

namespace Modules\Sensors\Services;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Modules\Sensors\Models\Sensor;

class SensorService
{
    /**
     * Retrieve all sensors with optional filtering and pagination
     *
     * @param array $filters Array of filter parameters
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     * @throws \Exception On database error
     */
    public function getAll(array $filters = [])
    {
        try {
            $query = Sensor::query();

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
        } catch (QueryException $e) {
            throw new \Exception('Database error while retrieving sensors', 500);
        } catch (\Exception $e) {
            throw new \Exception('Error retrieving sensors: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get a single sensor by ID
     *
     * @param string $id UUID of the sensor
     * @return Sensor
     * @throws ModelNotFoundException If sensor not found
     * @throws \Exception On other errors
     */
    public function get(string $id)
    {
        try {
            return Sensor::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw new \Exception('Sensor not found', 404);
        } catch (\Exception $e) {
            throw new \Exception('Error retrieving sensor: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Create a new sensor record
     *
     * @param array $data Validated sensor data
     * @return Sensor The created sensor
     * @throws \Exception On creation error
     */
    public function store(array $data)
    {
        try {
            DB::beginTransaction();

            // Validate sensorable relationship
            if (!isset($data['sensorable_type']) || !isset($data['sensorable_id'])) {
                throw new \Exception('Both sensorable_type and sensorable_id are required', 400);
            }

            // Get the mapped class from sensorableMap
            $modelClass = Sensor::getSensorableClass($data['sensorable_type']);
            $data['sensorable_type']=$modelClass; 

            if (!$modelClass) {
                throw new \Exception('Invalid sensorable_type provided', 400);
            }

            // Verify the related model exists
            if (!$modelClass::find($data['sensorable_id'])) {
                throw new \Exception('The specified sensorable_id does not exist', 404);
            }

            $sensor = Sensor::create($data);

            DB::commit();
            return $sensor;
        } catch (QueryException $e) {
            DB::rollBack();
            throw new \Exception('Database error while creating sensor: ' . $e->getMessage(), 500);
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage(), $e->getCode() ?: 400);
        }
    }

    /**
     * Update an existing sensor record
     *
     * @param array $data Validated update data
     * @param string $id UUID of the sensor to update
     * @return Sensor The updated sensor
     * @throws ModelNotFoundException If sensor not found
     * @throws \Exception On update error
     */
    public function update(array $data, string $id)
    {
        try {
            DB::beginTransaction();

            $sensor = Sensor::findOrFail($id);

            // Handle location update specifically
            if (array_key_exists('location', $data)) {
                $sensor->location = $data['location'];
            }

            $sensor->update($data);

            DB::commit();
            return $sensor;
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            throw new \Exception('Sensor not found', 404);
        } catch (QueryException $e) {
            DB::rollBack();
            throw new \Exception('Database error while updating sensor', 500);
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception('Error updating sensor: ' . $e->getMessage(), 400);
        }
    }

    /**
     * Delete a sensor record
     *
     * @param string $id UUID of the sensor to delete
     * @return void
     * @throws ModelNotFoundException If sensor not found
     * @throws \Exception On deletion error
     */
    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();

            $sensor = Sensor::findOrFail($id);
            $sensor->delete();

            DB::commit();
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            throw new \Exception('Sensor not found', 404);
        } catch (QueryException $e) {
            DB::rollBack();
            throw new \Exception('Database error while deleting sensor', 500);
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception('Error deleting sensor: ' . $e->getMessage(), 400);
        }
    }

    /**
     * Apply filters to the sensors query
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

        // Filter by sensor_type if provided
        if (isset($filters['sensor_type'])) {
            $query->where('sensor_type', $filters['sensor_type']);
        }

        // Filter by device_id if provided
        if (isset($filters['device_id'])) {
            $query->where('device_id', $filters['device_id']);
        }

        // You can add more filters as needed
    }
}

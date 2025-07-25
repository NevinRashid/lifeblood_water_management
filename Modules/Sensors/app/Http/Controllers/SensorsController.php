<?php

namespace Modules\Sensors\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Sensors\Http\Requests\Sensor\StoreSensorRequest;
use Modules\Sensors\Http\Requests\Sensor\UpdateSensorRequest;
use Modules\Sensors\Services\SensorService;

class SensorsController extends Controller
{
    protected SensorService $service;

    public function __construct(SensorService $service)
    {
        $this->service = $service;
    }

    /**
     * Get all sensors with optional filtering and pagination
     * 
     * @param Request $request The HTTP request containing filter parameters
     * @return JsonResponse Paginated list of sensors with success status
     */
    public function index(Request $request)
    {
        try {
            // Extract only the needed parameters
            $filters = [
                'status' => $request->input('status'),
                'sensor_type' => $request->input('sensor_type'),
                'per_page' => $request->input('per_page', 15),
                'sort_by' => $request->input('sort_by'),
                'sort_order' => $request->input('sort_order', 'asc'),
                'device_id' => $request->input('device_id'),
            ];

            // Remove null values
            $filters = array_filter($filters, fn($value) => !is_null($value));

            $sensors = $this->service->getAll($filters);

            return $this->successResponse('Sensors retrieved successfully', $sensors);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), null, $e->getCode());
        }
    }

    /**
     * Get a single sensor by ID
     * 
     * @param string $id The ID of the sensor to retrieve
     * @return JsonResponse The requested sensor data with success status
     */
    public function show(string $id)
    {
        try {
            $sensor = $this->service->get($id);
            return $this->successResponse('Sensor retrieved successfully', $sensor);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), null, $e->getCode());
        }
    }

    /**
     * Create a new sensor record
     * 
     * @param StoreSensorFormRequest $request The HTTP request containing sensor data
     * @return JsonResponse The newly created sensor with success status
     */
    public function store(StoreSensorRequest $request)
    {
        try {
            $validated = $request->validated();

            $sensor = $this->service->store($validated);
            return $this->successResponse('Sensor created successfully', $sensor, 201);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), null, $e->getCode() ?: 400);
        }
    }

    /**
     * Update an existing sensor record
     * 
     * @param UpdateSensorFormRequest $request The HTTP request containing updated data
     * @param string $id The ID of the sensor to update
     * @return JsonResponse The updated sensor data with success status
     */
    public function update(UpdateSensorRequest $request, string $id)
    {
        try {
            $validated = $request->validated();

            $sensor = $this->service->update($validated, $id);
            return $this->successResponse('Sensor updated successfully', $sensor);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), null, $e->getCode() ?: 400);
        }
    }

    /**
     * Delete a sensor record
     * 
     * @param string $id The ID of the sensor to delete
     * @return JsonResponse Success message with empty data
     */
    public function destroy(string $id)
    {
        try {
            $this->service->destroy($id);
            return $this->successResponse('Sensor deleted successfully', null, 204);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), null, $e->getCode());
        }
    }
}

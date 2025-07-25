<?php

namespace Modules\Sensors\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Sensors\Http\Requests\SensorReading\StoreSensorReadingRequest;
use Modules\Sensors\Http\Requests\SensorReading\UpdateSensorReadingRequest;
use Modules\Sensors\Services\SensorReadingService;

class SensorReadingController extends Controller
{
    /**
     * @var SensorReadingsService Service instance for sensor readings business logic
     */
    protected SensorReadingService $service;

    /**
     * Constructor with dependency injection
     * 
     * @param SensorReadingService $service Injected service instance
     */
    public function __construct(SensorReadingService $service)
    {
        $this->service = $service;
    }

    /**
     * Get paginated list of sensor readings with optional filters
     * 
     * @param Request $request HTTP request containing filter parameters
     * @return \Illuminate\Http\JsonResponse Paginated response
     */
    public function index(Request $request)
    {
        try {
            // Extract and sanitize filter parameters
            $filters = [
                'sensor_id' => $request->input('sensor_id'),
                'start_date' => $request->input('start_date'),
                'end_date' => $request->input('end_date'),
                'per_page' => $request->input('per_page', 15),
                'sort_by' => $request->input('sort_by', 'recorded_at'),
                'sort_order' => $request->input('sort_order', 'desc'),
            ];

            // Get filtered readings from service
            $readings = $this->service->getAll($filters);

            return $this->successResponse(
                'Sensor readings retrieved successfully',
                $readings
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                $e->getMessage(),
                null,
                $e->getCode()
            );
        }
    }

    /**
     * Get single sensor reading by ID
     * 
     * @param string $id Reading ID
     * @return \Illuminate\Http\JsonResponse Single reading response
     */
    public function show(string $id)
    {
        try {
            $reading = $this->service->get($id);
            return $this->successResponse(
                'Sensor reading retrieved successfully',
                $reading
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                $e->getMessage(),
                null,
                $e->getCode()
            );
        }
    }

    /**
     * Create new sensor reading
     * 
     * @param StoreSensorReading $request Validated request data
     * @return \Illuminate\Http\JsonResponse Created reading response
     */
    public function store(StoreSensorReadingRequest $request)
    {
        try {
            $reading = $this->service->store($request->validated());
            return $this->successResponse(
                'Sensor reading created successfully',
                $reading,
                201
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                $e->getMessage(),
                null,
                $e->getCode() ?: 400
            );
        }
    }

    /**
     * Update existing sensor reading
     * 
     * @param UpdateSensorReading $request Validated request data
     * @param string $id Reading ID to update
     * @return \Illuminate\Http\JsonResponse Updated reading response
     */
    public function update(UpdateSensorReadingRequest $request, string $id)
    {
        try {
            $reading = $this->service->update(
                $request->validated(),
                $id
            );
            return $this->successResponse(
                'Sensor reading updated successfully',
                $reading
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                $e->getMessage(),
                null,
                $e->getCode() ?: 400
            );
        }
    }

    /**
     * Delete sensor reading
     * 
     * @param string $id Reading ID to delete
     * @return \Illuminate\Http\JsonResponse Empty success response
     */
    public function destroy(string $id)
    {
        try {
            $this->service->destroy($id);
            return $this->successResponse(
                'Sensor reading deleted successfully',
                null,
                204
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                $e->getMessage(),
                null,
                $e->getCode()
            );
        }
    }
}

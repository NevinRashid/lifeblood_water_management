<?php

namespace Modules\DistributionNetwork\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;
use Modules\DistributionNetwork\Http\Requests\PumpStations\StorePumpingStationRequest;
use Modules\DistributionNetwork\Http\Requests\PumpStations\UpdatePumpingStationRequest;
use Modules\DistributionNetwork\Models\PumpingStation;
use Modules\DistributionNetwork\Services\PumpingStationsService;

class PumpingStationsController extends Controller
{
    protected PumpingStationsService $service;

    public static function middleware(): array
    {
        return [
            new Middleware('can:show_distribution_network_component', only: ['show']),
            new Middleware('can:view_all_distribution_network_component', only: ['index']),
        ];
    }

    public function __construct(PumpingStationsService $service)
    {
        $this->service = $service;
    }

    /**
     * Get all pumping stations with optional filtering and pagination
     * 
     * @param Request $request The HTTP request containing filter parameters
     * @return JsonResponse Paginated list of pumping stations with success status
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = [
                'status' => $request->input('status'),
                'network_id' => $request->input('distribution_network_id'),
                'per_page' => $request->input('per_page', 15),
                'lat' => $request->input('lat'),
                'lng' => $request->input('lng'),
            ];

            $filters = array_filter($filters, fn($value) => !is_null($value));

            $stations = $this->service->getAll($filters);
            return $this->successResponse('Pumping stations retrieved successfully', $stations);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), null, $e->getCode());
        }
    }

    /**
     * Get a single pumping station by ID
     * *Creating permission is checked in FormRequest
     * 
     * @param string $id The ID of the pumping station to retrieve
     * @return JsonResponse The requested pumping station data with success status
     */
    public function show(string $id): JsonResponse
    {
        try {
            $station = $this->service->get($id);
            return $this->successResponse('Pumping station retrieved successfully', $station);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), null, $e->getCode());
        }
    }

    /**
     * Create a new pumping station record
     * 
     * @param StorePumpingStationRequest $request The HTTP request containing pumping station data
     * @return JsonResponse The newly created pumping station with success status
     */
    public function store(StorePumpingStationRequest $request): JsonResponse
    {
        try {

            // $request->validated() already has Point object!
            $validated = $request->validated();

            $station = $this->service->store($validated);
            return $this->successResponse('Pumping station created successfully', $station, 201);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), null, $e->getCode() ?: 400);
        }
    }

    /**
     * Update an existing pumping station record
     * *Updating permission is checked in FormRequest
     
     * @param UpdatePumpingStationRequest $request The HTTP request containing updated data
     * @return JsonResponse The updated pumping station data with success status
     */
    public function update(UpdatePumpingStationRequest $request, PumpingStation $pumpingStation): JsonResponse
    {
        try {

            $validated = $request->validated();

            $station = $this->service->update($validated, $pumpingStation);
            return $this->successResponse('Pumping station updated successfully', $station);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), null, $e->getCode() ?: 400);
        }
    }

    /**
     * Delete a pumping station record
     * 
     * @param string $id The ID of the pumping station to delete
     * @return JsonResponse Success message with empty data
     */
    public function destroy(PumpingStation $pumpingStation): JsonResponse
    {
        try {
            if (!Gate::allows('delete_distribution_network_component', $pumpingStation)) {
                return $this->errorResponse('Unauthorized', null, 403);
            }
            $this->service->destroy($pumpingStation);
            return $this->successResponse('Pumping station deleted successfully', null, 204);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), null, $e->getCode());
        }
    }
}

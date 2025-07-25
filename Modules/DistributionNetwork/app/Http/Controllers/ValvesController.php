<?php

namespace Modules\DistributionNetwork\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Modules\DistributionNetwork\Http\Requests\Valves\StoreValveFormRequest;
use Modules\DistributionNetwork\Http\Requests\Valves\UpdateValveFormRequest;
use Modules\DistributionNetwork\Services\ValvesService;

class ValvesController extends Controller
{
    protected ValvesService $service;

    public function __construct(ValvesService $service)
    {

        $this->service = $service;
    }

    /**
     * Get all valves with optional filtering and pagination
     * 
     * @param Request $request The HTTP request containing filter parameters
     * @return JsonResponse Paginated list of valves with success status
     */
    public function index(Request $request)
    {
        try {

            // Extract only the needed parameters
            $filters = [
                'status' => $request->input('status'),
                'type' => $request->input('type'),
                'per_page' => $request->input('per_page', 15), // Default to 15 items per page
                'sort_by' => $request->input('sort_by'),
                'sort_order' => $request->input('sort_order', 'asc'),
            ];

            // Remove null values
            $filters = array_filter($filters, fn($value) => !is_null($value));

            $valves = $this->service->getAll($filters);

            return $this->successResponse('Valves retrieved successfully', $valves);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), null, $e->getCode());
        }
    }

    /**
     * Get a single valve by ID
     * 
     * @param string $id The ID of the valve to retrieve
     * @return JsonResponse The requested valve data with success status
     */
    public function show(string $id)
    {
        try {
            $valve = $this->service->get($id);
            return $this->successResponse('Valve retrieved successfully', $valve);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), null, $e->getCode());
        }
    }

    /**
     * Create a new valve record
     * 
     * @param StoreValveFormRequest $request The HTTP request containing valve data
     * @return JsonResponse The newly created valve with success status
     */
    public function store(StoreValveFormRequest $request)
    {
        try {
            // $request->validated() already has Point object!
            $validated = $request->validated();

            $valve = $this->service->store($validated);
            return $this->successResponse('Valve created successfully', $valve, 201);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), null, $e->getCode() ?: 400);
        }
    }

    /**
     * Update an existing valve record
     * 
     * @param UpdateValveFormRequest $request The HTTP request containing updated data
     * @param string $id The ID of the valve to update
     * @return JsonResponse The updated valve data with success status
     */
    public function update(UpdateValveFormRequest $request, string $id)
    {
        try {
            
            // $validated['location'] will be:
            // - Point object (if new lat/lng provided)
            // - null (if location:null was sent)
            // - undefined (if location was omitted)
            $validated = $request->validated();

            $valve = $this->service->update($validated, $id);
            return $this->successResponse('Valve updated successfully', $valve);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), null, $e->getCode() ?: 400);
        }
    }

    /**
     * Delete a valve record
     * 
     * @param string $id The ID of the valve to delete
     * @return JsonResponse Success message with empty data
     */
    public function destroy(string $id)
    {
        try {
            $this->service->destroy($id);
            return $this->successResponse('Valve deleted successfully', null, 204);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), null, $e->getCode());
        }
    }
}

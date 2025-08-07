<?php

namespace Modules\DistributionNetwork\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;
use Modules\DistributionNetwork\Http\Requests\Reservoirs\StoreReservoirRequest;
use Modules\DistributionNetwork\Http\Requests\Reservoirs\UpdateReservoirRequest;
use Modules\DistributionNetwork\Models\Reservoir;
use Modules\DistributionNetwork\Services\ReservoirService;

class ReservoirController extends Controller
{
    protected ReservoirService $service;

    public static function middleware(): array
    {
        return [
            new Middleware('can:show_distribution_network_component', only: ['show']),
            new Middleware('can:view_all_distribution_network_component', only: ['index']),
        ];
    }

    public function __construct(ReservoirService $service)
    {
        $this->service = $service;
    }

    /**
     * Get all reservoirs with optional filtering and pagination
     *
     * @param Request $request The HTTP request containing filter parameters
     * @return JsonResponse Paginated list of reservoirs with success status
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = [
                'status' => $request->input('status'),
                'tank_type' => $request->input('tank_type'),
                'network_id' => $request->input('distribution_network_id'),
                'per_page' => $request->input('per_page', 15),
            ];

            $filters = array_filter($filters, fn($value) => !is_null($value));

            $reservoirs = $this->service->getAll($filters);
            return $this->successResponse('Reservoirs retrieved successfully', $reservoirs);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), null, $e->getCode());
        }
    }

    /**
     * Get a single reservoir by ID
     *
     * @param string $id The ID of the reservoir to retrieve
     * @return JsonResponse The requested reservoir data with success status
     */
    public function show(string $id): JsonResponse
    {
        try {
            $reservoir = $this->service->get($id);
            return $this->successResponse('Reservoir retrieved successfully', $reservoir);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), null, $e->getCode());
        }
    }

    /**
     * Create a new reservoir record   
     * Creating permission is checked in FormRequest
     *
     * @param StoreReservoirRequest $request The HTTP request containing reservoir data
     * @return JsonResponse The newly created reservoir with success status
     */
    public function store(StoreReservoirRequest $request): JsonResponse
    {
        try {

            $validated = $request->validated();
            $reservoir = $this->service->store($validated);
            return $this->successResponse('Reservoir created successfully', $reservoir, 201);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), null, $e->getCode() ?: 400);
        }
    }

    /**
     * Update an existing reservoir record
     *
     * Updating permission is checked in FormRequest
     *
     * @param UpdateReservoirRequest $request The HTTP request containing updated data
     * @return JsonResponse The updated reservoir data with success status
     */
    public function update(UpdateReservoirRequest $request,  Reservoir $reservoir): JsonResponse
    {
        try {
            $validated = $request->validated();
            $reservoir = $this->service->update($validated, $reservoir);
            return $this->successResponse('Reservoir updated successfully', $reservoir);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), null, $e->getCode() ?: 400);
        }
    }

    /**
     * Delete a reservoir record
     *
     * @param string $id The ID of the reservoir to delete
     * @return JsonResponse Success message with empty data
     */
    public function destroy(Reservoir $reservoir): JsonResponse
    {
        try {
            if (!Gate::allows('delete_distribution_network_component', $reservoir)) {
                return $this->errorResponse('Unauthorized', null, 403);
            }
            $this->service->destroy($reservoir);
            return $this->successResponse('Reservoir deleted successfully', null, 204);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), null, $e->getCode());
        }
    }
}

<?php

namespace Modules\DistributionNetwork\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Modules\DistributionNetwork\Http\Requests\DistributionNetwork\StoreDistributionNetworkRequest;
use Modules\DistributionNetwork\Http\Requests\DistributionNetwork\UpdateCurrentVolume;
use Modules\DistributionNetwork\Http\Requests\DistributionNetwork\UpdateDistributionNetworkRequest;
use Modules\DistributionNetwork\Models\DistributionNetwork;
use Modules\DistributionNetwork\Services\DistributionNetworkService;

class DistributionNetworkController extends Controller
{
    protected DistributionNetworkService $networkService;

    /**
     * Summary of middleware
     * @return array<Middleware|string>
     */
    public static function middleware(): array
    {
        return [
            new Middleware('role:Super Admin|Distribution Network Manager', only:['store','show', 'update', 'destroy']),
            new Middleware('permission:view_distribution_network_map', only:['index']),
        ];
    }

    /**
     * Constructor for the DistributionNetworkController class.
     * Initializes the $networkService property via dependency injection.
     *
     * @param DistributionNetworkService $networkService
     */
    public function __construct(DistributionNetworkService $networkService)
    {
        $this->networkService = $networkService;
    }

    /**
     * This method return all networks from database.
     */
    public function index(Request $request)
    {
        $filters = $request->only(['name','water_source_id', 'manager_id']);
        return $this->successResponse(
                        'Operation succcessful'
                        , $this->networkService->getAllNetworks($filters)
                        , 200);
    }

    /**
     * Add a new network in the database using the networkService via the createNetwork method
     * passes the validated request data to createNetwork.
     *
     * @param StoreDistributionNetworkRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreDistributionNetworkRequest $request)
    {
        return $this->successResponse(
                        'Created succcessful'
                        , $this->networkService->createNetwork($request->validated())
                        , 201);
    }

    /**
     * Get network from database.
     * using the networkService via the showNetwork method
     *
     * @param DistributionNetwork $network
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(DistributionNetwork $network)
    {
        return $this->successResponse(
                        'Operation succcessful'
                        ,$this->networkService->showNetwork($network)
                        ,200);
    }

    /**
     * Update a network in the database using the networkService via the updateNetwork method.
     * passes the validated request data to updateNetwork.
     *
     * @param UpdateDistributionNetworkRequest $request
     *
     * @param DistributionNetwork $network
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateDistributionNetworkRequest  $request, DistributionNetwork $network)
    {
        return $this->successResponse(
                        'Updated succcessful'
                        ,$this->networkService->updateNetwork($request->validated(), $network));
    }

    /**
     * Remove the specified network from database.
     * using the networkService via the deleteNetwork method
     *
     * @param DistributionNetwork $network
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(DistributionNetwork $network)
    {
        $this->networkService->deleteNetwork($network);
        return $this->successResponse(
                        'Deleted succcessful'
                        ,null);
    }

    /**
     * Review distribution network tickets.
     */
    public function review(): JsonResponse
    {
        try {
            $tickets = $this->networkService->review();
            return $this->successResponse('Tickets retrieved successfully', $tickets);
        } catch (\Throwable $e) {
            return $this->errorResponse($e->getMessage(), null, $e->getCode() ?: 500);
        }
    }
    /**
     *
     */
    public function updateCurrentVolume(UpdateCurrentVolume $request , DistributionNetwork $network)
    {
        try {
            $current_volume = $request->validated();
            return $this->networkService->updateCurrentVolume($current_volume , $network);
        } catch (\Throwable $e) {
            return $this->errorResponse($e->getMessage(), null, $e->getCode() ?: 500);
        }
    }
}

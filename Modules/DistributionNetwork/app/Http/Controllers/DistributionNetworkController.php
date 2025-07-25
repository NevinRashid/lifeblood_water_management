<?php

namespace Modules\DistributionNetwork\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\DistributionNetwork\Http\Requests\DistributionNetwork\SotreDistributionNetworkRequest;
use Modules\DistributionNetwork\Http\Requests\DistributionNetwork\UpdateDistributionNetworkRequest;
use Modules\DistributionNetwork\Models\DistributionNetwork;
use Modules\DistributionNetwork\Services\DistributionNetworkService;

class DistributionNetworkController extends Controller
{
    protected DistributionNetworkService $networkService;

    /**
     * Constructor for the DistributionNetworkController class.
     * Initializes the $networkService property via dependency injection.
     *
     * @param DistributionNetworkService $networkService
     */
    public function __construct(DistributionNetworkService $networkService)
    {
        $this->networkService =$networkService;
    }

    /**
     * This method return all networks from database.
     */
    public function index()
    {
        return $this->successResponse(
                            'Operation succcessful'
                            ,$this->networkService->getAllNetworks()
                            ,200);
    }

    /**
     * Add a new network in the database using the networkService via the createNetwork method
     * passes the validated request data to createNetwork.
     *
     * @param SotreDistributionNetworkRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(SotreDistributionNetworkRequest $request)
    {
        return $this->successResponse(
                            'Created succcessful'
                            ,$this->networkService->createNetwork($request->validated())
                            ,201);
    }

    /**
     * Get network from database.
     * using the networkService via the showNetwork method
     *
     * @param DistributionNetwork $network
     *
     * @return \Illuminate\Http\Response
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
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateDistributionNetworkRequest  $request, DistributionNetwork $network)
    {
        return $this->successResponse(
                            'Updated succcessful'
                            ,$this->networkService->updateNetwork($request->validated(),$network));
    }

    /**
     * Remove the specified network from database.
     * using the networkService via the deleteNetwork method
     *
     * @param DistributionNetwork $network
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(DistributionNetwork $network)
    {
        $this->networkService->deleteNetwork($network);
        return $this->successResponse(
                    'Deleted succcessful'
                    ,null);
    }
}

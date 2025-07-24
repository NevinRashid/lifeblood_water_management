<?php

namespace Modules\DistributionNetwork\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\DistributionNetwork\Http\Requests\DistributionPoint\StoreDistributionPointRequest;
use Modules\DistributionNetwork\Http\Requests\DistributionPoint\UpdateDistributionPointRequest;
use Modules\DistributionNetwork\Models\DistributionPoint;
use Modules\DistributionNetwork\Services\DistributionPointService;

class DistributionPointController extends Controller
{
    protected DistributionPointService $pointService;

    /**
     * Constructor for the DistributionPointController class.
     * Initializes the $pointService property via dependency injection.
     *
     * @param DistributionPointService $pointService
     */
    public function __construct(DistributionPointService $pointService)
    {
        $this->pointService =$pointService;
    }

    /**
     * This method return all distributionPoints from database.
     * using the pointService via the getAllPoints method
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $filters = $request->only(['status','type', 'distribution_network_id']);
        return $this->successResponse(
                            'Operation succcessful'
                            ,$this->pointService->getAllPoints($filters)
                            ,200);
    }

    /**
     * Add a new distributionPoint in the database using the pointService via the createPoint method
     * passes the validated request data to createPoint.
     *
     * @param StoreDistributionPointRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(StoreDistributionPointRequest $request)
    {
        return $this->successResponse(
                            'Created succcessful'
                            ,$this->pointService->createPoint($request->validated())
                            ,201);
    }

    /**
     * Get distributionPoint from database.
     * using the pointService via the showPoint method
     *
     * @param DistributionPoint $point
     *
     * @return \Illuminate\Http\Response
     */
    public function show(DistributionPoint $point)
    {
        return $this->successResponse(
                            'Operation succcessful'
                            ,$this->pointService->showPoint($point)
                            ,200);
    }

    /**
     * Update a distributionPoint in the database using the pointService via the updatePoint method.
     * passes the validated request data to updatePoint.
     *
     * @param UpdateDistributionPointRequest $request
     *
     * @param DistributionPoint $point
     *
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateDistributionPointRequest $request, DistributionPoint $point)
    {
        return $this->successResponse(
                            'Updated succcessful'
                            ,$this->pointService->updatePoint($request->validated(),$point));
    }

    /**
     * Remove the specified distributionPoint from database.
     * using the pointService via the deletePoint method
     *
     * @param DistributionPoint $point
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(DistributionPoint $point)
    {
        $this->pointService->deletePoint($point);
        return $this->successResponse(
                    'Deleted succcessful'
                    ,null);
    }
}

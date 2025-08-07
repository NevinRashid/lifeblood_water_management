<?php

namespace Modules\DistributionNetwork\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;
use Modules\DistributionNetwork\Http\Requests\DistributionPoint\StoreDistributionPointRequest;
use Modules\DistributionNetwork\Http\Requests\DistributionPoint\UpdateDistributionPointRequest;
use Modules\DistributionNetwork\Models\DistributionPoint;
use Modules\DistributionNetwork\Services\DistributionPointService;

class DistributionPointController extends Controller
{
    protected DistributionPointService $pointService;

    /**
     * Summary of middleware
     * @return array<Middleware|string>
     */
    public static function middleware(): array
    {
        return [
            new Middleware('can:show_distribution_network_component', only: ['show']),
            new Middleware('can:view_all_distribution_network_component', only: ['index']),
        ];
    }

    /**
     * Constructor for the DistributionPointController class.
     * Initializes the $pointService property via dependency injection.
     *
     * @param DistributionPointService $pointService
     */
    public function __construct(DistributionPointService $pointService)
    {
        $this->pointService = $pointService;
    }

    /**
     * This method return all distributionPoints from database.
     * using the pointService via the getAllPoints method
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $filters = $request->only(['status', 'type', 'distribution_network_id']);
        return $this->successResponse(
            'Operation succcessful',
            $this->pointService->getAllPoints($filters),
            200
        );
    }

    /**
     * Add a new distributionPoint in the database using the pointService via the createPoint method
     * passes the validated request data to createPoint.
     *
     * @param StoreDistributionPointRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreDistributionPointRequest $request)
    {
        return $this->successResponse(
            'Created succcessful',
            $this->pointService->createPoint($request->validated()),
            201
        );
    }

    /**
     * Get distributionPoint from database.
     * using the pointService via the showPoint method
     *
     * @param DistributionPoint $point
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(DistributionPoint $point)
    {
        return $this->successResponse(
            'Operation succcessful',
            $this->pointService->showPoint($point),
            200
        );
    }

    /**
     * Update a distributionPoint in the database using the pointService via the updatePoint method.
     * passes the validated request data to updatePoint.
     *
     * @param UpdateDistributionPointRequest $request
     *
     * @param DistributionPoint $point
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateDistributionPointRequest $request, DistributionPoint $point)
    {
        return $this->successResponse(
            'Updated succcessful',
            $this->pointService->updatePoint($request->validated(), $point)
        );
    }

    /**
     * Remove the specified distributionPoint from database.
     * using the pointService via the deletePoint method
     *
     * @param DistributionPoint $point
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(DistributionPoint $point)
    {
        if (!Gate::allows('delete_distribution_network_component', $point)) {
            return $this->errorResponse('Unauthorized', null, 403);
        }
        $this->pointService->deletePoint($point);
        return $this->successResponse(
            'Deleted succcessful',
            null
        );
    }
}

<?php

namespace Modules\WaterDistributionOperations\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Routing\Controllers\Middleware;
use Modules\WaterDistributionOperations\Http\Requests\ReservoirsActivity\StoreReservoirsActivityRequest;
use Modules\WaterDistributionOperations\Http\Requests\ReservoirsActivity\UpdateReservoirsActivityRequest;
use Modules\WaterDistributionOperations\Services\ReservoirsActivityService;
use Modules\WaterDistributionOperations\Models\ReservoirActivity;

class ReservoirsActivityController extends Controller
{
    protected ReservoirsActivityService $reservoirsActivityService;

    /**
     * Summary of middleware
     * @return array<Middleware|string>
     */
    public static function middleware(): array
    {
        return [
            new Middleware('role:Super Admin|Reservoir And Tanker Supervisor', only:['store', 'update', 'destroy']),
            new Middleware('permission:show_reservoir_activity', only:['show']),
            new Middleware('permission:view_all_reservoirs_activity', only:['index']),
            new Middleware('permission:get_reservoir_current_level', only:['getCurrentLevel']),
        ];
    }

    /**
     * Constructor for the ReservoirsActivityController class.
     * Initializes the $reservoirsActivityService property via dependency injection.
     *
     * @param reservoirsActivityService $reservoirsActivityService
     */
    public function __construct(ReservoirsActivityService $reservoirsActivityService)
    {
        $this->reservoirsActivityService = $reservoirsActivityService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->successResponse(
                            'Operation succcessful'
                            ,$this->reservoirsActivityService->getAll()
                            ,200);
    }

    /**
     * Add a new reservoirsActivity the database using the reservoirsActivityService via the store method
     * passes the validated request data to store.
     *
     * @param StoreReservoirsActivityRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreReservoirsActivityRequest $request)
    {
        $activity = $this->reservoirsActivityService->store($request->validated());

        $alert = $this->reservoirsActivityService->checkAndAlertCriticalLevel($request->validated()['reservoir_id']);

        $result = [
                'data' =>$activity,
                'critical_alert' => $alert
                ];
        return $this->successResponse(
                            'Reservoir activity created successfully'
                            ,$result
                            ,201);
    }

    /**
     * Get reservoirActivity from database.
     * using the reservoirsActivityService via the get method
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        return $this->successResponse(
                            'Operation succcessful'
                            ,$this->reservoirsActivityService->get($id)
                            ,200);
    }


    /**
     * Update the specified reservoirsActivity in the database.
     *
     * @param UpdateReservoirsActivityRequest $request
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateReservoirsActivityRequest $request, $id)
    {
        return $this->successResponse(
                        'Reservoir activity updated successfully.'
                        ,$this->reservoirsActivityService->update($request->validated(),$id));
    }

    /**
     * Remove the specified reservoirActivity from database.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        /*$this->reservoirsActivityService->destroy($id);
        return $this->successResponse(
                        'Reservoir activity deleted successfully.'
                        , null);*/
        $this->reservoirsActivityService->destroy($id);
        return $this->successResponse('Beneficiary Deleted Successfully');
    }

    /**
     * Retrieve the current water level of the specified reservoir.
     * Calculates the net water level based on recorded reservoir activities
     * and returns a standardized success response containing:
     *  - reservoir_id: ID of the reservoir.
     *  - current_level: Calculated current water level.
     *
     * @param int $reservoirId  The ID of the reservoir to check.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCurrentLevel(int $reservoirId)
    {
        $level = $this->reservoirsActivityService->calculateCurrentLevel($reservoirId);
        $data = [
            'reservoir_id' => $reservoirId,
            'current_level' => $level,
        ];
        return $this->successResponse(
                            'Operation succcessful'
                            ,$data
                            ,200);
    }
}

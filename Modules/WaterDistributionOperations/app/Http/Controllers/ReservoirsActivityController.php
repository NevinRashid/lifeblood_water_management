<?php

namespace Modules\WaterDistributionOperations\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\WaterDistributionOperations\Services\ReservoirsActivityService;
use Modules\WaterDistributionOperations\Http\Requests\StoreReservoirsActivityRequest;
use Modules\WaterDistributionOperations\Http\Requests\UpdateReservoirsActivityRequest;

class ReservoirsActivityController extends Controller
{
    protected ReservoirsActivityService $reservoirsActivityService;

    public function __construct(ReservoirsActivityService $reservoirsActivityService)
    {
        $this->reservoirsActivityService = $reservoirsActivityService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $activities = $this->reservoirsActivityService->getAll();
        return response()->json(['data' => $activities]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreReservoirsActivityRequest $request)
    {
        $activity = $this->reservoirsActivityService->store($request->validated());
        return response()->json(['message' => 'Reservoir activity created successfully.', 'data' => $activity], 201);
    }

    /**
     * Show the specified resource.
     */
    public function show(int $id)
    {
        $activity = $this->reservoirsActivityService->get($id);
        return response()->json(['data' => $activity]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateReservoirsActivityRequest $request, int $id)
    {
        $activity = $this->reservoirsActivityService->update($id, $request->validated());
        return response()->json(['message' => 'Reservoir activity updated successfully.', 'data' => $activity]);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $this->reservoirsActivityService->delete($id);
        return response()->json(['message' => 'Reservoir activity deleted successfully.']);
    }

    public function getCurrentLevel($reservoirId) {

        $level = $this->service->calculateCurrentLevel($reservoirId);

        return response()->json([
            'reservoir_id' => $reservoirId,
            'current_level' => $level,
        ]);
    }
}

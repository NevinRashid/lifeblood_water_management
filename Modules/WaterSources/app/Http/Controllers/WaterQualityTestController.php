<?php

namespace Modules\WaterSources\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\WaterSources\Services\WaterQualityTestService;
use Modules\WaterSources\Http\Requests\StoreWaterQualityTestRequest;
use Modules\WaterSources\Http\Requests\UpdateWaterQualityTestRequest;

class WaterQualityTestController extends Controller
{

    public function __construct(protected WaterQualityTestService $service) {

        // $this->middleware('permission:record water quality analysis')->only('store' , 'update');
        // $this->middleware('permission:view water quality reports')->only(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $tests = $this->service->index($request->only([
            'water_source_id', 'date_from', 'date_to'
        ]));

        return response()->json([
            'message' => 'Quality Test List',
            'data' => $tests,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreWaterQualityTestRequest $request)
    {
        $result  = $this->service->store($request->validated());
        $test = $result['test'];
        $failedParameters = $result['failed_parameters'];

        return response()->json([
            'message' => 'Saved successfully',
            'data' => $test,
            'debug_failures' => $failedParameters
        ], 201);
    }
    /**
     * Show the specified resource.
     */
    public function show(int $id)
    {
        $test = $this->service->show($id);

        return response()->json([
            'message' => ' Quality Test Details ',
            'data' => $test,
        ]);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateWaterQualityTestRequest $request, int $id)
    {

        $result = $this->service->update($id, $request->validated());
        $test = $result['test'];
        $failedParameters = $result['failed_parameters'];

        return response()->json([
            'message' => 'Updated successfully',
            'data' => $test,
            'debug_failures' => $failedParameters
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $this->service->destroy($id);
        return response()->json(['message' => 'Deleted successfully']);
    }


        /**
         * Generate and return a specific water quality report.
         *
         * @param int $id
         * @return \Illuminate\Http\JsonResponse
         */
        public function generateReport(int $id)
        {
            $reportData = $this->service->getReportAndDispatchEmail($id);
            return response()->json([
                'message' => 'Report data retrieved. The email is being sent in the background.',
                'data' => $reportData
            ]);
        }

}

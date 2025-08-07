<?php

namespace Modules\WaterSources\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Routing\Controllers\Middleware;
use Modules\WaterSources\Services\WaterQualityTestService;
use Modules\WaterSources\Http\Requests\WaterQualityTest\StoreWaterQualityTestRequest;
use Modules\WaterSources\Http\Requests\WaterQualityTest\UpdateWaterQualityTestRequest;

class WaterQualityTestController extends Controller
{

    public function __construct(protected WaterQualityTestService $service) {
    }

    /**
     * Define the middleware for this controller.
     *
     * @return array
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view water quality reports', only: ['index', 'show','generateReport']),
            new Middleware('permission:record water quality analysis', only: ['store', 'update']),
            new Middleware('permission:destroy water quality analysis', only: ['destroy']),
        ];
    }
    /**
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $tests = $this->service->index($request->only([
            'water_source_id', 'date_from', 'date_to'
        ]));

          return $this->successResponse('Water quality tests retrieved successfully.', $tests);
    }


    /**
     *
     * @param \Modules\WaterSources\Http\Requests\WaterQualityTest\StoreWaterQualityTestRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreWaterQualityTestRequest $request)
    {
        $result  = $this->service->store($request->validated());
         $responseData = [
            'test' => $result['test'],
            'debug_failures' => $result['failed_parameters'],
        ];
          return $this->successResponse('Water quality test saved successfully.', $responseData, 201);

    }
    /**
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id)
    {
        $test = $this->service->show($id);

         return $this->successResponse('Water quality test details retrieved successfully.', $test);
    }
    /**
     *
     * @param \Modules\WaterSources\Http\Requests\WaterQualityTest\UpdateWaterQualityTestRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateWaterQualityTestRequest $request, int $id)
    {

        $result = $this->service->update($request->validated(),$id );
        $responseData = [
            'test' => $result['test'],
            'debug_failures' => $result['failed_parameters'],
        ];
       return $this->successResponse('Water quality test updated successfully.', $responseData);
    }
    /**
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id)
    {
        $this->service->destroy($id);
        return $this->successResponse('Water quality test deleted successfully.');
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
              return $this->successResponse(
            'Report data retrieved. The email is being sent in the background.',
            $reportData
        );
    }

}

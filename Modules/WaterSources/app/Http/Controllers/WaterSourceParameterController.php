<?php
namespace  Modules\WaterSources\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Modules\WaterSources\Models\WaterSource;
use Modules\WaterSources\Models\TestingParameter;
use Modules\WaterSources\Services\WaterSourceParameterService;
use Modules\WaterSources\Http\Requests\WaterSourceParameter\StoreWaterSourceParameterRequest;



 class WaterSourceParameterController extends Controller {

        public function __construct(private WaterSourceParameterService $service)
        {

        }


    public function index(WaterSource $waterSource)
    {
        $parameters = $this->service->getParametersForSource($waterSource);

        return response()->json([
            'message' => 'Parameters assigned successfully.',
            'data' => $parameters
        ], Response::HTTP_CREATED);
       ;
    }

    public function store(StoreWaterSourceParameterRequest $request, WaterSource $waterSource): JsonResponse
    {
        $assignedParameters = $this->service->assignParameters($waterSource, $request->validated()['parameters']);

        return response()->json([
            'message' => 'Parameters assigned successfully.',
            'data' => $assignedParameters
        ], Response::HTTP_CREATED);
    }


    public function update(StoreWaterSourceParameterRequest $request, WaterSource $waterSource): JsonResponse
    {
        $syncedParameters = $this->service->syncParameters($waterSource, $request->validated()['parameters']);

        return response()->json([
            'message' => 'Parameters synced successfully.',
            'data' => $syncedParameters
        ]);
    }


    public function destroy(WaterSource $waterSource, TestingParameter $parameter)
    {
        $this->service->removeParameter($waterSource, $parameter);

        return response()->json([
            'message' => 'Parameters Removed successfully'
        ]);
    }
 }

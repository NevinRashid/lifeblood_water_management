<?php
    namespace  Modules\WaterSources\Http\Controllers;

    use Illuminate\Http\Response;
    use Illuminate\Http\JsonResponse;
    use App\Http\Controllers\Controller;
    use Illuminate\Routing\Controllers\Middleware;
    use Modules\WaterSources\Models\WaterSource;
    use Modules\WaterSources\Models\TestingParameter;
    use Modules\WaterSources\Services\WaterSourceParameterService;
    use Modules\WaterSources\Http\Requests\WaterSourceParameter\StoreWaterSourceParameterRequest;



    class WaterSourceParameterController extends Controller {

        /**
         *
         * @return Middleware[]
         */
        public static function middleware(): array
        {
            return [
                new Middleware('permission:view water source parameters', only: ['index']),
                new Middleware('permission:assign water source parameters', only: ['store', 'update']),
                new Middleware('permission:unassign water source parameters', only: ['destroy']),
            ];
        }
            /**
             *
             * @param \Modules\WaterSources\Services\WaterSourceParameterService $service
             */
            public function __construct(private WaterSourceParameterService $service)
            {
            }

        /**
         *
         * @param \Modules\WaterSources\Models\WaterSource $waterSource
         * @return JsonResponse
         */
        public function index(WaterSource $waterSource)
        {
            $parameters = $this->service->getParametersForSource($waterSource);

            return response()->json([
                'message' => 'Parameters assigned successfully.',
                'data' => $parameters
            ], Response::HTTP_CREATED);
        ;
        }
        /**
         *
         * @param \Modules\WaterSources\Http\Requests\WaterSourceParameter\StoreWaterSourceParameterRequest $request
         * @param \Modules\WaterSources\Models\WaterSource $waterSource
         * @return JsonResponse
         */
        public function store(StoreWaterSourceParameterRequest $request, WaterSource $waterSource): JsonResponse
        {
            $assignedParameters = $this->service->assignParameters($waterSource, $request->validated()['parameters']);

            return response()->json([
                'message' => 'Parameters assigned successfully.',
                'data' => $assignedParameters
            ], Response::HTTP_CREATED);
        }

        /**
         *
         * @param \Modules\WaterSources\Http\Requests\WaterSourceParameter\StoreWaterSourceParameterRequest $request
         * @param \Modules\WaterSources\Models\WaterSource $waterSource
         * @return JsonResponse
         */
        public function update(StoreWaterSourceParameterRequest $request, WaterSource $waterSource): JsonResponse
        {
            $syncedParameters = $this->service->syncParameters($waterSource, $request->validated()['parameters']);

            return response()->json([
                'message' => 'Parameters synced successfully.',
                'data' => $syncedParameters
            ]);
        }

        /**
         *
         * @param \Modules\WaterSources\Models\WaterSource $waterSource
         * @param \Modules\WaterSources\Models\TestingParameter $parameter
         * @return JsonResponse
         */
        public function destroy(WaterSource $waterSource, TestingParameter $parameter)
        {
            $this->service->removeParameter($waterSource, $parameter);

            return response()->json([
                'message' => 'Parameters Removed successfully'
            ]);
        }
    }

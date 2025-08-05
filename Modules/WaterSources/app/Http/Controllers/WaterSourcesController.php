<?php

namespace Modules\WaterSources\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Exception;
use Modules\WaterSources\Models\WaterSource;
use Modules\WaterSources\Http\Requests\AddMediaRequest;
use Modules\WaterSources\Http\Resources\WaterSourceResource;
use Modules\WaterSources\Http\Requests\StoreWaterSourceRequest;
use Modules\WaterSources\Http\Requests\UpdateWaterSourceRequest;
use Modules\WaterSources\Services\WaterSourceService;

class WaterSourcesController extends Controller
{
    protected WaterSourceService $waterSourceService;

    public function __construct(WaterSourceService $waterSourceService)
    {

        $this->waterSourceService = $waterSourceService;

        //  $this->middleware('permission:view water source')->only('index', 'show', 'overview');

        // // Only users with 'create water source' permission can store a new one
        // $this->middleware('permission:create water source')->only('store');

        // // Only users with 'update water source' permission can update
        // $this->middleware('permission:update water source')->only('update');

        // // Only users with 'delete water source' permission can destroy
        // $this->middleware('permission:delete water source')->only('destroy');

        // // Only users with 'attach documents to water source' permission can add media
        // $this->middleware('permission:attach documents to water source')->only('addMedia');
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */


    public function index(Request $request): JsonResponse
    {
        $waterSources = $this->waterSourceService->getAll($request->all());


        $data = WaterSourceResource::collection($waterSources);

        return $this->successResponse('Water sources retrieved successfully.', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreWaterSourceRequest $request
     * @return JsonResponse
     */
    public function store(StoreWaterSourceRequest $request): JsonResponse
    {

        $waterSource = $this->waterSourceService->store($request->validated());

        // $data = new WaterSourceResource($waterSource->load('media'));
        $waterSource->load('media');
        return $this->successResponse(
            'Water source created successfully.',
            $waterSource,
            Response::HTTP_CREATED // Status Code 201
        );
    }

    /**
     * Display the specified resource.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        $waterSource = $this->waterSourceService->get($id);

        $data = new WaterSourceResource($waterSource->load('media'));

        return $this->successResponse('Water source retrieved successfully.', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateWaterSourceRequest $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(UpdateWaterSourceRequest $request, string $id): JsonResponse
    {
        $waterSource = $this->waterSourceService->update($request->validated(), $id);

        $data = new WaterSourceResource($waterSource->load('media'));

        return $this->successResponse('Water source updated successfully.', $data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        $this->waterSourceService->destroy($id);

        return $this->successResponse(
            'Water source deleted successfully.',
            null, // No data to return
            Response::HTTP_OK // We can use 200 OK or 204 No Content
        );
    }
    /**
     *
     *
     * @param AddMediaRequest $request
     * @param WaterSource $waterSource
     * @return \Illuminate\Http\JsonResponse
     */
    public function addMedia(AddMediaRequest $request, WaterSource $waterSource)
    {

        $updatedWaterSource = $this->waterSourceService->addMedia($request->validated(), $waterSource);

        return new WaterSourceResource($updatedWaterSource);
    }

    /**
     * Get an overview of the water situation
     *
     * Returns all active water sources with their associated networks,
     * reservoirs, and distribution points.
     *
     * @return JsonResponse
     */
    public function overview(): JsonResponse
    {
        try {
            $overview = $this->waterSourceService->overview();
            return $this->successResponse('review of water sources retrieved successfully', $overview);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), null, $e->getCode());
        }
    }
}

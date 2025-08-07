<?php

namespace Modules\WaterSources\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Modules\WaterSources\Http\Requests\WaterExtraction\FilterWaterExtractionRequest;
use Modules\WaterSources\Http\Requests\WaterExtraction\StoreWaterExtractionRequest;
use Modules\WaterSources\Http\Requests\WaterExtraction\UpdateWaterExtractionRequest;
use Modules\WaterSources\Services\WaterExtractionService;

class WaterExtractionController extends Controller implements HasMiddleware
{

    public static function middleware(): array
    {
        return [
            new Middleware('can:view_water_extraction', only: ['show']),
            new Middleware('can:delete_water_extraction', only: ['destroy']),
        ];
    }
    /**
     * Service to handle aterExtraction-related logic 
     * and separating it from the controller
     * 
     * @var WaterExtractionService
     */
    protected $waterExtractionService;

    /**
     * WaterExtractionController constructor
     *
     * @param WaterExtractionService $waterExtractionService
     */
    public function __construct(WaterExtractionService $waterExtractionService)
    {
        // Inject the WaterExtractionService to handle waterExtraction-related logic
        $this->waterExtractionService = $waterExtractionService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(FilterWaterExtractionRequest $request)
    {
        $filters = $request->validated();
        $waterExtarctions = $this->waterExtractionService->getAll($filters);

        return $this->successResponse(
            'All Water Extarctoins Show Successfully',
            $waterExtarctions
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreWaterExtractionRequest $request)
    {
        $data = $request->validated();
        $data = $this->waterExtractionService->store($data);
        return $this->successResponse(
            'Water Extraction Added Successfully',
            $data,
            201
        );
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        $waterExtraction = $this->waterExtractionService->get($id);

        return $this->successResponse('Water Extraction Shown Successfully', $waterExtraction);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateWaterExtractionRequest $request, $id)
    {
        $data = $request->validated();
        $data = $this->waterExtractionService->update($data, $id);
        return $this->successResponse('Water Extraction Updated Successfully', $data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $this->waterExtractionService->destroy($id);

        return $this->successResponse('Water Extraction Deleted Successfully');
    }
}

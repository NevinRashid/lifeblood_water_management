<?php

namespace Modules\WaterSources\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\WaterSources\Services\TestingParameterService;
use Modules\WaterSources\Http\Requests\StoreTestingParameterRequest;
use Modules\WaterSources\Http\Requests\UpdateTestingParameterRequest;

class TestingParameterController extends Controller
{

    protected $service;

    public function __construct(TestingParameterService $service)
    {   $this->service = $service;

        // $this->middleware('permission:create_water_quality_test')->only('store');
        // $this->middleware('permission:update_water_quality_test')->only('update');
        // $this->middleware('permission:view water quality reports')->only(['index', 'show']);
        // $this->middleware('permission:delete water quality test')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $parameters = $this->service->index();
        return response()->json(['success' => true, 'data' => $parameters]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTestingParameterRequest $request)
    {
        $parameter = $this->service->store($request->validated());
        return response()->json(['success' => true, 'data' => $parameter], 201);
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        $parameter = $this->service->show($id);
        return response()->json(['success' => true, 'data' => $parameter]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTestingParameterRequest $request, $id)
    {
        $parameter = $this->service->update($id, $request->validated());
        return response()->json(['success' => true, 'data' => $parameter]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $this->service->destroy($id);
        return response()->json(['success' => true, 'message' => 'Deleted successfully']);
    }
}

<?php

namespace Modules\WaterSources\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Routing\Controllers\Middleware;
use Modules\WaterSources\Services\TestingParameterService;
use Modules\WaterSources\Http\Requests\TestingParameter\StoreTestingParameterRequest;
use Modules\WaterSources\Http\Requests\TestingParameter\UpdateTestingParameterRequest;

class TestingParameterController extends Controller
{

    protected $service;

    public function __construct(TestingParameterService $service)
    {   $this->service = $service;

    }
    /**
     * Define the middleware for this controller.
     *
     * @return array
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view testing parameters', only: ['index', 'show']),
            new Middleware('permission:create testing parameter', only: ['store']),
            new Middleware('permission:update testing parameter', only: ['update']),
            new Middleware('permission:delete testing parameter', only: ['destroy']),
        ];
    }
    /**
     * Summary of index
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $parameters = $this->service->index();
        return response()->json(['success' => true, 'data' => $parameters]);
    }

    /**
     * Summary of store
     * @param \Modules\WaterSources\Http\Requests\TestingParameter\StoreTestingParameterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreTestingParameterRequest $request)
    {
        $parameter = $this->service->store($request->validated());
        return response()->json(['success' => true, 'data' => $parameter], 201);
    }

    /**
     * Summary of show
     * @param mixed $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $parameter = $this->service->show($id);
        return response()->json(['success' => true, 'data' => $parameter]);
    }

    /**
     * Summary of update
     * @param \Modules\WaterSources\Http\Requests\TestingParameter\UpdateTestingParameterRequest $request
     * @param mixed $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateTestingParameterRequest $request, $id)
    {
        $parameter = $this->service->update($request->validated(),$id );
        return response()->json(['success' => true, 'data' => $parameter]);
    }

    /**
     * Summary of destroy
     * @param mixed $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $this->service->destroy($id);
        return response()->json(['success' => true, 'message' => 'Deleted successfully']);
    }
}

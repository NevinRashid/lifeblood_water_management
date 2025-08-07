<?php

namespace Modules\DistributionNetwork\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Modules\DistributionNetwork\Http\Requests\Pipe\StorePipeRequest;
use Modules\DistributionNetwork\Http\Requests\Pipe\UpdatePipeRequest;
use Modules\DistributionNetwork\Models\Pipe;
use Modules\DistributionNetwork\Services\PipeService;

class PipeController extends Controller
{

    protected PipeService $pipeService;

    /**
     * Summary of middleware
     * @return array<Middleware|string>
     */
    public static function middleware(): array
    {
        return [
            new Middleware('role:Super Admin|Distribution Network Manager', only:['store','index','show', 'update', 'destroy']),
        ];
    }

    /**
     * Constructor for the PipeController class.
     * Initializes the $pipeService property via dependency injection.
     *
     * @param PipeService $pipeService
     */
    public function __construct(PipeService $pipeService)
    {
        $this->pipeService =$pipeService;
    }

    /**
     * This method return all pipes from database.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $filters = $request->only(['status', 'distribution_network_id']);
        return $this->successResponse(
                            'Operation succcessful'
                            ,$this->pipeService->getAllPipes($filters)
                            ,200);
    }

    /**
     * Add a new pipe the database using the pipeService via the createPipe method
     * passes the validated request data to createPipe.
     *
     * @param StorePipeRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StorePipeRequest $request)
    {
        return $this->successResponse(
                            'Created succcessful'
                            ,$this->pipeService->createPipe($request->validated())
                            ,201);
    }

    /**
     * Get pipe from database.
     * using the pipeService via the showPipe method
     *
     * @param Pipe $pipe
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Pipe $pipe)
    {
        return $this->successResponse(
                            'Operation succcessful'
                            ,$this->pipeService->showPipe($pipe)
                            ,200);
    }

    /**
     * Update a pipe in the database using the pipeService via the updatePipe method.
     * passes the validated request data to updatePipe.
     *
     * @param UpdatePipeRequest $request
     * @param Pipe $pipe
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdatePipeRequest $request, Pipe $pipe)
    {
        return $this->successResponse(
                        'Updated succcessful'
                        ,$this->pipeService->updatePipe($request->validated(),$pipe));
    }

    /**
     * Remove the specified pipe from database.
     *
     * @param Pipe $pipe
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Pipe $pipe)
    {
        $this->pipeService->deletePipe($pipe);
        return $this->successResponse(
                        'Deleted succcessful'
                        , null);
    }
}

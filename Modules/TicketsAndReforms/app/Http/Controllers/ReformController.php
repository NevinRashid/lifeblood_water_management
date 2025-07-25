<?php

namespace Modules\TicketsAndReforms\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\TicketsAndReforms\Http\Requests\Reform\StoreReformRequest;
use Modules\TicketsAndReforms\Http\Requests\Reform\UpdateReformRequest;
use Modules\TicketsAndReforms\Http\Requests\Reform\UploadImageReformRequest;
use Modules\TicketsAndReforms\Models\Reform;
use Modules\TicketsAndReforms\Services\ReformService;

class ReformController extends Controller
{
    protected ReformService $reformService;

    /**
     * Constructor for the ReformController class.
     * Initializes the $reformService property via dependency injection.
     *
     * @param ReformService $reformService
     */
    public function __construct(ReformService $reformService)
    {
        $this->reformService =$reformService;
    }

    /**
     * This method return all reforms from database.
     * using the reformService via the getAllReforms method
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $filters = $request->only(['team_id','status']);
        return $this->successResponse(
                            'Operation succcessful'
                            ,$this->reformService->getAllReforms($filters)
                            ,200);
    }

    /**
     * Add a new reform the database using the reformService via the createReform method
     * passes the validated request data to createReform.
     *
     * @param StoreReformRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(StoreReformRequest $request)
    {
        return $this->successResponse(
                            'Created succcessful'
                            ,$this->reformService->createReform($request->validated())
                            ,201);
    }

    /**
     * Get reform from database.
     * using the reformService via the showReform method
     *
     * @param Reform $reform
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Reform $reform)
    {
        return $this->successResponse(
                            'Operation succcessful'
                            ,$this->reformService->showReform($reform)
                            ,200);
    }

    /**
     * Update a Reform in the database using the reformService via the updateReform method.
     * passes the validated request data to updateReform.
     *
     * @param UpdateReformRequest $request
     *
     * @param Reform $reform
     *
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateReformRequest $request, Reform $reform)
    {
        return $this->successResponse(
                        'Updated succcessful'
                        ,$this->reformService->updateReform($request->validated(),$reform));
    }

    /**
     * Remove the specified reform from database.
     *
     * @param Reform $reform
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Reform $reform)
    {
        $this->reformService->deleteReform($reform);
        return $this->successResponse(
                        'Deleted succcessful'
                        , null);
    }

    /**
     * Add images before and after reform in the database using the reformService via the addReformImages method.
     * passes the validated request data to addReformImages.
     *
     * @param UploadImageReformRequest $request
     * @param Reform $reform
     *
     * @return \Illuminate\Http\Response
     */
    public function addImage(UploadImageReformRequest $request, Reform $reform)
    {
        return $this->successResponse(
                        'added images succcessful'
                        ,$this->reformService->addReformImages($request->validated(),$reform));
    }
}

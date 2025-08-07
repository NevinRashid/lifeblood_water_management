<?php

namespace Modules\WaterDistributionOperations\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Routing\Controllers\Middleware;
use Modules\WaterDistributionOperations\Models\Tanker;
use Modules\WaterDistributionOperations\Services\TankerService;
use Modules\WaterDistributionOperations\Http\Requests\Tankers\StoreTankerRequest;
use Modules\WaterDistributionOperations\Http\Requests\Tankers\UpdateTankerRequest;

class TankerController extends Controller
{
    /**
     *
     * @param \Modules\WaterDistributionOperations\Services\TankerService $tankerService
     */
    public function __construct(protected TankerService $tankerService)
    {

    }
      /**
       *
       * @return Middleware[]
       */
      public static function middleware(): array
    {
        return [
            new Middleware('permission:view_tanker_fleet', only: ['index', 'show']),
            new Middleware('permission:create_tanker', only: ['store']),
            new Middleware('permission:update_tanker', only: ['update']),
            new Middleware('permission:delete_tanker', only: ['destroy']),
        ];
    }
    /**
     *
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $tankers = Tanker::with('users')->latest()->paginate($request->get('per_page', 15));
         return $this->successResponse('Tankers retrieved successfully.', $tankers);
    }

    /**
     *
     * @param \Modules\WaterDistributionOperations\Http\Requests\Tankers\StoreTankerRequest $request
     * @return JsonResponse
     */
    public function store(StoreTankerRequest $request): JsonResponse
    {
        // dd($request);
        $tanker = $this->tankerService->store($request->validated());

         return $this->successResponse(
            'Tanker created successfully.',
            $tanker,
            Response::HTTP_CREATED // 201
        );
    }

    /**
     *
     * @param \Modules\WaterDistributionOperations\Models\Tanker $tanker
     * @return JsonResponse
     */
    public function show(Tanker $tanker): JsonResponse
    {

        $tanker->load('users');
        return $this->successResponse('Tanker details retrieved successfully.', $tanker);
    }

    /**
     *
     * @param \Modules\WaterDistributionOperations\Http\Requests\Tankers\UpdateTankerRequest $request
     * @param \Modules\WaterDistributionOperations\Models\Tanker $tanker
     * @return JsonResponse
     */
    public function update(UpdateTankerRequest $request, Tanker $tanker): JsonResponse
    {
        // dd($request);
        $updatedTanker = $this->tankerService->update($request->validated(),$tanker );

        return $this->successResponse('Tanker updated successfully.', $updatedTanker);
    }

    /**
     *
     * @param \Modules\WaterDistributionOperations\Models\Tanker $tanker
     * @return JsonResponse
     */
    public function destroy(Tanker $tanker): JsonResponse
    {
        $this->tankerService->destroy($tanker);
        return $this->successResponse('Tanker deleted successfully.');
    }
}

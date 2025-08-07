<?php

namespace Modules\WaterDistributionOperations\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Routing\Controllers\Middleware;
use Modules\WaterDistributionOperations\Models\DeliveryRoute;
use Modules\WaterDistributionOperations\Services\DeliveryRouteService;
use Modules\WaterDistributionOperations\Http\Requests\DeliveryRoute\StoreDeliveryRouteRequest;
use Modules\WaterDistributionOperations\Http\Requests\DeliveryRoute\UpdateDeliveryRouteRequest;

class DeliveryRouteController extends Controller
{
    /**
     *
     * @param \Modules\WaterDistributionOperations\Services\DeliveryRouteService $deliveryRouteService
     */
    public function __construct(protected DeliveryRouteService $deliveryRouteService)
    {

    }
        /**
         * Summary of middleware
         * @return Middleware[]
         */
        public static function middleware(): array
    {
        return [
            new Middleware('permission:plan_tanker_routes', only: ['store', 'update', 'destroy']),
            new Middleware('permission:view tanker routes', only: ['index', 'show']),
        ];
    }
    /**
     *
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $routes = $this->deliveryRouteService->getAllDeliveryRoutes($request->query());
        return $this->successResponse('Delivery routes retrieved successfully.', $routes);
    }
    /**
     *
     * @param \Modules\WaterDistributionOperations\Http\Requests\DeliveryRoute\StoreDeliveryRouteRequest $request
     * @return JsonResponse
     */
    public function store(StoreDeliveryRouteRequest $request): JsonResponse
    {
        $route = $this->deliveryRouteService->createDeliveryRoute($request->validated());
        return $this->successResponse('Delivery route created successfully.', $route, 201);
    }
    /**
     *
     * @param \Modules\WaterDistributionOperations\Models\DeliveryRoute $deliveryRoute
     * @return JsonResponse
     */
    public function show(DeliveryRoute $deliveryRoute): JsonResponse
    {
        $route = $this->deliveryRouteService->findDeliveryRoute($deliveryRoute);
        return $this->successResponse('Delivery route retrieved successfully.', $route);
    }
    /**
     *
     * @param \Modules\WaterDistributionOperations\Http\Requests\DeliveryRoute\UpdateDeliveryRouteRequest $request
     * @param \Modules\WaterDistributionOperations\Models\DeliveryRoute $deliveryRoute
     * @return JsonResponse
     */
    public function update(UpdateDeliveryRouteRequest $request, DeliveryRoute $deliveryRoute): JsonResponse
    {
        $route = $this->deliveryRouteService->updateDeliveryRoute($deliveryRoute, $request->validated());
        return $this->successResponse('Delivery route updated successfully.', $route);
    }
    /**
     *
     * @param \Modules\WaterDistributionOperations\Models\DeliveryRoute $deliveryRoute
     * @return JsonResponse
     */
    public function destroy(DeliveryRoute $deliveryRoute): JsonResponse
    {
        $this->deliveryRouteService->deleteDeliveryRoute($deliveryRoute);
        return $this->successResponse('Delivery route deleted successfully.', null);
    }
}

<?php
namespace Modules\WaterDistributionOperations\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Modules\WaterDistributionOperations\Models\DeliveryRoute;
use Modules\WaterDistributionOperations\Models\RouteDelivered;
use Modules\WaterDistributionOperations\Services\RouteDeliveryService;
use Modules\WaterDistributionOperations\Http\Requests\RouteDelivery\StoreRouteDeliveryRequest;
use Modules\WaterDistributionOperations\Http\Requests\RouteDelivery\UpdateRouteDeliveryRequest;

class RouteDeliveryController extends Controller
{
    /**
     *
     * @param \Modules\WaterDistributionOperations\Services\RouteDeliveryService $routeDeliveryService
     */
    public function __construct(protected RouteDeliveryService $routeDeliveryService) {

    }

    /**
     *
     * @param \Modules\WaterDistributionOperations\Models\DeliveryRoute $deliveryRoute
     * @return JsonResponse
     */
    public function index(DeliveryRoute $deliveryRoute): JsonResponse
    {
        $deliveries = $this->routeDeliveryService->getForRoute($deliveryRoute);
        return $this->successResponse('Deliveries for route retrieved successfully.', $deliveries);
    }
    /**
     *
     * @param \Modules\WaterDistributionOperations\Http\Requests\RouteDelivery\StoreRouteDeliveryRequest $request
     * @param \Modules\WaterDistributionOperations\Models\DeliveryRoute $deliveryRoute
     * @return JsonResponse
     */
    public function store(StoreRouteDeliveryRequest $request, DeliveryRoute $deliveryRoute): JsonResponse
    {
        $data = $request->validated();
        $data['delivery_route_id'] = $deliveryRoute->id;

        $delivery = $this->routeDeliveryService->createDelivery($data);
        return $this->successResponse('Delivery recorded successfully.', $delivery->load('distributionPoint'), 201);
    }

    /**
     *
     * @param \Modules\WaterDistributionOperations\Models\RouteDelivered $routeDelivered
     * @return JsonResponse
     */
    public function show(RouteDelivered $routeDelivered): JsonResponse
    {
        return $this->successResponse('Delivery details retrieved successfully.', $routeDelivered->load('distributionPoint', 'deliveryRoute'));
    }
    /**
     *
     * @param \Modules\WaterDistributionOperations\Http\Requests\RouteDelivery\UpdateRouteDeliveryRequest $request
     * @param \Modules\WaterDistributionOperations\Models\RouteDelivered $routeDelivered
     * @return JsonResponse
     */
    public function update(UpdateRouteDeliveryRequest $request, RouteDelivered $routeDelivered): JsonResponse
    {
        $updatedDelivery = $this->routeDeliveryService->updateDelivery($routeDelivered, $request->validated());
        return $this->successResponse('Delivery updated successfully.', $updatedDelivery);
    }

    /**
     * 
     * @param \Modules\WaterDistributionOperations\Models\RouteDelivered $routeDelivered
     * @return JsonResponse
     */
    public function destroy(RouteDelivered $routeDelivered): JsonResponse
    {
        $this->routeDeliveryService->deleteDelivery($routeDelivered);
        return $this->successResponse('Delivery deleted successfully.', null);
    }
}

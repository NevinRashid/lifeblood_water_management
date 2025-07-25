<?php
namespace Modules\WaterDistributionOperations\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\WaterDistributionOperations\Models\DeliveryRoute;
use Modules\WaterDistributionOperations\Models\RouteDelivered;

class RouteDeliveryService
{
    /**
     *
     *
     * @param \Modules\WaterDistributionOperations\Models\DeliveryRoute $deliveryRoute
     * @param array $filters
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getForRoute(DeliveryRoute $deliveryRoute, array $filters = []): LengthAwarePaginator
    {
        return $deliveryRoute->deliveries()
            ->with('distributionPoint')
            ->latest('arrival_time')
            ->paginate($filters['per_page'] ?? 15);
    }

    /**
     *
     * @param array $data
     * @return TModel
     */
    public function createDelivery(array $data): RouteDelivered
    {
        return RouteDelivered::create($data);
    }

    /**
     *
     * @param \Modules\WaterDistributionOperations\Models\RouteDelivered $routeDelivered
     * @param array $data
     * @return RouteDelivered
     */
    public function updateDelivery(RouteDelivered $routeDelivered, array $data): RouteDelivered
    {
        $routeDelivered->update($data);
        return $routeDelivered->fresh()->load('distributionPoint');
    }
    /**
     * 
     * @param \Modules\WaterDistributionOperations\Models\RouteDelivered $routeDelivered
     * @return bool|null
     */
    public function deleteDelivery(RouteDelivered $routeDelivered): bool
    {
        return $routeDelivered->delete();
    }
}

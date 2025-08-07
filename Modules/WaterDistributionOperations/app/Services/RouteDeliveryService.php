<?php

namespace Modules\WaterDistributionOperations\Services;

use App\Services\Base\BaseService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Modules\WaterDistributionOperations\Models\DeliveryRoute;
use Modules\WaterDistributionOperations\Models\RouteDelivered;

class RouteDeliveryService extends BaseService
{
    /**
     * RouteDeliveryService constructor.
     * Sets the base model for this service.
     */
    public function __construct()
    {
        $this->model = new RouteDelivered();
    }

    /**
     * Get a paginated list of deliveries for a specific route.
     * This is a custom method specific to this service's needs.
     *
     * @param \Modules\WaterDistributionOperations\Models\DeliveryRoute $deliveryRoute The route to get deliveries for.
     * @param array $filters An array of filters, e.g., ['per_page' => 20].
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getForRoute(DeliveryRoute $deliveryRoute, array $filters = []): LengthAwarePaginator
    {
        return $this->handle(function () use ($deliveryRoute, $filters) {
            return $deliveryRoute->deliveries()
                ->with('distributionPoint')
                ->latest('arrival_time')
                ->paginate($filters['per_page'] ?? 15);
        });
    }

    /**
     * Create a new delivery record.
     * Overrides the parent `store` method.
     *
     * @param array $data Data for creating the delivery record.
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function store(array $data): Model
    {
        return $this->handle(function () use ($data) {
            // Simply call the parent's store method which is already wrapped in handle().
            return parent::store($data);
        });
    }

    /**
     * Update an existing delivery record.
     * Overrides the parent `update` method to add eager loading.
     *
     * @param array $data The data to update.
     * @param string|\Illuminate\Database\Eloquent\Model $modelOrId The model instance or its ID.
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function update(array $data, string|Model $modelOrId): Model
    {
        return $this->handle(function () use ($data, $modelOrId) {
            $delivery = parent::update($data, $modelOrId);
            return $delivery->load('distributionPoint');
        });
    }

    /**
     *  * Delete a delivery record.
     * The parent's destroy method is sufficient and already uses handle().
     * We don't need to override it unless we add custom logic like cache clearing.
     * @param string|\Illuminate\Database\Eloquent\Model $modelOrId
     * @return bool|null
     */
    public function destroy(string|Model $modelOrId): bool
    {
        return parent::destroy($modelOrId);
    }

}


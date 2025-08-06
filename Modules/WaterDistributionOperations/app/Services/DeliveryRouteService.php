<?php

namespace Modules\WaterDistributionOperations\Services;

use App\Services\Base\BaseService;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use MatanYadaev\EloquentSpatial\Objects\Point;
use MatanYadaev\EloquentSpatial\Objects\LineString;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\WaterDistributionOperations\Events\DeliveryRouteCanceled;
use Modules\WaterDistributionOperations\Models\DeliveryRoute;

class DeliveryRouteService extends BaseService
{

    /**
     * 
     * @param array $data
     * @return array
     */
    private function processPathData(array $data): array
    {
        if (isset($data['path']) && is_array($data['path'])) {
            $points = array_map(
                fn($point) => new Point($point['lat'], $point['lng']),
                $data['path']
            );
            $data['path'] = new LineString($points);
        }
        return $data;
    }

    /**
     *
     * @param array $filters
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getAllDeliveryRoutes(array $filters = []): LengthAwarePaginator
    {
        $query = DeliveryRoute::query()
            ->with('userTanker.user', 'userTanker.tanker');

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['planned_date'])) {
            $query->whereDate('planned_date', $filters['planned_date']);
        }

        $query->latest();

        return $query->paginate($filters['per_page'] ?? 15);
    }
    /**
     *
     * @param array $data
     * @return TModel
     */
    public function createDeliveryRoute(array $data): DeliveryRoute
    {
        $processedData = $this->processPathData($data);

        try {
            return DeliveryRoute::create($processedData);
        } catch (QueryException $e) {
            Log::error('Failed to create delivery route: ' . $e->getMessage());
            $this->throwExceptionJson(
                'An error occurred while creating the route. Please check the provided data.',

            );
        }
    }
    /**
     *
     * @param \Modules\WaterDistributionOperations\Models\DeliveryRoute $deliveryRoute
     * @return DeliveryRoute
     */
    public function findDeliveryRoute(DeliveryRoute $deliveryRoute): DeliveryRoute
    {
        return $deliveryRoute->load('userTanker.user', 'userTanker.tanker', 'deliveries');
    }
    /**
     *
     * @param \Modules\WaterDistributionOperations\Models\DeliveryRoute $deliveryRoute
     * @param array $data
     * @return DeliveryRoute|null
     */
    public function updateDeliveryRoute(DeliveryRoute $deliveryRoute, array $data): DeliveryRoute
    {
        
        if (in_array($deliveryRoute->status, ['completed', 'in_progress'])) {
            $this->throwExceptionJson(
                'Cannot update a route that is already in progress or completed.',
                422
            );
        }
        $processedData = $this->processPathData($data);

        try {
            // Check if the status is being updated to 'canceled'.
            $isCanceled = isset($processedData['status']) && $processedData['status'] === 'cancelled';

            $deliveryRoute->update($processedData);

            // If the route was just canceled, dispatch the event.
            if ($isCanceled) {
                event(new DeliveryRouteCanceled($deliveryRoute));
            }

            // Use the 'fresh' method to get a new instance with the latest data.
            return $deliveryRoute->fresh();
        } catch (\Exception $e) {
            Log::error('Failed to update delivery route ' . $deliveryRoute->id . ': ' . $e->getMessage());
            $this->throwExceptionJson(
                'An unexpected error occurred while updating the route.'
            );
        }
    }
    /**
     *
     * @param \Modules\WaterDistributionOperations\Models\DeliveryRoute $deliveryRoute
     * @return bool|null
     */
    public function deleteDeliveryRoute(DeliveryRoute $deliveryRoute): bool
    {
        if ($deliveryRoute->status === 'in_progress') {
            $this->throwExceptionJson(
                'Cannot delete a route that is currently in progress.',
                422
            );
        }

        try {
            return $deliveryRoute->delete();
        } catch (\Exception $e) {
            Log::error('Failed to delete delivery route ' . $deliveryRoute->id . ': ' . $e->getMessage());

            $this->throwExceptionJson(
                'An unexpected error occurred while deleting the route.',
                500
            );
        }
    }
}

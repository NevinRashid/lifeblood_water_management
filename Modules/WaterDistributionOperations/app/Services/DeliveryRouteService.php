<?php

namespace Modules\WaterDistributionOperations\Services;

use App\Services\Base\BaseService;
use App\Exceptions\CrudException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use MatanYadaev\EloquentSpatial\Objects\Point;
use MatanYadaev\EloquentSpatial\Objects\LineString;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\WaterDistributionOperations\Events\DeliveryRouteCanceled;
use Modules\WaterDistributionOperations\Models\DeliveryRoute;

class DeliveryRouteService extends BaseService
{
    /**
     * The model instance.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected Model $model;

    /**
     * DeliveryRouteService constructor.
     *
     * @param \Modules\WaterDistributionOperations\Models\DeliveryRoute $deliveryRoute
     */
    public function __construct(DeliveryRoute $deliveryRoute)
    {
        $this->model = $deliveryRoute;
    }

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
        return $this->handle(function () use ($filters) {
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
        });
    }

    /**
     *
     * @param array $data
     * @return DeliveryRoute
     */
    public function createDeliveryRoute(array $data): DeliveryRoute
    {
        return $this->handle(function () use ($data) {
            $processedData = $this->processPathData($data);
            try {
                return DeliveryRoute::create($processedData);
            } catch (QueryException $e) {
                throw new CrudException(
                    'An error occurred while creating the route. Please check the provided data.',
                    422,
                    $e
                );
            }
        });
    }

    /**
     *
     * @param \Modules\WaterDistributionOperations\Models\DeliveryRoute $deliveryRoute
     * @return DeliveryRoute
     */
    public function findDeliveryRoute(DeliveryRoute $deliveryRoute): DeliveryRoute
    {
        return $this->handle(fn() => $deliveryRoute->load('userTanker.user', 'userTanker.tanker', 'deliveries'));
    }

    /**
     *
     * @param \Modules\WaterDistributionOperations\Models\DeliveryRoute $deliveryRoute
     * @param array $data
     * @return DeliveryRoute
     */
    public function updateDeliveryRoute(DeliveryRoute $deliveryRoute, array $data): DeliveryRoute
    {
        if (in_array($deliveryRoute->status, ['completed', 'in_progress'])) {
            $this->throwExceptionJson(
                'Cannot update a route that is already in progress or completed.',
                422
            );
        }

        return $this->handle(function () use ($deliveryRoute, $data) {
            $processedData = $this->processPathData($data);
            $deliveryRoute->update($processedData);
            return $deliveryRoute->fresh(); 
        });
    }

    /**
     *
     * @param \Modules\WaterDistributionOperations\Models\DeliveryRoute $deliveryRoute
     * @return bool
     */
    public function deleteDeliveryRoute(DeliveryRoute $deliveryRoute): bool
    {
        // **التحقق من منطق العمل يبقى كما هو**
        if ($deliveryRoute->status === 'in_progress') {
            $this->throwExceptionJson(
                'Cannot delete a route that is currently in progress.',
                422
            );
        }

        return $this->handle(fn() => $deliveryRoute->delete());
    }
}

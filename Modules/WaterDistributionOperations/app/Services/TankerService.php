<?php

namespace Modules\WaterDistributionOperations\Services;

use App\Services\Base\BaseService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use MatanYadaev\EloquentSpatial\Objects\Point;
use Modules\WaterDistributionOperations\Models\Tanker;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TankerService extends BaseService
{
    /**
     * @var string
     */
    protected string $cacheKeyAll = 'tankers.all';

    /**
     * @var string
     */
    protected string $cachePrefixShow = 'tankers.show.';

        public function __construct()
    {
        $this->model = new Tanker();
    }
    /**
     *
     * @param array $filters
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllTankers(array $filters = []): LengthAwarePaginator
    {

        return Cache::remember($this->cacheKeyAll, now()->addMinutes(60), function () use ($filters) {

            $query = Tanker::query()->with('users');

            if (!empty($filters['tank_number'])) {
                $query->where('tank_number', 'like', '%' . $filters['tank_number'] . '%');
            }
            if (!empty($filters['status'])) {
                $query->where('status', $filters['status']);
            }
            if (!empty($filters['min_capacity'])) {
                $query->where('capacity', '>=', $filters['min_capacity']);
            }

            $sortBy = $filters['sort_by'] ?? 'created_at';
            $sortDirection = $filters['sort_direction'] ?? 'desc';
            $query->orderBy($sortBy, $sortDirection);

            $perPage = $filters['per_page'] ?? 15;

            return $query->paginate($perPage);
        });
    }

    /**
     * Find a single tanker and load its assigned users, using caching.
     * This is a custom method specific to this service's needs.
     *
     * @param \Modules\WaterDistributionOperations\Models\Tanker $tanker The tanker model instance.
     * @return \Modules\WaterDistributionOperations\Models\Tanker
     */
    public function findTanker(Tanker $tanker): Tanker
    {
        return $this->handle(function () use ($tanker) {
            $cacheKey = $this->cachePrefixShow . $tanker->id;

            return Cache::remember($cacheKey, now()->addMinutes(60), function () use ($tanker) {
                return $tanker->load('users');
            });
        });
    }

    /**
     * Create a new tanker, handle spatial data, and invalidate the cache.
     * Overrides the parent `store` method.
     *
     * @param array $data Data for creating the tanker.
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function store(array $data)
    {
        return $this->handle(function () use ($data) {
            if (isset($data['current_location']['lat']) && isset($data['current_location']['lng'])) {
                $data['current_location'] = new Point($data['current_location']['lat'], $data['current_location']['lng']);
            }

            $tanker = parent::store($data);

            Cache::forget($this->cacheKeyAll);

            return $tanker;
        });
    }

    /**
     * Update an existing tanker, handle spatial data, and invalidate caches.
     * Overrides the parent `update` method.
     *
     * @param array $data The data to update.
     * @param string|\Illuminate\Database\Eloquent\Model $modelOrId The model instance or its ID.
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function update(array $data, string|Model $modelOrId): Model
    {
        return $this->handle(function () use ($data, $modelOrId) {
            // Find the model if an ID is passed, otherwise use the provided model instance.
            $tanker = ($modelOrId instanceof Model) ? $modelOrId : $this->model->findOrFail($modelOrId);

            if (isset($data['current_location']['lat']) && isset($data['current_location']['lng'])) {
                $data['current_location'] = new Point($data['current_location']['lat'], $data['current_location']['lng']);
            }

            $tanker->update($data);

            // Invalidate both the list cache and the specific item cache.
            Cache::forget($this->cacheKeyAll);
            Cache::forget($this->cachePrefixShow . $tanker->id);

            return $tanker->fresh('users'); // Return the fresh model with users loaded
        });
    }

    /**
     * Delete a tanker and invalidate its caches.
     * Overrides the parent `destroy` method to add cache logic.
     *
     * @param string|\Illuminate\Database\Eloquent\Model $modelOrId The model instance or its ID to delete.
     * @return bool
     */
    public function destroy( $modelOrId)
    {
        return $this->handle(function () use ($modelOrId) {
            $tanker = ($modelOrId instanceof Model) ? $modelOrId : $this->model->findOrFail($modelOrId);

            // Get the ID before deleting to use it in the cache key
            $id = $tanker->id;

            $deleted = $tanker->delete();

            if ($deleted) {
                Cache::forget($this->cacheKeyAll);
                Cache::forget($this->cachePrefixShow . $id);
            }

            return $deleted;
        });
    }
}

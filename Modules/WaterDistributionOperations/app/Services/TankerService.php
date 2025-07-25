<?php

namespace Modules\WaterDistributionOperations\Services;

use App\Services\Base\BaseService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\WaterDistributionOperations\Models\Tanker;
use MatanYadaev\EloquentSpatial\Objects\Point;
use Illuminate\Support\Facades\Cache;

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
     *
     * @param \Modules\WaterDistributionOperations\Models\Tanker $tanker
     * @return \Modules\WaterDistributionOperations\Models\Tanker
     */
    public function findTanker(Tanker $tanker): Tanker
    {

        $cacheKey = $this->cachePrefixShow . $tanker->id;

        return Cache::remember($cacheKey, now()->addMinutes(60), function () use ($tanker) {
            return $tanker->load('users');
        });
    }

    /**
     *
     * @param array $data
     * @return TModel
     */
    public function createTanker(array $data): Tanker
    {
        if (isset($data['current_location'])) {
            $data['current_location'] = new Point($data['current_location']['lat'], $data['current_location']['lng']);
        }

        $tanker = Tanker::create($data);
        Cache::forget($this->cacheKeyAll);

        return $tanker;
    }

    /**
     *
     * @param \Modules\WaterDistributionOperations\Models\Tanker $tanker
     * @param array $data
     * @return Tanker|null
     */
    public function updateTanker(Tanker $tanker, array $data): Tanker
    {
        if (isset($data['current_location'])) {
            $data['current_location'] = new Point($data['current_location']['lat'], $data['current_location']['lng']);
        }

        $tanker->update($data);

        Cache::forget($this->cacheKeyAll);
        Cache::forget($this->cachePrefixShow . $tanker->id);

        return $tanker->fresh();
    }

    /**
     * 
     * @param \Modules\WaterDistributionOperations\Models\Tanker $tanker
     * @return bool|null
     */
    public function deleteTanker(Tanker $tanker): ?bool
    {
        $deleted = $tanker->delete();

        if ($deleted) {

            Cache::forget($this->cacheKeyAll);
            Cache::forget($this->cachePrefixShow . $tanker->id);
        }

        return $deleted;
    }
}

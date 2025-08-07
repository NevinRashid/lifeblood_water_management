<?php

namespace Modules\WaterSources\Services;

use App\Services\Base\BaseService;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use MatanYadaev\EloquentSpatial\Objects\Point;
use Modules\WaterSources\Events\WaterSourceCreated;
use Modules\WaterSources\Models\WaterSource;
use Spatie\MediaLibrary\HasMedia;

class WaterSourceService extends BaseService
{
    /**
     * WaterSourceService constructor.
     * Sets the base model for the service.
     */
    public function __construct()
    {
        $this->model = new WaterSource();
    }

    /**
     *  * Prepare a query for water sources with advanced filtering and relationships.
     * Overrides the parent `query` method to add custom logic.
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function getAll(array $filters = []): LengthAwarePaginator
    {

        $relationsToLoad = isset($filters['with']) ? explode(',', $filters['with']) : [];
        $query = WaterSource::query()->with(array_merge(['media'], $relationsToLoad));

        if (!empty($filters['with_count'])) {
            $relationsToCount = is_array($filters['with_count']) ? $filters['with_count'] : explode(',', $filters['with_count']);
            $query->withCount($relationsToCount);
        }
        if (!empty($filters['has_media'])) {
            $query->whereHas('media');
        }

        if (!empty($filters['name'])) $query->where('name', 'like', '%' . $filters['name'] . '%');
        if (!empty($filters['status'])) $query->where('status', $filters['status']);
        if (!empty($filters['source'])) $query->where('source', $filters['source']);
        if (!empty($filters['latitude']) && !empty($filters['longitude']) && !empty($filters['distance'])) {
            $distanceInMeters = $filters['distance'] * 1000;
            $query->whereRaw('ST_Distance_Sphere(location, POINT(?, ?)) <= ?', [$filters['longitude'], $filters['latitude'], $distanceInMeters]);
        }
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);
        $perPage = $filters['per_page'] ?? 15;

        return $query->paginate($perPage);
    }

    /**
     * Get a single water source, ensuring media is loaded.
     * Overrides the parent `get` method for custom eager loading.
     *
     * @param string|Model $modelOrId The model instance or its ID.
     * @return WaterSource
     */
    public function get(string|Model $modelOrId): WaterSource
    {
        return $this->handle(function () use ($modelOrId) {
            if ($modelOrId instanceof WaterSource) {
                return $modelOrId->loadMissing('media');
            }
            return $this->model->with('media')->findOrFail($modelOrId);
        });
    }

    /**
     * Create a new water source with its location and media, within a transaction.
     * Overrides the parent `store` method for complex creation logic.
     *
     * @param array $data Data for creating the water source.
     * @return WaterSource
     */
    public function store(array $data): Model
    {
        return $this->handle(function () use ($data) {
            return DB::transaction(function () use ($data) {
                $mediaRequestData = $this->extractMedia($data);

                if (isset($data['location']) && is_array($data['location'])) {
                    $data['location'] = new Point($data['location']['latitude'], $data['location']['longitude']);
                }

                $newWaterSource = parent::store($data);

                event(new WaterSourceCreated($newWaterSource));

                if (!empty($mediaRequestData)) {
                    $this->attachMedia($newWaterSource, $mediaRequestData);
                }

                return $newWaterSource;
            });
        });
    }

    /**
     * Update an existing water source with its media, within a transaction.
     * Overrides the parent `update` method for complex update logic.
     *
     * @param array $data Data for updating the water source.
     * @param string|Model $modelOrId The model instance or its ID.
     * @return WaterSource
     */
    public function update(array $data, string|Model $modelOrId): Model
    {
        return $this->handle(function () use ($data, $modelOrId) {
            $waterSource = $this->get($modelOrId);
            $mediaRequestData = $this->extractMedia($data);

            return DB::transaction(function () use ($waterSource, $data, $mediaRequestData) {
                // We use the parent update method which handles the actual update.
                parent::update($data, $waterSource);

                if (!empty($mediaRequestData)) {
                    $this->attachMedia($waterSource, $mediaRequestData);
                }

                return $waterSource->fresh('media');
            });
        });
    }

    /**
     * Add media files to a specific water source.
     *
     * @param array $mediaData Array of media files keyed by collection type.
     * @param string|Model $modelOrId The model instance or its ID.
     * @return WaterSource
     */
    public function addMedia(array $mediaData, string|Model $modelOrId): WaterSource
    {
        return $this->handle(function () use ($mediaData, $modelOrId) {
            $waterSource = $this->get($modelOrId);
            $this->attachMedia($waterSource, $mediaData);
            return $waterSource->fresh('media');
        });
    }

    /**
     * Get an optimized overview of all active water sources and their related active infrastructure.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function overview(): Collection
    {
        return $this->handle(function () {
            return $this->model->with([
                'networks' => fn($query) => $query->whereHas('source', fn($q) => $q->where('status', 'active')),
                'networks.reservoirs' => fn($query) => $query->select(['id', 'name', 'location', 'tank_type', 'maximum_capacity', 'minimum_critical_level', 'status', 'distribution_network_id'])->where('status', 'active'),
                'networks.distributionPoints' => fn($query) => $query->select(['id', 'name', 'location', 'type', 'status', 'distribution_network_id'])
            ])->where('status', 'active')
              ->get(['id', 'name', 'source', 'location', 'capacity_per_day', 'capacity_per_hour', 'status']);
        });
    }


        public function destroy(string|Model $modelOrId): ?bool
    {
            $waterSource = $this->get($modelOrId);
            return $waterSource->delete();
    }
    /**
     * Extract media-related data from the request data array.
     *
     * @param array $data
     * @return array
     */
    private function extractMedia(array &$data): array
    {
        $mediaKeys = ['documents', 'images', 'videos'];
        $mediaRequestData = [];
        foreach ($mediaKeys as $key) {
            if (isset($data[$key])) {
                $mediaRequestData[$key] = $data[$key];
                unset($data[$key]);
            }
        }
        return $mediaRequestData;
    }

    /**
     * Attach media files to a model.
     *
     * @param \Spatie\MediaLibrary\HasMedia $model
     * @param array $mediaRequestData
     * @return void
     */
    private function attachMedia(HasMedia $model, array $mediaRequestData): void
    {
        $collectionMapping = [
            'documents' => 'water_source_documents',
            'images' => 'water_source_images',
            'videos' => 'water_source_videos',
        ];

        foreach ($mediaRequestData as $requestKey => $files) {
            if (isset($collectionMapping[$requestKey])) {
                $collectionName = $collectionMapping[$requestKey];
                if (!is_array($files)) {
                    $files = [$files];
                }
                foreach ($files as $file) {
                    if ($file) {
                        $model->addMedia($file)->toMediaCollection($collectionName);
                    }
                }
            }
        }
    }


}


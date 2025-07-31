<?php

namespace Modules\WaterSources\Services;

use  Exception;
use Spatie\MediaLibrary\HasMedia;
use App\Events\WaterSourceCreated;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Modules\WaterSources\Services\Service;
use Modules\WaterSources\Models\WaterSource;
use MatanYadaev\EloquentSpatial\Objects\Point;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class WaterSourceService extends Service
{


    /**
     *
     * @param array $filters
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getAll(array $filters = [])
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
     * Retrieve a single water source by its model instance or ID.
     *
     * @param string|Model $modelOrId
     * @return WaterSource
     */
    public function get(string|Model $modelOrId): WaterSource
    {
        try {
            if ($modelOrId instanceof WaterSource) {
                return $modelOrId->loadMissing('media');
            }
            return WaterSource::with('media')->findOrFail($modelOrId);
        } catch (ModelNotFoundException $e) {
            $this->throwExceptionJson('Water source not found.', 404);
        } catch (Exception $e) {
            Log::error("Error finding water source: " . $e->getMessage());
            $this->throwExceptionJson('An unexpected error occurred while retrieving the water source.');
        }
    }

    /**
     * Create a new water source.
     *
     * @param array $data
     * @return WaterSource
     */
    public function store(array $data): WaterSource
    {
        try {
            return DB::transaction(function () use ($data) {
                $mediaRequestData = $this->extractMedia($data);

                if (isset($data['location']) && is_array($data['location'])) {
                    $data['location'] = new Point($data['location']['latitude'], $data['location']['longitude']);
                }

                $newWaterSource = WaterSource::create($data);

                event(new WaterSourceCreated($newWaterSource));

                if (!empty($mediaRequestData)) {
                    $this->attachMedia($newWaterSource, $mediaRequestData);
                }

                return $newWaterSource;
            });
        } catch (Exception $e) {
            Log::error("Error storing water source: " . $e->getMessage());
            $this->throwExceptionJson('Failed to create the water source. Please check the provided data.');
        }
    }

    /**
     * Update an existing water source.
     *
     * @param array $data
     * @param string|Model $modelOrId
     * @return WaterSource
     */
    public function update(array $data, string|Model $modelOrId): WaterSource
    {
        try {
            $waterSource = $this->get($modelOrId);
            $mediaRequestData = $this->extractMedia($data);

            return DB::transaction(function () use ($waterSource, $data, $mediaRequestData) {
                $waterSource->fill($data);

                if ($waterSource->isDirty()) {
                    if ($waterSource->isDirty('status')) {
                        Log::info("Status for water source {$waterSource->id} changed from {$waterSource->getOriginal('status')} to {$waterSource->status}");
                    }
                    $waterSource->save();
                }

                if (!empty($mediaRequestData)) {
                    $this->attachMedia($waterSource, $mediaRequestData);
                }

                return $waterSource->fresh('media');
            });
        } catch (HttpResponseException $e) {
            throw $e; // Re-throw the specific 404 exception from get()
        } catch (Exception $e) {
            Log::error("Error updating water source {$modelOrId}: " . $e->getMessage());
            $this->throwExceptionJson('Failed to update the water source.');
        }
    }

    /**
     * Delete a water source.
     *
     * @param string|Model $modelOrId
     * @return bool|null
     */
    public function destroy(string|Model $modelOrId): ?bool
    {
        try {
            $waterSource = $this->get($modelOrId);
            return $waterSource->delete();
        } catch (HttpResponseException $e) {
            throw $e;
        } catch (Exception $e) {
            Log::error("Error deleting water source {$modelOrId}: " . $e->getMessage());
            $this->throwExceptionJson('Failed to delete the water source.');
        }
    }

    /**
     * Add media files to a water source.
     *
     * @param array $mediaData
     * @param string|Model $modelOrId
     * @return WaterSource
     */
    public function addMedia(array $mediaData, string|Model $modelOrId): WaterSource
    {
        try {
            $waterSource = $this->get($modelOrId);
            $this->attachMedia($waterSource, $mediaData);
            return $waterSource->fresh('media');
        } catch (HttpResponseException $e) {
            throw $e;
        } catch (Exception $e) {
            Log::error("Error adding media to water source {$modelOrId}: " . $e->getMessage());
            $this->throwExceptionJson('Failed to add media to the water source.');
        }
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
                    if ($file) { // Ensure the file is not null or empty
                        $model->addMedia($file)->toMediaCollection($collectionName);
                    }
                }
            }
        }
    }
}

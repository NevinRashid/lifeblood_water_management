<?php

namespace Modules\WaterSources\Services;

use App\Services\Base\BaseService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Modules\WaterSources\Events\WaterExtracted;
use Modules\WaterSources\Models\WaterExtraction;

class WaterExtractionService extends BaseService
{
    public function __construct(WaterExtraction $model)
    {
        $this->model = $model;
    }

    public function query(array $filters = [])
    {
        return $this->handle(function () use ($filters) {

            $query = parent::query($filters);

            $query->when(isset($filters['water_source_id']), function (Builder $q) use ($filters) {
                $q->where('water_source_id', $filters['water_source_id']);
            });

            $query->when(isset($filters['start_date']), function (Builder $q) use ($filters) {
                $q->where('extraction_date', '>=', $filters['start_date']);
            });

            $query->when(isset($filters['end_date']), function (Builder $q) use ($filters) {
                $q->where('extraction_date', '<=', $filters['end_date']);
            });

            $query->when(isset($filters['min_extracted']), function (Builder $q) use ($filters) {
                $q->where('extracted', '>=', $filters['min_extracted']);
            });

            $query->when(isset($filters['max_extracted']), function (Builder $q) use ($filters) {
                $q->where('extracted', '<=', $filters['max_extracted']);
            });

            $sortBy = $filters['sort_by'] ?? 'extraction_date';

            $sortDirection = $filters['sort_direction'] ?? 'desc';

            return $query->orderBy($sortBy, $sortDirection);
        });
    }

    public function getAll(array $filters = [])
    {
        return $this->handle(
            function () use ($filters) {
                if (!$filters)
                    return Cache::remember('waterExtractions', now()->addDay(), function () {
                        return parent::getAll();
                    });

                return parent::getAll($filters);
            }
        );
    }

    public function store(array $data)
    {
        return $this->handle(function () use ($data) {
            $waterExtraction = parent::store($data);
            Cache::forget('waterExtractions');
            // Fire the event with the extraction and network ID
            event(new WaterExtracted($waterExtraction, $data['distribution_network_id']));
            return $waterExtraction;
        });
    }

    public function update(array $data, string|\Illuminate\Database\Eloquent\Model $modelOrId)
    {
        return $this->handle(function () use ($data, $modelOrId) {
            $updatedWaterExtraction = parent::update($data, $modelOrId);
            Cache::forget('waterExtractions');
            return $updatedWaterExtraction;
        });
    }

    public function destroy(string $id)
    {
        return $this->handle(function () use ($id,) {
            $deletedWaterExtraction = parent::destroy($id);
            Cache::forget('waterExtractions');
            return $deletedWaterExtraction;
        });
    }
}

<?php

namespace Modules\Beneficiaries\Services;

use App\Services\Base\BaseService;
use Illuminate\Support\Facades\Cache;
use Modules\Beneficiaries\Models\WaterQuota;

class WaterQuotaService extends BaseService
{
    public function __construct(WaterQuota $model)
    {
        $this->model = $model;
    }

    public function getAll(array $filters = [])
    {
        return $this->handle(
            function () use ($filters) {
                if (!$filters) {
                    return Cache::remember('waterQuotas', now()->addDay(), function () {
                        return parent::getAll();
                    });
                }

                return parent::getAll($filters);
            }
        );
    }

    public function store(array $data)
    {
        return $this->handle(function () use ($data) {
            $beneficiaries = parent::store($data);
            Cache::forget('waterQuotas');
            return $beneficiaries;
        });
    }

    public function update(array $data, string|\Illuminate\Database\Eloquent\Model $modelOrId)
    {
        return $this->handle(function () use ($data, $modelOrId) {
            $updatedBeneficiaries = parent::update($data, $modelOrId);
            Cache::forget('waterQuotas');
            return $updatedBeneficiaries;
        });
    }

    public function destroy(string $id)
    {
        return $this->handle(function () use ($id,) {
            $deletedBeneficiaries = parent::destroy($id);
            Cache::forget('waterQuotas');
            return $deletedBeneficiaries;
        });
    }

    public function query(array $filters = [])
    {
        $query = parent::query();

        $query->when(
            isset($filters['received_volume']),
            fn($q) =>
            $q->where('received_volume', $filters['received_volume'])
        )
            ->when(
                isset($filters['allocation_date']),
                fn($q) =>
                $q->whereDate('allocation_date', $filters['allocation_date'])
            )
            ->when(
                isset($filters['status']),
                fn($q) =>
                $q->whereDate('status', $filters['status'])
            );

        $sortBy = $filters['sort_by'] ?? 'allocation_date';

        $sortDirection = $filters['sort_direction'] ?? 'asc';

        return $query->orderBy($sortBy, $sortDirection);
    }
}

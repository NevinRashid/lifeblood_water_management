<?php

namespace Modules\Beneficiaries\Services;

use App\Services\Base\BaseService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Modules\Beneficiaries\Enums\BeneficiaryType;
use Modules\Beneficiaries\Models\Beneficiary;
use Modules\DistributionNetwork\Models\DistributionPoint;
use Modules\UsersAndTeams\Models\User;

class BeneficiaryService extends BaseService
{
    public function __construct(Beneficiary $model)
    {
        $this->model = $model;
    }


    public function getAll(array $filters = [])
    {
        return $this->handle(
            function () use ($filters) {
                if (!$filters) {
                    return Cache::remember('beneficiaries_' . app()->getLocale(), now()->addDay(), function () {
                        $beneficiaries = parent::getAll();
                        return $beneficiaries->through(function ($beneficiary) {
                            return [
                                ...$beneficiary->toArray(),
                                'address' => $beneficiary->localized_address,
                            ];
                        });
                    });
                }

                return parent::getAll($filters);
            }
        );
    }

    public function store(array $data)
    {
        return $this->handle(function () use ($data) {
            $distribution_point_type= DistributionPoint::find($data['distribution_point_id'])?->type;
            if($distribution_point_type === 'tanker'){
                $data['benefit_type'] = BeneficiaryType::TANKER;
            }
            elseif ($distribution_point_type === 'water tap'){
                $data['benefit_type'] = BeneficiaryType::NETWORK;
            }

            $user = User::find($data['user_id']);
            if($user && !$user->hasRole('Affected Community Member')){
                $user->assignRole('Affected Community Member');
            }
            $beneficiaries = parent::store($data);
            foreach (config('translatable.locales') as $locale) {
                Cache::forget("beneficiaries_{$locale}");
            }
            return $beneficiaries;
        });
    }

    public function update(array $data, string|\Illuminate\Database\Eloquent\Model $modelOrId)
    {
        return $this->handle(function () use ($data, $modelOrId) {
            $updatedBeneficiaries = parent::update($data, $modelOrId);
            foreach (config('translatable.locales') as $locale) {
                Cache::forget("beneficiaries_{$locale}");
            }
            return $updatedBeneficiaries;
        });
    }

    public function destroy(string $id)
    {
        return $this->handle(function () use ($id,) {
            $deletedBeneficiaries = parent::destroy($id);
            foreach (config('translatable.locales') as $locale) {
                Cache::forget("beneficiaries_{$locale}");
            }
            return $deletedBeneficiaries;
        });
    }


    public function query(array $filters = [])
    {

        $query = parent::query($filters);

        $query->when(
            isset($filters['distribution_point_id']),
            fn(Builder $q) =>
            $q->where('distribution_point_id', $filters['distribution_point_id'])
        )
            ->when(
                isset($filters['user_id']),
                fn(Builder $q) =>
                $q->where('user_id', $filters['user_id'])
            )
            ->when(
                isset($filters['benefit_type']),
                fn(Builder $q) =>
                $q->where('benefit_type', $filters['benefit_type'])
            )
            ->when(
                isset($filters['status']),
                fn(Builder $q) =>
                $q->where('status', $filters['status'])
            )
            ->when(
                isset($filters['address']),
                fn(Builder $q) =>
                $q->where('address', 'like', '%' . $filters['address'] . '%')
            )
            ->when(
                isset($filters['household_size']),
                fn(Builder $q) =>
                $q->where('household_size', '=', $filters['household_size'])
            )
            ->when(
                isset($filters['has_children']) && $filters['has_children'],
                fn(Builder $q) =>
                $q->where('children_count', '>', 0)
            )
            ->when(
                isset($filters['has_elderly']) && $filters['has_elderly'],
                fn(Builder $q) =>
                $q->where('elderly_count', '>', 0)
            )
            ->when(
                isset($filters['has_disabled']) && $filters['has_disabled'],
                fn(Builder $q) =>
                $q->where('disabled_count', '>', 0)
            );

        $sortBy = $filters['sort_by'] ?? 'household_size';

        $sortDirection = $filters['sort_direction'] ?? 'asc';

        return $query->orderBy($sortBy, $sortDirection);
    }
}

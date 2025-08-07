<?php

namespace Modules\Beneficiaries\Services;

use App\Services\Base\BaseService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Modules\Beneficiaries\Enums\BeneficiaryType;
use Modules\Beneficiaries\Models\Beneficiary;
use Modules\DistributionNetwork\Models\DistributionPoint;
use Modules\UsersAndTeams\Models\User;

/**
 * Handles all business logic for Beneficiaries
 *
 * This service manages CRUD operations for beneficiaries and includes
 * a caching layer to speed up read operations, with logic to automatically
 * clear the cache when data changes
 */
class BeneficiaryService extends BaseService
{
    /**
     * BeneficiaryService constructor
     *
     * @param \Modules\Beneficiaries\Models\Beneficiary $model The Beneficiary model instance
     */
    public function __construct(Beneficiary $model)
    {
        $this->model = $model;
    }

    /**
     * Get all beneficiaries, with caching for unfiltered requests
     *
     * If no filters are provided, this method will cache the full list of
     * beneficiaries for one day to improve performance
     * The cache is language-specific
     *
     * @param array $filters Filters to apply to the query
     * @return mixed The result of the handle method, usually an array of beneficiaries
     */
    public function getAll(array $filters = [])
    {
        return $this->handle(
            function () use ($filters) {

                // If no filters are present, we can use the cache
                if (!$filters) {

                    return Cache::remember('beneficiaries_' . app()->getLocale(), now()->addDay(), function () {
                        // If not in cache, fetch from DB and convert to array before caching
                        return parent::getAll()->toArray();
                    });
                }

                // If filters are present, bypass the cache and query the DB directly
                return parent::getAll($filters)->toArray();
            }
        );
    }

    /**
     * Create a new beneficiary and clear the cache
     *
     * @param array $data The data for the new beneficiary
     * @return mixed The newly created beneficiary model
     */
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
            $beneficiary = parent::store($data);
            foreach (config('translatable.locales') as $locale) {
                Cache::forget("beneficiaries_{$locale}");
            }

            return $beneficiary;
        });
    }

    /**
     * Update a beneficiary and clear the cache
     *
     * @param array $data The new data for the beneficiary
     * @param string|\Illuminate\Database\Eloquent\Model $modelOrId The model or ID to update
     * @return mixed The updated beneficiary model
     */
    public function update(array $data, string|\Illuminate\Database\Eloquent\Model $modelOrId)
    {
        return $this->handle(function () use ($data, $modelOrId) {
            $updatedBeneficiary = parent::update($data, $modelOrId);

            // A record was changed, so invalidate the cache for all languages
            foreach (config('translatable.locales') as $locale) {
                Cache::forget("beneficiaries_{$locale}");
            }

            return $updatedBeneficiary;
        });
    }

    /**
     * Delete a beneficiary and clear the cache
     *
     * @param string $id The ID of the beneficiary to delete
     * @return mixed The result of the delete operation
     */
    public function destroy(string $id)
    {
        return $this->handle(function () use ($id) {
            $deletedResult = parent::destroy($id);

            // A record was removed, so invalidate the cache for all languages
            foreach (config('translatable.locales') as $locale) {
                Cache::forget("beneficiaries_{$locale}");
            }

            return $deletedResult;
        });
    }

    /**
     * Build a query for fetching beneficiaries with advanced filtering and sorting
     *
     * @param array $filters An array of filters to apply to the query
     * @option int    $distribution_point_id Filter by distribution point
     * @option int    $user_id Filter by the associated user
     * @option string $benefit_type Filter by the type of benefit received
     * @option string $status Filter by beneficiary status
     * @option string $address Search within the address field
     * @option int    $household_size Filter by the exact number of people in the household
     * @option bool   $has_children Filter for beneficiaries with one or more children
     * @option bool   $has_elderly Filter for beneficiaries with one or more elderly members
     * @option bool   $has_disabled Filter for beneficiaries with one or more disabled members
     * @option string $sort_by Column to sort by (defaults to 'household_size')
     * @option string $sort_direction Sorting direction (defaults to 'asc')
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(array $filters = [])
    {
        // Start with the base query builder
        $query = parent::query($filters);

        // Chain conditional filters based on what's provided
        $query->when(
            isset($filters['distribution_point_id']),
            fn (Builder $q) =>
            $q->where('distribution_point_id', $filters['distribution_point_id'])
        )
            ->when(
                isset($filters['user_id']),
                fn (Builder $q) =>
                $q->where('user_id', $filters['user_id'])
            )
            ->when(
                isset($filters['benefit_type']),
                fn (Builder $q) =>
                $q->where('benefit_type', $filters['benefit_type'])
            )
            ->when(
                isset($filters['status']),
                fn (Builder $q) =>
                $q->where('status', $filters['status'])
            )
            ->when(
                isset($filters['address']),
                fn (Builder $q) =>
                $q->where('address', 'like', '%' . $filters['address'] . '%')
            )
            ->when(
                isset($filters['household_size']),
                fn (Builder $q) =>
                $q->where('household_size', '=', $filters['household_size'])
            )
            ->when(
                isset($filters['has_children']) && $filters['has_children'],
                fn (Builder $q) =>
                $q->where('children_count', '>', 0)
            )
            ->when(
                isset($filters['has_elderly']) && $filters['has_elderly'],
                fn (Builder $q) =>
                $q->where('elderly_count', '>', 0)
            )
            ->when(
                isset($filters['has_disabled']) && $filters['has_disabled'],
                fn (Builder $q) =>
                $q->where('disabled_count', '>', 0)
            );

        // Set default sorting if not provided in filters
        $sortBy = $filters['sort_by'] ?? 'household_size';
        $sortDirection = $filters['sort_direction'] ?? 'asc';

        return $query->orderBy($sortBy, $sortDirection);
    }
}

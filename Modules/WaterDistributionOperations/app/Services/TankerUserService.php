<?php

namespace Modules\WaterDistributionOperations\Services;

use App\Services\Base\BaseService;
use Illuminate\Support\Facades\DB;
use Modules\UsersAndTeams\Models\User;
use Illuminate\Database\QueryException;
use Modules\WaterDistributionOperations\Models\Tanker;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TankerUserService extends BaseService
{
    /**
     *
     * @param \Modules\WaterDistributionOperations\Models\Tanker $tanker
     * @param int $userId
     * @return Tanker|null
     */
    public function assignUserToTanker(Tanker $tanker, int $userId): ?Tanker
    {
        try {
            $result = $tanker->users()->syncWithoutDetaching($userId);

            if (!empty($result['attached'])) {
                return $tanker->fresh()->load('users');
            }

            return null;

        } catch (QueryException $e) {
            $this->throwExceptionJson('Failed to assign user. Please check if the user exists.', 500);
        }
    }
    /**
     *
     * @param \Modules\WaterDistributionOperations\Models\Tanker $tanker
     * @param \Modules\UsersAndTeams\Models\User $user
     * @return bool
     */
    public function unassignUserFromTanker(Tanker $tanker, User $user): bool
    {
        try {
            $detachedCount = $tanker->users()->detach($user->id);

            return $detachedCount > 0;

        } catch (QueryException $e) {
            $this->throwExceptionJson('Failed to unassign user. Please try again.', 500);
        }
    }
    /**
     * 
     * @param \Modules\WaterDistributionOperations\Models\Tanker $tanker
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator<int, object|object{pivot: TPivotModel|TRelatedModel>}
     */
    public function getAssignedUsers(Tanker $tanker, int $perPage = 15): LengthAwarePaginator
    {
        try {
            return $tanker->users()->paginate($perPage);

        } catch (QueryException $e) {
            $this->throwExceptionJson('Failed to fetch assigned users.', 500);
        }
    }
}



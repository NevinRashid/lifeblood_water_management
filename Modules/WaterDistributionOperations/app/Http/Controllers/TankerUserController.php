<?php

namespace Modules\WaterDistributionOperations\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Modules\UsersAndTeams\Models\User;
use Illuminate\Routing\Controllers\Middleware;
use Modules\WaterDistributionOperations\Models\Tanker;
use Modules\WaterDistributionOperations\Services\TankerUserService;
use Modules\WaterDistributionOperations\Http\Requests\TankersUser\AssignUserToTankerRequest;

class TankerUserController extends Controller
{
    /**
     *
     * @param \Modules\WaterDistributionOperations\Services\TankerUserService $tankerUserService
     */
    public function __construct(protected TankerUserService $tankerUserService)
    {
    }
    

     /**
      *
      * @return Middleware[]
      */
     public static function middleware(): array
    {
        return [
            new Middleware('permission:assign user to tanker', only: ['store']),
            new Middleware('permission:unassign user from tanker', only: ['destroy']),
            new Middleware('permission:view tanker assignments', only: ['index']),
        ];
    }
    /**
     *
     * @param \Illuminate\Http\Request $request
     * @param \Modules\WaterDistributionOperations\Models\Tanker $tanker
     * @return JsonResponse
     */
    public function index(Request $request, Tanker $tanker): JsonResponse
    {
        $users = $this->tankerUserService->getAssignedUsers($tanker, $request->get('per_page', 15));
        return response()->json($users);
    }

    /**
     *
     * @param \Modules\WaterDistributionOperations\Http\Requests\TankersUser\AssignUserToTankerRequest $request
     * @param \Modules\WaterDistributionOperations\Models\Tanker $tanker
     * @return JsonResponse
     */
    public function store(AssignUserToTankerRequest $request, Tanker $tanker): JsonResponse
{
    $assignedTanker = $this->tankerUserService->assignUserToTanker(
        $tanker,
        $request->validated('user_id')
    );

    $message = $assignedTanker
        ? 'User assigned successfully.'
        : 'User was already assigned to this tanker.';

    return response()->json([
        'message' => $message,
        'data' => $assignedTanker
    ]);
}

   /**
    *
    * @param \Modules\WaterDistributionOperations\Models\Tanker $tanker
    * @param \Modules\UsersAndTeams\Models\User $user
    * @return JsonResponse
    */
   public function destroy(Tanker $tanker, User $user): JsonResponse
{
    $success = $this->tankerUserService->unassignUserFromTanker($tanker, $user);

    $message = $success
        ? 'User unassigned successfully.'
        : 'User was not assigned to this tanker.';

    return response()->json(['message' => $message]);
}

}

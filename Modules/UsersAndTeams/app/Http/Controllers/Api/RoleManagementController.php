<?php
namespace Modules\UsersAndTeams\Http\Controllers\Api;

use Illuminate\Http\Request;

use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Modules\UsersAndTeams\Models\User;
use Illuminate\Routing\Controllers\Middleware;
use Modules\UsersAndTeams\Services\RoleManagementService;
use Modules\UsersAndTeams\Http\Requests\Admin\UserRoleRequest;

class RoleManagementController extends Controller
{
    protected RoleManagementService $roleService;

    public function __construct(RoleManagementService $roleService)
    {
        $this->roleService = $roleService;
    }
     /**
         * Define the middleware for this controller.
         *
         * @return array
         */
        public static function middleware(): array
        {
            return [
                new Middleware('permission:manage User Role', only: ['index', 'assign', 'update','revoke']),
            ];
        }

    /**
     * Get a list of all available roles.
     */
    public function index(): JsonResponse
    {
        $roles = $this->roleService->getAllRoles();
        return response()->json($roles);
    }

    /**
     * Assign a role to a specific user.
     */
    public function assign(UserRoleRequest $request, User $user): JsonResponse
    {
        $updatedUser = $this->roleService->assignRoleToUser($user, $request->validated()['role']);
        return response()->json([
            'message' => "Role '{$request->role}' assigned successfully.",
            'user' => $updatedUser,
        ]);
    }

     public function update(UserRoleRequest $request, User $user): JsonResponse
    {


            $updatedUser = $this->roleService->updateUserRole($user, $request->validated()['role']);

            return response()->json([
                'message' => "User's role has been updated to '{$request->role}'.",
                'user' => $updatedUser,
            ]);

    }

    /**
     * Revoke a role from a specific user.
     */
    public function revoke(UserRoleRequest $request, User $user): JsonResponse
    {



            $updatedUser = $this->roleService->revokeRoleFromUser($user, $request->validated()['role']);
            return response()->json([
                'message' => "Role '{$request->role}' revoked successfully.",
                'user' => $updatedUser,
            ]);

    }
}

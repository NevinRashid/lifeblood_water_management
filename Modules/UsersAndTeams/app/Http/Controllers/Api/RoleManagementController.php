<?php
namespace Modules\UsersAndTeams\Http\Controllers\Api;

use Illuminate\Http\Request;

use Illuminate\Http\Response;
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
        return $this->successResponse('Roles retrieved successfully.', $roles);
    }

    /**
     * Assign a role to a specific user.
     */
    public function assign(UserRoleRequest $request, User $user): JsonResponse
    {
        $validatedData = $request->validated();
        $roleName = $validatedData['role'];

        $updatedUser = $this->roleService->assignRoleToUser($user, $roleName);
     if (!$updatedUser) {
            return $this->errorResponse("User already has the '{$roleName}' role.", null, Response::HTTP_CONFLICT); // 409
        }

        return $this->successResponse(
            "Role '{$roleName}' assigned to user successfully.",
            $updatedUser
        );
    }

     public function update(UserRoleRequest $request, User $user): JsonResponse
    {
                $validatedData = $request->validated();
                $roleName = $validatedData['role'];
                    $updatedUser = $this->roleService->updateUserRole($user, $roleName);
                    return $this->successResponse(
                    "User's role has been updated to '{$roleName}'.",
                    $updatedUser
                );

    }

    /**
     * Revoke a role from a specific user.
     */
    public function revoke(UserRoleRequest $request, User $user): JsonResponse
    {

            $validatedData = $request->validated();
            $roleName = $validatedData['role'];
                $updatedUser = $this->roleService->revokeRoleFromUser($user, $request->validated()['role']);
                if (!$updatedUser) {
                return $this->errorResponse("User does not have the '{$roleName}' role to revoke.", null, Response::HTTP_NOT_FOUND); // 404
            }
            return $this->successResponse(
                "Role '{$roleName}' revoked from user successfully.",
                $updatedUser
            );


    }
}

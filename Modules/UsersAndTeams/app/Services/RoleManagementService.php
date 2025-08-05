<?php

namespace Modules\UsersAndTeams\Services;


use Exception;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use Modules\UsersAndTeams\Models\User;
use Illuminate\Database\Eloquent\Collection;

class RoleManagementService
{
    /**
     * Get a list of all available roles.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllRoles(): Collection
    {
        return Role::all();
    }

 public function updateUserRole(User $user, string $newRoleName): User
    {
        try {
            // syncRoles is the perfect method for this.
            // It accepts a role name, an ID, a Role model, or an array of them.
            $user->syncRoles([$newRoleName]);

            Log::info("User {$user->id}'s role synchronized to '{$newRoleName}'");

            // Return the user with their roles reloaded to confirm the change.
            return $user->load('roles');

        } catch (Exception $e) {
            // This will catch 'RoleDoesNotExist' or other database issues.
            Log::error("Failed to update role for user {$user->id} to '{$newRoleName}'", [
                'error' => $e->getMessage()
            ]);
            // Re-throw to be handled by the controller
            throw $e;
        }
    }

    public function assignRoleToUser(User $user, string $roleName): User
    {
        try {
            // The assignRole method is smart enough not to add the role if it already exists.
            $user->assignRole($roleName);
            Log::info("Role '{$roleName}' assigned to user {$user->id}");

            // Return the user with their roles reloaded.
            return $user->load('roles');

        } catch (Exception $e) {
            Log::error("Failed to assign role '{$roleName}' to user {$user->id}", [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
    

    public function revokeRoleFromUser(User $user, string $roleName): User
    {
        try {
            // Check if the user actually has this role before trying to remove it.
            if ($user->hasRole($roleName)) {
                $user->removeRole($roleName);
                Log::info("Role '{$roleName}' revoked from user {$user->id}");
            }

            // Return the user with their roles reloaded.
            return $user->load('roles');

        } catch (Exception $e) {
            Log::error("Failed to revoke role '{$roleName}' from user {$user->id}", [
                'error' => $e->getMessage()
            ]);
            // Re-throw the exception so the controller can handle it.
            throw $e;
        }
    }
}

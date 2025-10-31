<?php

namespace Modules\User\Policies;

use Modules\User\Enums\{Permission, UserType};
use Modules\User\Models\User;

/**
 * Policy for User resource authorization.
 * Centralizes all authorization logic for user-related actions.
 */
class UserPolicy
{
    /**
     * Determine if the user can view any users.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission(Permission::ViewUsers);
    }

    /**
     * Determine if the user can view a specific user.
     */
    public function view(User $user, User $model): bool
    {
        // Users can view their own profile
        if ($user->id === $model->id) {
            return true;
        }

        // Otherwise, need ViewUsers permission
        return $user->hasPermission(Permission::ViewUsers);
    }

    /**
     * Determine if the user can create users.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission(Permission::CreateUsers);
    }

    /**
     * Determine if the user can create a user of a specific type.
     */
    public function createUserType(User $user, UserType $userType): bool
    {
        // Must have basic create permission
        if (!$user->hasPermission(Permission::CreateUsers)) {
            return false;
        }

        // Super Admin creation requires special permission
        if ($userType === UserType::SuperAdmin) {
            return $user->hasPermission(Permission::CreateSuperAdmin);
        }

        // Admin and Driver creation requires admin user permission
        if (in_array($userType, [UserType::Admin, UserType::Driver])) {
            return $user->hasPermission(Permission::CreateAdminUsers);
        }

        // Student can be created by anyone with CreateUsers permission
        return true;
    }

    /**
     * Determine if the user can update another user.
     */
    public function update(User $user, User $model): bool
    {
        // Users can update their own profile
        if ($user->id === $model->id) {
            return true;
        }

        // Otherwise, need UpdateUsers permission
        return $user->hasPermission(Permission::UpdateUsers);
    }

    /**
     * Determine if the user can update another user's type.
     */
    public function updateUserType(User $user, User $model, UserType $newType): bool
    {
        // Cannot change your own type
        if ($user->id === $model->id) {
            return false;
        }

        // Need UpdateUserType permission
        if (!$user->hasPermission(Permission::UpdateUserType)) {
            return false;
        }

        // Cannot change a Super Admin's type
        if ($model->user_type === UserType::SuperAdmin) {
            return false;
        }

        // Cannot promote to Super Admin without special permission
        if ($newType === UserType::SuperAdmin && !$user->hasPermission(Permission::CreateSuperAdmin)) {
            return false;
        }

        return true;
    }

    /**
     * Determine if the user can delete another user.
     */
    public function delete(User $user, User $model): bool
    {
        // Cannot delete yourself
        if ($user->id === $model->id) {
            return false;
        }

        // Cannot delete a Super Admin
        if ($model->user_type === UserType::SuperAdmin) {
            return false;
        }

        return $user->hasPermission(Permission::DeleteUsers);
    }

    /**
     * Determine if the user can restore a deleted user.
     */
    public function restore(User $user, User $model): bool
    {
        return $user->hasPermission(Permission::UpdateUsers);
    }

    /**
     * Determine if the user can permanently delete a user.
     */
    public function forceDelete(User $user, User $model): bool
    {
        // Cannot permanently delete a Super Admin
        if ($model->user_type === UserType::SuperAdmin) {
            return false;
        }

        return $user->isSuperAdmin();
    }
}

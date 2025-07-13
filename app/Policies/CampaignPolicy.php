<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\User;

final class CampaignPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, [
            UserRole::USER,
            UserRole::ADMIN,
            UserRole::SUPER_ADMIN,
        ]);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user): bool
    {
        return in_array($user->role, [
            UserRole::USER,
            UserRole::ADMIN,
            UserRole::SUPER_ADMIN,
        ]);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, [
            UserRole::ADMIN,
            UserRole::SUPER_ADMIN,
        ]);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user): bool
    {
        return in_array($user->role, [
            UserRole::ADMIN,
            UserRole::SUPER_ADMIN,
        ]);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user): bool
    {
        return in_array($user->role, [
            UserRole::ADMIN,
            UserRole::SUPER_ADMIN,
        ]);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user): bool
    {
        return in_array($user->role, [
            UserRole::ADMIN,
            UserRole::SUPER_ADMIN,
        ]);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user): bool
    {
        return $user->role === UserRole::SUPER_ADMIN;
    }

    /**
     * Determine whether the user can send emails for the model.
     */
    public function sendEmails(User $user): bool
    {
        return in_array($user->role, [
            UserRole::ADMIN,
            UserRole::SUPER_ADMIN,
        ]);
    }
}

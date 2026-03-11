<?php

namespace App\Policies;

use App\Models\User;
use App\Models\FundSourceGroup;
use Illuminate\Auth\Access\HandlesAuthorization;

class FundSourceGroupPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_fund::source::group');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, FundSourceGroup $fundSourceGroup): bool
    {
        return $user->can('view_fund::source::group');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_fund::source::group');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, FundSourceGroup $fundSourceGroup): bool
    {
        return $user->can('update_fund::source::group');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, FundSourceGroup $fundSourceGroup): bool
    {
        return $user->can('delete_fund::source::group');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_fund::source::group');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, FundSourceGroup $fundSourceGroup): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, FundSourceGroup $fundSourceGroup): bool
    {
        return false;
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, FundSourceGroup $fundSourceGroup): bool
    {
        return false;
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return false;
    }
}

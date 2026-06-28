<?php

namespace App\Policies;

use App\Models\PersonalTask;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class PersonalTaskPolicy
{
        use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
        public function viewNotifications(User $user, PersonalTask $personalTask)
        {
            // Retrieve the employee associated with the user
            $employee = $user->employee;

            if (!$employee) {
                return false;
            }

            // Check if the employee is associated with the PersonalTask
            return $personalTask->employees->contains($employee);
        }
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PersonalTask $personalTask): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PersonalTask $personalTask): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PersonalTask $personalTask): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, PersonalTask $personalTask): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, PersonalTask $personalTask): bool
    {
        return false;
    }
}

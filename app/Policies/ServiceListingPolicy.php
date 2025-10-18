<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ServiceListing;
use App\Models\User;

class ServiceListingPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->role === 'student';
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ServiceListing $serviceListing): bool
    {
        return $user->id === $serviceListing->student_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role === 'student';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ServiceListing $serviceListing): bool
    {
        return $user->id === $serviceListing->student_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ServiceListing $serviceListing): bool
    {
        return $user->id === $serviceListing->student_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ServiceListing $serviceListing): bool
    {
        return $user->id === $serviceListing->student_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ServiceListing $serviceListing): bool
    {
        return $user->id === $serviceListing->student_id;
    }
}

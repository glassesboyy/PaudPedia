<?php

namespace App\Policies;

use App\Models\Mentor;
use App\Models\User;
use App\Services\Content\MentorService;

class MentorPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['admin', 'moderator']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Mentor $mentor): bool
    {
        return $user->hasRole(['admin', 'moderator']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(['admin', 'moderator']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Mentor $mentor): bool
    {
        return $user->hasRole(['admin', 'moderator']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Mentor $mentor): bool
    {
        if (!$user->hasRole(['admin', 'moderator'])) {
            return false;
        }

        // Check if mentor can be deleted (not connected to any webinar/course)
        $service = app(MentorService::class);
        return $service->canBeDeleted($mentor);
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->hasRole(['admin', 'moderator']);
    }
}

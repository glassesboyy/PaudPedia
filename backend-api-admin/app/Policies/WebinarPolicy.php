<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Webinar;
use Illuminate\Auth\Access\HandlesAuthorization;

class WebinarPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any webinars.
     */
    public function viewAny(User $user): bool
    {
        // Admin dan Moderator dapat melihat semua webinar
        return $user->hasRole(['admin', 'moderator']);
    }

    /**
     * Determine whether the user can view the webinar.
     */
    public function view(User $user, Webinar $webinar): bool
    {
        // Admin dan Moderator dapat melihat detail webinar
        return $user->hasRole(['admin', 'moderator']);
    }

    /**
     * Determine whether the user can create webinars.
     */
    public function create(User $user): bool
    {
        // Admin dan Moderator dapat membuat webinar
        return $user->hasRole(['admin', 'moderator']);
    }

    /**
     * Determine whether the user can update the webinar.
     */
    public function update(User $user, Webinar $webinar): bool
    {
        // Admin dan Moderator dapat mengupdate webinar
        return $user->hasRole(['admin', 'moderator']);
    }

    /**
     * Determine whether the user can delete the webinar.
     */
    public function delete(User $user, Webinar $webinar): bool
    {
        // Admin dan Moderator dapat menghapus webinar
        return $user->hasRole(['admin', 'moderator']);
    }

    /**
     * Determine whether the user can restore the webinar.
     */
    public function restore(User $user, Webinar $webinar): bool
    {
        // Admin dan Moderator dapat restore webinar yang dihapus
        return $user->hasRole(['admin', 'moderator']);
    }

    /**
     * Determine whether the user can permanently delete the webinar.
     */
    public function forceDelete(User $user, Webinar $webinar): bool
    {
        // Hanya Admin yang dapat permanently delete
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can bulk delete webinars.
     */
    public function deleteAny(User $user): bool
    {
        // Admin dan Moderator dapat bulk delete
        return $user->hasRole(['admin', 'moderator']);
    }
}

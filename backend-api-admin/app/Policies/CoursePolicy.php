<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CoursePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any courses.
     */
    public function viewAny(User $user): bool
    {
        // Admin dan Moderator dapat melihat semua kursus
        return $user->hasRole(['admin', 'moderator']);
    }

    /**
     * Determine whether the user can view the course.
     */
    public function view(User $user, Course $course): bool
    {
        // Admin dan Moderator dapat melihat detail kursus
        return $user->hasRole(['admin', 'moderator']);
    }

    /**
     * Determine whether the user can create courses.
     */
    public function create(User $user): bool
    {
        // Admin dan Moderator dapat membuat kursus
        return $user->hasRole(['admin', 'moderator']);
    }

    /**
     * Determine whether the user can update the course.
     */
    public function update(User $user, Course $course): bool
    {
        // Admin dan Moderator dapat mengupdate kursus
        return $user->hasRole(['admin', 'moderator']);
    }

    /**
     * Determine whether the user can delete the course.
     */
    public function delete(User $user, Course $course): bool
    {
        // Admin dan Moderator dapat menghapus kursus (soft delete)
        return $user->hasRole(['admin', 'moderator']);
    }

    /**
     * Determine whether the user can restore the course.
     */
    public function restore(User $user, Course $course): bool
    {
        // Admin dan Moderator dapat restore kursus yang dihapus
        return $user->hasRole(['admin', 'moderator']);
    }

    /**
     * Determine whether the user can permanently delete the course.
     */
    public function forceDelete(User $user, Course $course): bool
    {
        // Admin dan Moderator dapat permanently delete
        return $user->hasRole(['admin', 'moderator']);
    }

    /**
     * Determine whether the user can bulk delete courses.
     */
    public function deleteAny(User $user): bool
    {
        // Admin dan Moderator dapat bulk delete
        return $user->hasRole(['admin', 'moderator']);
    }
}

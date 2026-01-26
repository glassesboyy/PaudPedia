<?php

namespace App\Services\Content;

use App\Models\Mentor;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class MentorService
{
    /**
     * Helper method to count relations including soft deleted if supported
     *
     * @param HasMany $relation
     * @return int
     */
    private function countWithTrashedIfSupported(HasMany $relation): int
    {
        $model = $relation->getRelated();
        
        // Check if model uses SoftDeletes trait
        if (in_array(SoftDeletes::class, class_uses_recursive($model))) {
            return $relation->withTrashed()->count();
        }
        
        return $relation->count();
    }

    /**
     * Check if mentor can be deleted
     * Mentor cannot be deleted if they have any webinars or courses (including soft deleted)
     *
     * @param Mentor $mentor
     * @return bool
     */
    public function canBeDeleted(Mentor $mentor): bool
    {
        $totalWebinars = $this->countWithTrashedIfSupported($mentor->webinars());
        $totalCourses = $this->countWithTrashedIfSupported($mentor->courses());

        return $totalWebinars === 0 && $totalCourses === 0;
    }

    /**
     * Check if mentor can be deactivated
     * Mentor cannot be deactivated if they have any webinars or courses (including soft deleted)
     *
     * @param Mentor $mentor
     * @return bool
     */
    public function canBeDeactivated(Mentor $mentor): bool
    {
        // If mentor is already inactive, they can be activated without restriction
        if (!$mentor->is_active) {
            return true;
        }

        // If trying to deactivate, check for relations
        $totalWebinars = $this->countWithTrashedIfSupported($mentor->webinars());
        $totalCourses = $this->countWithTrashedIfSupported($mentor->courses());

        return $totalWebinars === 0 && $totalCourses === 0;
    }

    /**
     * Get detailed message about related content
     *
     * @param Mentor $mentor
     * @param string $action 'delete' or 'deactivate'
     * @return string
     */
    public function getRelatedContentMessage(Mentor $mentor, string $action = 'delete'): string
    {
        $totalWebinars = $this->countWithTrashedIfSupported($mentor->webinars());
        $totalCourses = $this->countWithTrashedIfSupported($mentor->courses());

        $parts = [];
        
        if ($totalWebinars > 0) {
            $parts[] = "{$totalWebinars} webinar";
        }
        
        if ($totalCourses > 0) {
            $parts[] = "{$totalCourses} kursus";
        }

        $content = implode(' dan ', $parts);
        
        if ($action === 'delete') {
            return "Mentor tidak dapat dihapus karena masih terhubung dengan {$content}.";
        }
        
        return "Mentor tidak dapat dinonaktifkan karena masih terhubung dengan {$content}.";
    }

    /**
     * Toggle mentor active status
     *
     * @param Mentor $mentor
     * @return Mentor
     * @throws \Exception
     */
    public function toggleActiveStatus(Mentor $mentor): Mentor
    {
        DB::beginTransaction();
        try {
            // If trying to deactivate, check if allowed
            if ($mentor->is_active && !$this->canBeDeactivated($mentor)) {
                throw new \Exception($this->getRelatedContentMessage($mentor, 'deactivate'));
            }

            $mentor->is_active = !$mentor->is_active;
            $mentor->save();

            DB::commit();

            return $mentor->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete mentor with validation
     *
     * @param Mentor $mentor
     * @return bool
     * @throws \Exception
     */
    public function deleteMentor(Mentor $mentor): bool
    {
        if (!$this->canBeDeleted($mentor)) {
            throw new \Exception($this->getRelatedContentMessage($mentor, 'delete'));
        }

        DB::beginTransaction();
        try {
            $mentor->delete();
            
            DB::commit();
            
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get total related content count
     *
     * @param Mentor $mentor
     * @return array
     */
    public function getRelatedContentCount(Mentor $mentor): array
    {
        $totalWebinars = $this->countWithTrashedIfSupported($mentor->webinars());
        $totalCourses = $this->countWithTrashedIfSupported($mentor->courses());
        
        return [
            'webinars' => $totalWebinars,
            'courses' => $totalCourses,
            'total' => $totalWebinars + $totalCourses,
        ];
    }
}

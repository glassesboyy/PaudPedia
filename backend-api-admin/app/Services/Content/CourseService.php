<?php

namespace App\Services\Content;

use App\Models\Course;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CourseService
{
    /**
     * Create a new course with modules and lessons
     *
     * @param array $data
     * @return Course
     * @throws \Exception
     */
    public function createCourse(array $data): Course
    {
        DB::beginTransaction();
        try {
            // Generate slug if not provided
            if (!isset($data['slug']) || empty($data['slug'])) {
                $data['slug'] = $this->generateUniqueSlug($data['title']);
            }

            // Extract modules data before creating course
            $modulesData = $data['modules'] ?? [];
            unset($data['modules']);

            // Create course
            $course = Course::create($data);

            // Create modules and lessons
            foreach ($modulesData as $moduleIndex => $moduleData) {
                $lessonsData = $moduleData['lessons'] ?? [];
                unset($moduleData['lessons']);

                $moduleData['order'] = $moduleData['order'] ?? $moduleIndex;
                $module = $course->modules()->create($moduleData);

                foreach ($lessonsData as $lessonIndex => $lessonData) {
                    $lessonData['order'] = $lessonData['order'] ?? $lessonIndex;
                    $module->lessons()->create($lessonData);
                }
            }

            DB::commit();

            return $course->load(['mentor', 'category', 'modules.lessons']);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update an existing course
     *
     * @param Course $course
     * @param array $data
     * @return Course
     * @throws \Exception
     */
    public function updateCourse(Course $course, array $data): Course
    {
        DB::beginTransaction();
        try {
            // Update slug if title changed
            if (isset($data['title']) && $data['title'] !== $course->title) {
                $data['slug'] = $this->generateUniqueSlug($data['title'], $course->id);
            }

            // Extract modules data
            $modulesData = $data['modules'] ?? null;
            unset($data['modules']);

            // Update course
            $course->update($data);

            // Update modules and lessons if provided
            if ($modulesData !== null) {
                $this->syncModulesAndLessons($course, $modulesData);
            }

            DB::commit();

            return $course->fresh(['mentor', 'category', 'modules.lessons']);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete a course (soft delete)
     *
     * @param Course $course
     * @return bool
     * @throws \Exception
     */
    public function deleteCourse(Course $course): bool
    {
        DB::beginTransaction();
        try {
            $deleted = $course->delete();

            DB::commit();

            return $deleted;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Bulk delete courses
     *
     * @param array $ids
     * @return int Number of deleted records
     * @throws \Exception
     */
    public function bulkDeleteCourses(array $ids): int
    {
        DB::beginTransaction();
        try {
            $count = Course::whereIn('id', $ids)->delete();

            DB::commit();

            return $count;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Restore a soft-deleted course
     *
     * @param int $id
     * @return Course|null
     * @throws \Exception
     */
    public function restoreCourse(int $id): ?Course
    {
        DB::beginTransaction();
        try {
            $course = Course::withTrashed()->find($id);

            if ($course && $course->trashed()) {
                $course->restore();
            }

            DB::commit();

            return $course?->load(['mentor', 'category', 'modules.lessons']);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Force delete a course (permanent)
     *
     * @param Course $course
     * @return bool
     * @throws \Exception
     */
    public function forceDeleteCourse(Course $course): bool
    {
        DB::beginTransaction();
        try {
            // Delete all modules and lessons (cascade handled by DB constraints)
            $deleted = $course->forceDelete();

            DB::commit();

            return $deleted;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Toggle course publish status
     *
     * @param Course $course
     * @return Course
     */
    public function togglePublishStatus(Course $course): Course
    {
        $course->update(['is_published' => !$course->is_published]);

        return $course->fresh();
    }

    /**
     * Get published courses
     *
     * @return Collection
     */
    public function getPublishedCourses(): Collection
    {
        return Course::with(['mentor', 'category', 'modules.lessons'])
            ->published()
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get courses by level
     *
     * @param string $level
     * @return Collection
     */
    public function getCoursesByLevel(string $level): Collection
    {
        return Course::with(['mentor', 'category'])
            ->where('level', $level)
            ->published()
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Generate unique slug for course
     *
     * @param string $title
     * @param int|null $excludeId
     * @return string
     */
    public function generateUniqueSlug(string $title, ?int $excludeId = null): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $count = 1;

        while ($this->slugExists($slug, $excludeId)) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }

    /**
     * Check if slug exists
     *
     * @param string $slug
     * @param int|null $excludeId
     * @return bool
     */
    protected function slugExists(string $slug, ?int $excludeId = null): bool
    {
        $query = Course::where('slug', $slug);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * Sync modules and lessons for a course
     *
     * @param Course $course
     * @param array $modulesData
     * @return void
     */
    protected function syncModulesAndLessons(Course $course, array $modulesData): void
    {
        $existingModuleIds = $course->modules()->pluck('id')->toArray();
        $incomingModuleIds = [];

        foreach ($modulesData as $moduleIndex => $moduleData) {
            $lessonsData = $moduleData['lessons'] ?? [];
            unset($moduleData['lessons']);

            $moduleData['order'] = $moduleData['order'] ?? $moduleIndex;

            if (isset($moduleData['id']) && in_array($moduleData['id'], $existingModuleIds)) {
                // Update existing module
                $module = $course->modules()->find($moduleData['id']);
                $module->update($moduleData);
                $incomingModuleIds[] = $module->id;
            } else {
                // Create new module
                $module = $course->modules()->create($moduleData);
                $incomingModuleIds[] = $module->id;
            }

            // Sync lessons
            $this->syncLessons($module, $lessonsData);
        }

        // Delete modules that are no longer present
        $modulesToDelete = array_diff($existingModuleIds, $incomingModuleIds);
        if (!empty($modulesToDelete)) {
            $course->modules()->whereIn('id', $modulesToDelete)->each(function ($module) {
                $module->lessons()->delete();
                $module->delete();
            });
        }
    }

    /**
     * Sync lessons for a module
     *
     * @param \App\Models\Module $module
     * @param array $lessonsData
     * @return void
     */
    protected function syncLessons(\App\Models\Module $module, array $lessonsData): void
    {
        $existingLessonIds = $module->lessons()->pluck('id')->toArray();
        $incomingLessonIds = [];

        foreach ($lessonsData as $lessonIndex => $lessonData) {
            $lessonData['order'] = $lessonData['order'] ?? $lessonIndex;

            if (isset($lessonData['id']) && in_array($lessonData['id'], $existingLessonIds)) {
                // Update existing lesson
                $lesson = $module->lessons()->find($lessonData['id']);
                $lesson->update($lessonData);
                $incomingLessonIds[] = $lesson->id;
            } else {
                // Create new lesson
                $lesson = $module->lessons()->create($lessonData);
                $incomingLessonIds[] = $lesson->id;
            }
        }

        // Delete lessons that are no longer present
        $lessonsToDelete = array_diff($existingLessonIds, $incomingLessonIds);
        if (!empty($lessonsToDelete)) {
            $module->lessons()->whereIn('id', $lessonsToDelete)->delete();
        }
    }

    /**
     * Get course statistics
     *
     * @return array
     */
    public function getStatistics(): array
    {
        return [
            'total' => Course::count(),
            'published' => Course::published()->count(),
            'draft' => Course::where('is_published', false)->count(),
            'total_enrollments' => \App\Models\CourseEnrollment::count(),
            'total_revenue' => $this->calculateTotalRevenue(),
        ];
    }

    /**
     * Calculate total revenue from course sales
     *
     * @return float
     */
    protected function calculateTotalRevenue(): float
    {
        return Course::withCount(['orderItems' => function ($query) {
            $query->whereHas('order', function ($q) {
                $q->where('payment_status', 'paid');
            });
        }])
        ->get()
        ->sum(function ($course) {
            return $course->price * $course->order_items_count;
        });
    }
}

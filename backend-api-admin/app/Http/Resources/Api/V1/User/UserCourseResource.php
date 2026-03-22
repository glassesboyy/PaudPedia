<?php

namespace App\Http\Resources\Api\V1\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * User Course Resource — enrolled course with progress.
 *
 * Used in: GET /api/v1/user/courses
 */
class UserCourseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $course = $this->course;
        $totalLessons = $course->total_lessons ?? 0;
        $completedLessons = $this->lessonProgress()->where('is_completed', true)->count();

        return [
            'id' => $this->id,
            'course_id' => $course->id,
            'title' => $course->title,
            'slug' => $course->slug,
            'thumbnail_url' => $course->thumbnail_url ? asset('storage/' . $course->thumbnail_url) : null,
            'level' => $course->level?->value,
            'level_label' => $course->level?->label(),
            'mentor' => $this->whenLoaded('course', function () use ($course) {
                return $course->relationLoaded('mentor') && $course->mentor ? [
                    'id' => $course->mentor->id,
                    'name' => $course->mentor->name,
                    'photo_url' => $course->mentor->photo_url ? asset('storage/' . $course->mentor->photo_url) : null,
                ] : null;
            }),
            'progress_percentage' => $this->progress_percentage,
            'total_lessons' => $totalLessons,
            'completed_lessons' => $completedLessons,
            'is_completed' => $this->isCompleted(),
            'first_lesson_id' => $this->getFirstLessonId($course),
            'enrolled_at' => $this->enrolled_at?->toIso8601String(),
            'completed_at' => $this->completed_at?->toIso8601String(),
        ];
    }

    /**
     * Get the first lesson ID from the course (by module order → lesson order).
     */
    private function getFirstLessonId(mixed $course): ?int
    {
        $firstModule = $course->modules()->orderBy('order')->first();

        if (!$firstModule) {
            return null;
        }

        $firstLesson = $firstModule->lessons()->orderBy('order')->first();

        return $firstLesson?->id;
    }
}

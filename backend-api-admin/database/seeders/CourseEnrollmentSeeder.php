<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Seeder;

class CourseEnrollmentSeeder extends Seeder
{
    public function run(): void
    {
        // Get paid orders with course items
        $paidOrders = Order::where('status', 'paid')
            ->whereHas('items', function($query) {
                $query->where('item_type', 'course');
            })
            ->with(['items', 'user'])
            ->get();

        if ($paidOrders->isEmpty()) {
            return;
        }

        foreach ($paidOrders as $order) {
            foreach ($order->items as $item) {
                if ($item->item_type !== 'course') {
                    continue;
                }

                $course = Course::find($item->item_id);
                if (!$course) {
                    continue;
                }

                // Check if enrollment already exists
                $existingEnrollment = CourseEnrollment::where('course_id', $course->id)
                    ->where('user_id', $order->user_id)
                    ->first();

                if ($existingEnrollment) {
                    continue;
                }

                // Create enrollment
                $enrollment = CourseEnrollment::create([
                    'course_id' => $course->id,
                    'user_id' => $order->user_id,
                    'enrolled_at' => $order->paid_at ?? now(),
                    'progress_percentage' => 0,
                    'completed_at' => null,
                    'certificate_url' => null,
                ]);

                // Create random progress for lessons
                $lessons = Lesson::whereHas('module', function($query) use ($course) {
                    $query->where('course_id', $course->id);
                })->get();

                if ($lessons->isEmpty()) {
                    continue;
                }

                // Randomly complete some lessons
                $completionRate = fake()->numberBetween(0, 100);
                $lessonsToComplete = (int) (($completionRate / 100) * $lessons->count());

                foreach ($lessons->take($lessonsToComplete) as $lesson) {
                    LessonProgress::create([
                        'enrollment_id' => $enrollment->id,
                        'lesson_id' => $lesson->id,
                        'is_completed' => true,
                        'completed_at' => now()->subDays(fake()->numberBetween(1, 30)),
                    ]);
                }

                // Update enrollment progress
                $enrollment->updateProgress();
            }
        }
    }
}

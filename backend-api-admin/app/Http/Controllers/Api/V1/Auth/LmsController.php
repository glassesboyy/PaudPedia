<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Enums\ContentType;
use App\Http\Controllers\Api\V1\BaseController;
use App\Models\CourseEnrollment;
use App\Models\Lesson;
use App\Services\Lms\CertificateGeneratorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class LmsController extends BaseController
{
    public function show(Request $request, string $courseSlug): JsonResponse
    {
        $enrollment = $this->resolveEnrollment($request, $courseSlug);

        if (!$enrollment) {
            return $this->forbidden('Anda belum terdaftar pada kursus ini');
        }

        $enrollment->load([
            'course.mentor',
            'course.modules.lessons' => function ($query) {
                $query->orderBy('order');
            },
            'lessonProgress',
        ]);

        $enrollment->updateProgress();
        $enrollment->refresh();

        $completedByLessonId = $enrollment->lessonProgress
            ->where('is_completed', true)
            ->pluck('is_completed', 'lesson_id');

        $modules = $enrollment->course->modules->map(function ($module) use ($completedByLessonId) {
            return [
                'id' => $module->id,
                'title' => $module->title,
                'description' => $module->description,
                'order' => $module->order,
                'lessons' => $module->lessons->map(function ($lesson) use ($completedByLessonId) {
                    return [
                        'id' => $lesson->id,
                        'slug' => (string) $lesson->id,
                        'title' => $lesson->title,
                        'type' => $lesson->content_type?->value,
                        'duration_minutes' => $lesson->duration_minutes,
                        'order' => $lesson->order,
                        'is_completed' => (bool) ($completedByLessonId[$lesson->id] ?? false),
                    ];
                })->values(),
            ];
        })->values();

        $allLessons = $modules->flatMap(fn ($module) => $module['lessons'])->values();
        $nextLesson = $allLessons->firstWhere('is_completed', false) ?? $allLessons->first();

        return $this->success([
            'course' => [
                'id' => $enrollment->course->id,
                'title' => $enrollment->course->title,
                'slug' => $enrollment->course->slug,
                'thumbnail_url' => $enrollment->course->thumbnail_url ? asset('storage/' . $enrollment->course->thumbnail_url) : null,
                'mentor' => $enrollment->course->mentor ? [
                    'id' => $enrollment->course->mentor->id,
                    'name' => $enrollment->course->mentor->name,
                ] : null,
                'modules' => $modules,
            ],
            'progress' => $this->buildProgressPayload($enrollment),
            'next_lesson_slug' => $nextLesson['slug'] ?? null,
            'certificate' => [
                'available' => !empty($enrollment->certificate_url),
                'download_url' => !empty($enrollment->certificate_url)
                    ? route('api.v1.user.lms.courses.certificate.download', ['courseSlug' => $courseSlug])
                    : null,
            ],
        ], 'Data LMS berhasil dimuat');
    }

    public function lesson(Request $request, string $courseSlug, Lesson $lesson): JsonResponse
    {
        $enrollment = $this->resolveEnrollment($request, $courseSlug);

        if (!$enrollment) {
            return $this->forbidden('Anda belum terdaftar pada kursus ini');
        }

        if ((int) $lesson->module?->course_id !== (int) $enrollment->course_id) {
            return $this->notFound('Materi tidak ditemukan pada kursus ini');
        }

        $lessonType = $lesson->content_type?->value;

        return $this->success([
            'id' => $lesson->id,
            'slug' => (string) $lesson->id,
            'title' => $lesson->title,
            'type' => $lessonType,
            'duration_minutes' => $lesson->duration_minutes,
            'video_url' => $lessonType === ContentType::VIDEO->value ? $lesson->video_url : null,
            'text_content' => $lessonType === ContentType::TEXT->value ? $lesson->text_content : null,
            'pdf_url' => $lessonType === ContentType::PDF->value
                ? route('api.v1.user.lms.courses.lessons.pdf', ['courseSlug' => $courseSlug, 'lesson' => $lesson->id])
                : null,
        ], 'Detail materi berhasil dimuat');
    }

    public function markLessonComplete(Request $request, string $courseSlug, Lesson $lesson): JsonResponse
    {
        $enrollment = $this->resolveEnrollment($request, $courseSlug);

        if (!$enrollment) {
            return $this->forbidden('Anda belum terdaftar pada kursus ini');
        }

        if ((int) $lesson->module?->course_id !== (int) $enrollment->course_id) {
            return $this->notFound('Materi tidak ditemukan pada kursus ini');
        }

        $progress = $enrollment->lessonProgress()->firstOrCreate(
            ['lesson_id' => $lesson->id],
            ['is_completed' => false]
        );

        if (!$progress->is_completed) {
            $progress->markAsCompleted();
        }

        $enrollment->refresh();

        return $this->success([
            'lesson_id' => $lesson->id,
            'is_completed' => true,
            'progress' => $this->buildProgressPayload($enrollment),
            'certificate' => [
                'available' => !empty($enrollment->certificate_url),
                'download_url' => !empty($enrollment->certificate_url)
                    ? route('api.v1.user.lms.courses.certificate.download', ['courseSlug' => $courseSlug])
                    : null,
            ],
        ], 'Materi berhasil ditandai selesai');
    }

    public function progress(Request $request, string $courseSlug): JsonResponse
    {
        $enrollment = $this->resolveEnrollment($request, $courseSlug);

        if (!$enrollment) {
            return $this->forbidden('Anda belum terdaftar pada kursus ini');
        }

        $enrollment->updateProgress();
        $enrollment->refresh();

        return $this->success($this->buildProgressPayload($enrollment), 'Progress kursus berhasil dimuat');
    }

    public function lessonPdf(Request $request, string $courseSlug, Lesson $lesson): BinaryFileResponse|JsonResponse
    {
        $enrollment = $this->resolveEnrollment($request, $courseSlug);

        if (!$enrollment) {
            return $this->forbidden('Anda belum terdaftar pada kursus ini');
        }

        if ((int) $lesson->module?->course_id !== (int) $enrollment->course_id) {
            return $this->notFound('Materi tidak ditemukan pada kursus ini');
        }

        if ($lesson->content_type?->value !== ContentType::PDF->value || !$lesson->pdf_file) {
            return $this->notFound('File PDF tidak tersedia untuk materi ini');
        }

        if (!Storage::disk('local')->exists($lesson->pdf_file)) {
            return $this->notFound('File PDF tidak ditemukan');
        }

        $fullPath = Storage::disk('local')->path($lesson->pdf_file);
        $fileName = 'lesson-' . $lesson->id . '.pdf';

        return response()->file($fullPath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $fileName . '"',
        ]);
    }

    public function generateCertificate(Request $request, string $courseSlug, CertificateGeneratorService $generator): JsonResponse
    {
        $enrollment = $this->resolveEnrollment($request, $courseSlug);

        if (!$enrollment) {
            return $this->forbidden('Anda belum terdaftar pada kursus ini');
        }

        $enrollment->updateProgress();
        $enrollment->refresh();

        if ((int) $enrollment->progress_percentage < 100) {
            return $this->error('Sertifikat hanya tersedia setelah progress mencapai 100%', 422);
        }

        if (empty($enrollment->certificate_url)) {
            $path = $generator->generateForEnrollment($enrollment->loadMissing(['user', 'course']));
            $enrollment->certificate_url = $path;
            $enrollment->save();
            $enrollment->refresh();
        }

        return $this->success([
            'available' => !empty($enrollment->certificate_url),
            'download_url' => route('api.v1.user.lms.courses.certificate.download', ['courseSlug' => $courseSlug]),
        ], 'Sertifikat berhasil disiapkan');
    }

    public function downloadCertificate(Request $request, string $courseSlug): BinaryFileResponse|JsonResponse
    {
        $enrollment = $this->resolveEnrollment($request, $courseSlug);

        if (!$enrollment) {
            return $this->forbidden('Anda belum terdaftar pada kursus ini');
        }

        if (empty($enrollment->certificate_url)) {
            return $this->notFound('Sertifikat belum tersedia');
        }

        if (!Storage::disk('public')->exists($enrollment->certificate_url)) {
            return $this->notFound('File sertifikat tidak ditemukan');
        }

        $fullPath = Storage::disk('public')->path($enrollment->certificate_url);
        $filename = 'sertifikat-' . $enrollment->course->slug . '.pdf';

        return response()->download($fullPath, $filename, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    protected function resolveEnrollment(Request $request, string $courseSlug): ?CourseEnrollment
    {
        return CourseEnrollment::query()
            ->where('user_id', $request->user()->id)
            ->whereHas('course', function ($query) use ($courseSlug) {
                $query->where('slug', $courseSlug)->where('is_published', true);
            })
            ->with(['course', 'lessonProgress'])
            ->first();
    }

    protected function buildProgressPayload(CourseEnrollment $enrollment): array
    {
        $enrollment->loadMissing('course');

        $totalLessons = $enrollment->course->total_lessons ?? 0;
        $completedLessons = $enrollment->lessonProgress()->where('is_completed', true)->count();

        return [
            'total_lessons' => $totalLessons,
            'completed_lessons' => $completedLessons,
            'progress_percentage' => (int) $enrollment->progress_percentage,
            'is_completed' => (int) $enrollment->progress_percentage >= 100,
            'completed_at' => $enrollment->completed_at?->toIso8601String(),
        ];
    }
}

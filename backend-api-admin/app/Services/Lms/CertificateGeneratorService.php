<?php

namespace App\Services\Lms;

use App\Models\CourseEnrollment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class CertificateGeneratorService
{
    public function generateForEnrollment(CourseEnrollment $enrollment): string
    {
        $enrollment->loadMissing(['user', 'course']);

        $payload = [
            'user_name' => $enrollment->user?->name ?? 'Peserta',
            'course_title' => $enrollment->course?->title ?? 'Kursus',
            'issue_date' => $enrollment->completed_at?->format('d F Y') ?? now()->format('d F Y'),
            'certificate_number' => sprintf('LMS-%s-%s', $enrollment->course_id, str_pad((string) $enrollment->id, 6, '0', STR_PAD_LEFT)),
        ];

        $pdf = Pdf::loadView('certificates.course', $payload)->setPaper('a4', 'landscape');

        $path = 'certificates/courses/course-' . $enrollment->course_id . '-user-' . $enrollment->user_id . '.pdf';

        Storage::disk('public')->put($path, $pdf->output());

        return $path;
    }
}

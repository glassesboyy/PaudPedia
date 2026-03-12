<?php

namespace App\Http\Resources\Api\V1\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * User Certificate Resource — certificate from completed course.
 *
 * Used in: GET /api/v1/user/certificates
 * Wraps a CourseEnrollment that has a certificate_url.
 */
class UserCertificateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $course = $this->course;

        return [
            'id' => $this->id,
            'course_id' => $course?->id,
            'course_title' => $course?->title,
            'course_slug' => $course?->slug,
            'issue_date' => $this->completed_at?->toIso8601String(),
            'issue_date_formatted' => $this->completed_at?->format('d M Y'),
            'certificate_url' => $this->certificate_url ? asset('storage/' . $this->certificate_url) : null,
            'download_url' => $this->certificate_url ? asset('storage/' . $this->certificate_url) : null,
        ];
    }
}

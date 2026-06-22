<?php

namespace App\Http\Controllers\Api\V1\School;

use App\Enums\AttendanceStatus;
use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Attendance;
use App\Models\School;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * GET /api/v1/schools/{id}/classes/{classId}/reports/status
     */
    public function statusList(Request $request, int $id, int $classId)
    {
        $school = School::findOrFail($id);
        
        $semester = $request->get('semester', '1');
        $academicYear = $request->get('academic_year', date('Y') . '/' . (date('Y') + 1));
        
        $semesterEnum = \App\Enums\Semester::tryFrom($semester);
        
        $reports = \App\Models\StudentReport::whereHas('student', function($q) use ($classId) {
            $q->where('class_id', $classId);
        })
        ->where('semester', $semesterEnum)
        ->where('academic_year', $academicYear)
        ->pluck('student_id')
        ->toArray();
        
        return response()->json([
            'generated_student_ids' => $reports
        ]);
    }

    /**
     * GET /api/v1/schools/{id}/reports/students/{studentId}/pdf
     * 
     * Generate and download PDF report.
     */
    public function downloadPdf(Request $request, int $id, int $studentId)
    {
        $school = School::findOrFail($id);

        if (!$school->isPro()) {
            return response()->json([
                'message' => 'Fitur rapor hanya tersedia untuk Pro Plan.',
                'upgrade_required' => true,
            ], 403);
        }

        $user = $request->user();
        $student = $this->resolveStudent($user, $school, $studentId);

        if (!$student) {
            return response()->json(['message' => 'Akses ditolak atau siswa tidak ditemukan.'], 403);
        }

        $semester = $request->get('semester', '1');
        $academicYear = $request->get('academic_year', $student->class?->academic_year ?? date('Y') . '/' . (date('Y') + 1));
        
        $semesterEnum = \App\Enums\Semester::tryFrom($semester);
        $studentReport = \App\Models\StudentReport::where('student_id', $student->id)
            ->where('semester', $semesterEnum)
            ->where('academic_year', $academicYear)
            ->with('details.program')
            ->first();

        if (!$studentReport) {
            return response()->json([
                'message' => 'Rapor Naratif belum disusun oleh Guru untuk periode ini.'
            ], 400);
        }

        $data = $this->buildReportData($school, $student, $studentReport, $semester, $academicYear);

        $pdf = Pdf::loadView('reports.student-report', $data);
        $pdf->setPaper('a4', 'portrait');

        $filename = 'Rapor_' . str_replace(' ', '_', $student->name) . '_' . $semester . '_' . str_replace('/', '-', $academicYear) . '.pdf';

        return $pdf->download($filename);
    }

    protected function buildReportData(School $school, Student $student, \App\Models\StudentReport $studentReport, string $semester, string $academicYear): array
    {
        // Attendance summary
        // Calculate date range based on academic year and semester
        $years = explode('/', $academicYear);
        if (count($years) === 2) {
            $startYear = (int)$years[0];
            $endYear = (int)$years[1];
            
            if ($semester === '1') {
                $startDate = "{$startYear}-07-01";
                $endDate = "{$startYear}-12-31";
            } else {
                $startDate = "{$endYear}-01-01";
                $endDate = "{$endYear}-06-30";
            }
            
            $attendances = Attendance::where('student_id', $student->id)
                ->whereBetween('date', [$startDate, $endDate])
                ->get();
        } else {
            $attendances = Attendance::where('student_id', $student->id)->get();
        }
        $attendanceSummary = [
            'total' => $attendances->count(),
            'present' => $attendances->where('status', AttendanceStatus::PRESENT)->count(),
            'sick' => $attendances->where('status', AttendanceStatus::SICK)->count(),
            'permission' => $attendances->where('status', AttendanceStatus::PERMISSION)->count(),
            'absent' => $attendances->where('status', AttendanceStatus::ABSENT)->count(),
        ];

        // Assessment data
        // For PDF, we compute final scale per indicator to show in a table, or just pass the StudentReport data
        // The PDF will show the narrative and maybe we don't need the matrix in the PDF, just the narrative!
        // But the user said: "saya ingin pada bagian tabelnya, terdapat 1 kolom tambahan yakni "Capaian Akhir Semester" ... (dan ini dibuat per indikator)" - Oh that was for the "Penyusunan Rapor Naratif" UI, not the PDF.
        // Wait, for the PDF they want "nilai (yang diambil dari fitur rapor naratif)".
        
        $reportDetails = $studentReport->details->map(fn ($detail) => [
            'program' => $detail->program->name,
            'narrative' => $detail->narrative,
        ]);

        // School logo URL
        $logoUrl = null;
        if ($school->logo_url) {
            $logoPath = storage_path('app/public/' . $school->logo_url);
            if (file_exists($logoPath)) {
                $logoData = base64_encode(file_get_contents($logoPath));
                $logoMime = mime_content_type($logoPath);
                $logoUrl = "data:{$logoMime};base64,{$logoData}";
            }
        }

        // Student photo URL
        $photoUrl = null;
        if ($student->photo_url) {
            $photoPath = storage_path('app/public/' . $student->photo_url);
            if (file_exists($photoPath)) {
                $photoData = base64_encode(file_get_contents($photoPath));
                $photoMime = mime_content_type($photoPath);
                $photoUrl = "data:{$photoMime};base64,{$photoData}";
            }
        }

        // Get programs
        $programs = \App\Models\DevelopmentProgram::with('indicators')
            ->where('school_id', $school->id)
            ->orderBy('order')
            ->get();

        // Get matrix
        $assessments = Assessment::where('student_id', $student->id)
            ->where('academic_year', $academicYear)
            ->where('semester', \App\Enums\Semester::tryFrom($semester))
            ->get();
            
        $matrix = [];
        foreach ($assessments as $ast) {
            if (!isset($matrix[$ast->indicator_id])) {
                $matrix[$ast->indicator_id] = [];
            }
            $matrix[$ast->indicator_id][$ast->assessment_month] = [
                'scale' => $ast->scale->value,
                'scale_label' => $ast->scale->label(),
                'scale_color' => $ast->scale->color(),
            ];
        }

        return [
            'school' => [
                'name' => $school->name,
                'npsn' => $school->npsn,
                'address' => $school->address,
                'phone' => $school->phone,
                'email' => $school->email,
                'logo_url' => $logoUrl,
                'headmaster_name' => $school->headmaster_name,
            ],
            'student' => [
                'name' => $student->name,
                'nisn' => $student->nisn,
                'birth_date' => $student->birth_date,
                'gender' => $student->gender,
                'class_name' => $student->class?->name ?? '-',
                'photo_url' => $photoUrl,
            ],
            'semester' => $semester,
            'semester_label' => $semester === '1' ? 'Semester 1 (Ganjil)' : 'Semester 2 (Genap)',
            'academic_year' => $academicYear,
            'attendance' => $attendanceSummary,
            'report' => [
                'introduction_notes' => $studentReport->introduction_notes,
                'closing_notes' => $studentReport->closing_notes,
                'recommendation' => $studentReport->recommendation,
                'details' => $reportDetails,
            ],
            'programs' => $programs,
            'matrix' => $matrix,
            'generated_at' => now()->format('d F Y'),
        ];
    }

    /**
     * Resolve student based on user role (parent, teacher, or headmaster).
     */
    protected function resolveStudent($user, School $school, int $studentId): ?Student
    {
        if ($user->hasRole('parent')) {
            $parentProfile = $user->parentProfile;
            if (!$parentProfile) return null;
            
            return Student::where('id', $studentId)
                ->where('parent_profile_id', $parentProfile->id)
                ->first();
        }

        $membership = $user->schoolMemberships()
            ->where('school_id', $school->id)
            ->first();

        if (!$membership) return null;

        $query = Student::where('id', $studentId)->where('school_id', $school->id)->with('class');

        if ($membership->isTeacher()) {
            $teacher = \App\Models\Teacher::where('user_id', $user->id)
                ->where('school_id', $school->id)
                ->first();

            if (!$teacher) return null;

            $classIds = \App\Models\ClassRoom::where('homeroom_teacher_id', $teacher->id)->pluck('id');
            $query->whereIn('class_id', $classIds);
        }

        return $query->first();
    }
}

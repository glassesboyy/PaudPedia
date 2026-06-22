<?php

namespace App\Http\Controllers\Api\V1\School;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\ClassRoom;
use App\Models\School;
use App\Models\Student;
use App\Models\DevelopmentProgram;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Enum;
use App\Enums\AssessmentScale;
use App\Enums\Semester;

class AssessmentController extends Controller
{
    /**
     * Get assessment list for a specific class and month (for input form)
     */
    public function index(Request $request, $id, $classId)
    {
        $school = School::findOrFail($id);
        
        $membership = $request->user()->schoolMemberships()
            ->where('school_id', $school->id)
            ->first();

        if (!$membership) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $class = ClassRoom::where('school_id', $school->id)
            ->where('id', $classId)
            ->with('homeroomTeacher')
            ->firstOrFail();

        // Check RBAC for Teacher
        if ($membership->isTeacher()) {
            if (!$class->homeroomTeacher || $class->homeroomTeacher->user_id !== $request->user()->id) {
                return response()->json(['message' => 'Akses ditolak. Anda hanya dapat melihat penilaian kelas Anda sendiri.'], 403);
            }
        }

        $month = $request->get('month'); // e.g. "2024-01"
        $semesterEnum = Semester::tryFrom($request->get('semester'));
        $academicYear = $request->get('academic_year', $class->academic_year);

        if (!$month || !$semesterEnum) {
            return response()->json(['message' => 'Month and Semester are required parameters.'], 400);
        }

        // Get all active students
        $students = Student::where('class_id', $class->id)
            ->where('school_id', $school->id)
            ->where('status', \App\Enums\StudentStatus::ACTIVE)
            ->orderBy('name')
            ->get();

        // Get programs & indicators
        $programs = DevelopmentProgram::with('indicators')
            ->where('school_id', $school->id)
            ->orderBy('order')
            ->get();

        // Get assessments for this month
        $assessments = Assessment::whereHas('student', function ($query) use ($class) {
                $query->where('class_id', $class->id);
            })
            ->where('assessment_month', $month)
            ->where('semester', $semesterEnum)
            ->where('academic_year', $academicYear)
            ->get();

        // Group assessments by student_id -> indicator_id
        $groupedAssessments = [];
        foreach ($assessments as $a) {
            $groupedAssessments[$a->student_id][$a->indicator_id] = $a->scale->value;
        }

        // Prepare the payload
        $result = $students->map(function ($student) use ($groupedAssessments) {
            return [
                'student_id' => $student->id,
                'name' => $student->name,
                'nisn' => $student->nisn,
                'assessments' => $groupedAssessments[$student->id] ?? [] // Map of indicator_id => scale value
            ];
        });

        return response()->json([
            'data' => $result,
            'programs' => $programs,
            'month' => $month,
            'semester' => $semesterEnum->value,
            'academic_year' => $academicYear,
            'class' => [
                'id' => $class->id,
                'name' => $class->name
            ]
        ]);
    }

    /**
     * Bulk store or update Assessment for a class
     */
    public function store(Request $request, $id, $classId)
    {
        $school = School::findOrFail($id);
        
        $membership = $request->user()->schoolMemberships()
            ->where('school_id', $school->id)
            ->first();

        if (!$membership) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        if ($membership->isHeadmaster()) {
            return response()->json(['message' => 'Akses ditolak. Hanya Guru Kelas yang dapat mengisi penilaian.'], 403);
        }

        $class = ClassRoom::where('school_id', $school->id)
            ->where('id', $classId)
            ->with('homeroomTeacher')
            ->firstOrFail();

        if ($membership->isTeacher()) {
            if (!$class->homeroomTeacher || $class->homeroomTeacher->user_id !== $request->user()->id) {
                return response()->json(['message' => 'Akses ditolak. Anda hanya dapat mengisi penilaian kelas Anda sendiri.'], 403);
            }
        }

        // Validate
        $validator = Validator::make($request->all(), [
            'month' => 'required|string', // "YYYY-MM"
            'semester' => ['required', new Enum(Semester::class)],
            'academic_year' => 'required|string|max:20',
            'assessments' => 'required|array',
            'assessments.*.student_id' => 'required|exists:students,id',
            'assessments.*.indicator_id' => 'required|exists:development_indicators,id',
            'assessments.*.scale' => ['required', new Enum(AssessmentScale::class)],
            'assessments.*.notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $month = $request->month;
        $semester = Semester::from($request->semester);
        $academicYear = $request->academic_year;
        $assessmentsData = $request->assessments;
        
        $savedAssessments = [];

        foreach ($assessmentsData as $data) {
            $student = Student::where('id', $data['student_id'])
                ->where('class_id', $class->id)
                ->first();

            if (!$student) continue;

            $scaleEnum = AssessmentScale::from($data['scale']);

            $assessment = Assessment::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'indicator_id' => $data['indicator_id'],
                    'assessment_month' => $month,
                ],
                [
                    'semester' => $semester,
                    'academic_year' => $academicYear,
                    'scale' => $scaleEnum,
                    'notes' => $data['notes'] ?? null,
                    'assessed_at' => now(),
                ]
            );

            $savedAssessments[] = $assessment;
        }

        return response()->json([
            'message' => 'Penilaian berhasil disimpan.',
            'count' => count($savedAssessments)
        ]);
    }

    /**
     * Get assessment matrix for a student (Semester Report Builder)
     */
    public function studentMatrix(Request $request, $id, $studentId)
    {
        $school = School::findOrFail($id);
        
        $user = $request->user();
        if ($user->hasRole('parent')) {
            $parentProfile = $user->parentProfile;
            if (!$parentProfile) {
                return response()->json(['message' => 'Akses ditolak.'], 403);
            }
            $student = Student::where('id', $studentId)
                ->where('parent_profile_id', $parentProfile->id)
                ->firstOrFail();
        } else {
            // Must be staff
            $membership = $user->schoolMemberships()
                ->where('school_id', $school->id)
                ->first();

            if (!$membership) {
                return response()->json(['message' => 'Akses ditolak.'], 403);
            }
            $student = Student::where('id', $studentId)
                ->where('school_id', $school->id)
                ->firstOrFail();
        }

        $semesterEnum = Semester::tryFrom($request->get('semester', Semester::SEMESTER_1->value));
        $academicYear = $request->get('academic_year');

        // Group assessments by indicator and month
        $assessments = Assessment::where('student_id', $student->id)
            ->where('semester', $semesterEnum)
            ->when($academicYear, function($query) use ($academicYear) {
                $query->where('academic_year', $academicYear);
            })
            ->orderBy('assessment_month', 'asc')
            ->get();
            
        $matrix = [];
        
        foreach ($assessments as $assessment) {
            if (!isset($matrix[$assessment->indicator_id])) {
                $matrix[$assessment->indicator_id] = [];
            }
            // Store by month
            $matrix[$assessment->indicator_id][$assessment->assessment_month] = [
                'scale' => $assessment->scale->value,
                'scale_label' => $assessment->scale->label(),
                'scale_color' => $assessment->scale->color(),
            ];
        }

        $programs = DevelopmentProgram::with('indicators')
            ->where('school_id', $school->id)
            ->orderBy('order')
            ->get();

        return response()->json([
            'programs' => $programs,
            'matrix' => $matrix,
            'student' => [
                'id' => $student->id,
                'name' => $student->name,
            ]
        ]);
    }

    /**
     * Get assessment history for a specific student (grouped by semester)
     */
    public function studentHistory(Request $request, $id, $studentId)
    {
        $school = School::findOrFail($id);
        
        $user = $request->user();
        if ($user->hasRole('parent')) {
            $parentProfile = $user->parentProfile;
            if (!$parentProfile) {
                return response()->json(['message' => 'Akses ditolak.'], 403);
            }
            $student = Student::where('id', $studentId)
                ->where('parent_profile_id', $parentProfile->id)
                ->firstOrFail();
        } else {
            // Must be staff
            $membership = $user->schoolMemberships()
                ->where('school_id', $school->id)
                ->first();

            if (!$membership) {
                return response()->json(['message' => 'Akses ditolak.'], 403);
            }
            $student = Student::where('id', $studentId)
                ->where('school_id', $school->id)
                ->firstOrFail();
        }

        $programs = \App\Models\DevelopmentProgram::with('indicators')
            ->where('school_id', $school->id)
            ->orderBy('order')
            ->get();
            
        // Get all assessments for this student
        $assessments = Assessment::where('student_id', $student->id)->get();
        
        // Group by academic_year and semester
        $grouped = [];
        foreach ($assessments as $ast) {
            $key = $ast->academic_year . '|' . $ast->semester->value;
            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'academic_year' => $ast->academic_year,
                    'semester' => $ast->semester->value,
                    'semester_label' => $ast->semester->value === '1' ? 'Semester 1 (Ganjil)' : 'Semester 2 (Genap)',
                    'matrix' => []
                ];
            }
            
            if (!isset($grouped[$key]['matrix'][$ast->indicator_id])) {
                $grouped[$key]['matrix'][$ast->indicator_id] = [];
            }
            
            $grouped[$key]['matrix'][$ast->indicator_id][$ast->assessment_month] = [
                'scale' => $ast->scale->value,
                'scale_label' => $ast->scale->label(),
                'scale_color' => $ast->scale->color(),
            ];
        }
        
        // Get generated reports
        $reports = \App\Models\StudentReport::where('student_id', $student->id)->get();
        $generatedSet = [];
        foreach ($reports as $r) {
            $generatedSet[$r->academic_year . '|' . $r->semester->value] = true;
        }

        $history = [];
        foreach ($grouped as $key => $data) {
            $data['is_report_generated'] = isset($generatedSet[$key]);
            $history[] = $data;
        }

        // Sort descending by academic year and semester
        usort($history, function($a, $b) {
            if ($a['academic_year'] === $b['academic_year']) {
                return $b['semester'] <=> $a['semester'];
            }
            return $b['academic_year'] <=> $a['academic_year'];
        });

        return response()->json([
            'programs' => $programs,
            'history' => $history
        ]);
    }
}

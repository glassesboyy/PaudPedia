<?php

namespace App\Http\Controllers\Api\V1\School;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\ClassRoom;
use App\Models\School;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Validation\Rules\Enum;
use App\Enums\AssessmentScale;
use App\Enums\Semester;

class AssessmentController extends Controller
{
    /**
     * Get assessment list for a specific class, aspect, and semester
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
            ->firstOrFail();

        $aspect = $request->get('aspect');
        $semesterEnum = Semester::tryFrom($request->get('semester'));
        $academicYear = $request->get('academic_year', $class->academic_year);

        if (!$aspect || !$semesterEnum) {
            return response()->json(['message' => 'Aspect and Semester are required parameters.'], 400);
        }

        // Get students in this class
        $students = Student::where('class_id', $class->id)
            ->where('school_id', $school->id)
            ->where('status', \App\Enums\StudentStatus::ACTIVE)
            ->orderBy('name')
            ->get();

        // Get assessments
        $assessments = Assessment::whereHas('student', function ($query) use ($class) {
                $query->where('class_id', $class->id);
            })
            ->where('aspect', $aspect)
            ->where('semester', $semesterEnum)
            ->where('academic_year', $academicYear)
            ->get()
            ->keyBy('student_id');

        $result = $students->map(function ($student) use ($assessments) {
            $assessment = $assessments->get($student->id);
            return [
                'student_id' => $student->id,
                'name' => $student->name,
                'nisn' => $student->nisn,
                'scale' => $assessment ? $assessment->scale->value : null,
                'notes' => $assessment ? $assessment->notes : null,
                'assessment_id' => $assessment ? $assessment->id : null,
            ];
        });

        return response()->json([
            'data' => $result,
            'aspect' => $aspect,
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

        $class = ClassRoom::where('school_id', $school->id)
            ->where('id', $classId)
            ->firstOrFail();

        // Validate
        $validator = Validator::make($request->all(), [
            'aspect' => 'required|string|max:100',
            'semester' => ['required', new Enum(Semester::class)],
            'academic_year' => 'required|string|max:20',
            'assessments' => 'required|array',
            'assessments.*.student_id' => 'required|exists:students,id',
            'assessments.*.scale' => ['required', new Enum(AssessmentScale::class)],
            'assessments.*.notes' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $aspect = $request->aspect;
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
                    'aspect' => $aspect,
                    'semester' => $semester,
                    'academic_year' => $academicYear,
                ],
                [
                    'description' => $scaleEnum->description(),
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
     * Get assessment history for a student
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

        // Group assessments by semester
        $assessments = Assessment::where('student_id', $student->id)
            ->orderBy('academic_year', 'desc')
            ->orderBy('semester', 'desc')
            ->orderBy('aspect', 'asc')
            ->get();
            
        $grouped = [];
        
        foreach ($assessments as $assessment) {
            $key = $assessment->academic_year . ' - ' . $assessment->semester->label();
            
            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'academic_year' => $assessment->academic_year,
                    'semester' => $assessment->semester->value,
                    'semester_label' => $assessment->semester->label(),
                    'items' => []
                ];
            }
            
            $grouped[$key]['items'][] = [
                'id' => $assessment->id,
                'aspect' => $assessment->aspect,
                'scale' => $assessment->scale->value,
                'scale_label' => $assessment->scale->label(),
                'scale_color' => $assessment->scale->color(),
                'description' => $assessment->description,
                'notes' => $assessment->notes,
                'assessed_at' => $assessment->assessed_at?->format('Y-m-d')
            ];
        }

        return response()->json([
            'history' => array_values($grouped)
        ]);
    }
}

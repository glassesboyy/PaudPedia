<?php

namespace App\Http\Controllers\Api\V1\School;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\School;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Validation\Rules\Enum;
use App\Enums\AttendanceStatus;

class AttendanceController extends Controller
{
    /**
     * Get attendance list for a specific class and date
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

        // Get date, default to today
        $date = $request->get('date', date('Y-m-d'));

        // Get students in this class OR students who have attendance in this class on this date
        $students = Student::where('school_id', $school->id)
            ->where(function ($q) use ($class, $date) {
                // Currently active in this class
                $q->where(function ($subQ) use ($class) {
                    $subQ->where('class_id', $class->id)
                         ->where('status', \App\Enums\StudentStatus::ACTIVE);
                });
                // OR has attendance record for this class on this date
                $q->orWhereHas('attendance', function ($subQ) use ($class, $date) {
                    $subQ->where('class_id', $class->id)
                         ->whereDate('date', $date);
                });
            })
            ->orderBy('name')
            ->get();

        // Get attendances for these students on the specific date FOR THIS CLASS
        $attendances = Attendance::whereIn('student_id', $students->pluck('id'))
            ->where('class_id', $class->id)
            ->whereDate('date', $date)
            ->get()
            ->keyBy('student_id');

        // Merge student data with attendance data
        $result = $students->map(function ($student) use ($attendances, $date) {
            $attendance = $attendances->get($student->id);
            return [
                'student_id' => $student->id,
                'name' => $student->name,
                'nisn' => $student->nisn,
                'status' => $attendance ? $attendance->status->value : null,
                'notes' => $attendance ? $attendance->notes : null,
                'attendance_id' => $attendance ? $attendance->id : null,
                'date' => $date,
                'proof_file_url' => $attendance && $attendance->proof_file_path ? asset('storage/' . $attendance->proof_file_path) : null
            ];
        });

        return response()->json([
            'data' => $result,
            'date' => $date,
            'class' => [
                'id' => $class->id,
                'name' => $class->name
            ]
        ]);
    }

    /**
     * Bulk store or update attendance for a class
     */
    public function store(Request $request, $id, $classId)
    {
        $school = School::findOrFail($id);
        
        $membership = $request->user()->schoolMemberships()
            ->where('school_id', $school->id)
            ->first();

        if (!$membership || !$membership->isTeacher()) {
            return response()->json(['message' => 'Hanya guru yang dapat mengisi absensi.'], 403);
        }

        $class = ClassRoom::where('school_id', $school->id)
            ->where('id', $classId)
            ->firstOrFail();

        // Validate request
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'attendances' => 'required|array',
            'attendances.*.student_id' => 'required|exists:students,id',
            'attendances.*.status' => ['required', new Enum(AttendanceStatus::class)],
            'attendances.*.notes' => 'nullable|string|max:255',
            'attendances.*.proof_file' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $date = $request->date;
        $attendancesData = $request->attendances;
        
        // Find teacher id for this class (the currenlty logged in teacher)
        $teacher = $membership->isTeacher() ? $request->user()->teacherProfile : null;
        $teacherId = $teacher ? $teacher->id : $class->homeroom_teacher_id;

        $savedAttendances = [];

        foreach ($attendancesData as $key => $data) {
            // Verify student belongs to this class
            $student = Student::where('id', $data['student_id'])
                ->where('class_id', $class->id)
                ->first();

            if (!$student) continue;

            $updateData = [
                'status' => $data['status'],
                'notes' => $data['notes'] ?? null,
                'class_id' => $class->id,
            ];

            // In Laravel, the file from an array input might be in the data array or in $request->file
            // We can check if a file was uploaded for this specific attendance record
            $fileKey = "attendances.{$key}.proof_file";
            if ($request->hasFile($fileKey)) {
                $path = $request->file($fileKey)->store('attendances', 'public');
                $updateData['proof_file_path'] = $path;
            } elseif (isset($data['remove_proof_file']) && $data['remove_proof_file'] === 'true') {
                $updateData['proof_file_path'] = null;
            }

            $attendance = Attendance::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'date' => $date,
                ],
                $updateData
            );

            $savedAttendances[] = $attendance;
        }

        return response()->json([
            'message' => 'Absensi berhasil disimpan.',
            'count' => count($savedAttendances)
        ]);
    }

    /**
     * Get attendance summary for a student (for parent and teacher)
     */
    public function studentSummary(Request $request, $id, $studentId)
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

        $month = $request->get('month');
        $year = $request->get('year');

        $query = Attendance::where('student_id', $student->id);

        if ($month && $year) {
            $query->whereMonth('date', $month)->whereYear('date', $year);
        }

        $history = $query->orderBy('date', 'desc')->get();
        
        $totalDays = $history->count();
        $presentDays = $history->where('status', AttendanceStatus::PRESENT)->count();
        $sickDays = $history->where('status', AttendanceStatus::SICK)->count();
        $permissionDays = $history->where('status', AttendanceStatus::PERMISSION)->count();
        $absentDays = $history->where('status', AttendanceStatus::ABSENT)->count();

        $percentage = $totalDays > 0 ? round(($presentDays / $totalDays) * 100, 2) : 0;

        return response()->json([
            'summary' => [
                'total_recorded_days' => $totalDays,
                'present' => $presentDays,
                'sick' => $sickDays,
                'permission' => $permissionDays,
                'absent' => $absentDays,
                'percentage' => $percentage
            ],
            'history' => $history->map(function ($item) {
                return [
                    'id' => $item->id,
                    'date' => $item->date->format('Y-m-d'),
                    'status' => $item->status->value,
                    'status_label' => $item->status->label(),
                    'notes' => $item->notes
                ];
            })
        ]);
    }
}

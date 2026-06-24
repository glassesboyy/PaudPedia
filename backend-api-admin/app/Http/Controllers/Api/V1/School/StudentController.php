<?php

namespace App\Http\Controllers\Api\V1\School;

use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Requests\Api\V1\School\StoreStudentRequest;
use App\Http\Requests\Api\V1\School\UpdateStudentRequest;
use App\Http\Resources\Api\V1\School\StudentResource;
use App\Models\Student;
use App\Models\School;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StudentController extends BaseController
{
    /**
     * Get list of students in a school.
     */
    public function index(Request $request, int $schoolId): JsonResponse
    {
        $membership = $request->user()->schoolMemberships()
            ->where('school_id', $schoolId)
            ->first();

        if (!$membership || (!$membership->isHeadmaster() && !$membership->isTeacher() && !$membership->isParent())) {
            return $this->error('Akses ditolak.', 403);
        }

        $query = Student::where('school_id', $schoolId)
            ->with(['parent', 'class']);

        // Strict role-based filtering
        if ($membership->isTeacher()) {
            if ($request->boolean('only_my_class')) {
                $teacher = \App\Models\Teacher::where('user_id', $request->user()->id)
                    ->where('school_id', $schoolId)
                    ->first();
                
                if ($teacher) {
                    $classIds = \App\Models\ClassRoom::where('homeroom_teacher_id', $teacher->id)->pluck('id');
                    $query->whereIn('class_id', $classIds);
                } else {
                    $query->whereRaw('1 = 0'); // Empty result if not a registered teacher
                }
            }
            // else: Teacher now has global read-only access to all students in school
        } elseif ($membership->isParent()) {
            // Parent can only see their own active children
            $query->whereHas('parent', function ($q) use ($request) {
                $q->where('user_id', $request->user()->id);
            })->where('status', 'active');
        } elseif ($request->has('parent_user_id')) {
            // For specifically requested parent filtering (e.g. by headmaster)
            $query->whereHas('parent', function ($q) use ($request) {
                $q->where('user_id', $request->get('parent_user_id'));
            });
        }

        // Search by name or NISN
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        // Filter by class
        if ($classId = $request->get('class_id')) {
            $query->where('class_id', $classId);
        }

        // Filter by status
        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        // Filter by gender
        if ($gender = $request->get('gender')) {
            $query->where('gender', $gender);
        }

        // Filter by class level
        if ($level = $request->get('level')) {
            $query->whereHas('class', function ($q) use ($level) {
                $q->where('level', $level);
            });
        }

        $students = $query->orderBy('name', 'asc')
            ->paginate($request->get('per_page', 10));

        return $this->successPaginated(
            StudentResource::collection($students),
            'Data siswa berhasil diambil.'
        );
    }

    /**
     * Store a newly created student.
     */
    public function store(StoreStudentRequest $request, int $schoolId): JsonResponse
    {
        $membership = $request->user()->schoolMemberships()
            ->where('school_id', $schoolId)
            ->first();

        if (!$membership || !$membership->isHeadmaster()) {
            return $this->error('Hanya Kepala Sekolah yang dapat mendaftarkan siswa baru.', 403);
        }

        $school = School::findOrFail($schoolId);

        if (!$school->canAddStudent()) {
            return $this->error('Batas jumlah siswa untuk paket Anda telah tercapai. Silakan upgrade ke pro.', 422);
        }

        $validated = $request->validated();

        // Handle photo upload
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('students/photos', 'public');
        }

        $student = Student::create([
            'school_id' => $school->id,
            'parent_profile_id' => $validated['parent_profile_id'],
            'class_id' => $validated['class_id'] ?? null,
            'name' => $validated['name'],
            'nisn' => $validated['nisn'] ?? null,
            'birth_date' => $validated['birth_date'],
            'gender' => $validated['gender'],
            'address' => $validated['address'] ?? null,
            'photo_url' => $photoPath,
            'enrollment_date' => $validated['enrollment_date'] ?? now()->format('Y-m-d'),
            'status' => $validated['status'] ?? 'active',
        ]);

        $student->load(['parent', 'class']);

        return $this->created(
            new StudentResource($student),
            'Siswa berhasil didaftarkan.'
        );
    }

    /**
     * Display the specified student.
     */
    public function show(Request $request, int $schoolId, int $studentId): JsonResponse
    {
        $membership = $request->user()->schoolMemberships()
            ->where('school_id', $schoolId)
            ->first();

        if (!$membership || (!$membership->isHeadmaster() && !$membership->isTeacher() && !$membership->isParent())) {
            return $this->error('Akses ditolak.', 403);
        }

        $student = Student::where('school_id', $schoolId)
            ->where('id', $studentId)
            ->with(['parent', 'class'])
            ->first();

        if (!$student) {
            return $this->notFound('Data siswa tidak ditemukan.');
        }

        // Parent validation: Can only view their own children
        if ($membership->isParent()) {
            if ($student->parent && $student->parent->user_id !== $request->user()->id) {
                return $this->error('Akses ditolak. Anda hanya dapat melihat data anak Anda sendiri.', 403);
            }
            if ($student->status->value !== 'active') {
                return $this->error('Akses ditolak. Data anak tidak aktif.', 403);
            }
        }

        return $this->success(new StudentResource($student), 'Detail siswa berhasil diambil.');
    }

    /**
     * Update the specified student.
     */
    public function update(UpdateStudentRequest $request, int $schoolId, int $studentId): JsonResponse
    {
        $membership = $request->user()->schoolMemberships()
            ->where('school_id', $schoolId)
            ->first();

        if (!$membership || !$membership->isHeadmaster()) {
            return $this->error('Hanya Kepala Sekolah yang dapat memperbarui data siswa.', 403);
        }

        $student = Student::where('school_id', $schoolId)
            ->where('id', $studentId)
            ->first();

        if (!$student) {
            return $this->notFound('Data siswa tidak ditemukan.');
        }

        $validated = $request->validated();

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo
            if ($student->photo_url && Storage::disk('public')->exists($student->photo_url)) {
                Storage::disk('public')->delete($student->photo_url);
            }
            $validated['photo_url'] = $request->file('photo')->store('students/photos', 'public');
        }

        // Remove 'photo' key from validated data since we use 'photo_url'
        unset($validated['photo']);

        $student->update($validated);
        $student->refresh();
        $student->load(['parent', 'class']);

        return $this->success(new StudentResource($student), 'Data siswa berhasil diperbarui.');
    }

    /**
     * Remove the specified student.
     */
    public function destroy(Request $request, int $schoolId, int $studentId): JsonResponse
    {
        $membership = $request->user()->schoolMemberships()
            ->where('school_id', $schoolId)
            ->first();

        if (!$membership || !$membership->isHeadmaster()) {
            return $this->error('Hanya Kepala Sekolah yang dapat menghapus data siswa.', 403);
        }

        $student = Student::where('school_id', $schoolId)
            ->where('id', $studentId)
            ->first();

        if (!$student) {
            return $this->notFound('Data siswa tidak ditemukan.');
        }

        $student->delete();

        return $this->success(null, 'Data siswa berhasil dihapus.');
    }
}

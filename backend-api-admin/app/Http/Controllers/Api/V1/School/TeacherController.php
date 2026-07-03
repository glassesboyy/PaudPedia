<?php

namespace App\Http\Controllers\Api\V1\School;

use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Resources\Api\V1\School\TeacherResource;
use App\Models\School;
use App\Models\SchoolMember;
use App\Models\Teacher;
use App\Models\User;
use App\Enums\RoleType;
use App\Mail\TeacherCredentialMail;
use App\Http\Requests\Api\V1\School\StoreTeacherRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class TeacherController extends BaseController
{
    /**
     * Get list of teachers in a school.
     *
     * @param Request $request
     * @param int $schoolId
     * @return JsonResponse
     */
    public function index(Request $request, int $schoolId): JsonResponse
    {
        $membership = $request->user()->schoolMemberships()
            ->where('school_id', $schoolId)
            ->first();

        // Allow Headmasters and Teachers to see teacher list
        if (!$membership || (!$membership->isManager() && !$membership->isTeacher())) {
            return $this->error('Akses ditolak. Anda bukan pengelola atau pengajar sekolah ini.', 403);
        }

        $query = Teacher::where('school_id', $schoolId)
            ->with(['user.schoolMemberships' => function ($q) use ($schoolId) {
                $q->where('school_id', $schoolId)->where('role_type', \App\Enums\RoleType::TEACHER);
            }]);

        // Filter by Search (Name or NIP)
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nip', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by Status (Active/Inactive)
        if ($request->has('status')) {
            $isActive = $request->get('status') === 'active';
            $query->whereHas('user.schoolMemberships', function ($q) use ($schoolId, $isActive) {
                $q->where('school_id', $schoolId)
                  ->where('role_type', \App\Enums\RoleType::TEACHER)
                  ->where('is_active', $isActive);
            });
        }

        // Filter by Specialization
        if ($specialization = $request->get('specialization')) {
            $query->where('specialization', 'like', "%{$specialization}%");
        }

        $teachers = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 10));

        return $this->success([
            'teachers' => TeacherResource::collection($teachers),
            'meta' => [
                'current_page' => $teachers->currentPage(),
                'last_page' => $teachers->lastPage(),
                'per_page' => $teachers->perPage(),
                'total' => $teachers->total(),
            ]
        ], 'Data guru berhasil diambil.');
    }

    /**
     * Get specific teacher details.
     *
     * @param Request $request
     * @param int $schoolId
     * @param int $teacherId
     * @return JsonResponse
     */
    public function show(Request $request, int $schoolId, int $teacherId): JsonResponse
    {
        $membership = $request->user()->schoolMemberships()
            ->where('school_id', $schoolId)
            ->first();

        // Allow Headmasters and Teachers to see teacher list
        if (!$membership || (!$membership->isManager() && !$membership->isTeacher())) {
            return $this->error('Akses ditolak. Anda bukan pengelola atau pengajar sekolah ini.', 403);
        }

        $teacher = Teacher::where('school_id', $schoolId)
            ->with(['user.schoolMemberships' => function ($q) use ($schoolId) {
                $q->where('school_id', $schoolId)->where('role_type', \App\Enums\RoleType::TEACHER);
            }, 'homeroomClasses'])
            ->findOrFail($teacherId);

        return $this->success(new TeacherResource($teacher), 'Data guru berhasil diambil.');
    }

    /**
     * Invite/create a new teacher.
     *
     * @param Request $request
     * @param int $schoolId
     * @return JsonResponse
     */
    public function store(StoreTeacherRequest $request, int $schoolId): JsonResponse
    {
        $membership = $request->user()->schoolMemberships()
            ->where('school_id', $schoolId)
            ->first();

        if (!$membership || !$membership->isOperator()) {
            return $this->error('Hanya Operator Sekolah yang dapat mendaftarkan guru baru.', 403);
        }

        $school = $membership->school;

        // Check subscription limit (Lite/Pro)
        if (!$school->canAddTeacher()) {
            return $this->error('Batas jumlah guru untuk paket Anda telah tercapai. Silakan upgrade ke pro.', 422);
        }

        $validated = $request->validated();

        return DB::transaction(function () use ($validated, $school) {
            $user = User::where('email', $validated['email'])->first();
            $newPassword = null;
            $isNewUser = false;

            if (!$user) {
                // Create new user account
                $newPassword = Str::random(8); // Generating random 8 char password
                $user = User::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'password' => Hash::make($newPassword),
                    'phone' => $validated['phone'],
                    'is_active' => true,
                    'email_verified_at' => now(), // Auto verify the account created by headmaster
                ]);

                $user->assignRole('teacher');
                $isNewUser = true;
            } else {
                // Ensure existing user is not already a member
                if ($school->members()->where('user_id', $user->id)->exists()) {
                    return response()->json([
                        'message' => 'User ini sudah terdaftar sebagai anggota di sekolah ini.',
                        'errors' => [
                            'email' => ['User ini sudah terdaftar sebagai anggota di sekolah ini.']
                        ]
                    ], 422);
                }
                
                // Add teacher role if not already has it
                if (!$user->hasRole('teacher')) {
                    $user->assignRole('teacher');
                }
            }

            // Create school membership
            SchoolMember::create([
                'school_id' => $school->id,
                'user_id' => $user->id,
                'role_type' => RoleType::TEACHER,
                'joined_at' => now(),
            ]);

            // Create teacher record
            $teacher = Teacher::create([
                'user_id' => $user->id,
                'school_id' => $school->id,
                'nip' => $validated['nip'],
                'specialization' => $validated['specialization'],
            ]);

            // Send Email if new user
            if ($isNewUser && $newPassword) {
                try {
                    Mail::to($user->email)->send(new TeacherCredentialMail($user, $school, $newPassword));
                } catch (\Exception $e) {
                    // Log error but don't fail transaction? Or fail?
                    // Better log it
                    \Log::error('Gagal mengirim email kredensial guru: ' . $e->getMessage());
                }
            }

            return $this->success(
                new TeacherResource($teacher), 
                $isNewUser 
                    ? 'Guru berhasil didaftarkan dan kredensial telah dikirim ke email.' 
                    : 'User berhasil ditambahkan sebagai guru di sekolah ini.',
                201
            );
        });
    }

    /**
     * Toggle active status of a teacher in a school.
     *
     * @param Request $request
     * @param int $schoolId
     * @param int $teacherId
     * @return JsonResponse
     */
    public function toggleActive(Request $request, int $schoolId, int $teacherId): JsonResponse
    {
        $membership = $request->user()->schoolMemberships()
            ->where('school_id', $schoolId)
            ->first();

        if (!$membership || !$membership->isOperator()) {
            return $this->error('Akses ditolak. Hanya Operator Sekolah yang dapat mengubah status guru.', 403);
        }

        $teacher = Teacher::where('school_id', $schoolId)->where('id', $teacherId)->first();

        if (!$teacher) {
            return $this->error('Data guru tidak ditemukan.', 404);
        }

        $schoolMember = SchoolMember::where('school_id', $schoolId)
            ->where('user_id', $teacher->user_id)
            ->where('role_type', RoleType::TEACHER)
            ->first();

        if (!$schoolMember) {
            return $this->error('Data keanggotaan tidak ditemukan.', 404);
        }

        // Toggle status
        $schoolMember->update([
            'is_active' => !$schoolMember->is_active
        ]);

        $statusText = $schoolMember->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return $this->success(null, "Guru berhasil $statusText.");
    }

    /**
     * Remove a teacher from school.
     *
     * @param Request $request
     * @param int $schoolId
     * @param int $teacherId
     * @return JsonResponse
     */
    public function destroy(Request $request, int $schoolId, int $teacherId): JsonResponse
    {
        $membership = $request->user()->schoolMemberships()
            ->where('school_id', $schoolId)
            ->first();

        if (!$membership || !$membership->isOperator()) {
            return $this->error('Akses ditolak. Hanya Operator Sekolah yang dapat menghapus guru.', 403);
        }

        $teacher = Teacher::where('school_id', $schoolId)->where('id', $teacherId)->first();

        if (!$teacher) {
            return $this->error('Data guru tidak ditemukan.', 404);
        }

        $hasClass = \App\Models\ClassRoom::where('homeroom_teacher_id', $teacher->id)->exists();
        
        if ($hasClass) {
            return $this->error('Tidak dapat menghapus guru karena terdaftar sebagai wali kelas. Silakan gunakan fitur Nonaktifkan guru.', 400);
        }

        return DB::transaction(function () use ($teacher, $schoolId) {
            $userId = $teacher->user_id;

            // Delete teacher record
            $teacher->delete();

            // Delete school membership
            SchoolMember::where('school_id', $schoolId)
                ->where('user_id', $userId)
                ->where('role_type', RoleType::TEACHER)
                ->delete();

            return $this->success(null, 'Guru berhasil dihapus dari sekolah ini.');
        });
    }
}

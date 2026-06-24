<?php

namespace App\Http\Controllers\Api\V1\School;

use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Requests\Api\V1\School\StoreParentRequest;
use App\Http\Requests\Api\V1\School\UpdateParentRequest;
use App\Http\Resources\Api\V1\School\ParentProfileResource;
use App\Models\ParentProfile;
use App\Models\School;
use App\Models\SchoolMember;
use App\Models\User;
use App\Enums\RoleType;
use App\Mail\ParentCredentialMail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ParentProfileController extends BaseController
{
    /**
     * Get list of parents in a school.
     */
    public function index(Request $request, int $schoolId): JsonResponse
    {
        $membership = $request->user()->schoolMemberships()
            ->where('school_id', $schoolId)
            ->first();

        if (!$membership || (!$membership->isHeadmaster() && !$membership->isTeacher())) {
            return $this->error('Akses ditolak.', 403);
        }

        $query = ParentProfile::where('school_id', $schoolId)
            ->withCount('children');

        // Teacher now has global read-only access to all parent profiles in school
        if ($membership->isTeacher()) {
             // No filtering needed
        }

        // Search by name, email, or phone
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('father_name', 'like', "%{$search}%")
                  ->orWhere('mother_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter by minimum number of children
        if ($minChildren = $request->get('min_children')) {
            $query->has('children', '>=', (int) $minChildren);
        }

        $parents = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 10));

        return $this->successPaginated(
            ParentProfileResource::collection($parents),
            'Data orang tua berhasil diambil.'
        );
    }

    /**
     * Create a new parent profile.
     */
    public function store(StoreParentRequest $request, int $schoolId): JsonResponse
    {
        $membership = $request->user()->schoolMemberships()
            ->where('school_id', $schoolId)
            ->first();

        if (!$membership || !$membership->isHeadmaster()) {
            return $this->error('Hanya Kepala Sekolah yang dapat mendaftarkan orang tua baru.', 403);
        }

        $school = School::findOrFail($schoolId);
        $validated = $request->validated();

        return DB::transaction(function () use ($validated, $school) {
            $user = User::where('email', $validated['email'])->first();
            $newPassword = null;
            $isNewUser = false;

            if (!$user) {
                $newPassword = Str::random(8);
                $displayName = $validated['father_name'] ?? $validated['mother_name'] ?? 'Orang Tua';
                $user = User::create([
                    'name' => $displayName,
                    'email' => $validated['email'],
                    'password' => Hash::make($newPassword),
                    'phone' => (string) $validated['phone'],
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]);
                $user->assignRole('parent');
                $isNewUser = true;
            } else {
                // Check if user already has a parent profile in this school
                if (ParentProfile::where('school_id', $school->id)->where('user_id', $user->id)->exists()) {
                    return response()->json([
                        'message' => 'User ini sudah terdaftar sebagai orang tua di sekolah ini.',
                        'errors' => [
                            'email' => ['User ini sudah terdaftar sebagai orang tua di sekolah ini.']
                        ]
                    ], 422);
                }
                if (!$user->hasRole('parent')) {
                    $user->assignRole('parent');
                }
            }

            // Create school membership if not exists
            SchoolMember::firstOrCreate([
                'school_id' => $school->id,
                'user_id' => $user->id,
                'role_type' => RoleType::PARENT,
            ], [
                'joined_at' => now(),
            ]);

            // Create parent profile
            $parent = ParentProfile::create([
                'school_id' => $school->id,
                'user_id' => $user->id,
                'email' => $validated['email'],
                'father_name' => $validated['father_name'] ?? null,
                'mother_name' => $validated['mother_name'] ?? null,
                'phone' => (string) $validated['phone'],
                'father_occupation' => $validated['father_occupation'] ?? null,
                'mother_occupation' => $validated['mother_occupation'] ?? null,
                'address' => $validated['address'] ?? null,
            ]);

            // Send email if new user
            if ($isNewUser && $newPassword) {
                try {
                    Mail::to($user->email)->send(new ParentCredentialMail($user, $school, $newPassword));
                } catch (\Exception $e) {
                    \Log::error('Gagal mengirim email kredensial orang tua: ' . $e->getMessage());
                }
            }

            return $this->created(
                new ParentProfileResource($parent),
                $isNewUser
                    ? 'Orang tua berhasil didaftarkan dan kredensial telah dikirim ke email.'
                    : 'User berhasil ditambahkan sebagai orang tua di sekolah ini.'
            );
        });
    }

    /**
     * Display the specified parent.
     */
    public function show(Request $request, int $schoolId, int $parentId): JsonResponse
    {
        $membership = $request->user()->schoolMemberships()
            ->where('school_id', $schoolId)
            ->first();

        if (!$membership || (!$membership->isHeadmaster() && !$membership->isTeacher())) {
            return $this->error('Akses ditolak.', 403);
        }

        $parent = ParentProfile::where('school_id', $schoolId)
            ->where('id', $parentId)
            ->with(['children.class'])
            ->first();

        if (!$parent) {
            return $this->notFound('Data orang tua tidak ditemukan.');
        }

        return $this->success(new ParentProfileResource($parent), 'Detail orang tua berhasil diambil.');
    }

    /**
     * Update the specified parent.
     */
    public function update(UpdateParentRequest $request, int $schoolId, int $parentId): JsonResponse
    {
        $membership = $request->user()->schoolMemberships()
            ->where('school_id', $schoolId)
            ->first();

        if (!$membership || !$membership->isHeadmaster()) {
            return $this->error('Hanya Kepala Sekolah yang dapat memperbarui data orang tua.', 403);
        }

        $parent = ParentProfile::where('school_id', $schoolId)
            ->where('id', $parentId)
            ->first();

        if (!$parent) {
            return $this->notFound('Data orang tua tidak ditemukan.');
        }

        $parent->update($request->validated());
        $parent->refresh();

        return $this->success(new ParentProfileResource($parent), 'Data orang tua berhasil diperbarui.');
    }

    /**
     * Remove the specified parent.
     */
    public function destroy(Request $request, int $schoolId, int $parentId): JsonResponse
    {
        $membership = $request->user()->schoolMemberships()
            ->where('school_id', $schoolId)
            ->first();

        if (!$membership || !$membership->isHeadmaster()) {
            return $this->error('Hanya Kepala Sekolah yang dapat menghapus data orang tua.', 403);
        }

        $parent = ParentProfile::where('school_id', $schoolId)
            ->where('id', $parentId)
            ->withCount('children')
            ->first();

        if (!$parent) {
            return $this->notFound('Data orang tua tidak ditemukan.');
        }

        return DB::transaction(function () use ($parent, $schoolId) {
            $userId = $parent->user_id;

            // Delete parent profile (cascade deletes students due to FK)
            $parent->delete();

            // Remove school membership
            SchoolMember::where('school_id', $schoolId)
                ->where('user_id', $userId)
                ->where('role_type', RoleType::PARENT)
                ->delete();

            return $this->success(null, 'Data orang tua berhasil dihapus.');
        });
    }
}

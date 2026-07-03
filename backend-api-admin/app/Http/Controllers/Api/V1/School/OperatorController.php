<?php

namespace App\Http\Controllers\Api\V1\School;

use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Resources\Api\V1\School\OperatorResource;
use App\Models\School;
use App\Models\SchoolMember;
use App\Models\User;
use App\Enums\RoleType;
use App\Mail\OperatorCredentialMail;
use App\Http\Requests\Api\V1\School\StoreOperatorRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class OperatorController extends BaseController
{
    /**
     * Get list of operators in a school.
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

        if (!$membership || !$membership->isHeadmaster()) {
            return $this->error('Akses ditolak. Hanya Kepala Sekolah yang dapat melihat daftar Operator Sekolah.', 403);
        }

        $query = SchoolMember::where('school_id', $schoolId)
            ->where('role_type', RoleType::OPERATOR)
            ->with('user');

        if ($search = $request->get('search')) {
            $query->whereHas('user', function ($uq) use ($search) {
                $uq->where('name', 'like', "%{$search}%")
                   ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->has('status')) {
            $isActive = $request->get('status') === 'active';
            $query->where('is_active', $isActive);
        }

        $operators = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 10));

        return $this->success([
            'operators' => OperatorResource::collection($operators),
            'meta' => [
                'current_page' => $operators->currentPage(),
                'last_page' => $operators->lastPage(),
                'per_page' => $operators->perPage(),
                'total' => $operators->total(),
            ]
        ], 'Daftar Operator Sekolah berhasil diambil.');
    }

    /**
     * Get specific operator details.
     *
     * @param Request $request
     * @param int $schoolId
     * @param int $operatorId
     * @return JsonResponse
     */
    public function show(Request $request, int $schoolId, int $operatorId): JsonResponse
    {
        $membership = $request->user()->schoolMemberships()
            ->where('school_id', $schoolId)
            ->first();

        if (!$membership || !$membership->isHeadmaster()) {
            return $this->error('Akses ditolak. Hanya Kepala Sekolah yang dapat melihat detail Operator Sekolah.', 403);
        }

        $operator = SchoolMember::where('school_id', $schoolId)
            ->where('role_type', RoleType::OPERATOR)
            ->with('user')
            ->findOrFail($operatorId);

        return $this->success(new OperatorResource($operator), 'Data Operator Sekolah berhasil diambil.');
    }

    /**
     * Invite/create a new operator.
     *
     * @param StoreOperatorRequest $request
     * @param int $schoolId
     * @return JsonResponse
     */
    public function store(StoreOperatorRequest $request, int $schoolId): JsonResponse
    {
        $membership = $request->user()->schoolMemberships()
            ->where('school_id', $schoolId)
            ->first();

        if (!$membership || !$membership->isHeadmaster()) {
            return $this->error('Akses ditolak. Hanya Kepala Sekolah yang dapat mendaftarkan Operator Sekolah baru.', 403);
        }

        $school = $membership->school;
        $validated = $request->validated();

        return DB::transaction(function () use ($validated, $school) {
            $user = User::where('email', $validated['email'])->first();
            $newPassword = null;
            $isNewUser = false;

            if (!$user) {
                $newPassword = Str::random(8);
                $user = User::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'password' => Hash::make($newPassword),
                    'phone' => $validated['phone'],
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]);

                $user->assignRole('operator');
                $isNewUser = true;
            } else {
                if ($school->members()->where('user_id', $user->id)->exists()) {
                    return response()->json([
                        'message' => 'User ini sudah terdaftar sebagai anggota di sekolah ini.',
                        'errors' => [
                            'email' => ['User ini sudah terdaftar sebagai anggota di sekolah ini.']
                        ]
                    ], 422);
                }
                
                if (!$user->hasRole('operator')) {
                    $user->assignRole('operator');
                }
            }

            $schoolMember = SchoolMember::create([
                'school_id' => $school->id,
                'user_id' => $user->id,
                'role_type' => RoleType::OPERATOR,
                'is_active' => true,
                'joined_at' => now(),
            ]);

            if ($isNewUser && $newPassword) {
                try {
                    Mail::to($user->email)->send(new OperatorCredentialMail($user, $school, $newPassword));
                } catch (\Exception $e) {
                    \Log::error('Gagal mengirim email kredensial operator: ' . $e->getMessage());
                }
            }

            return $this->success(
                new OperatorResource($schoolMember), 
                $isNewUser 
                    ? 'Operator Sekolah berhasil didaftarkan dan kredensial telah dikirim ke email.' 
                    : 'User berhasil ditambahkan sebagai Operator Sekolah di lembaga ini.',
                201
            );
        });
    }

    /**
     * Toggle active status of an operator in a school.
     *
     * @param Request $request
     * @param int $schoolId
     * @param int $operatorId
     * @return JsonResponse
     */
    public function toggleActive(Request $request, int $schoolId, int $operatorId): JsonResponse
    {
        $membership = $request->user()->schoolMemberships()
            ->where('school_id', $schoolId)
            ->first();

        if (!$membership || !$membership->isHeadmaster()) {
            return $this->error('Akses ditolak. Hanya Kepala Sekolah yang dapat mengubah status Operator Sekolah.', 403);
        }

        $operatorMember = SchoolMember::where('school_id', $schoolId)
            ->where('id', $operatorId)
            ->where('role_type', RoleType::OPERATOR)
            ->first();

        if (!$operatorMember) {
            return $this->error('Data Operator Sekolah tidak ditemukan.', 404);
        }

        $operatorMember->update([
            'is_active' => !$operatorMember->is_active
        ]);

        $statusText = $operatorMember->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return $this->success(new OperatorResource($operatorMember), "Operator Sekolah berhasil $statusText.");
    }

    /**
     * Remove an operator from school.
     *
     * @param Request $request
     * @param int $schoolId
     * @param int $operatorId
     * @return JsonResponse
     */
    public function destroy(Request $request, int $schoolId, int $operatorId): JsonResponse
    {
        $membership = $request->user()->schoolMemberships()
            ->where('school_id', $schoolId)
            ->first();

        if (!$membership || !$membership->isHeadmaster()) {
            return $this->error('Akses ditolak. Hanya Kepala Sekolah yang dapat menghapus Operator Sekolah.', 403);
        }

        $operatorMember = SchoolMember::where('school_id', $schoolId)
            ->where('id', $operatorId)
            ->where('role_type', RoleType::OPERATOR)
            ->first();

        if (!$operatorMember) {
            return $this->error('Data Operator Sekolah tidak ditemukan.', 404);
        }

        $operatorMember->delete();

        return $this->success(null, 'Operator Sekolah berhasil dihapus dari lembaga ini.');
    }
}

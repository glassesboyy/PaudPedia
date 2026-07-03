<?php

namespace App\Http\Controllers\Api\V1\School;

use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Requests\Api\V1\School\UpdateSchoolRequest;
use App\Http\Resources\Api\V1\School\SchoolResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SchoolController extends BaseController
{
    /**
     * Get all school memberships for the current authenticated user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function myMemberships(Request $request): JsonResponse
    {
        $user = $request->user()->load(['schoolMemberships.school']);

        return $this->success($user->schoolMemberships, 'Data keanggotaan sekolah berhasil diambil.');
    }

    /**
     * Display the specified school.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $membership = $request->user()->schoolMemberships()
            ->where('school_id', $id)
            ->first();

        if (!$membership) {
            return $this->error('Akses ditolak. Anda bukan anggota sekolah ini.', 403);
        }

        $school = $membership->school;

        return $this->success(new SchoolResource($school), 'Data profil sekolah berhasil diambil.');
    }

    /**
     * Update the specified school profile.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateSchoolRequest $request, int $id): JsonResponse
    {
        $membership = $request->user()->schoolMemberships()
            ->where('school_id', $id)
            ->first();

        if (!$membership || !$membership->isManager()) {
            return $this->error('Akses ditolak. Hanya Kepala Sekolah dan Operator yang dapat mengubah profil.', 403);
        }

        $school = $membership->school;

        $validated = $request->validated();

        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($school->logo_url && \Storage::disk('public')->exists($school->logo_url)) {
                \Storage::disk('public')->delete($school->logo_url);
            }

            $path = $request->file('logo')->store('schools/logos', 'public');
            $validated['logo_url'] = $path;
            unset($validated['logo']);
        }

        $school->update($validated);

        return $this->success(new SchoolResource($school), 'Profil sekolah berhasil diperbarui.');
    }
}

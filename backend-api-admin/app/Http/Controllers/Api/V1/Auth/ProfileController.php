<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Requests\Api\V1\Auth\UpdateProfileRequest;
use App\Http\Requests\Api\V1\Auth\UploadAvatarRequest;
use App\Http\Resources\Api\V1\Auth\UserResource;
use App\Services\Api\ProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends BaseController
{
    public function __construct(
        protected ProfileService $profileService
    ) {}

    /**
     * Get authenticated user profile.
     *
     * FR-UA-06: Edit profil pengguna (read)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function show(Request $request): JsonResponse
    {
        $user = $this->profileService->getProfile($request->user());

        return $this->success(new UserResource($user), 'Data profil berhasil diambil.');
    }

    /**
     * Update authenticated user profile data.
     *
     * FR-UA-06: Edit profil pengguna (nama, telepon, gender, tanggal lahir, alamat)
     *
     * @param UpdateProfileRequest $request
     * @return JsonResponse
     */
    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = $this->profileService->updateProfile(
            $request->user(),
            $request->validated()
        );

        return $this->success(new UserResource($user), 'Profil berhasil diperbarui.');
    }

    /**
     * Upload or replace user avatar.
     *
     * FR-UA-06: Edit profil pengguna (foto/avatar)
     *
     * @param UploadAvatarRequest $request
     * @return JsonResponse
     */
    public function uploadAvatar(UploadAvatarRequest $request): JsonResponse
    {
        $user = $this->profileService->updateAvatar(
            $request->user(),
            $request->file('avatar')
        );

        return $this->success(new UserResource($user), 'Avatar berhasil diperbarui.');
    }

    /**
     * Remove user avatar (reset to default).
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function destroyAvatar(Request $request): JsonResponse
    {
        $user = $this->profileService->removeAvatar($request->user());

        return $this->success(new UserResource($user), 'Avatar berhasil dihapus.');
    }
}

<?php

namespace App\Services\Api;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProfileService
{
    /**
     * Get user profile with roles loaded.
     *
     * @param User $user
     * @return User
     */
    public function getProfile(User $user): User
    {
        $user->load('roles');

        return $user;
    }

    /**
     * Update user profile data (text fields only).
     *
     * @param User $user
     * @param array $data
     * @return User
     */
    public function updateProfile(User $user, array $data): User
    {
        DB::beginTransaction();
        try {
            $user->update([
                'name' => $data['name'],
                'phone' => $data['phone'] ?? null,
                'gender' => $data['gender'] ?? null,
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'address' => $data['address'] ?? null,
            ]);

            DB::commit();

            return $user->fresh()->load('roles');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Upload or replace user avatar.
     *
     * Old avatar is automatically cleaned up by the User model boot method.
     *
     * @param User $user
     * @param UploadedFile $file
     * @return User
     */
    public function updateAvatar(User $user, UploadedFile $file): User
    {
        DB::beginTransaction();
        try {
            $path = $file->store('avatars', 'public');

            $user->update(['avatar_url' => $path]);

            DB::commit();

            return $user->fresh()->load('roles');
        } catch (\Exception $e) {
            DB::rollBack();

            // Clean up newly uploaded file on failure
            if (isset($path) && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }

            throw $e;
        }
    }

    /**
     * Remove user avatar (reset to default).
     *
     * Old avatar is automatically cleaned up by the User model boot method.
     *
     * @param User $user
     * @return User
     */
    public function removeAvatar(User $user): User
    {
        $user->update(['avatar_url' => null]);

        return $user->fresh()->load('roles');
    }
}

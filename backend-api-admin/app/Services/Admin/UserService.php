<?php

namespace App\Services\Admin;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserService
{
    /**
     * Create a new user
     *
     * @param array $data
     * @return User
     * @throws \Exception
     */
    public function createUser(array $data): User
    {
        DB::beginTransaction();
        try {
            // Handle avatar upload if exists
            if (isset($data['avatar_url']) && is_file($data['avatar_url'])) {
                $data['avatar_url'] = $data['avatar_url']->store('avatars', 'public');
            }

            // Hash password
            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }

            $user = User::create($data);

            // Assign roles if provided
            if (isset($data['roles'])) {
                $user->syncRoles($data['roles']);
            }

            DB::commit();

            return $user->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Delete uploaded avatar if exists
            if (isset($data['avatar_url']) && is_string($data['avatar_url'])) {
                Storage::disk('public')->delete($data['avatar_url']);
            }
            
            throw $e;
        }
    }

    /**
     * Update an existing user
     *
     * @param User $user
     * @param array $data
     * @return User
     * @throws \Exception
     */
    public function updateUser(User $user, array $data): User
    {
        DB::beginTransaction();
        try {
            $oldAvatarUrl = $user->avatar_url;

            // Handle avatar upload if exists
            if (isset($data['avatar_url']) && is_file($data['avatar_url'])) {
                // Delete old avatar
                if ($oldAvatarUrl) {
                    Storage::disk('public')->delete($oldAvatarUrl);
                }
                
                $data['avatar_url'] = $data['avatar_url']->store('avatars', 'public');
            }

            // Hash password if changed
            if (isset($data['password']) && !empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']); // Don't update if empty
            }

            $user->update($data);

            // Sync roles if provided
            if (isset($data['roles'])) {
                $user->syncRoles($data['roles']);
            }

            DB::commit();

            return $user->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Delete new uploaded avatar if exists
            if (isset($data['avatar_url']) && is_string($data['avatar_url']) && $data['avatar_url'] !== $oldAvatarUrl) {
                Storage::disk('public')->delete($data['avatar_url']);
            }
            
            throw $e;
        }
    }

    /**
     * Delete user
     *
     * @param User $user
     * @return bool
     * @throws \Exception
     */
    public function deleteUser(User $user): bool
    {
        DB::beginTransaction();
        try {
            $avatarUrl = $user->avatar_url;

            $user->delete();

            // Delete avatar if exists
            if ($avatarUrl) {
                Storage::disk('public')->delete($avatarUrl);
            }

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Toggle user active status
     *
     * @param User $user
     * @return User
     * @throws \Exception
     */
    public function toggleActiveStatus(User $user): User
    {
        DB::beginTransaction();
        try {
            $user->is_active = !$user->is_active;
            $user->save();

            DB::commit();

            return $user->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get user statistics
     *
     * @param User $user
     * @return array
     */
    public function getUserStatistics(User $user): array
    {
        return [
            'total_enrollments' => $user->enrollments()->count(),
            'total_orders' => $user->orders()->count(),
            'total_articles' => $user->articles()->count(),
            'is_teacher' => $user->isTeacher(),
            'is_parent' => $user->isParent(),
            'roles' => $user->getRoleNames()->toArray(),
        ];
    }
}

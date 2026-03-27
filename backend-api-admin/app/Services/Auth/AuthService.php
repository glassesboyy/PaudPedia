<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Models\School;
use App\Models\SchoolMember;
use App\Enums\RoleType;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AuthService
{
    /**
     * Register a new user.
     *
     * @param array $data
     * @return array
     */
    public function register(array $data): array
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'phone' => $data['phone'] ?? null,
            'is_active' => true,
        ]);

        // Assign default role for B2C users
        $user->assignRole('user');

        // Fire registered event (triggers email verification)
        try {
            event(new Registered($user));
        } catch (\Throwable $e) {
            Log::error('Failed to send verification email on registration', [
                'user_id' => $user->id,
                'email'   => $user->email,
                'error'   => $e->getMessage(),
            ]);
        }

        $user->load(['schoolMemberships.school:id,name,npsn', 'roles:id,name']);

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer',
        ];
    }

    /**
     * Register a new user and a new school (Headmaster onboarding).
     *
     * @param array $data
     * @return array
     */
    public function registerSchool(array $data): array
    {
        return DB::transaction(function () use ($data) {
            // 1. Create User
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'is_active' => true,
            ]);

            // Assign roles
            $user->assignRole(['user', 'headmaster']);

            // 2. Create School
            $school = School::create([
                'name' => $data['school_name'],
                'npsn' => $data['school_npsn'],
                'address' => $data['school_address'],
                'is_active' => true,
            ]);

            // 3. Create School Membership (Headmaster)
            SchoolMember::create([
                'school_id' => $school->id,
                'user_id' => $user->id,
                'role_type' => RoleType::HEADMASTER,
                'is_active' => true,
                'joined_at' => Carbon::now(),
            ]);

            try {
                event(new Registered($user));
            } catch (\Throwable $e) {
                Log::error('Failed to send verification email on school registration', [
                    'user_id' => $user->id,
                    'error'   => $e->getMessage(),
                ]);
            }

            $user->load(['schoolMemberships.school:id,name,npsn', 'roles:id,name']);

            $token = $user->createToken('auth_token')->plainTextToken;

            return [
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer',
                'school_id' => $school->id,
            ];
        });
    }

    /**
     * Register a new school for an existing authenticated user.
     *
     * @param User $user
     * @param array $data
     * @return School
     */
    public function registerSchoolUpgrade(User $user, array $data): array
    {
        return DB::transaction(function () use ($user, $data) {
            // 1. Create School
            $school = School::create([
                'name' => $data['school_name'],
                'npsn' => $data['school_npsn'],
                'address' => $data['school_address'],
                'is_active' => true,
            ]);

            // 2. Create School Membership (Headmaster)
            SchoolMember::create([
                'school_id' => $school->id,
                'user_id' => $user->id,
                'role_type' => RoleType::HEADMASTER,
                'is_active' => true,
                'joined_at' => Carbon::now(),
            ]);

            // 3. Assign role to user
            $user->assignRole('headmaster');

            // 4. Return user and new token for consistency
            $user->load(['schoolMemberships.school:id,name,npsn', 'roles:id,name']);
            $token = $user->createToken('auth_token')->plainTextToken;

            return [
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer',
                'school_id' => $school->id,
            ];
        });
    }

    /**
     * Login user with email and password.
     *
     * @param array $credentials
     * @return array|null
     */
    public function login(array $credentials): ?array
    {
        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return null;
        }

        if (!$user->is_active) {
            return ['error' => 'inactive'];
        }

        // Load memberships for initial state
        $user->load(['schoolMemberships.school:id,name,npsn', 'roles:id,name']);

        // Create new token
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer',
            'email_verified' => $user->hasVerifiedEmail(),
        ];
    }

    /**
     * Logout user (revoke current token).
     *
     * @param User $user
     * @param bool $allDevices
     * @return bool
     */
    public function logout(User $user, bool $allDevices = false): bool
    {
        if ($allDevices) {
            // Revoke all tokens
            $user->tokens()->delete();
        } else {
            // Revoke current token
            /** @var \Laravel\Sanctum\PersonalAccessToken|null $token */
            $token = $user->currentAccessToken();
            if ($token) {
                $token->delete();
            }
        }

        return true;
    }

    /**
     * Verify user email with signed URL hash.
     *
     * @param User $user
     * @param string $hash
     * @return bool
     */
    public function verifyEmail(User $user, string $hash): bool
    {
        // Check if hash matches user email
        if (!hash_equals(sha1($user->getEmailForVerification()), $hash)) {
            return false;
        }

        if ($user->hasVerifiedEmail()) {
            return true; // Already verified
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return true;
    }

    /**
     * Resend email verification notification.
     *
     * @param User $user
     * @return bool
     */
    public function resendVerificationEmail(User $user): bool
    {
        if ($user->hasVerifiedEmail()) {
            return false; // Already verified
        }

        try {
            $user->sendEmailVerificationNotification();
        } catch (\Throwable $e) {
            Log::error('Failed to resend verification email', [
                'user_id' => $user->id,
                'email'   => $user->email,
                'error'   => $e->getMessage(),
            ]);

            return false;
        }

        return true;
    }

    /**
     * Send password reset link to user email.
     *
     * @param string $email
     * @return string
     */
    public function sendPasswordResetLink(string $email): string
    {
        try {
            $status = Password::sendResetLink(['email' => $email]);
        } catch (\Throwable $e) {
            Log::error('Failed to send password reset link', [
                'email' => $email,
                'error' => $e->getMessage(),
            ]);

            return 'fail';
        }

        return $status;
    }

    /**
     * Reset user password with token.
     *
     * @param array $data
     * @return string
     */
    public function resetPassword(array $data): string
    {
        $status = Password::reset(
            [
                'email' => $data['email'],
                'password' => $data['password'],
                'password_confirmation' => $data['password_confirmation'],
                'token' => $data['token'],
            ],
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        return $status;
    }

    /**
     * Change user password.
     *
     * @param User $user
     * @param string $currentPassword
     * @param string $newPassword
     * @return bool
     */
    public function changePassword(User $user, string $currentPassword, string $newPassword): bool
    {
        if (!Hash::check($currentPassword, $user->password)) {
            return false;
        }

        $user->update([
            'password' => Hash::make($newPassword),
        ]);

        // Optional: Revoke all other tokens for security
        // $user->tokens()->where('id', '!=', $user->currentAccessToken()->id)->delete();

        return true;
    }

    /**
     * Get authenticated user with relationships.
     *
     * @param User $user
     * @return User
     */
    public function getAuthenticatedUser(User $user): User
    {
        return $user->load([
            'roles:id,name',
            'schoolMemberships.school:id,name,npsn'
        ]);
    }
}

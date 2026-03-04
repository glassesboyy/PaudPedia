<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Requests\Api\V1\Auth\ChangePasswordRequest;
use App\Http\Requests\Api\V1\Auth\ForgotPasswordRequest;
use App\Http\Requests\Api\V1\Auth\LoginRequest;
use App\Http\Requests\Api\V1\Auth\RegisterRequest;
use App\Http\Requests\Api\V1\Auth\ResetPasswordRequest;
use App\Http\Resources\Api\V1\Auth\UserResource;
use App\Models\User;
use App\Services\Auth\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class AuthController extends BaseController
{
    public function __construct(
        protected AuthService $authService
    ) {}

    /**
     * Register a new user.
     * 
     * FR-UA-01: Pendaftaran pengguna (email & password)
     * 
     * @unauthenticated
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->authService->register($request->validated());

        return $this->created([
            'user' => new UserResource($result['user']),
            'token' => $result['token'],
            'token_type' => $result['token_type'],
        ], 'Registrasi berhasil. Silakan cek email untuk verifikasi.');
    }

    /**
     * Login user with email and password.
     * 
     * FR-UA-02: Login pengguna (email & password)
     * 
     * @unauthenticated
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login($request->validated());

        if ($result === null) {
            return $this->error('Email atau password salah.', 401);
        }

        if (isset($result['error']) && $result['error'] === 'inactive') {
            return $this->error('Akun Anda tidak aktif. Silakan hubungi admin.', 403);
        }

        return $this->success([
            'user' => new UserResource($result['user']),
            'token' => $result['token'],
            'token_type' => $result['token_type'],
            'email_verified' => $result['email_verified'],
        ], 'Login berhasil.');
    }

    /**
     * Logout user (revoke token).
     * 
     * FR-UA-06: Logout pengguna
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $allDevices = $request->boolean('all_devices', false);
        
        $this->authService->logout($request->user(), $allDevices);

        return $this->success(null, 'Logout berhasil.');
    }

    /**
     * Get authenticated user profile.
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function me(Request $request): JsonResponse
    {
        $user = $this->authService->getAuthenticatedUser($request->user());

        return $this->success(new UserResource($user), 'Data user berhasil diambil.');
    }

    /**
     * Verify email with hash.
     * 
     * FR-UA-03: Verifikasi email setelah registrasi
     * 
     * @unauthenticated
     * @param Request $request
     * @param int $id
     * @param string $hash
     * @return JsonResponse
     */
    public function verifyEmail(Request $request, int $id, string $hash): JsonResponse
    {
        $user = User::findOrFail($id);

        // Validate expiration manually (more robust for SPA flows where
        // the signed URL was generated for a different host/port).
        $expires = (int) $request->query('expires', 0);
        if ($expires && $expires < time()) {
            return $this->error('Link verifikasi sudah kedaluwarsa.', 403);
        }

        $verified = $this->authService->verifyEmail($user, $hash);

        if (!$verified) {
            return $this->error('Verifikasi email gagal.', 400);
        }

        return $this->success(null, 'Email berhasil diverifikasi.');
    }

    /**
     * Resend verification email.
     * 
     * FR-UA-03: Verifikasi email setelah registrasi (resend)
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function resendVerificationEmail(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return $this->error('Email sudah terverifikasi.', 400);
        }

        $this->authService->resendVerificationEmail($user);

        return $this->success(null, 'Email verifikasi berhasil dikirim ulang.');
    }

    /**
     * Send password reset link.
     * 
     * FR-UA-04: Reset kata sandi (forgot password)
     * 
     * @unauthenticated
     * @param ForgotPasswordRequest $request
     * @return JsonResponse
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $status = $this->authService->sendPasswordResetLink($request->email);

        if ($status === Password::RESET_LINK_SENT) {
            return $this->success(null, 'Link reset password telah dikirim ke email Anda.');
        }

        return $this->error('Gagal mengirim link reset password.', 400);
    }

    /**
     * Reset password with token.
     * 
     * FR-UA-04: Reset kata sandi (forgot password)
     * 
     * @unauthenticated
     * @param ResetPasswordRequest $request
     * @return JsonResponse
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $status = $this->authService->resetPassword($request->validated());

        if ($status === Password::PASSWORD_RESET) {
            return $this->success(null, 'Password berhasil direset. Silakan login dengan password baru.');
        }

        $messages = [
            Password::INVALID_TOKEN => 'Token reset password tidak valid atau sudah kadaluarsa.',
            Password::INVALID_USER => 'Email tidak ditemukan.',
        ];

        return $this->error($messages[$status] ?? 'Gagal mereset password.', 400);
    }

    /**
     * Change user password.
     * 
     * FR-UA-05: Ubah password
     * 
     * @param ChangePasswordRequest $request
     * @return JsonResponse
     */
    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $changed = $this->authService->changePassword(
            $request->user(),
            $request->current_password,
            $request->password
        );

        if (!$changed) {
            return $this->error('Password saat ini tidak sesuai.', 400);
        }

        return $this->success(null, 'Password berhasil diubah.');
    }
}

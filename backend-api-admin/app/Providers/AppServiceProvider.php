<?php

namespace App\Providers;

use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ── Email Verification: generate frontend URLs ─────────────
        // Build the verification URL pointing directly to the frontend app.
        // The frontend page /auth/verify-email/{id}/{hash} will then call the
        // backend API to actually verify the email.
        VerifyEmail::createUrlUsing(function (object $notifiable) {
            $frontendUrl = rtrim(Config::get('app.frontend_url', 'http://localhost:3000'), '/');

            // Generate the signed URL so we can extract expires & signature params
            $verifyUrl = URL::temporarySignedRoute(
                'api.v1.auth.verification.verify',
                Carbon::now()->addMinutes(60),
                [
                    'id' => $notifiable->getKey(),
                    'hash' => sha1($notifiable->getEmailForVerification()),
                ],
            );

            // Parse the query string (expires, signature) from the signed URL
            $parsed = parse_url($verifyUrl);
            $queryString = $parsed['query'] ?? '';

            // Build the frontend URL directly (no str_replace needed)
            return sprintf(
                '%s/auth/verify-email/%s/%s?%s',
                $frontendUrl,
                $notifiable->getKey(),
                sha1($notifiable->getEmailForVerification()),
                $queryString,
            );
        });

        // ── Email Verification: customise the mail ─────────────────
        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            return (new MailMessage)
                ->subject('Verifikasi Email Anda — ' . Config::get('app.name'))
                ->greeting('Halo ' . $notifiable->name . '!')
                ->line('Terima kasih telah mendaftar di ' . Config::get('app.name') . '. Silakan klik tombol di bawah untuk memverifikasi alamat email Anda.')
                ->action('Verifikasi Email', $url)
                ->line('Link ini akan kedaluwarsa dalam 60 menit.')
                ->line('Jika Anda tidak membuat akun, abaikan email ini.')
                ->salutation('Salam, Tim ' . Config::get('app.name'));
        });

        Event::listen(Failed::class, function (Failed $event) {
            if ($event->user && ! $event->user->is_active) {
                Log::warning('inactive user login attempt', [
                    'user_id' => $event->user->id,
                    'email' => $event->user->email,
                ]);
            }
        });

        // Configure Scramble API Documentation Authentication
        Scramble::configure()
            ->withDocumentTransformers(function (OpenApi $openApi) {
                $openApi->secure(
                    SecurityScheme::http('bearer', 'sanctum')
                );
            });
    }
}

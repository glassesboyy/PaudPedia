<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application.
| These routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group.
|
*/

/*
|--------------------------------------------------------------------------
| Public API Routes (v1)
|--------------------------------------------------------------------------
*/
Route::prefix('v1')->name('api.v1.')->group(function () {
    // Public routes (no authentication required)
    require __DIR__ . '/api/v1/public.php';

    // Auth routes (register, login, logout, password reset, etc.)
    require __DIR__ . '/api/v1/auth.php';

    // User dashboard routes (authenticated)
    require __DIR__ . '/api/v1/user.php';

    // Webhook routes (no authentication — called by payment gateway)
    Route::prefix('webhooks')->name('webhooks.')->group(function () {
        Route::post('/midtrans', [\App\Http\Controllers\Api\V1\Webhook\MidtransController::class, 'handle'])
            ->name('midtrans');
    });
});

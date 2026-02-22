<?php

use Illuminate\Http\Request;
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

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

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

    // Authenticated routes (protected by Sanctum)
    // Route::middleware('auth:sanctum')->group(function () {
    //     require __DIR__ . '/api/v1/user.php';
    // });
});

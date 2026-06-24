<?php

use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Auth\ProfileController;
use App\Http\Controllers\Api\V1\Public\TestimonialController;
use App\Http\Controllers\Api\V1\School\SchoolController;
use App\Http\Controllers\Api\V1\School\TeacherController;
use App\Http\Controllers\Api\V1\School\ClassRoomController;
use App\Http\Controllers\Api\V1\School\ParentProfileController;
use App\Http\Controllers\Api\V1\School\StudentController;
use App\Http\Controllers\Api\V1\School\AttendanceController;
use App\Http\Controllers\Api\V1\School\AssessmentController;
use App\Http\Controllers\Api\V1\School\SubscriptionController;
use App\Http\Controllers\Api\V1\School\FinanceController;
use App\Http\Controllers\Api\V1\School\ReportController;
use App\Http\Controllers\Api\V1\School\DashboardController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Auth API Routes (v1)
|--------------------------------------------------------------------------
|
| Authentication routes for B2C users.
| Prefix: /api/v1/auth
|
*/

// Guest routes (no authentication required)
Route::prefix('auth')->name('auth.')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/register-school', [AuthController::class, 'registerSchool'])->name('register.school');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.email');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
    Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
        ->middleware(['throttle:6,1'])
        ->name('verification.verify');
});

// Authenticated routes (verification not required)
Route::prefix('auth')->name('auth.')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/me', [AuthController::class, 'me'])->name('me'); 
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Authenticated & Verified routes
Route::prefix('auth')->name('auth.')->middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::post('/change-password', [AuthController::class, 'changePassword'])->name('password.change');
    
    // Profile management (FR-UA-06)
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [ProfileController::class, 'uploadAvatar'])->name('profile.avatar.upload');
    Route::delete('/profile/avatar', [ProfileController::class, 'destroyAvatar'])->name('profile.avatar.destroy');
});

// Resend verification email (requires auth but NOT verified!)
Route::prefix('auth')->name('auth.')->middleware('auth:sanctum')->group(function () {
    Route::post('/email/verification-notification', [AuthController::class, 'resendVerificationEmail'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
});

// Other authenticated routes (outside auth prefix)
Route::middleware([
    'auth:sanctum', 
    'verified', 
    \App\Http\Middleware\CheckActiveSchoolMember::class,
    \App\Http\Middleware\CheckSchoolLimits::class
])->group(function () {
    // School registration (authenticated upgrade)
    Route::post('/schools/register', [AuthController::class, 'registerSchoolUpgrade'])->name('schools.register');

    // School profile management
    Route::get('/schools/{id}', [SchoolController::class, 'show'])->name('schools.show');
    Route::put('/schools/{id}', [SchoolController::class, 'update'])->name('schools.update');

    // Teacher management
    Route::get('/schools/{id}/teachers', [TeacherController::class, 'index'])->name('teachers.index');
    Route::post('/schools/{id}/teachers', [TeacherController::class, 'store'])->name('teachers.store');
    Route::get('/schools/{id}/teachers/{teacherId}', [TeacherController::class, 'show'])->name('teachers.show');
    Route::patch('/schools/{id}/teachers/{teacherId}/toggle-active', [TeacherController::class, 'toggleActive'])->name('teachers.toggle-active');
    Route::delete('/schools/{id}/teachers/{teacherId}', [TeacherController::class, 'destroy'])->name('teachers.destroy');

    // Class management
    Route::get('/schools/{id}/classes', [ClassRoomController::class, 'index'])->name('classes.index');
    Route::post('/schools/{id}/classes', [ClassRoomController::class, 'store'])->name('classes.store');
    Route::get('/schools/{id}/classes/{classId}', [ClassRoomController::class, 'show'])->name('classes.show');
    Route::put('/schools/{id}/classes/{classId}', [ClassRoomController::class, 'update'])->name('classes.update');
    Route::delete('/schools/{id}/classes/{classId}', [ClassRoomController::class, 'destroy'])->name('classes.destroy');

    // Parent management
    Route::get('/schools/{id}/parents', [ParentProfileController::class, 'index'])->name('parents.index');
    Route::post('/schools/{id}/parents', [ParentProfileController::class, 'store'])->name('parents.store');
    Route::get('/schools/{id}/parents/{parentId}', [ParentProfileController::class, 'show'])->name('parents.show');
    Route::put('/schools/{id}/parents/{parentId}', [ParentProfileController::class, 'update'])->name('parents.update');
    Route::delete('/schools/{id}/parents/{parentId}', [ParentProfileController::class, 'destroy'])->name('parents.destroy');

    // Student management
    Route::get('/schools/{id}/students', [StudentController::class, 'index'])->name('students.index');
    Route::post('/schools/{id}/students', [StudentController::class, 'store'])->name('students.store');
    Route::get('/schools/{id}/students/{studentId}', [StudentController::class, 'show'])->name('students.show');
    Route::put('/schools/{id}/students/{studentId}', [StudentController::class, 'update'])->name('students.update');
    Route::delete('/schools/{id}/students/{studentId}', [StudentController::class, 'destroy'])->name('students.destroy');

    // Attendance management
    Route::get('/schools/{id}/classes/{classId}/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/schools/{id}/classes/{classId}/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
    Route::get('/schools/{id}/students/{studentId}/attendance/summary', [AttendanceController::class, 'studentSummary'])->name('attendance.summary');

    // Assessment management
    Route::get('/schools/{id}/classes/{classId}/assessments', [AssessmentController::class, 'index'])->name('assessments.index');
    Route::post('/schools/{id}/classes/{classId}/assessments', [AssessmentController::class, 'store'])->name('assessments.store');
    Route::get('/schools/{id}/students/{studentId}/assessments/history', [AssessmentController::class, 'studentHistory'])->name('assessments.history');
    Route::get('/schools/{id}/students/{studentId}/assessments/matrix', [AssessmentController::class, 'studentMatrix'])->name('assessments.matrix');

    // Report / Raport Narrative Management
    Route::get('/schools/{id}/classes/{classId}/students/{studentId}/report', [\App\Http\Controllers\Api\V1\School\StudentReportController::class, 'show'])->name('student-reports.show');
    Route::post('/schools/{id}/classes/{classId}/students/{studentId}/report', [\App\Http\Controllers\Api\V1\School\StudentReportController::class, 'store'])->name('student-reports.store');

    // Assessment Settings (Programs & Indicators)
    Route::get('/schools/{id}/development-programs', [\App\Http\Controllers\Api\V1\School\AssessmentSettingController::class, 'getPrograms'])->name('assessments.settings.programs');
    Route::post('/schools/{id}/development-programs', [\App\Http\Controllers\Api\V1\School\AssessmentSettingController::class, 'storeProgram'])->name('assessments.settings.programs.store');
    Route::put('/schools/{id}/development-programs/{programId}', [\App\Http\Controllers\Api\V1\School\AssessmentSettingController::class, 'updateProgram'])->name('assessments.settings.programs.update');
    Route::delete('/schools/{id}/development-programs/{programId}', [\App\Http\Controllers\Api\V1\School\AssessmentSettingController::class, 'destroyProgram'])->name('assessments.settings.programs.destroy');
    Route::post('/schools/{id}/development-programs/{programId}/indicators', [\App\Http\Controllers\Api\V1\School\AssessmentSettingController::class, 'storeIndicator'])->name('assessments.settings.indicators.store');
    Route::put('/schools/{id}/development-indicators/{indicatorId}', [\App\Http\Controllers\Api\V1\School\AssessmentSettingController::class, 'updateIndicator'])->name('assessments.settings.indicators.update');
    Route::delete('/schools/{id}/development-indicators/{indicatorId}', [\App\Http\Controllers\Api\V1\School\AssessmentSettingController::class, 'destroyIndicator'])->name('assessments.settings.indicators.destroy');

    // Subscription management (FR-SM)
    Route::get('/schools/{id}/subscription', [SubscriptionController::class, 'show'])->name('subscription.show');
    Route::post('/schools/{id}/subscription/upgrade', [SubscriptionController::class, 'upgrade'])->name('subscription.upgrade');
    Route::get('/schools/{id}/subscription/payment-history', [SubscriptionController::class, 'paymentHistory'])->name('subscription.payments');

    // Finance management (FR-FN) — Pro Plan only
    Route::get('/schools/{id}/dashboard/headmaster', [DashboardController::class, 'headmasterSummary'])->name('dashboard.headmaster');
    Route::get('/schools/{id}/finances/spp', [FinanceController::class, 'sppIndex'])->name('finances.spp.index');
    Route::post('/schools/{id}/finances/spp/batch', [FinanceController::class, 'sppBatchStore'])->name('finances.spp.batch');
    Route::post('/schools/{id}/finances/spp', [FinanceController::class, 'sppStore'])->name('finances.spp.store');
    Route::put('/schools/{id}/finances/spp/{financeId}', [FinanceController::class, 'sppUpdate'])->name('finances.spp.update');
    Route::get('/schools/{id}/finances/savings', [FinanceController::class, 'savingsIndex'])->name('finances.savings.index');
    Route::post('/schools/{id}/finances/savings', [FinanceController::class, 'savingsStore'])->name('finances.savings.store');
    Route::get('/schools/{id}/students/{studentId}/finances', [FinanceController::class, 'studentFinances'])->name('finances.student');

    // Report / Raport management (FR-RP) — Pro Plan only
    // Assessment & Report Export (FR-SA-04)
    Route::get('/schools/{id}/reports/students/{studentId}/data', [ReportController::class, 'reportData'])->name('reports.data');
    Route::get('/schools/{id}/reports/students/{studentId}/pdf', [ReportController::class, 'downloadPdf'])->name('reports.pdf');
    Route::get('/schools/{id}/classes/{classId}/reports-status', [ReportController::class, 'statusList'])->name('reports.status');

    // School memberships
    Route::get('/my-memberships', [SchoolController::class, 'myMemberships'])
        ->name('my-memberships');

    // Testimonials - Submit testimonial (requires authentication)
    Route::post('/testimonials', [TestimonialController::class, 'store'])
        ->name('testimonials.store');
});

// Super Admin routes
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::apiResource('admin/development-programs', \App\Http\Controllers\Api\V1\Admin\DevelopmentProgramController::class);
    Route::apiResource('admin/development-indicators', \App\Http\Controllers\Api\V1\Admin\DevelopmentIndicatorController::class)->only(['store', 'update', 'destroy']);
});

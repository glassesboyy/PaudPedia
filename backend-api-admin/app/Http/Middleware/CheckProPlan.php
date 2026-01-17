<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Check if school has Pro Plan for premium features
 * 
 * Usage in routes:
 * Route::get('/finance', [FinanceController::class, 'index'])
 *     ->middleware('pro.plan');
 */
class CheckProPlan
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        if (!$user) {
            abort(401, 'Unauthenticated.');
        }

        // Get school from request or user's active school
        $schoolId = $request->route('school_id') ?? $request->input('school_id');
        
        if (!$schoolId) {
            // Get user's first school membership
            $membership = $user->schoolMemberships()->first();
            $schoolId = $membership?->school_id;
        }

        if (!$schoolId) {
            abort(403, 'No school associated with this account.');
        }

        $school = \App\Models\School::find($schoolId);

        if (!$school || !$school->isPro()) {
            abort(403, 'This feature requires Pro Plan subscription. Please upgrade your plan.');
        }

        return $next($request);
    }
}

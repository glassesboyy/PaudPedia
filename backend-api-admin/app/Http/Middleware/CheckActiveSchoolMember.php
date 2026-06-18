<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckActiveSchoolMember
{
    /**
     * Handle an incoming request.
     * Ensure the authenticated user is an ACTIVE member of the school.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $schoolId = $request->route('id') ?? $request->route('schoolId');

        if (!$user || !$schoolId) {
            return $next($request);
        }

        $membership = $user->schoolMemberships()
            ->where('school_id', $schoolId)
            ->first();

        if (!$membership) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Anda tidak memiliki akses ke sekolah ini.'
            ], 403);
        }

        if (!$membership->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Akun Anda telah dinonaktifkan dari sekolah ini.'
            ], 403);
        }

        // Add the active membership to the request for easy access in controllers
        $request->merge(['active_membership' => $membership]);

        return $next($request);
    }
}

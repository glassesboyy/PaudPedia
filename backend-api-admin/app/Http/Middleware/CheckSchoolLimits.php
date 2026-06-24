<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Models\School;
use App\Services\Setting\SiteSettingService;

class CheckSchoolLimits
{
    public function __construct(protected SiteSettingService $siteSettingService) {}

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only apply to non-GET requests (Soft Restriction / Core Lock)
        if ($request->isMethod('GET')) {
            return $next($request);
        }

        // Exclude specific routes that should always be allowed (e.g. upgrading subscription, profile)
        if ($request->routeIs('api.v1.auth.subscription.upgrade') || 
            $request->routeIs('api.v1.auth.schools.register') ||
            $request->routeIs('api.v1.auth.profile.*') ||
            $request->routeIs('api.v1.auth.logout')) {
            return $next($request);
        }

        // Get the school from the route parameter. The parameter is usually 'id' for schools.
        // E.g. /api/v1/auth/schools/{id}/...
        $schoolId = $request->route('id');
        if (!$schoolId) {
            return $next($request);
        }

        $school = School::find($schoolId);
        if (!$school) {
            return $next($request);
        }

        if ($school->isPro()) {
            return $next($request);
        }

        // Free plan logic
        $freeMaxStudents = (int) $this->siteSettingService->getSetting('free_max_students', 20);
        $freeMaxTeachers = (int) $this->siteSettingService->getSetting('free_max_teachers', 5);

        $studentUsage = $school->total_students;
        $teacherUsage = $school->total_teachers;

        if ($studentUsage > $freeMaxStudents || $teacherUsage > $freeMaxTeachers) {
            // Check if user is trying to delete a resource (allow deletion to get under limit)
            if ($request->isMethod('DELETE')) {
                return $next($request);
            }
            
            // Check if user is toggling active status to deactivate (allow to get under limit)
            // It's a bit complex, but generally deleting is allowed.
            
            return response()->json([
                'message' => 'Akses Terkunci: Jumlah data Siswa (' . $studentUsage . '/' . $freeMaxStudents . ') atau Guru (' . $teacherUsage . '/' . $freeMaxTeachers . ') melebihi batas Paket Gratis. Silakan hapus data atau Upgrade ke Pro.'
            ], 403);
        }

        // If not over limit, check if they are trying to create new ones and would exceed limit
        if ($request->isMethod('POST')) {
            if ($request->routeIs('api.v1.auth.students.store') && $studentUsage >= $freeMaxStudents) {
                return response()->json(['message' => 'Kapasitas Siswa penuh. Upgrade ke Pro untuk menambah siswa.'], 403);
            }
            if ($request->routeIs('api.v1.auth.teachers.store') && $teacherUsage >= $freeMaxTeachers) {
                return response()->json(['message' => 'Kapasitas Guru penuh. Upgrade ke Pro untuk menambah guru.'], 403);
            }
        }

        return $next($request);
    }
}

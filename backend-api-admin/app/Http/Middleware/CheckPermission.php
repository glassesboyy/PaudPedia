<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Check if user has required permission
 * 
 * Usage in routes:
 * Route::get('/students', [StudentController::class, 'index'])
 *     ->middleware('permission:view students');
 * 
 * Usage in Controller:
 * $this->authorize('create students');
 * 
 * Usage in Blade/View:
 * @can('edit students')
 *     <button>Edit</button>
 * @endcan
 */
class CheckPermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (!$request->user() || !$request->user()->can($permission)) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}

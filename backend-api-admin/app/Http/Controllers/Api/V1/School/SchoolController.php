<?php

namespace App\Http\Controllers\Api\V1\School;

use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Resources\Api\V1\Auth\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SchoolController extends BaseController
{
    /**
     * Get all school memberships for the current authenticated user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function myMemberships(Request $request): JsonResponse
    {
        $user = $request->user()->load(['schoolMemberships.school']);

        // We can reuse UserResource or just return the memberships
        return $this->success($user->schoolMemberships, 'Data keanggotaan sekolah berhasil diambil.');
    }
}

<?php

namespace App\Http\Controllers\Api\V1\School;

use App\Models\School;
use App\Models\User;
use App\Models\SchoolMember;
use App\Models\SchoolTransferRequest;
use App\Mail\HeadmasterTransferMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Enums\RoleType;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransferController extends Controller
{
    /**
     * Initiate a transfer request (Headmaster only)
     */
    public function initiate(Request $request, int $schoolId): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        $membership = $request->user()->schoolMemberships()
            ->where('school_id', $schoolId)
            ->first();

        if (!$membership || !$membership->isHeadmaster()) {
            return response()->json(['message' => 'Unauthorized. Only headmaster can transfer ownership.'], 403);
        }

        $school = $membership->school;
        $targetEmail = $request->email;
        $targetUser = User::where('email', $targetEmail)->first();

        if ($targetUser->id === $request->user()->id) {
            return response()->json(['message' => 'You cannot transfer ownership to yourself.'], 422);
        }

        // Fresh User Validation
        $existingMembership = SchoolMember::where('school_id', $schoolId)
            ->where('user_id', $targetUser->id)
            ->first();

        if ($existingMembership) {
            return response()->json([
                'message' => 'Pengguna ini sudah terdaftar di sekolah ini. Transfer hanya dapat dilakukan ke akun yang belum terafiliasi dengan sekolah Anda.'
            ], 422);
        }

        // Cancel previous pending requests for this school
        SchoolTransferRequest::where('school_id', $schoolId)
            ->where('status', 'pending')
            ->update(['status' => 'rejected']);

        $transferRequest = SchoolTransferRequest::create([
            'school_id' => $schoolId,
            'from_user_id' => $request->user()->id,
            'to_email' => $targetEmail,
            'token' => Str::random(64),
            'status' => 'pending',
            'expired_at' => now()->addDays(3),
        ]);

        Mail::to($targetEmail)->send(new HeadmasterTransferMail($transferRequest));

        return response()->json([
            'message' => 'Undangan transfer kepemilikan berhasil dikirim ke ' . $targetEmail,
        ]);
    }

    /**
     * Get transfer request details by token
     */
    public function show(string $token): JsonResponse
    {
        $transferRequest = SchoolTransferRequest::where('token', $token)
            ->with(['school', 'fromUser'])
            ->first();

        if (!$transferRequest) {
            return response()->json(['message' => 'Invalid or expired token.'], 404);
        }

        return response()->json($transferRequest);
    }

    /**
     * Accept a transfer request
     */
    public function accept(Request $request, string $token): JsonResponse
    {
        $transferRequest = SchoolTransferRequest::where('token', $token)
            ->where('status', 'pending')
            ->where('expired_at', '>', now())
            ->first();

        if (!$transferRequest) {
            return response()->json(['message' => 'Invalid or expired token.'], 404);
        }

        $user = $request->user();
        if ($user->email !== $transferRequest->to_email) {
            return response()->json(['message' => 'Anda tidak berhak menerima undangan ini.'], 403);
        }

        DB::beginTransaction();
        try {
            // 1. Upgrade User B to Headmaster
            $newMembership = new SchoolMember([
                'school_id' => $transferRequest->school_id,
                'user_id' => $user->id,
            ]);
            $newMembership->role_type = RoleType::HEADMASTER;
            $newMembership->is_active = true;
            $newMembership->save();

            // 2. Downgrade User A to Inactive Teacher
            SchoolMember::where('school_id', $transferRequest->school_id)
                ->where('user_id', $transferRequest->from_user_id)
                ->update([
                    'role_type' => RoleType::TEACHER,
                    'is_active' => false
                ]);

            // 3. Mark request as completed
            $transferRequest->update(['status' => 'completed']);

            // 4. Invalidate tokens for User A
            // We can revoke their PATs so they are logged out.
            $oldHeadmaster = User::find($transferRequest->from_user_id);
            if ($oldHeadmaster) {
                $oldHeadmaster->tokens()->delete();
            }

            DB::commit();

            return response()->json(['message' => 'Anda berhasil menjadi Kepala Sekolah baru!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Terjadi kesalahan sistem.'], 500);
        }
    }

    /**
     * Reject a transfer request
     */
    public function reject(Request $request, string $token): JsonResponse
    {
        $transferRequest = SchoolTransferRequest::where('token', $token)
            ->where('status', 'pending')
            ->first();

        if (!$transferRequest) {
            return response()->json(['message' => 'Invalid token.'], 404);
        }

        $user = $request->user();
        if ($user->email !== $transferRequest->to_email) {
            return response()->json(['message' => 'Anda tidak berhak menolak undangan ini.'], 403);
        }

        $transferRequest->update(['status' => 'rejected']);

        return response()->json(['message' => 'Undangan berhasil ditolak.']);
    }
}

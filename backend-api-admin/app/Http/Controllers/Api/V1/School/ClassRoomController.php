<?php

namespace App\Http\Controllers\Api\V1\School;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\School\StoreClassRoomRequest;
use App\Http\Requests\Api\V1\School\UpdateClassRoomRequest;
use App\Http\Resources\Api\V1\School\ClassRoomResource;
use App\Models\ClassRoom;
use App\Models\School;
use Illuminate\Http\Request;

class ClassRoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, $id)
    {
        // $id is the school_id
        $school = School::findOrFail($id);
        
        $membership = $request->user()->schoolMemberships()
            ->where('school_id', $school->id)
            ->first();

        if (!$membership) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $query = ClassRoom::where('school_id', $school->id)
            ->with(['homeroomTeacher.user']);

        // Teacher HANYA boleh melihat kelas miliknya (homeroom)
        if ($membership->isTeacher()) {
            $query->whereHas('homeroomTeacher', function ($q) use ($request) {
                $q->where('user_id', $request->user()->id);
            });
        } elseif ($request->has('teacher_user_id')) {
            // For headmaster to filter by specific teacher
            $query->whereHas('homeroomTeacher', function ($q) use ($request) {
                $q->where('user_id', $request->get('teacher_user_id'));
            });
        }

        $classes = $query->latest()
            ->paginate($request->get('per_page', 10));

        return ClassRoomResource::collection($classes);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClassRoomRequest $request, $id)
    {
        $school = School::findOrFail($id);
        
        $membership = $request->user()->schoolMemberships()
            ->where('school_id', $school->id)
            ->first();

        if (!$membership || !$membership->isHeadmaster()) {
            return response()->json(['message' => 'Hanya Kepala Sekolah yang dapat menambah kelas baru.'], 403);
        }

        $class = ClassRoom::create(array_merge(
            $request->validated(),
            ['school_id' => $school->id]
        ));

        return new ClassRoomResource($class);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id, $classId)
    {
        $membership = $request->user()->schoolMemberships()
            ->where('school_id', $id)
            ->first();

        $class = ClassRoom::where('school_id', $id)
            ->where('id', $classId)
            ->with(['homeroomTeacher.user', 'students'])
            ->firstOrFail();

        // Teacher HANYA boleh melihat detail kelas miliknya
        if ($membership && $membership->isTeacher()) {
            if (!$class->homeroomTeacher || $class->homeroomTeacher->user_id !== $request->user()->id) {
                return response()->json(['message' => 'Akses ditolak. Anda hanya dapat melihat detail kelas Anda sendiri.'], 403);
            }
        }

        return new ClassRoomResource($class);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClassRoomRequest $request, $id, $classId)
    {
        $school = School::findOrFail($id);
        
        $membership = $request->user()->schoolMemberships()
            ->where('school_id', $school->id)
            ->first();

        if (!$membership || !$membership->isHeadmaster()) {
            return response()->json(['message' => 'Hanya Kepala Sekolah yang dapat memperbarui data kelas.'], 403);
        }

        $class = ClassRoom::where('school_id', $id)
            ->where('id', $classId)
            ->firstOrFail();

        $class->update($request->validated());

        // Refresh to get potentially updated relations
        $class->refresh();

        return new ClassRoomResource($class);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id, $classId)
    {
        $school = School::findOrFail($id);
        
        $membership = $request->user()->schoolMemberships()
            ->where('school_id', $school->id)
            ->first();

        if (!$membership || !$membership->isHeadmaster()) {
            return response()->json(['message' => 'Hanya Kepala Sekolah yang dapat menghapus kelas.'], 403);
        }

        $class = ClassRoom::where('school_id', $id)
            ->where('id', $classId)
            ->firstOrFail();

        // Check if there are students in this class before deleting
        if ($class->students()->exists()) {
            return response()->json([
                'message' => 'Tidak dapat menghapus kelas karena masih memiliki siswa terdaftar.'
            ], 422);
        }

        $class->delete();

        return response()->json(null, 204);
    }
}

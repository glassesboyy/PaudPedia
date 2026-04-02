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
        // Authorization check if needed...

        $classes = ClassRoom::where('school_id', $school->id)
            ->with(['homeroomTeacher.user'])
            ->latest()
            ->paginate($request->get('per_page', 10));

        return ClassRoomResource::collection($classes);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClassRoomRequest $request, $id)
    {
        $school = School::findOrFail($id);
        
        $class = ClassRoom::create(array_merge(
            $request->validated(),
            ['school_id' => $school->id]
        ));

        return new ClassRoomResource($class);
    }

    /**
     * Display the specified resource.
     */
    public function show($id, $classId)
    {
        $class = ClassRoom::where('school_id', $id)
            ->where('id', $classId)
            ->with(['homeroomTeacher.user'])
            ->firstOrFail();

        return new ClassRoomResource($class);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClassRoomRequest $request, $id, $classId)
    {
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
    public function destroy($id, $classId)
    {
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

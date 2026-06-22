<?php

namespace App\Http\Controllers\Api\V1\School;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\DevelopmentProgram;
use App\Models\DevelopmentIndicator;
use Illuminate\Http\Request;

class AssessmentSettingController extends Controller
{
    /**
     * Get all programs and indicators for a school
     */
    public function getPrograms(Request $request, $id)
    {
        $school = School::findOrFail($id);
        
        $user = $request->user();
        $membership = $user->schoolMemberships()
            ->where('school_id', $school->id)
            ->first();

        if (!$membership) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $programs = DevelopmentProgram::with('indicators')
            ->where('school_id', $school->id)
            ->orderBy('order')
            ->get();

        return response()->json($programs);
    }

    /**
     * Store a new program
     */
    public function storeProgram(Request $request, $id)
    {
        $school = School::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'order' => 'integer'
        ]);

        $program = DevelopmentProgram::create([
            'school_id' => $school->id,
            'name' => $request->name,
            'order' => $request->get('order', 0)
        ]);

        return response()->json($program->load('indicators'), 201);
    }

    /**
     * Update a program
     */
    public function updateProgram(Request $request, $id, $programId)
    {
        $program = DevelopmentProgram::where('school_id', $id)
            ->where('id', $programId)
            ->firstOrFail();
            
        $request->validate([
            'name' => 'required|string|max:255',
            'order' => 'integer'
        ]);

        $program->update($request->only(['name', 'order']));

        return response()->json($program->load('indicators'));
    }

    /**
     * Delete a program
     */
    public function destroyProgram($id, $programId)
    {
        $program = DevelopmentProgram::where('school_id', $id)
            ->where('id', $programId)
            ->firstOrFail();
            
        $program->delete();

        return response()->json(['message' => 'Program dihapus']);
    }

    /**
     * Store an indicator
     */
    public function storeIndicator(Request $request, $id, $programId)
    {
        $program = DevelopmentProgram::where('school_id', $id)
            ->where('id', $programId)
            ->firstOrFail();
            
        $request->validate([
            'name' => 'required|string',
            'order' => 'integer'
        ]);

        $indicator = DevelopmentIndicator::create([
            'program_id' => $program->id,
            'name' => $request->name,
            'order' => $request->get('order', 0)
        ]);

        return response()->json($indicator, 201);
    }

    /**
     * Update an indicator
     */
    public function updateIndicator(Request $request, $id, $indicatorId)
    {
        $indicator = DevelopmentIndicator::whereHas('program', function($q) use ($id) {
            $q->where('school_id', $id);
        })->where('id', $indicatorId)->firstOrFail();
            
        $request->validate([
            'name' => 'required|string',
            'order' => 'integer'
        ]);

        $indicator->update($request->only(['name', 'order']));

        return response()->json($indicator);
    }

    /**
     * Delete an indicator
     */
    public function destroyIndicator($id, $indicatorId)
    {
        $indicator = DevelopmentIndicator::whereHas('program', function($q) use ($id) {
            $q->where('school_id', $id);
        })->where('id', $indicatorId)->firstOrFail();
            
        $indicator->delete();

        return response()->json(['message' => 'Indikator dihapus']);
    }
}

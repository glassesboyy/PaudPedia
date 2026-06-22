<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\DevelopmentProgram;
use Illuminate\Http\Request;

class DevelopmentProgramController extends Controller
{
    public function index()
    {
        $programs = DevelopmentProgram::with('indicators')->orderBy('order')->get();
        return response()->json([
            'message' => 'Development programs retrieved successfully',
            'data' => $programs
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'order' => 'integer'
        ]);

        if (!isset($validated['order'])) {
            $validated['order'] = DevelopmentProgram::max('order') + 1;
        }

        $program = DevelopmentProgram::create($validated);

        return response()->json([
            'message' => 'Development program created successfully',
            'data' => $program
        ], 201);
    }

    public function update(Request $request, DevelopmentProgram $program)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'order' => 'sometimes|integer'
        ]);

        $program->update($validated);

        return response()->json([
            'message' => 'Development program updated successfully',
            'data' => $program
        ]);
    }

    public function destroy(DevelopmentProgram $program)
    {
        $program->delete();
        return response()->json([
            'message' => 'Development program deleted successfully'
        ]);
    }
}

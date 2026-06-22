<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\DevelopmentIndicator;
use Illuminate\Http\Request;

class DevelopmentIndicatorController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'program_id' => 'required|exists:development_programs,id',
            'name' => 'required|string|max:255',
            'order' => 'integer'
        ]);

        if (!isset($validated['order'])) {
            $validated['order'] = DevelopmentIndicator::where('program_id', $validated['program_id'])->max('order') + 1;
        }

        $indicator = DevelopmentIndicator::create($validated);

        return response()->json([
            'message' => 'Indicator created successfully',
            'data' => $indicator
        ], 201);
    }

    public function update(Request $request, DevelopmentIndicator $indicator)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'order' => 'sometimes|integer'
        ]);

        $indicator->update($validated);

        return response()->json([
            'message' => 'Indicator updated successfully',
            'data' => $indicator
        ]);
    }

    public function destroy(DevelopmentIndicator $indicator)
    {
        $indicator->delete();
        return response()->json([
            'message' => 'Indicator deleted successfully'
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api\V1\School;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\DevelopmentProgram;
use App\Models\DevelopmentIndicator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    private function checkOperatorAccess(Request $request, $schoolId)
    {
        $membership = $request->user()->schoolMemberships()
            ->where('school_id', $schoolId)
            ->first();

        if (!$membership || !$membership->isOperator()) {
            abort(response()->json(['message' => 'Hanya Operator Sekolah yang berhak mengubah konfigurasi Master Penilaian.'], 403));
        }
    }

    /**
     * Store a new program
     */
    public function storeProgram(Request $request, $id)
    {
        $this->checkOperatorAccess($request, $id);
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
        $this->checkOperatorAccess($request, $id);
        $program = DevelopmentProgram::where('school_id', $id)
            ->where('id', $programId)
            ->firstOrFail();
            
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'order' => 'integer',
            'is_active' => 'boolean'
        ]);

        $program->update($request->only(['name', 'order', 'is_active']));

        return response()->json($program->load('indicators'));
    }

    /**
     * Delete a program
     */
    public function destroyProgram(Request $request, $id, $programId)
    {
        $this->checkOperatorAccess($request, $id);
        $program = DevelopmentProgram::with('indicators')->where('school_id', $id)
            ->where('id', $programId)
            ->firstOrFail();
            
        // Cek apakah indikator dari program ini sudah memiliki riwayat penilaian
        $indicatorIds = $program->indicators->pluck('id');
        $hasAssessments = DB::table('assessments')->whereIn('indicator_id', $indicatorIds)->exists();
        $hasReportDetails = DB::table('student_report_details')->where('program_id', $program->id)->exists();

        if ($hasAssessments || $hasReportDetails) {
            return response()->json([
                'message' => 'Program perkembangan ini tidak dapat dihapus karena sudah memiliki riwayat penilaian siswa atau catatan rapor. Gunakan fitur nonaktifkan program untuk menonaktifkan program ini.'
            ], 422);
        }

        $program->delete();

        return response()->json(['message' => 'Program dihapus']);
    }

    /**
     * Store an indicator
     */
    public function storeIndicator(Request $request, $id, $programId)
    {
        $this->checkOperatorAccess($request, $id);
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
        $this->checkOperatorAccess($request, $id);
        $indicator = DevelopmentIndicator::whereHas('program', function($q) use ($id) {
            $q->where('school_id', $id);
        })->where('id', $indicatorId)->firstOrFail();
            
        $request->validate([
            'name' => 'sometimes|required|string',
            'order' => 'integer',
            'is_active' => 'boolean'
        ]);

        $indicator->update($request->only(['name', 'order', 'is_active']));

        return response()->json($indicator);
    }

    /**
     * Delete an indicator
     */
    public function destroyIndicator(Request $request, $id, $indicatorId)
    {
        $this->checkOperatorAccess($request, $id);
        $indicator = DevelopmentIndicator::whereHas('program', function($q) use ($id) {
            $q->where('school_id', $id);
        })->where('id', $indicatorId)->firstOrFail();
            
        // Cek apakah indikator ini sudah dinilai pada tabel assessments
        $hasAssessments = DB::table('assessments')->where('indicator_id', $indicator->id)->exists();
        if ($hasAssessments) {
            return response()->json([
                'message' => 'Indikator ini tidak dapat dihapus karena sudah memiliki riwayat penilaian siswa. Gunakan fitur nonaktifkan indicator untuk menonaktifkan indicator ini.'
            ], 422);
        }

        $indicator->delete();

        return response()->json(['message' => 'Indikator dihapus']);
    }
}

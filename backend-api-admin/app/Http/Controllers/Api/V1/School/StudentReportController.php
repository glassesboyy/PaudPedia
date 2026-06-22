<?php

namespace App\Http\Controllers\Api\V1\School;

use App\Http\Controllers\Controller;
use App\Models\StudentReport;
use App\Models\StudentReportDetail;
use App\Models\Student;
use App\Models\ClassRoom;
use App\Enums\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StudentReportController extends Controller
{
    /**
     * Get report for a student
     */
    public function show($schoolId, $classId, $studentId, Request $request)
    {
        $request->validate([
            'semester' => ['required', Rule::enum(Semester::class)],
            'academic_year' => 'required|string',
        ]);

        $membership = $request->user()->schoolMemberships()
            ->where('school_id', $schoolId)
            ->first();

        if (!$membership) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $class = ClassRoom::where('school_id', $schoolId)
            ->where('id', $classId)
            ->with('homeroomTeacher')
            ->firstOrFail();

        if ($membership->isTeacher()) {
            if (!$class->homeroomTeacher || $class->homeroomTeacher->user_id !== $request->user()->id) {
                return response()->json(['message' => 'Akses ditolak. Anda hanya dapat melihat rapor kelas Anda sendiri.'], 403);
            }
        }

        $report = StudentReport::with('details.program')
            ->where('school_id', $schoolId)
            ->where('student_id', $studentId)
            ->where('semester', $request->semester)
            ->where('academic_year', $request->academic_year)
            ->first();

        return response()->json([
            'message' => 'Rapor siswa berhasil diambil',
            'data' => $report
        ]);
    }

    /**
     * Create or update report for a student
     */
    public function store($schoolId, $classId, $studentId, Request $request)
    {
        $request->validate([
            'semester' => ['required', Rule::enum(Semester::class)],
            'academic_year' => 'required|string',
            'introduction_notes' => 'required|string',
            'closing_notes' => 'required|string',
            'recommendation' => 'required|string',
            'details' => 'array',
            'details.*.program_id' => 'required|exists:development_programs,id',
            'details.*.narrative' => 'required|string',
        ], [
            'details.*.narrative.required' => 'Kolom narasi untuk setiap program perkembangan wajib diisi.',
            'introduction_notes.required' => 'Kolom Catatan Pendahuluan wajib diisi.',
            'closing_notes.required' => 'Kolom Catatan Penutup wajib diisi.',
            'recommendation.required' => 'Kolom Rekomendasi/Saran wajib diisi.',
        ]);

        $membership = $request->user()->schoolMemberships()
            ->where('school_id', $schoolId)
            ->first();

        if (!$membership) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        if ($membership->isHeadmaster()) {
            return response()->json(['message' => 'Akses ditolak. Hanya Guru Kelas yang dapat mengisi rapor.'], 403);
        }

        $class = ClassRoom::where('school_id', $schoolId)
            ->where('id', $classId)
            ->with('homeroomTeacher')
            ->firstOrFail();

        if ($membership->isTeacher()) {
            if (!$class->homeroomTeacher || $class->homeroomTeacher->user_id !== $request->user()->id) {
                return response()->json(['message' => 'Akses ditolak. Anda hanya dapat mengisi rapor kelas Anda sendiri.'], 403);
            }
        }

        DB::beginTransaction();

        try {
            $report = StudentReport::updateOrCreate(
                [
                    'school_id' => $schoolId,
                    'student_id' => $studentId,
                    'semester' => $request->semester,
                    'academic_year' => $request->academic_year,
                ],
                [
                    'class_id' => $classId,
                    'teacher_id' => $class->homeroom_teacher_id,
                    'introduction_notes' => $request->introduction_notes,
                    'closing_notes' => $request->closing_notes,
                    'recommendation' => $request->recommendation,
                ]
            );

            // Sync details
            if ($request->has('details')) {
                // Delete existing details
                $report->details()->delete();

                // Insert new details
                foreach ($request->details as $detail) {
                    StudentReportDetail::create([
                        'student_report_id' => $report->id,
                        'program_id' => $detail['program_id'],
                        'narrative' => $detail['narrative'],
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Rapor siswa berhasil disimpan',
                'data' => $report->load('details.program')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal menyimpan rapor siswa',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

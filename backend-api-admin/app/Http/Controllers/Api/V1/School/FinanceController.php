<?php

namespace App\Http\Controllers\Api\V1\School;

use App\Enums\FinanceType;
use App\Enums\TransactionType;
use App\Http\Controllers\Controller;
use App\Models\Finance;
use App\Models\School;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Enum;

class FinanceController extends Controller
{
    /**
     * Middleware-like check: school must be on Pro Plan.
     */
    protected function ensureProPlan(School $school): ?JsonResponse
    {
        if (!$school->isPro()) {
            return response()->json([
                'message' => 'Fitur keuangan hanya tersedia untuk Pro Plan.',
                'upgrade_required' => true,
            ], 403);
        }
        return null;
    }

    /**
     * Helper: Get accessible student IDs based on role.
     */
    protected function getAccessibleStudentIds(Request $request, int $schoolId): array
    {
        $membership = $request->user()->schoolMemberships()
            ->where('school_id', $schoolId)
            ->first();

        if (!$membership) return [];

        if ($membership->isHeadmaster()) {
            return Student::where('school_id', $schoolId)->pluck('id')->toArray();
        }

        if ($membership->isTeacher()) {
            $teacher = \App\Models\Teacher::where('user_id', $request->user()->id)
                ->where('school_id', $schoolId)
                ->first();

            if (!$teacher) return [];

            $classIds = \App\Models\ClassRoom::where('homeroom_teacher_id', $teacher->id)->pluck('id');
            return Student::where('school_id', $schoolId)
                ->whereIn('class_id', $classIds)
                ->pluck('id')->toArray();
        }

        return [];
    }

    /**
     * GET /api/v1/schools/{id}/finances/summary
     * 
     * Financial dashboard summary.
     */
    public function summary(Request $request, int $id): JsonResponse
    {
        $school = School::findOrFail($id);
        
        if ($gate = $this->ensureProPlan($school)) return $gate;

        $membership = $request->user()->schoolMemberships()
            ->where('school_id', $school->id)
            ->first();

        if (!$membership || !in_array($membership->role_type->value, ['headmaster', 'teacher'])) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $studentIds = $this->getAccessibleStudentIds($request, $school->id);

        // SPP summary
        $totalSppCollected = Finance::whereIn('student_id', $studentIds)
            ->where('type', FinanceType::SPP)
            ->where('is_paid', true)
            ->sum('amount');

        $totalSppPending = Finance::whereIn('student_id', $studentIds)
            ->where('type', FinanceType::SPP)
            ->where('is_paid', false)
            ->sum('amount');

        // Savings summary
        $totalDeposits = Finance::whereIn('student_id', $studentIds)
            ->where('type', FinanceType::TABUNGAN)
            ->where('transaction_type', TransactionType::DEPOSIT)
            ->sum('amount');

        $totalWithdrawals = Finance::whereIn('student_id', $studentIds)
            ->where('type', FinanceType::TABUNGAN)
            ->where('transaction_type', TransactionType::WITHDRAWAL)
            ->sum('amount');

        // Recent transactions
        $recentTransactions = Finance::whereIn('student_id', $studentIds)
            ->with('student:id,name,nisn')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(fn ($f) => $this->formatFinanceRecord($f));

        return response()->json([
            'spp_collected' => (float) $totalSppCollected,
            'spp_pending' => (float) $totalSppPending,
            'savings_balance' => (float) ($totalDeposits - $totalWithdrawals),
            'total_deposits' => (float) $totalDeposits,
            'total_withdrawals' => (float) $totalWithdrawals,
            'recent_transactions' => $recentTransactions,
        ]);
    }

    /**
     * GET /api/v1/schools/{id}/finances/spp
     * 
     * List SPP payment records.
     */
    public function sppIndex(Request $request, int $id): JsonResponse
    {
        $school = School::findOrFail($id);
        if ($gate = $this->ensureProPlan($school)) return $gate;

        $membership = $request->user()->schoolMemberships()
            ->where('school_id', $school->id)
            ->first();

        if (!$membership || !in_array($membership->role_type->value, ['headmaster', 'teacher'])) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $studentIds = $this->getAccessibleStudentIds($request, $school->id);

        $query = Finance::whereIn('student_id', $studentIds)
            ->where('type', FinanceType::SPP)
            ->with('student:id,name,nisn,class_id', 'student.class:id,name');

        // Optional filters
        if ($request->has('month')) {
            $query->where('month', $request->month);
        }
        if ($request->has('is_paid')) {
            $query->where('is_paid', $request->boolean('is_paid'));
        }
        if ($request->has('class_id')) {
            $classStudentIds = Student::where('school_id', $school->id)
                ->where('class_id', $request->class_id)
                ->pluck('id');
            $query->whereIn('student_id', $classStudentIds);
        }
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->whereHas('student', function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%");
            });
        }

        $records = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 20));

        $records->getCollection()->transform(fn ($f) => $this->formatFinanceRecord($f));

        return response()->json($records);
    }

    /**
     * POST /api/v1/schools/{id}/finances/spp/batch
     * 
     * Generate SPP Bills for an entire class.
     */
    public function sppBatchStore(Request $request, int $id): JsonResponse
    {
        $school = School::findOrFail($id);
        if ($gate = $this->ensureProPlan($school)) return $gate;

        $membership = $request->user()->schoolMemberships()
            ->where('school_id', $school->id)
            ->first();

        if (!$membership || !in_array($membership->role_type->value, ['headmaster', 'teacher'])) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'class_id' => 'required|exists:classes,id',
            'amount' => 'required|numeric|min:1',
            'month' => 'required|string|max:7', // e.g. "2026-05"
            'description' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Verify class belongs to this school
        $class = \App\Models\ClassRoom::where('id', $request->class_id)
            ->where('school_id', $school->id)
            ->first();

        if (!$class) {
            return response()->json(['message' => 'Kelas tidak valid.'], 404);
        }

        // Validate Month is within Academic Year
        if ($class->academic_year) {
            $parts = explode('/', $class->academic_year);
            if (count($parts) === 2) {
                $startYear = $parts[0];
                $endYear = $parts[1];
                $minMonth = "{$startYear}-07";
                $maxMonth = "{$endYear}-06";

                if ($request->month < $minMonth || $request->month > $maxMonth) {
                    return response()->json([
                        'message' => 'Bulan tagihan di luar rentang tahun ajaran kelas (' . $class->academic_year . ').'
                    ], 422);
                }
            }
        }

        $accessibleStudentIds = $this->getAccessibleStudentIds($request, $school->id);

        $students = Student::where('class_id', $class->id)
            ->where('school_id', $school->id)
            ->whereIn('id', $accessibleStudentIds)
            ->get();

        if ($students->isEmpty()) {
            return response()->json(['message' => 'Tidak ada siswa di kelas ini.'], 422);
        }

        $count = 0;
        foreach ($students as $student) {
            // Check if ANY SPP record exists for this month for this student
            $existing = Finance::where('student_id', $student->id)
                ->where('type', FinanceType::SPP)
                ->where('month', $request->month)
                ->first();

            if (!$existing) {
                Finance::create([
                    'student_id' => $student->id,
                    'type' => FinanceType::SPP,
                    'amount' => $request->amount,
                    'description' => $request->description,
                    'month' => $request->month,
                    'is_paid' => false,
                    'paid_at' => null,
                    'payment_method' => null,
                    'recorded_by' => $request->user()->id,
                ]);
                $count++;
            }
        }

        return response()->json([
            'message' => "Berhasil menerbitkan $count tagihan SPP untuk kelas {$class->name}."
        ], 201);
    }
    public function sppStore(Request $request, int $id): JsonResponse
    {
        $school = School::findOrFail($id);
        if ($gate = $this->ensureProPlan($school)) return $gate;

        $membership = $request->user()->schoolMemberships()
            ->where('school_id', $school->id)
            ->first();

        if (!$membership || !in_array($membership->role_type->value, ['headmaster', 'teacher'])) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'amount' => 'required|numeric|min:1',
            'month' => 'required|string|max:7', // e.g. "2026-05"
            'payment_method' => ['required', 'in:cash,transfer'],
            'description' => 'nullable|string|max:255',
            'is_paid' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Verify student belongs to this school
        $student = Student::where('id', $request->student_id)
            ->where('school_id', $school->id)
            ->first();

        if (!$student) {
            return response()->json(['message' => 'Siswa tidak ditemukan di sekolah ini.'], 404);
        }

        // Prevent duplicate SPP for same student + same month
        $existing = Finance::where('student_id', $student->id)
            ->where('type', FinanceType::SPP)
            ->where('month', $request->month)
            ->where('is_paid', true)
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'SPP untuk siswa ini pada bulan tersebut sudah dibayar.',
            ], 422);
        }

        $finance = Finance::create([
            'student_id' => $student->id,
            'type' => FinanceType::SPP,
            'amount' => $request->amount,
            'description' => $request->description,
            'month' => $request->month,
            'is_paid' => $request->boolean('is_paid', true),
            'paid_at' => $request->boolean('is_paid', true) ? now() : null,
            'payment_method' => $request->payment_method,
            'recorded_by' => $request->user()->id,
        ]);

        return response()->json([
            'message' => 'Pembayaran SPP berhasil dicatat.',
            'data' => $this->formatFinanceRecord($finance->load('student:id,name,nisn')),
        ], 201);
    }

    /**
     * PUT /api/v1/schools/{id}/finances/spp/{financeId}
     * 
     * Update an SPP payment record.
     */
    public function sppUpdate(Request $request, int $id, int $financeId): JsonResponse
    {
        $school = School::findOrFail($id);
        if ($gate = $this->ensureProPlan($school)) return $gate;

        $membership = $request->user()->schoolMemberships()
            ->where('school_id', $school->id)
            ->first();

        if (!$membership || !in_array($membership->role_type->value, ['headmaster', 'teacher'])) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $studentIds = Student::where('school_id', $school->id)->pluck('id');
        $finance = Finance::whereIn('student_id', $studentIds)
            ->where('id', $financeId)
            ->where('type', FinanceType::SPP)
            ->firstOrFail();

        $validator = Validator::make($request->all(), [
            'amount' => 'sometimes|numeric|min:1',
            'payment_method' => ['sometimes', 'in:cash,transfer'],
            'description' => 'nullable|string|max:255',
            'is_paid' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $updateData = $request->only(['amount', 'payment_method', 'description', 'is_paid']);
        if (isset($updateData['is_paid']) && $updateData['is_paid'] && !$finance->is_paid) {
            $updateData['paid_at'] = now();
        }

        $finance->update($updateData);

        return response()->json([
            'message' => 'Data SPP berhasil diperbarui.',
            'data' => $this->formatFinanceRecord($finance->fresh()->load('student:id,name,nisn')),
        ]);
    }

    /**
     * GET /api/v1/schools/{id}/finances/savings
     * 
     * List savings transactions.
     */
    public function savingsIndex(Request $request, int $id): JsonResponse
    {
        $school = School::findOrFail($id);
        if ($gate = $this->ensureProPlan($school)) return $gate;

        $membership = $request->user()->schoolMemberships()
            ->where('school_id', $school->id)
            ->first();

        if (!$membership || !in_array($membership->role_type->value, ['headmaster', 'teacher'])) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $studentIds = $this->getAccessibleStudentIds($request, $school->id);

        $query = Finance::whereIn('student_id', $studentIds)
            ->where('type', FinanceType::TABUNGAN)
            ->with('student:id,name,nisn,class_id', 'student.class:id,name');

        if ($request->has('student_id')) {
            $query->where('student_id', $request->student_id);
        }
        if ($request->has('class_id')) {
            $classStudentIds = Student::where('school_id', $school->id)
                ->where('class_id', $request->class_id)
                ->pluck('id');
            $query->whereIn('student_id', $classStudentIds);
        }
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->whereHas('student', function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%");
            });
        }

        $records = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 20));

        $records->getCollection()->transform(fn ($f) => $this->formatFinanceRecord($f));

        // Also calculate balance per student if filtering by student
        $balanceData = null;
        if ($request->has('student_id')) {
            $deposits = Finance::where('student_id', $request->student_id)
                ->where('type', FinanceType::TABUNGAN)
                ->where('transaction_type', TransactionType::DEPOSIT)
                ->sum('amount');
            $withdrawals = Finance::where('student_id', $request->student_id)
                ->where('type', FinanceType::TABUNGAN)
                ->where('transaction_type', TransactionType::WITHDRAWAL)
                ->sum('amount');
            $balanceData = [
                'balance' => (float) ($deposits - $withdrawals),
                'total_deposits' => (float) $deposits,
                'total_withdrawals' => (float) $withdrawals,
            ];
        }

        $response = $records->toArray();
        if ($balanceData) {
            $response['balance_info'] = $balanceData;
        }

        return response()->json($response);
    }

    /**
     * POST /api/v1/schools/{id}/finances/savings
     * 
     * Record a savings deposit or withdrawal.
     */
    public function savingsStore(Request $request, int $id): JsonResponse
    {
        $school = School::findOrFail($id);
        if ($gate = $this->ensureProPlan($school)) return $gate;

        $membership = $request->user()->schoolMemberships()
            ->where('school_id', $school->id)
            ->first();

        if (!$membership || !in_array($membership->role_type->value, ['headmaster', 'teacher'])) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'amount' => 'required|numeric|min:1',
            'transaction_type' => ['required', new Enum(TransactionType::class)],
            'description' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $student = Student::where('id', $request->student_id)
            ->where('school_id', $school->id)
            ->first();

        if (!$student) {
            return response()->json(['message' => 'Siswa tidak ditemukan di sekolah ini.'], 404);
        }

        $accessibleStudentIds = $this->getAccessibleStudentIds($request, $school->id);
        if (!in_array($student->id, $accessibleStudentIds)) {
            return response()->json(['message' => 'Anda tidak memiliki akses ke siswa ini.'], 403);
        }

        // For withdrawal, check balance
        if ($request->transaction_type === 'withdrawal') {
            $deposits = Finance::where('student_id', $student->id)
                ->where('type', FinanceType::TABUNGAN)
                ->where('transaction_type', TransactionType::DEPOSIT)
                ->sum('amount');
            $withdrawals = Finance::where('student_id', $student->id)
                ->where('type', FinanceType::TABUNGAN)
                ->where('transaction_type', TransactionType::WITHDRAWAL)
                ->sum('amount');
            $currentBalance = $deposits - $withdrawals;

            if ($request->amount > $currentBalance) {
                return response()->json([
                    'message' => 'Saldo tidak mencukupi. Saldo saat ini: Rp ' . number_format($currentBalance, 0, ',', '.'),
                    'current_balance' => $currentBalance,
                ], 422);
            }
        }

        $finance = Finance::create([
            'student_id' => $student->id,
            'type' => FinanceType::TABUNGAN,
            'amount' => $request->amount,
            'description' => $request->description,
            'month' => now()->format('Y-m'),
            'transaction_type' => $request->transaction_type,
            'is_paid' => true,
            'paid_at' => now(),
            'recorded_by' => $request->user()->id,
        ]);

        return response()->json([
            'message' => $request->transaction_type === 'deposit'
                ? 'Setoran tabungan berhasil dicatat.'
                : 'Penarikan tabungan berhasil dicatat.',
            'data' => $this->formatFinanceRecord($finance->load('student:id,name,nisn')),
        ], 201);
    }

    /**
     * GET /api/v1/schools/{id}/students/{studentId}/finances
     * 
     * Get student financial summary — accessible by parent.
     */
    public function studentFinances(Request $request, int $id, int $studentId): JsonResponse
    {
        $school = School::findOrFail($id);
        
        // For parent: no Pro check needed (they should see data if school is Pro)
        $user = $request->user();
        
        if ($user->hasRole('parent')) {
            $parentProfile = $user->parentProfile;
            if (!$parentProfile) {
                return response()->json(['message' => 'Akses ditolak.'], 403);
            }
            $student = Student::where('id', $studentId)
                ->where('parent_profile_id', $parentProfile->id)
                ->firstOrFail();
        } else {
            $membership = $user->schoolMemberships()
                ->where('school_id', $school->id)
                ->first();
            if (!$membership) {
                return response()->json(['message' => 'Akses ditolak.'], 403);
            }
            $student = Student::where('id', $studentId)
                ->where('school_id', $school->id)
                ->firstOrFail();
        }

        // SPP history
        $sppHistory = Finance::where('student_id', $student->id)
            ->where('type', FinanceType::SPP)
            ->orderBy('month', 'desc')
            ->get()
            ->map(fn ($f) => $this->formatFinanceRecord($f));

        // Savings
        $savingsHistory = Finance::where('student_id', $student->id)
            ->where('type', FinanceType::TABUNGAN)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn ($f) => $this->formatFinanceRecord($f));

        $deposits = Finance::where('student_id', $student->id)
            ->where('type', FinanceType::TABUNGAN)
            ->where('transaction_type', TransactionType::DEPOSIT)
            ->sum('amount');
        $withdrawals = Finance::where('student_id', $student->id)
            ->where('type', FinanceType::TABUNGAN)
            ->where('transaction_type', TransactionType::WITHDRAWAL)
            ->sum('amount');

        $totalSppPaid = Finance::where('student_id', $student->id)
            ->where('type', FinanceType::SPP)
            ->where('is_paid', true)
            ->sum('amount');

        return response()->json([
            'student' => [
                'id' => $student->id,
                'name' => $student->name,
                'nisn' => $student->nisn,
            ],
            'spp' => [
                'total_paid' => (float) $totalSppPaid,
                'history' => $sppHistory,
            ],
            'savings' => [
                'balance' => (float) ($deposits - $withdrawals),
                'total_deposits' => (float) $deposits,
                'total_withdrawals' => (float) $withdrawals,
                'history' => $savingsHistory,
            ],
        ]);
    }

    /**
     * Format a finance record for JSON response.
     */
    protected function formatFinanceRecord(Finance $finance): array
    {
        return [
            'id' => $finance->id,
            'student_id' => $finance->student_id,
            'student_name' => $finance->student?->name,
            'student_nisn' => $finance->student?->nisn,
            'class_name' => $finance->student?->class?->name,
            'type' => $finance->type->value,
            'type_label' => $finance->type->shortLabel(),
            'amount' => (float) $finance->amount,
            'amount_formatted' => $finance->formatted_amount,
            'description' => $finance->description,
            'month' => $finance->month,
            'is_paid' => $finance->is_paid,
            'payment_method' => $finance->payment_method?->value,
            'payment_method_label' => $finance->payment_method?->label(),
            'transaction_type' => $finance->transaction_type?->value,
            'transaction_type_label' => $finance->transaction_type?->label(),
            'paid_at' => $finance->paid_at?->toISOString(),
            'created_at' => $finance->created_at->toISOString(),
        ];
    }
}

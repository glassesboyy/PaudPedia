<?php

namespace App\Http\Controllers\Api\V1\School;

use App\Http\Controllers\Api\V1\BaseController;
use App\Models\School;
use App\Models\Attendance;
use App\Models\Finance;
use App\Enums\AttendanceStatus;
use App\Enums\FinanceType;
use App\Enums\TransactionType;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends BaseController
{
    /**
     * GET /api/v1/schools/{id}/dashboard/headmaster
     * 
     * Get headmaster dashboard summary (Pro Plan only).
     */
    public function headmasterSummary(Request $request, int $id): JsonResponse
    {
        $school = School::findOrFail($id);
        
        // Ensure user is authorized
        $membership = $request->user()->schoolMemberships()
            ->where('school_id', $school->id)
            ->first();

        if (!$membership || !in_array($membership->role_type->value, ['headmaster', 'operator'])) {
            return $this->error('Akses ditolak. Hanya Kepala Sekolah dan Operator.', 403);
        }

        // Filter parameter
        $filter = $request->query('filter', Carbon::now()->format('Y-m'));
        $isAllTime = $filter === 'all';
        
        if (!$isAllTime) {
            try {
                $filterDate = Carbon::createFromFormat('Y-m', $filter);
            } catch (\Exception $e) {
                $filterDate = Carbon::now();
                $filter = $filterDate->format('Y-m');
            }
            $startOfMonth = $filterDate->copy()->startOfMonth();
            $endOfMonth = $filterDate->copy()->endOfMonth();
        } else {
            $filterDate = Carbon::now();
        }

        // 1. Finance Summary
        $sppQuery = Finance::whereHas('student', function($q) use ($school) {
                $q->where('school_id', $school->id);
            })->where('type', FinanceType::SPP);
            
        if (!$isAllTime) {
            $sppQuery->where('month', $filter);
        }

        $totalSppCollected = (clone $sppQuery)->where('is_paid', true)->sum('amount');
        $totalSppPending = (clone $sppQuery)->where('is_paid', false)->sum('amount');

        $tabunganQuery = Finance::whereHas('student', function($q) use ($school) {
                $q->where('school_id', $school->id);
            })->where('type', FinanceType::TABUNGAN);

        if (!$isAllTime) {
            $tabunganQuery->whereBetween('created_at', [$startOfMonth, $endOfMonth]);
        }

        $totalDeposits = (clone $tabunganQuery)->where('transaction_type', TransactionType::DEPOSIT)->sum('amount');
        $totalWithdrawals = (clone $tabunganQuery)->where('transaction_type', TransactionType::WITHDRAWAL)->sum('amount');

        $savingsBalance = $totalDeposits - $totalWithdrawals;

        // 2. Best Attendance
        $attendanceStart = $isAllTime ? Carbon::now()->startOfMonth() : $startOfMonth;
        $attendanceEnd = $isAllTime ? Carbon::now()->endOfMonth() : $endOfMonth;

        $bestAttendance = Attendance::select('student_id', DB::raw('count(*) as present_count'))
            ->whereHas('student', function($q) use ($school) {
                $q->where('school_id', $school->id);
            })
            ->where('status', AttendanceStatus::PRESENT)
            ->whereBetween('date', [$attendanceStart->format('Y-m-d'), $attendanceEnd->format('Y-m-d')])
            ->groupBy('student_id')
            ->orderByDesc('present_count')
            ->limit(5)
            ->with('student:id,name,class_id,nisn', 'student.class:id,name')
            ->get()
            ->map(function ($item) {
                return [
                    'student_id' => $item->student_id,
                    'student_name' => $item->student->name,
                    'nisn' => $item->student->nisn,
                    'class_name' => $item->student->class->name ?? '-',
                    'present_count' => $item->present_count
                ];
            });

        // 3. SPP Arrears
        $sppArrearsQuery = Finance::whereHas('student', function($q) use ($school) {
                $q->where('school_id', $school->id);
            })
            ->where('type', FinanceType::SPP)
            ->where('is_paid', false);
            
        if (!$isAllTime) {
            $sppArrearsQuery->where('month', $filter);
        }
        
        $sppArrears = $sppArrearsQuery->with('student:id,name,class_id,nisn', 'student.class:id,name')
            ->orderByDesc('amount')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'student_id' => $item->student_id,
                    'student_name' => $item->student->name,
                    'nisn' => $item->student->nisn,
                    'class_name' => $item->student->class->name ?? '-',
                    'amount' => (float) $item->amount,
                    'month' => $item->month
                ];
            });

        // 4. Top Savings Balance
        $topSavingsQuery = Finance::select('student_id', 
                DB::raw('SUM(CASE WHEN transaction_type = \'deposit\' THEN amount ELSE 0 END) as total_deposits'),
                DB::raw('SUM(CASE WHEN transaction_type = \'withdrawal\' THEN amount ELSE 0 END) as total_withdrawals')
            )
            ->whereHas('student', function($q) use ($school) {
                $q->where('school_id', $school->id);
            })
            ->where('type', FinanceType::TABUNGAN);
            
        if (!$isAllTime) {
            $topSavingsQuery->whereBetween('created_at', [$startOfMonth, $endOfMonth]);
        }
        
        $topSavings = $topSavingsQuery->groupBy('student_id')
            ->havingRaw('(SUM(CASE WHEN transaction_type = \'deposit\' THEN amount ELSE 0 END) - SUM(CASE WHEN transaction_type = \'withdrawal\' THEN amount ELSE 0 END)) > 0')
            ->orderByRaw('(SUM(CASE WHEN transaction_type = \'deposit\' THEN amount ELSE 0 END) - SUM(CASE WHEN transaction_type = \'withdrawal\' THEN amount ELSE 0 END)) DESC')
            ->limit(5)
            ->with('student:id,name,class_id,nisn', 'student.class:id,name')
            ->get()
            ->map(function ($item) {
                $balance = $item->total_deposits - $item->total_withdrawals;
                return [
                    'student_id' => $item->student_id,
                    'student_name' => $item->student->name,
                    'nisn' => $item->student->nisn,
                    'class_name' => $item->student->class->name ?? '-',
                    'balance' => (float) $balance
                ];
            });

        // 5. Chart Data (Range months trend)
        $chartStartReq = $request->query('chart_start');
        $chartEndReq = $request->query('chart_end');

        if (!$chartStartReq || !$chartEndReq) {
            $endChartDateObj = Carbon::now()->endOfMonth();
            $startChartDateObj = Carbon::now()->subMonths(5)->startOfMonth();
        } else {
            try {
                $startChartDateObj = Carbon::createFromFormat('Y-m', $chartStartReq)->startOfMonth();
                $endChartDateObj = Carbon::createFromFormat('Y-m', $chartEndReq)->endOfMonth();
                
                if ($startChartDateObj->greaterThan($endChartDateObj)) {
                    $temp = $startChartDateObj->copy();
                    $startChartDateObj = $endChartDateObj->copy()->startOfMonth();
                    $endChartDateObj = $temp->endOfMonth();
                }
            } catch (\Exception $e) {
                $endChartDateObj = Carbon::now()->endOfMonth();
                $startChartDateObj = Carbon::now()->subMonths(5)->startOfMonth();
            }
        }

        if ($startChartDateObj->diffInMonths($endChartDateObj) > 11) {
            $endChartDateObj = $startChartDateObj->copy()->addMonths(11)->endOfMonth();
        }

        $chartData = [];
        $months = [];
        
        $currentMonth = $startChartDateObj->copy();
        while ($currentMonth->lessThanOrEqualTo($endChartDateObj)) {
            $monthStr = $currentMonth->format('Y-m');
            $months[] = $monthStr;
            $chartData[$monthStr] = [
                'spp' => 0,
                'tabungan' => 0,
                'total' => 0
            ];
            $currentMonth->addMonth();
        }

        $sppChartData = Finance::whereHas('student', function($q) use ($school) {
                $q->where('school_id', $school->id);
            })
            ->where('type', FinanceType::SPP)
            ->where('is_paid', true)
            ->whereIn('month', $months)
            ->select('month', DB::raw('SUM(amount) as total'))
            ->groupBy('month')
            ->get();

        foreach ($sppChartData as $row) {
            if (isset($chartData[$row->month])) {
                $chartData[$row->month]['spp'] = (float) $row->total;
                $chartData[$row->month]['total'] += (float) $row->total;
            }
        }

        $startChartDate = $startChartDateObj->copy()->startOfDay();
        $endChartDate = $endChartDateObj->copy()->endOfDay();
        
        $tabunganChartData = Finance::whereHas('student', function($q) use ($school) {
                $q->where('school_id', $school->id);
            })
            ->where('type', FinanceType::TABUNGAN)
            ->where('transaction_type', TransactionType::DEPOSIT)
            ->whereBetween('created_at', [$startChartDate, $endChartDate])
            ->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'), DB::raw('SUM(amount) as total'))
            ->groupBy(DB::raw('DATE_FORMAT(created_at, "%Y-%m")'))
            ->get();

        foreach ($tabunganChartData as $row) {
            if (isset($chartData[$row->month])) {
                $chartData[$row->month]['tabungan'] = (float) $row->total;
                $chartData[$row->month]['total'] += (float) $row->total;
            }
        }

        $formattedChartData = [
            'labels' => [],
            'spp' => [],
            'tabungan' => [],
            'total' => []
        ];

        foreach ($months as $month) {
            $formattedChartData['labels'][] = $month;
            $formattedChartData['spp'][] = $chartData[$month]['spp'];
            $formattedChartData['tabungan'][] = $chartData[$month]['tabungan'];
            $formattedChartData['total'][] = $chartData[$month]['total'];
        }

        return $this->success([
            'finance_summary' => [
                'spp_collected' => (float) $totalSppCollected,
                'spp_pending' => (float) $totalSppPending,
                'savings_balance' => (float) $savingsBalance,
                'total_deposits' => (float) $totalDeposits,
            ],
            'best_attendance' => $bestAttendance,
            'spp_arrears' => $sppArrears,
            'top_savings' => $topSavings,
            'chart_data' => $formattedChartData
        ], 'Data ringkasan dashboard berhasil diambil.');
    }
}

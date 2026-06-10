import { http as api } from '@/services/http/client'
import type {
  FinanceSummary,
  FinanceRecord,
  StudentFinanceSummary,
  SppPaymentPayload,
  SavingsPayload,
} from '../types/finance.types'

export const financeService = {
  /**
   * Get financial dashboard summary
   */
  getSummary(schoolId: number) {
    return api.get<FinanceSummary>(`/api/v1/schools/${schoolId}/finances/summary`)
  },

  /**
   * Get SPP payment list with filters
   */
  getSppList(schoolId: number, params?: { month?: string; is_paid?: boolean; class_id?: number; per_page?: number; page?: number }) {
    return api.get<{ data: FinanceRecord[]; meta?: unknown }>(`/api/v1/schools/${schoolId}/finances/spp`, { params })
  },

  /**
   * Record a new SPP payment
   */
  storeSpp(schoolId: number, payload: SppPaymentPayload) {
    return api.post<{ message: string; data: FinanceRecord }>(`/api/v1/schools/${schoolId}/finances/spp`, payload)
  },

  /**
   * Update an SPP payment
   */
  updateSpp(schoolId: number, financeId: number, payload: Partial<SppPaymentPayload>) {
    return api.put<{ message: string; data: FinanceRecord }>(`/api/v1/schools/${schoolId}/finances/spp/${financeId}`, payload)
  },

  /**
   * Get savings transactions with filters
   */
  getSavingsList(schoolId: number, params?: { student_id?: number; class_id?: number; per_page?: number; page?: number }) {
    return api.get<{ data: FinanceRecord[]; balance_info?: { balance: number; total_deposits: number; total_withdrawals: number }; meta?: unknown }>(`/api/v1/schools/${schoolId}/finances/savings`, { params })
  },

  /**
   * Record a savings deposit or withdrawal
   */
  storeSavings(schoolId: number, payload: SavingsPayload) {
    return api.post<{ message: string; data: FinanceRecord }>(`/api/v1/schools/${schoolId}/finances/savings`, payload)
  },

  /**
   * Get student financial summary (for parent view)
   */
  getStudentFinances(schoolId: number, studentId: number) {
    return api.get<StudentFinanceSummary>(`/api/v1/schools/${schoolId}/students/${studentId}/finances`)
  },
}

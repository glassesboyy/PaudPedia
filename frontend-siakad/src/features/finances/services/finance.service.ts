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
    if (payload.proof_file) {
      const formData = new FormData()
      Object.entries(payload).forEach(([key, value]) => {
        if (value !== undefined && value !== null) {
          if (key === 'is_paid') {
            formData.append(key, value ? '1' : '0')
          } else {
            formData.append(key, value instanceof File ? value : String(value))
          }
        }
      })
      return api.post<{ message: string; data: FinanceRecord }>(`/api/v1/schools/${schoolId}/finances/spp`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
    }
    return api.post<{ message: string; data: FinanceRecord }>(`/api/v1/schools/${schoolId}/finances/spp`, payload)
  },

  /**
   * Batch generate SPP bills for a class
   */
  storeSppBatch(schoolId: number, payload: { class_id: number; amount: number; month: string; description?: string }) {
    return api.post<{ message: string }>(`/api/v1/schools/${schoolId}/finances/spp/batch`, payload)
  },

  /**
   * Update an SPP payment
   */
  updateSpp(schoolId: number, financeId: number, payload: Partial<SppPaymentPayload>) {
    if (payload.proof_file) {
      const formData = new FormData()
      formData.append('_method', 'PUT')
      Object.entries(payload).forEach(([key, value]) => {
        if (value !== undefined && value !== null) {
          if (key === 'is_paid') {
            formData.append(key, value ? '1' : '0')
          } else {
            formData.append(key, value instanceof File ? value : String(value))
          }
        }
      })
      return api.post<{ message: string; data: FinanceRecord }>(`/api/v1/schools/${schoolId}/finances/spp/${financeId}`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
    }
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
    if (payload.proof_file) {
      const formData = new FormData()
      Object.entries(payload).forEach(([key, value]) => {
        if (value !== undefined && value !== null) {
          formData.append(key, value instanceof File ? value : String(value))
        }
      })
      return api.post<{ message: string; data: FinanceRecord }>(`/api/v1/schools/${schoolId}/finances/savings`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
    }
    return api.post<{ message: string; data: FinanceRecord }>(`/api/v1/schools/${schoolId}/finances/savings`, payload)
  },

  /**
   * Get student financial summary (for parent view)
   */
  getStudentFinances(schoolId: number, studentId: number) {
    return api.get<StudentFinanceSummary>(`/api/v1/schools/${schoolId}/students/${studentId}/finances`)
  },
}

import { http as api } from '@/services/http/client'

export interface HeadmasterDashboardSummary {
  finance_summary: {
    spp_collected: number
    spp_pending: number
    savings_balance: number
    total_deposits: number
  }
  best_attendance: Array<{
    student_id: number
    student_name: string
    nisn: string
    class_name: string
    present_count: number
  }>
  spp_arrears: Array<{
    student_id: number
    student_name: string
    nisn: string
    class_name: string
    amount: number
    month: string
  }>
  top_savings: Array<{
    student_id: number
    student_name: string
    nisn: string
    class_name: string
    balance: number
  }>
  chart_data: {
    labels: string[]
    spp: number[]
    tabungan: number[]
    total: number[]
  }
}

export const dashboardService = {
  getHeadmasterSummary(schoolId: number, filter?: string, chartStart?: string, chartEnd?: string) {
    const params = new URLSearchParams()
    if (filter) params.append('filter', filter)
    if (chartStart) params.append('chart_start', chartStart)
    if (chartEnd) params.append('chart_end', chartEnd)
    
    const query = params.toString() ? `?${params.toString()}` : ''
    return api.get<{ message: string; data: HeadmasterDashboardSummary }>(`/api/v1/schools/${schoolId}/dashboard/headmaster${query}`)
  }
}

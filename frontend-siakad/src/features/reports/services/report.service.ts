import { http as api } from '@/services/http/client'

export interface ReportData {
  school: {
    name: string
    npsn: string
    address: string
    phone: string | null
    email: string | null
    logo_url: string | null
    headmaster_name: string
  }
  student: {
    name: string
    nisn: string | null
    birth_date: string
    gender: string
    class_name: string
    photo_url: string | null
  }
  semester: string
  semester_label: string
  academic_year: string
  attendance: {
    total: number
    present: number
    sick: number
    permission: number
    absent: number
  }
  assessments: Array<{
    id: number
    aspect: string
    scale: string
    scale_label: string
    notes: string | null
  }>
  generated_at: string
}

export const reportService = {
  /**
   * Get report data for preview
   */
  getReportData(schoolId: number, studentId: number, params?: { semester?: string; academic_year?: string }) {
    return api.get<ReportData>(`/api/v1/schools/${schoolId}/reports/students/${studentId}/data`, { params })
  },

  /**
   * Download PDF report
   */
  downloadPdf(schoolId: number, studentId: number, params?: { semester?: string; academic_year?: string }) {
    return api.get(`/api/v1/schools/${schoolId}/reports/students/${studentId}/pdf`, {
      params,
      responseType: 'blob',
    })
  },

  /**
   * Get list of student IDs who have generated reports for a class
   */
  getReportsStatusList(schoolId: number, classId: number, params?: { semester?: string; academic_year?: string }) {
    return api.get<{ generated_student_ids: number[] }>(`/api/v1/schools/${schoolId}/classes/${classId}/reports-status`, { params })
  }
}

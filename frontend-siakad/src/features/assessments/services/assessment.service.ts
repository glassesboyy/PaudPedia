import { http as api } from '@/services/http/client'
import type { 
  AssessmentResponse, 
  BulkAssessmentPayload, 
  StudentAssessmentHistoryResponse,
  Semester
} from '@/types/assessment.types'

export const assessmentService = {
  /**
   * Get assessment list for a specific class, month, and semester
   */
  getAssessmentList(schoolId: number, classId: number, month: string, semester: Semester, academicYear: string) {
    return api.get<AssessmentResponse>(`/api/v1/schools/${schoolId}/classes/${classId}/assessments`, {
      params: { month, semester, academic_year: academicYear }
    })
  },

  /**
   * Store or update assessment in bulk
   */
  storeBulkAssessment(schoolId: number, classId: number, payload: BulkAssessmentPayload) {
    return api.post<{ message: string; count: number }>(`/api/v1/schools/${schoolId}/classes/${classId}/assessments`, payload)
  },

  /**
   * Get assessment history for a specific student (grouped by semester)
   */
  getStudentHistory(schoolId: number, studentId: number) {
    return api.get<StudentAssessmentHistoryResponse>(`/api/v1/schools/${schoolId}/students/${studentId}/assessments/history`)
  },

  /**
   * Get assessment matrix for a student (6 months)
   */
  getStudentMatrix(schoolId: number, studentId: number, semester: Semester, academicYear: string) {
    return api.get<any>(`/api/v1/schools/${schoolId}/students/${studentId}/assessments/matrix`, {
      params: { semester, academic_year: academicYear }
    })
  },

  /**
   * Get student narrative report draft
   */
  getStudentReport(schoolId: number, classId: number, studentId: number, semester: Semester, academicYear: string) {
    return api.get<any>(`/api/v1/schools/${schoolId}/classes/${classId}/students/${studentId}/report`, {
      params: { semester, academic_year: academicYear }
    })
  },

  /**
   * Save student narrative report draft
   */
  saveStudentReport(schoolId: number, classId: number, studentId: number, payload: any) {
    return api.post<any>(`/api/v1/schools/${schoolId}/classes/${classId}/students/${studentId}/report`, payload)
  },

  // --- Master Data Settings ---

  getPrograms(schoolId: number) {
    return api.get<any>(`/api/v1/schools/${schoolId}/development-programs`)
  },

  storeProgram(schoolId: number, payload: { name: string; order: number }) {
    return api.post<any>(`/api/v1/schools/${schoolId}/development-programs`, payload)
  },

  updateProgram(schoolId: number, programId: number, payload: { name?: string; order?: number; is_active?: boolean }) {
    return api.put<any>(`/api/v1/schools/${schoolId}/development-programs/${programId}`, payload)
  },

  destroyProgram(schoolId: number, programId: number) {
    return api.delete<any>(`/api/v1/schools/${schoolId}/development-programs/${programId}`)
  },

  storeIndicator(schoolId: number, programId: number, payload: { name: string; order: number }) {
    return api.post<any>(`/api/v1/schools/${schoolId}/development-programs/${programId}/indicators`, payload)
  },

  updateIndicator(schoolId: number, indicatorId: number, payload: { name?: string; order?: number; is_active?: boolean }) {
    return api.put<any>(`/api/v1/schools/${schoolId}/development-indicators/${indicatorId}`, payload)
  },

  destroyIndicator(schoolId: number, indicatorId: number) {
    return api.delete<any>(`/api/v1/schools/${schoolId}/development-indicators/${indicatorId}`)
  }
}

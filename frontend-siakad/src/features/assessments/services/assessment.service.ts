import { http as api } from '@/services/http/client'
import type { 
  AssessmentResponse, 
  BulkAssessmentPayload, 
  StudentAssessmentHistoryResponse,
  Semester
} from '@/types/assessment.types'

export const assessmentService = {
  /**
   * Get assessment list for a specific class, aspect, and semester
   */
  getAssessmentList(schoolId: number, classId: number, aspect: string, semester: Semester, academicYear: string) {
    return api.get<AssessmentResponse>(`/api/v1/schools/${schoolId}/classes/${classId}/assessments`, {
      params: { aspect, semester, academic_year: academicYear }
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
  }
}

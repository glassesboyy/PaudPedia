import { http as api } from '@/services/http/client'
import type { 
  AttendanceResponse, 
  BulkAttendancePayload, 
  StudentAttendanceSummaryResponse 
} from '@/types/attendance.types'

export const attendanceService = {
  /**
   * Get attendance list for a class on a specific date
   */
  getAttendanceList(schoolId: number, classId: number, date: string) {
    return api.get<AttendanceResponse>(`/api/v1/schools/${schoolId}/classes/${classId}/attendance`, {
      params: { date }
    })
  },

  /**
   * Store or update attendance in bulk
   */
  storeBulkAttendance(schoolId: number, classId: number, payload: BulkAttendancePayload | FormData) {
    return api.post<{ message: string; count: number }>(`/api/v1/schools/${schoolId}/classes/${classId}/attendance`, payload)
  },

  /**
   * Get attendance history and summary for a single student
   */
  getStudentHistory(schoolId: number, studentId: number, params?: { month?: number; year?: number }) {
    return api.get<StudentAttendanceSummaryResponse>(`/api/v1/schools/${schoolId}/students/${studentId}/attendance/summary`, {
      params
    })
  }
}

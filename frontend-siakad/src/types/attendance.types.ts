import type { AttendanceStatus } from './enums'

export interface AttendanceRecord {
  student_id: number
  name: string
  nisn: string | null
  status: AttendanceStatus | null
  notes: string | null
  attendance_id: number | null
  date: string
}

export interface AttendanceResponse {
  data: AttendanceRecord[]
  date: string
  class: {
    id: number
    name: string
  }
}

export interface AttendanceSummary {
  total_recorded_days: number
  present: number
  sick: number
  permission: number
  absent: number
  percentage: number
}

export interface AttendanceHistoryItem {
  id: number
  date: string
  status: AttendanceStatus
  status_label: string
  notes: string | null
}

export interface StudentAttendanceSummaryResponse {
  summary: AttendanceSummary
  history: AttendanceHistoryItem[]
}

export interface BulkAttendancePayload {
  date: string
  attendances: {
    student_id: number
    status: AttendanceStatus
    notes?: string | null
  }[]
}

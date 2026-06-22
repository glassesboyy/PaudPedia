import type { PAUDScale as AssessmentScale } from './enums'
export type Semester = '1' | '2'

export interface DevelopmentIndicator {
  id: number
  program_id: number
  name: string
  order: number
}

export interface DevelopmentProgram {
  id: number
  name: string
  order: number
  indicators: DevelopmentIndicator[]
}

export interface AssessmentRecord {
  student_id: number
  name: string
  nisn: string | null
  assessments: Record<number, AssessmentScale> // mapping indicator_id to scale value
  notes?: Record<number, string> // Optional notes mapping indicator_id to notes (if you want notes per indicator. Actually, the backend API uses notes. Let's see how the backend stores it.)
}

export interface AssessmentResponse {
  data: AssessmentRecord[]
  programs: DevelopmentProgram[]
  month: string
  semester: Semester
  academic_year: string
  class: {
    id: number
    name: string
  }
}

export interface BulkAssessmentPayload {
  month: string
  semester: Semester
  academic_year: string
  assessments: {
    student_id: number
    indicator_id: number
    scale: AssessmentScale
    notes?: string | null
  }[]
}

export interface AssessmentHistoryItem {
  id: number
  aspect: string
  scale: AssessmentScale
  scale_label: string
  scale_color: string
  description: string
  notes: string | null
  assessed_at: string | null
}

export interface AssessmentSemesterGroup {
  academic_year: string
  semester: Semester
  semester_label: string
  is_report_generated?: boolean
  matrix: Record<number, Record<string, {
    scale: AssessmentScale
    scale_label: string
    scale_color: string
  }>>
}

export interface StudentAssessmentHistoryResponse {
  programs?: DevelopmentProgram[]
  history: AssessmentSemesterGroup[]
}

export interface AssessmentMatrixResponse {
  programs: DevelopmentProgram[]
  matrix: Record<number, Record<string, {
    scale: AssessmentScale
    scale_label: string
    scale_color: string
  }>>
  student: {
    id: number
    name: string
  }
}

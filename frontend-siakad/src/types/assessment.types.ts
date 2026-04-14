import type { PAUDScale as AssessmentScale } from './enums'
export type Semester = '1' | '2'

export interface AssessmentRecord {
  student_id: number
  name: string
  nisn: string | null
  scale: AssessmentScale | null
  notes: string | null
  assessment_id: number | null
}

export interface AssessmentResponse {
  data: AssessmentRecord[]
  aspect: string
  semester: Semester
  academic_year: string
  class: {
    id: number
    name: string
  }
}

export interface BulkAssessmentPayload {
  aspect: string
  semester: Semester
  academic_year: string
  assessments: {
    student_id: number
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
  items: AssessmentHistoryItem[]
}

export interface StudentAssessmentHistoryResponse {
  history: AssessmentSemesterGroup[]
}

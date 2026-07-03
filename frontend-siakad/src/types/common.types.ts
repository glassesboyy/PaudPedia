import type { SubscriptionPlan, UserRole } from './enums'

export interface User {
  id: number
  name: string
  email: string
  avatar: string | null
  email_verified_at: string | null
  roles?: string[]
  school_memberships?: SchoolMembership[]
}

export interface School {
  id: number
  name: string
  npsn: string
  address: string
  phone: string | null
  email: string | null
  logo: string | null
  logo_url: string | null
  subscription_plan: SubscriptionPlan
  student_limit: number | null
  teacher_limit: number | null
  total_students: number
  total_teachers: number
  total_classes: number
  total_parents: number
}

export interface SchoolMembership {
  id: number
  school_id: number
  school: School
  role_type: UserRole
  is_active: boolean
}

export interface Operator {
  id: number
  user_id: number
  name: string
  email: string
  phone: string | null
  avatar_url: string | null
  is_active: boolean
  joined_at: string
}

export interface Teacher {
  id: number
  user_id: number
  name: string
  email: string
  phone: string | null
  avatar_url: string | null
  nip: string | null
  specialization: string | null
  is_active: boolean
  joined_at: string
}

export interface ClassRoom {
  id: number
  school_id: number
  homeroom_teacher_id: number | null
  homeroom_teacher_name: string | null
  name: string
  level: string | null
  capacity: number | null
  current_students: number
  academic_year: string | null
}

export interface ParentProfile {
  id: number
  school_id: number
  user_id: number
  email: string
  father_name: string | null
  mother_name: string | null
  phone: string
  father_occupation: string | null
  mother_occupation: string | null
  address: string | null
  children_count?: number
  children?: Student[]
  created_at: string
  updated_at: string
}

export interface Student {
  id: number
  school_id: number
  class_id: number | null
  parent_profile_id: number
  name: string
  nisn: string | null
  birth_date: string
  gender: string
  address: string | null
  photo_url: string | null
  enrollment_date: string | null
  status: string
  parent?: ParentProfile
  class?: ClassRoom
  created_at: string
  updated_at: string
}

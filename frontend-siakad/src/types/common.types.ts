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
}

export interface SchoolMembership {
  school_id: number
  school: School
  role_type: UserRole
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
  bio: string | null
  is_active: boolean
  joined_at: string
}

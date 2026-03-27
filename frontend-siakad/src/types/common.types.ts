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
  logo: string | null
  subscription_plan: SubscriptionPlan
  student_limit: number | null
  teacher_limit: number | null
}

export interface SchoolMembership {
  school_id: number
  school: School
  role_type: UserRole
}

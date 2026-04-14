export enum UserRole {
  HEADMASTER = 'headmaster',
  TEACHER = 'teacher',
  PARENT = 'parent',
}

export enum SubscriptionPlan {
  FREE = 'free',
  PRO = 'pro',
}

export enum AttendanceStatus {
  PRESENT = 'present',
  SICK = 'sick',
  PERMISSION = 'permission',
  ABSENT = 'absent',
}

export enum PAUDScale {
  BB = 'BB', // Belum Berkembang
  MB = 'MB', // Mulai Berkembang
  BSH = 'BSH', // Berkembang Sesuai Harapan
  BSB = 'BSB', // Berkembang Sangat Baik
}

export enum ClassLevel {
  KB = 'Kelompok Bermain (KB)',
  TK_A = 'TK A',
  TK_B = 'TK B',
  TPA = 'TPA',
  SPS = 'SPS',
}

export enum Gender {
  MALE = 'male',
  FEMALE = 'female',
}

export enum StudentStatus {
  ACTIVE = 'active',
  GRADUATED = 'graduated',
  TRANSFERRED = 'transferred',
}

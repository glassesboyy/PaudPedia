export const ENDPOINTS = {
  AUTH: {
    LOGIN: '/api/v1/auth/login',
    LOGOUT: '/api/v1/auth/logout',
    ME: '/api/v1/auth/me',
    REGISTER_SCHOOL: '/api/v1/auth/register-school',
  },
  SCHOOL: {
    BASE: (schoolId: number) => `/api/v1/school/${schoolId}`,
    PROFILE: (schoolId: number) => `/api/v1/school/${schoolId}/profile`,
    SUBSCRIPTION: (schoolId: number) => `/api/v1/school/${schoolId}/subscription`,
    MEMBERSHIPS: '/api/v1/my-memberships',
  },
  STUDENTS: {
    LIST: (schoolId: number) => `/api/v1/school/${schoolId}/students`,
    DETAIL: (schoolId: number, id: number) => `/api/v1/school/${schoolId}/students/${id}`,
  },
  TEACHERS: {
    LIST: (schoolId: number) => `/api/v1/school/${schoolId}/teachers`,
    DETAIL: (schoolId: number, id: number) => `/api/v1/school/${schoolId}/teachers/${id}`,
  },
  PARENTS: {
    LIST: (schoolId: number) => `/api/v1/school/${schoolId}/parents`,
    DETAIL: (schoolId: number, id: number) => `/api/v1/school/${schoolId}/parents/${id}`,
  },
  CLASSES: {
    LIST: (schoolId: number) => `/api/v1/school/${schoolId}/classes`,
    DETAIL: (schoolId: number, id: number) => `/api/v1/school/${schoolId}/classes/${id}`,
  },
  ATTENDANCE: {
    LIST: (schoolId: number) => `/api/v1/school/${schoolId}/attendance`,
    BULK: (schoolId: number) => `/api/v1/school/${schoolId}/attendance/bulk`,
  },
  ASSESSMENTS: {
    LIST: (schoolId: number) => `/api/v1/school/${schoolId}/assessments`,
    CATEGORIES: (schoolId: number) => `/api/v1/school/${schoolId}/assessments/categories`,
  },
  FINANCES: {
    SPP: (schoolId: number) => `/api/v1/school/${schoolId}/finances/spp`,
    SAVINGS: (schoolId: number) => `/api/v1/school/${schoolId}/finances/savings`,
  },
  REPORTS: {
    DETAIL: (schoolId: number, studentId: number) =>
      `/api/v1/school/${schoolId}/reports/student/${studentId}`,
    PDF: (schoolId: number, studentId: number) =>
      `/api/v1/school/${schoolId}/reports/student/${studentId}/pdf`,
  },
}

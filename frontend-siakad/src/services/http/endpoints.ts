export const ENDPOINTS = {
  AUTH: {
    LOGIN: '/api/v1/auth/login',
    LOGOUT: '/api/v1/auth/logout',
    ME: '/api/v1/auth/me',
    REGISTER_SCHOOL: '/api/v1/auth/register-school',
  },
  SCHOOL: {
    BASE: (schoolId: number) => `/api/v1/schools/${schoolId}`,
    PROFILE: (schoolId: number) => `/api/v1/schools/${schoolId}`,
    SUBSCRIPTION: (schoolId: number) => `/api/v1/schools/${schoolId}/subscription`,
    MEMBERSHIPS: '/api/v1/my-memberships',
  },
  STUDENTS: {
    LIST: (schoolId: number) => `/api/v1/schools/${schoolId}/students`,
    DETAIL: (schoolId: number, id: number) => `/api/v1/schools/${schoolId}/students/${id}`,
  },
  TEACHERS: {
    LIST: (schoolId: number) => `/api/v1/schools/${schoolId}/teachers`,
    DETAIL: (schoolId: number, id: number) => `/api/v1/schools/${schoolId}/teachers/${id}`,
    TOGGLE_ACTIVE: (schoolId: number, id: number) => `/api/v1/schools/${schoolId}/teachers/${id}/toggle-active`,
  },
  PARENTS: {
    LIST: (schoolId: number) => `/api/v1/schools/${schoolId}/parents`,
    DETAIL: (schoolId: number, id: number) => `/api/v1/schools/${schoolId}/parents/${id}`,
  },
  CLASSES: {
    LIST: (schoolId: number) => `/api/v1/schools/${schoolId}/classes`,
    DETAIL: (schoolId: number, id: number) => `/api/v1/schools/${schoolId}/classes/${id}`,
  },
  ATTENDANCE: {
    LIST: (schoolId: number) => `/api/v1/schools/${schoolId}/attendance`,
    BULK: (schoolId: number) => `/api/v1/schools/${schoolId}/attendance/bulk`,
  },
  ASSESSMENTS: {
    LIST: (schoolId: number) => `/api/v1/schools/${schoolId}/assessments`,
    CATEGORIES: (schoolId: number) => `/api/v1/schools/${schoolId}/assessments/categories`,
  },
  FINANCES: {
    SPP: (schoolId: number) => `/api/v1/schools/${schoolId}/finances/spp`,
    SAVINGS: (schoolId: number) => `/api/v1/schools/${schoolId}/finances/savings`,
  },
  REPORTS: {
    DETAIL: (schoolId: number, studentId: number) =>
      `/api/v1/schools/${schoolId}/reports/student/${studentId}`,
    PDF: (schoolId: number, studentId: number) =>
      `/api/v1/schools/${schoolId}/reports/student/${studentId}/pdf`,
  },
}

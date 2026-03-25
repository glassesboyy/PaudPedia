import { createRouter, createWebHistory } from 'vue-router'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    // Auth routes (public)
    {
      path: '/login',
      name: 'Login',
      component: () => import('@/features/auth/views/LoginView.vue'),
      meta: { guest: true },
    },
    {
      path: '/register',
      name: 'RegisterSchool',
      component: () => import('@/features/auth/views/RegisterSchoolView.vue'),
      meta: { guest: true },
    },
    {
      path: '/forgot-password',
      name: 'ForgotPassword',
      component: () => import('@/features/auth/views/ForgotPasswordView.vue'),
      meta: { guest: true },
    },
    {
      path: '/reset-password',
      name: 'ResetPassword',
      component: () => import('@/features/auth/views/ResetPasswordView.vue'),
      meta: { guest: true },
    },

    // School selection (after login, before dashboard)
    {
      path: '/select-school',
      name: 'SelectSchool',
      component: () => import('@/app/layouts/SchoolSelectorLayout.vue'),
      meta: { requiresAuth: true },
    },

    // Dashboard routes (role-based, nested under DashboardLayout)
    {
      path: '/',
      component: () => import('@/app/layouts/DashboardLayout.vue'),
      meta: { requiresAuth: true, requiresSchool: true },
      children: [
        {
          path: '',
          name: 'Dashboard',
          component: () => import('@/features/dashboard/views/HeadmasterDashboard.vue'),
        },

        // School Management
        {
          path: 'school/profile',
          name: 'SchoolProfile',
          component: () => import('@/features/school/views/SchoolProfileView.vue'),
          meta: { roles: ['headmaster'] },
        },
        {
          path: 'school/subscription',
          name: 'Subscription',
          component: () => import('@/features/school/views/SubscriptionView.vue'),
          meta: { roles: ['headmaster'] },
        },

        // Classes
        {
          path: 'classes',
          name: 'ClassList',
          component: () => import('@/features/classes/views/ClassListView.vue'),
          meta: { roles: ['headmaster'] },
        },
        {
          path: 'classes/create',
          name: 'ClassCreate',
          component: () => import('@/features/classes/views/ClassFormView.vue'),
          meta: { roles: ['headmaster'] },
        },
        {
          path: 'classes/:id',
          name: 'ClassDetail',
          component: () => import('@/features/classes/views/ClassDetailView.vue'),
          meta: { roles: ['headmaster'] },
        },

        // Teachers
        {
          path: 'teachers',
          name: 'TeacherList',
          component: () => import('@/features/teachers/views/TeacherListView.vue'),
          meta: { roles: ['headmaster'] },
        },
        {
          path: 'teachers/create',
          name: 'TeacherCreate',
          component: () => import('@/features/teachers/views/TeacherFormView.vue'),
          meta: { roles: ['headmaster'] },
        },

        // Parents
        {
          path: 'parents',
          name: 'ParentList',
          component: () => import('@/features/parents/views/ParentListView.vue'),
          meta: { roles: ['headmaster'] },
        },
        {
          path: 'parents/create',
          name: 'ParentCreate',
          component: () => import('@/features/parents/views/ParentFormView.vue'),
          meta: { roles: ['headmaster'] },
        },

        // Students
        {
          path: 'students',
          name: 'StudentList',
          component: () => import('@/features/students/views/StudentListView.vue'),
          meta: { roles: ['headmaster', 'teacher'] },
        },
        {
          path: 'students/create',
          name: 'StudentCreate',
          component: () => import('@/features/students/views/StudentFormView.vue'),
          meta: { roles: ['headmaster'] },
        },
        {
          path: 'students/:id',
          name: 'StudentDetail',
          component: () => import('@/features/students/views/StudentDetailView.vue'),
          meta: { roles: ['headmaster', 'teacher'] },
        },

        // Attendance
        {
          path: 'attendance',
          name: 'AttendanceInput',
          component: () => import('@/features/attendance/views/AttendanceInputView.vue'),
          meta: { roles: ['headmaster', 'teacher'] },
        },
        {
          path: 'attendance/history',
          name: 'AttendanceHistory',
          component: () => import('@/features/attendance/views/AttendanceHistoryView.vue'),
          meta: { roles: ['headmaster', 'teacher'] },
        },

        // Assessments
        {
          path: 'assessments',
          name: 'AssessmentInput',
          component: () => import('@/features/assessments/views/AssessmentInputView.vue'),
          meta: { roles: ['headmaster', 'teacher'] },
        },

        // Finances (Pro Plan)
        {
          path: 'finances',
          name: 'FinanceOverview',
          component: () => import('@/features/finances/views/FinanceOverviewView.vue'),
          meta: { roles: ['headmaster'], requiresPro: true },
        },
        {
          path: 'finances/spp',
          name: 'SppManagement',
          component: () => import('@/features/finances/views/SppManagementView.vue'),
          meta: { roles: ['headmaster'], requiresPro: true },
        },

        // Reports (Pro Plan)
        {
          path: 'reports',
          name: 'ReportSelection',
          component: () => import('@/features/reports/views/ReportSelectionView.vue'),
          meta: { roles: ['headmaster', 'teacher'], requiresPro: true },
        },

        // Parent Portal
        {
          path: 'children',
          name: 'ChildrenList',
          component: () => import('@/features/parent-portal/views/ChildrenListView.vue'),
          meta: { roles: ['parent'] },
        },
        {
          path: 'children/:id',
          name: 'ChildDetail',
          component: () => import('@/features/parent-portal/views/ChildDetailView.vue'),
          meta: { roles: ['parent'] },
        },
      ],
    },

    // 404
    {
      path: '/:pathMatch(.*)*',
      name: 'NotFound',
      component: {
        template: '<div class="flex items-center justify-center h-screen"><h1 class="text-2xl">404 — Halaman tidak ditemukan</h1></div>',
      },
    },
  ],
})

export default router

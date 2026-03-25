import type { AxiosInstance, AxiosError } from 'axios'
import { useAuthStore } from '@/stores/auth.store'
import { useSchoolStore } from '@/stores/school.store'
import type { ValidationError } from '@/types'

export function setupInterceptors(instance: AxiosInstance) {
  // Request interceptor - add auth token and school context
  instance.interceptors.request.use((config) => {
    const authStore = useAuthStore()
    const schoolStore = useSchoolStore()

    // Add auth token
    if (authStore.token) {
      config.headers.Authorization = `Bearer ${authStore.token}`
    }

    // Add school context header (optional, for validation)
    if (schoolStore.currentSchoolId) {
      config.headers['X-School-ID'] = String(schoolStore.currentSchoolId)
    }

    return config
  })

  // Response interceptor - handle errors globally
  instance.interceptors.response.use(
    // On success: unwrap response.data
    (response) => response.data,

    // On error: handle by status code
    (error: AxiosError<ValidationError>) => {
      // Network error (no response)
      if (!error.response) {
        showToast('Koneksi gagal. Periksa jaringan Anda.')
        return Promise.reject(error)
      }

      const { status } = error.response

      switch (status) {
        case 401:
          // Unauthorized: force logout
          useAuthStore().logout()
          break

        case 403:
          // Forbidden: wrong school or role
          showToast('Anda tidak memiliki akses')
          break

        case 422:
          // Validation errors: extract and return for form use
          return Promise.reject(error.response.data?.errors ?? error.response.data)

        case 500:
          showToast('Terjadi kesalahan pada server. Coba lagi nanti.')
          break
      }

      return Promise.reject(error)
    },
  )
}

/**
 * Temporary toast helper.
 * Will be replaced by the useToast composable once implemented.
 */
function showToast(message: string) {
  // TODO: Replace with useToast().error(message) once toast composable is implemented
  console.error('[Toast]', message)
}

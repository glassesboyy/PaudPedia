/**
 * User Dashboard Service
 *
 * API calls for authenticated user dashboard:
 * - My Courses, Products, Webinars, Certificates, Transactions
 */
import type {
    Transaction,
    UserCertificate,
    UserCourse,
    UserProduct,
    UserWebinar,
} from '~~/types'
import { useApiFetch } from './api/client'
import { API_ENDPOINTS } from './api/endpoints'
import type { ApiResponse, PaginatedResponse } from './api/types'

export const dashboardService = {
  // ── My Courses (FR-UA-08) ──────────────────────────────
  async getCourses(params?: Record<string, unknown>): Promise<PaginatedResponse<UserCourse>> {
    const apiFetch = useApiFetch()
    return apiFetch(API_ENDPOINTS.USER.COURSES, { params })
  },

  // ── My Products (FR-UA-09) ─────────────────────────────
  async getProducts(params?: Record<string, unknown>): Promise<PaginatedResponse<UserProduct>> {
    const apiFetch = useApiFetch()
    return apiFetch(API_ENDPOINTS.USER.PRODUCTS, { params })
  },

  getProductDownloadUrl(productId: number): string {
    const config = useRuntimeConfig()
    return `${config.public.apiBase}${API_ENDPOINTS.USER.PRODUCT_DOWNLOAD(productId)}`
  },

  // ── My Webinars (FR-UA-10) ─────────────────────────────
  async getWebinars(params?: Record<string, unknown>): Promise<PaginatedResponse<UserWebinar>> {
    const apiFetch = useApiFetch()
    return apiFetch(API_ENDPOINTS.USER.WEBINARS, { params })
  },

  // ── My Certificates (FR-UA-11) ─────────────────────────
  async getCertificates(params?: Record<string, unknown>): Promise<PaginatedResponse<UserCertificate>> {
    const apiFetch = useApiFetch()
    return apiFetch(API_ENDPOINTS.USER.CERTIFICATES, { params })
  },

  // ── Transactions (FR-UA-12) ────────────────────────────
  async getTransactions(params?: Record<string, unknown>): Promise<PaginatedResponse<Transaction>> {
    const apiFetch = useApiFetch()
    return apiFetch(API_ENDPOINTS.USER.TRANSACTIONS, { params })
  },

  async getTransactionDetail(id: number): Promise<ApiResponse<Transaction>> {
    const apiFetch = useApiFetch()
    return apiFetch(API_ENDPOINTS.USER.TRANSACTION_DETAIL(id))
  },
}

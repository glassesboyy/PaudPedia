/**
 * Contact Service
 *
 * Fetches contact information from the backend.
 * Note: Contact form submission is handled via MailJS on the frontend,
 * not through the backend API.
 */
import type { ContactPageInfo } from '~~/types'
import { useApiFetch } from './api/client'
import { API_ENDPOINTS } from './api/endpoints'
import type { ApiResponse } from './api/types'

export const contactService = {
  /** Fetch company contact information */
  async getInfo(): Promise<ApiResponse<ContactPageInfo>> {
    const apiFetch = useApiFetch()
    return apiFetch(API_ENDPOINTS.CONTACT.INDEX)
  },
}

/**
 * Landing Page Service
 *
 * Fetches aggregated landing page data from the backend.
 * The /landing endpoint returns all sections in a single cached response.
 */
import type { LandingPageData } from '~~/types'
import { useApiFetch } from './api/client'
import { API_ENDPOINTS } from './api/endpoints'
import type { ApiResponse } from './api/types'

export const landingService = {
  /** Fetch all landing page data in one request (cached 10min server-side) */
  async getData(): Promise<ApiResponse<LandingPageData>> {
    const apiFetch = useApiFetch()
    return apiFetch(API_ENDPOINTS.LANDING.INDEX)
  },
}

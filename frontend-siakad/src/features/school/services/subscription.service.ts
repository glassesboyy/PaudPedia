import { http as api } from '@/services/http/client'
import type { SubscriptionInfo, SubscriptionOrder, UpgradeResponse } from '@/types/subscription.types'

export const subscriptionService = {
  /**
   * Get current subscription info for a school
   */
  getInfo(schoolId: number) {
    return api.get<SubscriptionInfo>(`/api/v1/schools/${schoolId}/subscription`)
  },

  /**
   * Initiate Pro Plan upgrade via Midtrans
   */
  initiateUpgrade(schoolId: number) {
    return api.post<UpgradeResponse>(`/api/v1/schools/${schoolId}/subscription/upgrade`)
  },

  /**
   * Get subscription payment history
   */
  getPaymentHistory(schoolId: number) {
    return api.get<{ data: SubscriptionOrder[] }>(`/api/v1/schools/${schoolId}/subscription/payment-history`)
  },
}

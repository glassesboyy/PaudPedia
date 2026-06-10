export interface SubscriptionInfo {
  plan: 'free' | 'pro'
  plan_label: string
  is_pro: boolean
  student_usage: number
  student_limit: number | null
  teacher_usage: number
  teacher_limit: number | null
  features: string[]
  subscription_started_at: string | null
  subscription_ended_at: string | null
  pro_monthly_price: number
  pro_monthly_price_formatted: string
}

export interface SubscriptionOrder {
  id: number
  amount: number
  amount_formatted: string
  status: 'pending' | 'paid' | 'failed' | 'expired'
  status_label: string
  duration_months: number
  payment_method: string | null
  paid_at: string | null
  created_at: string
}

export interface UpgradeResponse {
  message: string
  snap_token: string
  order_id: string
  amount: number
  amount_formatted: string
}

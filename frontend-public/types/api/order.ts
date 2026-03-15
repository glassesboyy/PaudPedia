/**
 * Order Types
 */
import type { PaginationParams } from './common'

export type OrderStatus = 'pending' | 'paid' | 'failed' | 'cancelled' | 'expired'

export interface Order {
  id: number
  order_number: string
  status: OrderStatus
  total: number
  discount: number
  grand_total: number
  items: OrderItem[]
  payment_method: string | null
  paid_at: string | null
  created_at: string
  updated_at: string
}

export interface OrderItem {
  id: number
  itemable_type: string
  itemable_id: number
  name: string
  price: number
  quantity: number
}

export interface OrderListParams extends PaginationParams {
  status?: OrderStatus
}

// ── Checkout Types ─────────────────────────────────────

export interface CheckoutResponse {
  order: CheckoutOrder
  snap_token: string
}

export interface CheckoutOrder {
  id: number
  order_number: string
  status: OrderStatus
  status_label: string
  status_color: string
  total_amount: number
  discount_amount: number
  final_amount: number
  payment_method: string | null
  items: CheckoutOrderItem[]
  paid_at: string | null
  created_at: string
  created_date: string
}

export interface CheckoutOrderItem {
  id: number
  type: string
  type_label: string
  title: string
  price: number
  quantity: number
  subtotal: number
}

// ── Promo Validation Types ─────────────────────────────

export interface PromoValidationResponse {
  valid: boolean
  message: string | null
  discount: number
  discount_display: string
  promo_code: {
    code: string
    discount_type: 'percentage' | 'fixed'
    discount_value: number
  } | null
}

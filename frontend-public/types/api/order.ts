/**
 * Order Types
 */
import type { PaginationParams } from './common'

export type OrderStatus = 'pending' | 'paid' | 'failed' | 'cancelled' | 'refunded'

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

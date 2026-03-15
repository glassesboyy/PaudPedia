/**
 * Cart Item (Client-side model)
 */
export interface CartItem {
  id: number
  type: 'course' | 'webinar' | 'product'
  name: string
  slug: string
  price: number
  thumbnail: string
  quantity: number
}

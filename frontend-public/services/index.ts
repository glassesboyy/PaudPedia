/**
 * Service Layer Barrel Export
 *
 * Import all services from here:
 * ```ts
 * import { courseService, authService } from '~/services'
 * ```
 */

export { articleService } from './article.service'
export { authService } from './auth.service'
export { cartService } from './cart.service'
export { categoryService } from './category.service'
export { checkoutService } from './checkout.service'
export { contactService } from './contact.service'
export { courseService } from './course.service'
export { dashboardService } from './dashboard.service'
export { landingService } from './landing.service'
export { lmsService } from './lms.service'
export { mentorService } from './mentor.service'
export { orderService } from './order.service'
export { productService } from './product.service'
export { webinarService } from './webinar.service'

// Re-export API utilities
export { useApiFetch } from './api/client'
export { API_ENDPOINTS } from './api/endpoints'
export type { ApiResponse, PaginatedResponse, PaginationMeta, ValidationErrorResponse } from './api/types'


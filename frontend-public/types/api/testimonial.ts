/**
 * Testimonial Types
 *
 * Matches TestimonialResource from the backend API.
 */

export interface Testimonial {
  id: number
  name: string
  title: string
  content: string
  rating: number
  star_rating: string
  photo_url: string | null
  is_featured: boolean
  created_at: string
}

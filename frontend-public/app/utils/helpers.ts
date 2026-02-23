/**
 * General Helpers
 *
 * Miscellaneous utility functions.
 */

/**
 * Calculate discount percentage.
 */
export function discountPercent(originalPrice: number, currentPrice: number): number {
  if (originalPrice <= 0) return 0
  return Math.round((1 - currentPrice / originalPrice) * 100)
}

/**
 * Generate a full asset URL from a relative path.
 */
export function assetUrl(path: string): string {
  const config = useRuntimeConfig()
  const base = (config.public.apiBase as string).replace('/api/v1', '')
  return `${base}/storage/${path}`
}

/**
 * Ensure a value is an array.
 */
export function toArray<T>(value: T | T[]): T[] {
  return Array.isArray(value) ? value : [value]
}

/**
 * Create a debounced version of a function.
 */
export function debounce<T extends (...args: unknown[]) => unknown>(
  fn: T,
  delay: number,
): (...args: Parameters<T>) => void {
  let timeout: ReturnType<typeof setTimeout>
  return (...args: Parameters<T>) => {
    clearTimeout(timeout)
    timeout = setTimeout(() => fn(...args), delay)
  }
}

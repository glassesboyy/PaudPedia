/**
 * Formatters
 *
 * Pure functions for formatting display values.
 */

/**
 * Format a number as Indonesian Rupiah currency.
 *
 * @example formatCurrency(150000) → 'Rp150.000'
 */
export function formatCurrency(value: number): string {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
  }).format(value)
}

/**
 * Format an ISO date string to a human-readable local date.
 *
 * @example formatDate('2026-02-24T10:00:00Z') → '24 Februari 2026'
 */
export function formatDate(isoString: string): string {
  return new Date(isoString).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  })
}

/**
 * Format an ISO date string to a short date.
 *
 * @example formatDateShort('2026-02-24T10:00:00Z') → '24 Feb 2026'
 */
export function formatDateShort(isoString: string): string {
  return new Date(isoString).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  })
}

/**
 * Format an ISO datetime string including time.
 *
 * @example formatDateTime('2026-02-24T10:30:00Z') → '24 Feb 2026, 10:30'
 */
export function formatDateTime(isoString: string): string {
  return new Date(isoString).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

/**
 * Truncate text to a maximum length, appending ellipsis.
 */
export function truncate(text: string, maxLength: number): string {
  if (text.length <= maxLength) return text
  return `${text.slice(0, maxLength).trimEnd()}…`
}

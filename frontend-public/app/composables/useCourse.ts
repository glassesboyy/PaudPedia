/**
 * useCourse Composable
 *
 * Course-related helpers & computed properties.
 * Will be extended during feature development.
 */
export function useCourse() {
  /**
   * Format the course level label for display.
   */
  function levelLabel(level: string): string {
    const labels: Record<string, string> = {
      beginner: 'Pemula',
      intermediate: 'Menengah',
      advanced: 'Lanjutan',
    }
    return labels[level] ?? level
  }

  /**
   * Format duration from hours to human-readable string.
   */
  function formatDuration(hours: number): string {
    if (hours < 1) return `${Math.round(hours * 60)} menit`
    const h = Math.floor(hours)
    const m = Math.round((hours - h) * 60)
    return m > 0 ? `${h} jam ${m} menit` : `${h} jam`
  }

  return {
    levelLabel,
    formatDuration,
  }
}

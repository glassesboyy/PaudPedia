/**
 * useFilters Composable
 *
 * Reusable filter / sort state bound to URL query params.
 */
import type { PaginationParams } from '~~/types'

export function useFilters(defaults?: Partial<PaginationParams>) {
  const route = useRoute()
  const router = useRouter()

  const filters = reactive<PaginationParams>({
    page: Number(route.query.page) || defaults?.page || 1,
    per_page: Number(route.query.per_page) || defaults?.per_page || 12,
    search: (route.query.search as string) || defaults?.search || '',
    sort_by: (route.query.sort_by as string) || defaults?.sort_by || 'created_at',
    sort_order: (route.query.sort_order as 'asc' | 'desc') || defaults?.sort_order || 'desc',
  })

  /** Sync filters back to URL query params */
  function applyFilters() {
    router.push({ query: { ...route.query, ...filters } })
  }

  function resetFilters() {
    Object.assign(filters, {
      page: 1,
      search: '',
      sort_by: 'created_at',
      sort_order: 'desc',
    })
    applyFilters()
  }

  function goToPage(page: number) {
    filters.page = page
    applyFilters()
  }

  return {
    filters,
    applyFilters,
    resetFilters,
    goToPage,
  }
}

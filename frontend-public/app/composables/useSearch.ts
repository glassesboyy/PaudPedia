/**
 * useSearch Composable
 *
 * Global search state & helpers.
 */
export function useSearch() {
  const query = ref('')
  const isOpen = ref(false)

  function open() {
    isOpen.value = true
  }

  function close() {
    isOpen.value = false
    query.value = ''
  }

  function submit() {
    if (!query.value.trim()) return
    navigateTo({ path: '/search', query: { q: query.value } })
    close()
  }

  return {
    query,
    isOpen,
    open,
    close,
    submit,
  }
}

/**
 * SidebarSection — Shared type for sidebar-navigated page sections.
 *
 * Used by SidebarLayout component and pages (Privacy Policy, Terms, FAQ).
 */
export interface SidebarSection {
  id: string
  icon: string
  title: string
  /** Optional subtitle shown below the title in the sidebar (e.g. "3 pertanyaan") */
  subtitle?: string
  /** Plain text content — rendered by built-in content panel when no slot is used */
  content?: string
  list?: string[]
  /** If true, appends a NuxtLink to /contact after the content text */
  contactLink?: boolean
}

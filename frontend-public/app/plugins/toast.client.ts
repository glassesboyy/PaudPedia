/**
 * Toast Plugin (Client-only)
 *
 * Ensures the toast system is available on the client.
 * Server-side toast calls are silently ignored.
 */
export default defineNuxtPlugin(() => {
  // Toast rendering is handled by the UI store + a future ToastContainer component.
  // This plugin can be used to integrate a third-party toast library if needed.
})

/**
 * useBreakpoints Composable
 *
 * Responsive breakpoint helpers using @vueuse/core.
 */
import { useBreakpoints as _useBreakpoints } from '@vueuse/core'

const breakpointsTailwind = {
  sm: 640,
  md: 768,
  lg: 1024,
  xl: 1280,
  '2xl': 1536,
}

export function useBreakpoint() {
  const breakpoints = _useBreakpoints(breakpointsTailwind)

  return {
    isMobile: breakpoints.smaller('md'),
    isTablet: breakpoints.between('md', 'lg'),
    isDesktop: breakpoints.greaterOrEqual('lg'),
    current: breakpoints.current(),
  }
}

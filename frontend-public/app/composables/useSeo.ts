/**
 * useSeo Composable
 *
 * Convenience wrapper around useSeoMeta for consistent SEO tags.
 */
interface SeoOptions {
  title: string
  description?: string
  image?: string
  url?: string
}

export function useSeo(options: SeoOptions) {
  const config = useRuntimeConfig()
  const appName = config.public.appName as string

  useSeoMeta({
    title: `${options.title} | ${appName}`,
    ogTitle: `${options.title} | ${appName}`,
    description: options.description,
    ogDescription: options.description,
    ogImage: options.image,
    ogUrl: options.url,
    twitterCard: 'summary_large_image',
  })

  useHead({
    title: `${options.title} | ${appName}`,
  })
}

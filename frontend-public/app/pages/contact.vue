<script setup lang="ts">
/**
 * Contact Page
 * Route: /contact
 *
 * FR-PP-05: Halaman Kontak.
 * Contact form is frontend-only using MailJS (no backend POST endpoint).
 * Contact info fetched from backend API.
 */
import type { ContactFormData, ContactPageInfo } from '~~/types'

useSeo({
  title: 'Hubungi Kami',
  description: 'Hubungi tim PaudPedia melalui form kontak, email, WhatsApp, atau telepon.',
})

// ─── Contact Info ───
const contactInfo = ref<ContactPageInfo | null>(null)
const loading = ref(true)
const error = ref(false)

async function fetchContactInfo() {
  loading.value = true
  error.value = false
  try {
    const { contactService } = await import('~~/services')
    const res = await contactService.getInfo()
    if (res.success && res.data) {
      contactInfo.value = res.data
    }
    else {
      error.value = true
    }
  }
  catch {
    error.value = true
  }
  finally {
    loading.value = false
  }
}

onMounted(fetchContactInfo)

// ─── Contact Form (MailJS — frontend only) ───
const form = reactive<ContactFormData>({
  name: '',
  email: '',
  subject: '',
  message: '',
})
const sending = ref(false)
const sent = ref(false)
const formError = ref('')

async function handleSubmit() {
  formError.value = ''

  // Basic validation
  if (!form.name.trim() || !form.email.trim() || !form.subject.trim() || !form.message.trim()) {
    formError.value = 'Semua field wajib diisi.'
    return
  }
  if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) {
    formError.value = 'Alamat email tidak valid.'
    return
  }

  sending.value = true
  try {
    // MailJS integration placeholder
    // In production, replace with actual emailjs.send() call:
    // await emailjs.send('SERVICE_ID', 'TEMPLATE_ID', { ...form }, 'PUBLIC_KEY')
    await new Promise(resolve => setTimeout(resolve, 1500)) // simulate

    sent.value = true
    form.name = ''
    form.email = ''
    form.subject = ''
    form.message = ''
  }
  catch {
    formError.value = 'Gagal mengirim pesan. Silakan coba lagi.'
  }
  finally {
    sending.value = false
  }
}

const infoCards = computed(() => {
  if (!contactInfo.value) return []
  return [
    {
      icon: 'lucide:mail',
      label: 'Email',
      value: contactInfo.value.email,
      href: `mailto:${contactInfo.value.email}`,
    },
    {
      icon: 'lucide:phone',
      label: 'Telepon',
      value: contactInfo.value.phone,
      href: `tel:${contactInfo.value.phone}`,
    },
    {
      icon: 'lucide:message-circle',
      label: 'WhatsApp',
      value: contactInfo.value.whatsapp,
      href: contactInfo.value.whatsapp_link,
    },
    {
      icon: 'lucide:map-pin',
      label: 'Alamat',
      value: contactInfo.value.address,
      href: null,
    },
  ]
})
</script>

<template>
  <div>
    <!-- Hero -->
    <section class="bg-gradient-to-br from-primary-50 to-surface">
      <div class="container py-14 sm:py-20 text-center">
        <h1 class="text-3xl sm:text-4xl font-bold text-heading">Hubungi Kami</h1>
        <p class="mt-4 text-base text-body max-w-2xl mx-auto leading-relaxed">
          Punya pertanyaan atau saran? Kami senang mendengar dari Anda.
        </p>
      </div>
    </section>

    <!-- Content: Contact Info + Form -->
    <section class="bg-surface">
      <div class="container py-14 sm:py-20">
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-10 max-w-5xl mx-auto">

          <!-- Contact Info Cards -->
          <div class="lg:col-span-2 space-y-4">
            <h2 class="text-lg font-semibold text-heading mb-4">Informasi Kontak</h2>

            <template v-if="loading">
              <div v-for="i in 4" :key="i" class="p-4 rounded-xl border border-border bg-surface-muted animate-pulse">
                <div class="h-4 w-20 bg-muted/20 rounded mb-2" />
                <div class="h-3 w-32 bg-muted/20 rounded" />
              </div>
            </template>

            <template v-else-if="error">
              <div class="p-4 rounded-xl border border-danger-200 bg-danger-50 text-sm text-danger-600">
                Gagal memuat informasi kontak.
                <button type="button" class="underline ml-1" @click="fetchContactInfo">Coba lagi</button>
              </div>
            </template>

            <template v-else>
              <component
                :is="card.href ? 'a' : 'div'"
                v-for="card in infoCards"
                :key="card.label"
                :href="card.href ?? undefined"
                :target="card.href?.startsWith('http') ? '_blank' : undefined"
                :rel="card.href?.startsWith('http') ? 'noopener noreferrer' : undefined"
                class="flex items-start gap-3 p-4 rounded-xl border border-border hover:border-primary-200 hover:bg-primary-50/50 transition-colors"
                :class="card.href ? 'cursor-pointer' : ''"
              >
                <div class="shrink-0 w-10 h-10 rounded-xl bg-primary-100 flex items-center justify-center">
                  <Icon :name="card.icon" class="w-5 h-5 text-primary-600" />
                </div>
                <div>
                  <p class="text-xs text-muted font-medium">{{ card.label }}</p>
                  <p class="text-sm text-heading mt-0.5">{{ card.value }}</p>
                </div>
              </component>
            </template>

            <!-- Social Media -->
            <div v-if="contactInfo?.social_media" class="pt-4">
              <p class="text-xs text-muted font-medium mb-3">Media Sosial</p>
              <div class="flex gap-3">
                <a
                  v-if="contactInfo.social_media.instagram"
                  :href="contactInfo.social_media.instagram"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="w-10 h-10 rounded-xl bg-surface-muted flex items-center justify-center hover:bg-primary-100 transition-colors"
                  aria-label="Instagram"
                >
                  <Icon name="lucide:instagram" class="w-5 h-5 text-body" />
                </a>
                <a
                  v-if="contactInfo.social_media.facebook"
                  :href="contactInfo.social_media.facebook"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="w-10 h-10 rounded-xl bg-surface-muted flex items-center justify-center hover:bg-primary-100 transition-colors"
                  aria-label="Facebook"
                >
                  <Icon name="lucide:facebook" class="w-5 h-5 text-body" />
                </a>
                <a
                  v-if="contactInfo.social_media.youtube"
                  :href="contactInfo.social_media.youtube"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="w-10 h-10 rounded-xl bg-surface-muted flex items-center justify-center hover:bg-primary-100 transition-colors"
                  aria-label="YouTube"
                >
                  <Icon name="lucide:youtube" class="w-5 h-5 text-body" />
                </a>
                <a
                  v-if="contactInfo.social_media.tiktok"
                  :href="contactInfo.social_media.tiktok"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="w-10 h-10 rounded-xl bg-surface-muted flex items-center justify-center hover:bg-primary-100 transition-colors"
                  aria-label="TikTok"
                >
                  <Icon name="lucide:music-2" class="w-5 h-5 text-body" />
                </a>
              </div>
            </div>
          </div>

          <!-- Contact Form -->
          <div class="lg:col-span-3">
            <div class="p-6 sm:p-8 rounded-2xl border border-border bg-surface">
              <h2 class="text-lg font-semibold text-heading mb-6">Kirim Pesan</h2>

              <!-- Success State -->
              <div v-if="sent" class="text-center py-10">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-success-100 mb-4">
                  <Icon name="lucide:check-circle" class="w-8 h-8 text-success-600" />
                </div>
                <h3 class="text-base font-semibold text-heading mb-2">Pesan Terkirim!</h3>
                <p class="text-sm text-body mb-6">Terima kasih, kami akan segera merespons pesan Anda.</p>
                <button
                  type="button"
                  class="text-sm text-primary-600 hover:underline"
                  @click="sent = false"
                >
                  Kirim pesan lain
                </button>
              </div>

              <!-- Form -->
              <form v-else class="space-y-5" @submit.prevent="handleSubmit">
                <div>
                  <label for="contact-name" class="block text-sm font-medium text-heading mb-1.5">
                    Nama Lengkap <span class="text-danger-500">*</span>
                  </label>
                  <input
                    id="contact-name"
                    v-model="form.name"
                    type="text"
                    placeholder="Masukkan nama lengkap"
                    class="w-full px-4 py-2.5 rounded-xl border border-border text-sm text-heading placeholder:text-muted/50 focus:outline-none focus:ring-2 focus:ring-primary-300 focus:border-primary-400 transition-colors"
                  />
                </div>

                <div>
                  <label for="contact-email" class="block text-sm font-medium text-heading mb-1.5">
                    Email <span class="text-danger-500">*</span>
                  </label>
                  <input
                    id="contact-email"
                    v-model="form.email"
                    type="email"
                    placeholder="Masukkan alamat email"
                    class="w-full px-4 py-2.5 rounded-xl border border-border text-sm text-heading placeholder:text-muted/50 focus:outline-none focus:ring-2 focus:ring-primary-300 focus:border-primary-400 transition-colors"
                  />
                </div>

                <div>
                  <label for="contact-subject" class="block text-sm font-medium text-heading mb-1.5">
                    Subjek <span class="text-danger-500">*</span>
                  </label>
                  <input
                    id="contact-subject"
                    v-model="form.subject"
                    type="text"
                    placeholder="Masukkan subjek pesan"
                    class="w-full px-4 py-2.5 rounded-xl border border-border text-sm text-heading placeholder:text-muted/50 focus:outline-none focus:ring-2 focus:ring-primary-300 focus:border-primary-400 transition-colors"
                  />
                </div>

                <div>
                  <label for="contact-message" class="block text-sm font-medium text-heading mb-1.5">
                    Pesan <span class="text-danger-500">*</span>
                  </label>
                  <textarea
                    id="contact-message"
                    v-model="form.message"
                    rows="5"
                    placeholder="Tulis pesan Anda..."
                    class="w-full px-4 py-2.5 rounded-xl border border-border text-sm text-heading placeholder:text-muted/50 focus:outline-none focus:ring-2 focus:ring-primary-300 focus:border-primary-400 transition-colors resize-none"
                  />
                </div>

                <!-- Error -->
                <p v-if="formError" class="text-sm text-danger-600">{{ formError }}</p>

                <button
                  type="submit"
                  :disabled="sending"
                  class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-primary-500 text-white text-sm font-medium hover:bg-primary-600 focus:ring-2 focus:ring-primary-300 transition-colors disabled:opacity-60 disabled:cursor-not-allowed"
                >
                  <Icon v-if="sending" name="lucide:loader-2" class="w-4 h-4 animate-spin" />
                  <Icon v-else name="lucide:send" class="w-4 h-4" />
                  {{ sending ? 'Mengirim...' : 'Kirim Pesan' }}
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

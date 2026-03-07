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
  description: 'Hubungi tim PaudPedia melalui form kontak, email, atau telepon.',
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

const socialIcons: Record<string, string> = {
  instagram: 'lucide:instagram',
  facebook: 'lucide:facebook',
  youtube: 'lucide:youtube',
  linkedin: 'lucide:linkedin',
  twitter: 'lucide:twitter',
  tiktok: 'simple-icons:tiktok',
  telegram: 'lucide:send',
  discord: 'simple-icons:discord',
}

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
      href: contactInfo.value.email ? `mailto:${contactInfo.value.email}` : null,
    },
    {
      icon: 'lucide:phone',
      label: 'Telepon',
      value: contactInfo.value.phone,
      href: contactInfo.value.phone ? `tel:${contactInfo.value.phone}` : null,
    },
    {
      icon: 'lucide:map-pin',
      label: 'Alamat',
      value: contactInfo.value.address,
      href: null,
    },
    {
      icon: 'lucide:clock',
      label: 'Jam Operasional',
      value: 'Senin – Jumat, 08.00 – 17.00 WIB',
      href: null,
    },
  ].filter(card => card.value)
})
</script>

<template>
  <div>
    <!-- Hero -->
    <PageHero
      badge="Hubungi Kami"
      badge-icon="lucide:message-square"
      title="Ada yang Bisa Kami"
      highlight="Bantu?"
      description="Punya pertanyaan, saran, atau ingin berkolaborasi? Tim kami siap membantu Anda."
    />

    <!-- Section 1: Image + Form in one card -->
    <section class="bg-surface">
      <div class="container py-14 sm:py-20">
        <div class="max-w-5xl mx-auto rounded-2xl border border-border bg-surface shadow-soft overflow-hidden">
          <div class="grid grid-cols-1 lg:grid-cols-2">
            <!-- Left: Image -->
            <div class="hidden lg:block relative min-h-[480px]">
              <img
                src="/assets/contact/contact-1.jpg"
                alt="Hubungi PaudPedia"
                class="absolute inset-0 w-full h-full object-cover"
              />
            </div>

            <!-- Right: Contact Form -->
            <div class="p-6 sm:p-8">
            <div class="flex items-center gap-3 mb-6">
              <div class="w-10 h-10 rounded-xl bg-primary-50 flex items-center justify-center">
                <Icon name="lucide:pen-line" class="w-5 h-5 text-primary-600" />
              </div>
              <div>
                <h2 class="text-lg font-semibold text-heading">Kirim Pesan</h2>
                <p class="text-xs text-muted">Isi form berikut dan kami akan segera merespons.</p>
              </div>
            </div>

            <!-- Success State -->
            <div v-if="sent" class="text-center py-12">
              <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-success-50 border-2 border-success-200 mb-5">
                <Icon name="lucide:check-circle" class="w-8 h-8 text-success-600" />
              </div>
              <h3 class="text-lg font-semibold text-heading mb-2">Pesan Terkirim!</h3>
              <p class="text-sm text-body mb-6 max-w-sm mx-auto">Terima kasih telah menghubungi kami. Kami akan segera merespons pesan Anda.</p>
              <button
                type="button"
                class="inline-flex items-center gap-2 text-sm text-primary-600 font-medium hover:underline"
                @click="sent = false"
              >
                <Icon name="lucide:plus" class="w-4 h-4" />
                Kirim pesan lain
              </button>
            </div>

            <!-- Form -->
            <form v-else class="space-y-5" @submit.prevent="handleSubmit">
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                  <label for="contact-name" class="block text-sm font-medium text-heading mb-1.5">
                    Nama Lengkap <span class="text-danger-500">*</span>
                  </label>
                  <input
                    id="contact-name"
                    v-model="form.name"
                    type="text"
                    placeholder="Masukkan nama lengkap"
                    class="w-full px-4 py-2.5 rounded-xl border border-border bg-surface text-sm text-heading placeholder:text-muted/50 focus:outline-none focus:ring-2 focus:ring-primary-300 focus:border-primary-400 transition-all"
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
                    class="w-full px-4 py-2.5 rounded-xl border border-border bg-surface text-sm text-heading placeholder:text-muted/50 focus:outline-none focus:ring-2 focus:ring-primary-300 focus:border-primary-400 transition-all"
                  />
                </div>
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
                  class="w-full px-4 py-2.5 rounded-xl border border-border bg-surface text-sm text-heading placeholder:text-muted/50 focus:outline-none focus:ring-2 focus:ring-primary-300 focus:border-primary-400 transition-all"
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
                  class="w-full px-4 py-2.5 rounded-xl border border-border bg-surface text-sm text-heading placeholder:text-muted/50 focus:outline-none focus:ring-2 focus:ring-primary-300 focus:border-primary-400 transition-all resize-none"
                />
              </div>

              <!-- Error -->
              <div v-if="formError" class="flex items-center gap-2 p-3 rounded-xl bg-danger-50 border border-danger-200">
                <Icon name="lucide:alert-circle" class="w-4 h-4 text-danger-500 shrink-0" />
                <p class="text-sm text-danger-600">{{ formError }}</p>
              </div>

              <button
                type="submit"
                :disabled="sending"
                class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-primary-500 text-white text-sm font-medium hover:bg-primary-600 focus:ring-2 focus:ring-primary-300 transition-all disabled:opacity-60 disabled:cursor-not-allowed"
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

    <!-- Section 2: Contact Info -->
    <section class="bg-surface-muted">
      <div class="container py-14 sm:py-20">
        <div class="text-center mb-10 sm:mb-14">
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary-50 border border-primary-100 mb-4">
            <Icon name="lucide:info" class="w-3.5 h-3.5 text-primary-500" />
            <span class="text-xs font-semibold text-primary-600 uppercase tracking-wider">Informasi Kontak</span>
          </div>
          <h2 class="text-2xl sm:text-3xl font-bold text-heading">Cara Menghubungi Kami</h2>
          <p class="mt-3 text-base text-body max-w-2xl mx-auto">
            Kami bisa dihubungi melalui berbagai saluran berikut.
          </p>
        </div>

        <!-- Loading skeleton -->
        <template v-if="loading">
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 max-w-4xl mx-auto">
            <div v-for="i in 4" :key="i" class="p-5 rounded-2xl border border-border bg-surface animate-pulse">
              <div class="w-11 h-11 rounded-xl bg-muted/20 mb-3" />
              <div class="h-3 w-16 bg-muted/20 rounded mb-2" />
              <div class="h-4 w-28 bg-muted/20 rounded" />
            </div>
          </div>
        </template>

        <!-- Error state -->
        <template v-else-if="error">
          <div class="max-w-md mx-auto p-5 rounded-2xl border border-danger-200 bg-danger-50 text-sm text-danger-600 text-center">
            <Icon name="lucide:alert-circle" class="w-5 h-5 mx-auto mb-2" />
            <p class="font-medium mb-1">Gagal memuat informasi kontak.</p>
            <button type="button" class="underline text-xs" @click="fetchContactInfo">Coba lagi</button>
          </div>
        </template>

        <!-- Info cards grid -->
        <template v-else>
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 max-w-4xl mx-auto">
            <component
              :is="card.href ? 'a' : 'div'"
              v-for="card in infoCards"
              :key="card.label"
              :href="card.href ?? undefined"
              :target="card.href?.startsWith('http') ? '_blank' : undefined"
              :rel="card.href?.startsWith('http') ? 'noopener noreferrer' : undefined"
              class="group p-5 rounded-2xl border border-border bg-surface hover:shadow-medium hover:border-primary-200 hover:-translate-y-0.5 transition-all duration-300"
              :class="card.href ? 'cursor-pointer' : ''"
            >
              <div class="w-11 h-11 rounded-xl bg-primary-50 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform duration-300">
                <Icon :name="card.icon" class="w-5 h-5 text-primary-600" />
              </div>
              <p class="text-xs text-muted font-medium uppercase tracking-wide">{{ card.label }}</p>
              <p class="text-sm text-heading font-medium mt-1">{{ card.value }}</p>
            </component>
          </div>

          <!-- Social Media -->
          <div v-if="contactInfo?.social_media && Object.keys(contactInfo.social_media).length" class="mt-10 text-center">
            <p class="text-xs text-muted font-medium uppercase tracking-wide mb-4">Media Sosial</p>
            <div class="flex flex-wrap justify-center gap-3">
              <template v-for="(url, platform) in contactInfo.social_media" :key="platform">
                <a
                  v-if="url"
                  :href="url"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="w-10 h-10 rounded-xl bg-surface border border-border flex items-center justify-center text-muted hover:text-primary-600 hover:bg-primary-50 hover:border-primary-200 transition-all duration-200"
                  :aria-label="String(platform)"
                >
                  <Icon :name="socialIcons[String(platform)] || 'lucide:link'" class="w-4.5 h-4.5" />
                </a>
              </template>
            </div>
          </div>
        </template>
      </div>
    </section>
  </div>
</template>

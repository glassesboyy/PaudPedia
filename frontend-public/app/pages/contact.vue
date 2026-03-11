<script setup lang="ts">
/**
 * Contact Page
 * Route: /contact
 *
 * FR-PP-05: Halaman Kontak.
 * Contact form sends email via EmailJS (no backend POST endpoint).
 * Contact info fetched from backend API.
 */
import emailjs from '@emailjs/browser'
import type { ContactFormData, ContactPageInfo } from '~~/types'

useSeo({
  title: 'Hubungi Kami',
  description: 'Hubungi tim PaudPedia melalui form kontak, email, atau telepon.',
})

// ─── EmailJS Config (from runtime config) ───
const config = useRuntimeConfig()
const emailjsServiceId = config.public.emailjsServiceId as string
const emailjsTemplateId = config.public.emailjsTemplateId as string
const emailjsPublicKey = config.public.emailjsPublicKey as string
const emailjsConfigured = computed(() => !!emailjsServiceId && !!emailjsTemplateId && !!emailjsPublicKey)

// ─── Toast ───
const toast = useToast()

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

// ─── Contact Form (EmailJS) ───
const form = reactive<ContactFormData>({
  name: '',
  email: '',
  subject: '',
  message: '',
})
const sending = ref(false)
const sent = ref(false)
const formError = ref('')
const fieldErrors = reactive({
  name: '',
  email: '',
  subject: '',
  message: '',
})

const MESSAGE_MAX = 2000

function clearFieldErrors() {
  fieldErrors.name = ''
  fieldErrors.email = ''
  fieldErrors.subject = ''
  fieldErrors.message = ''
}

function validateForm(): boolean {
  clearFieldErrors()
  let valid = true

  if (!form.name.trim()) {
    fieldErrors.name = 'Nama lengkap wajib diisi.'
    valid = false
  }
  else if (form.name.trim().length < 2) {
    fieldErrors.name = 'Nama minimal 2 karakter.'
    valid = false
  }

  if (!form.email.trim()) {
    fieldErrors.email = 'Alamat email wajib diisi.'
    valid = false
  }
  else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) {
    fieldErrors.email = 'Format email tidak valid.'
    valid = false
  }

  if (!form.subject.trim()) {
    fieldErrors.subject = 'Subjek pesan wajib diisi.'
    valid = false
  }

  if (!form.message.trim()) {
    fieldErrors.message = 'Pesan tidak boleh kosong.'
    valid = false
  }
  else if (form.message.length > MESSAGE_MAX) {
    fieldErrors.message = `Pesan maksimal ${MESSAGE_MAX} karakter.`
    valid = false
  }

  return valid
}

async function handleSubmit() {
  formError.value = ''

  if (!validateForm()) return

  if (!emailjsConfigured.value) {
    formError.value = 'Konfigurasi EmailJS belum diatur. Silakan hubungi administrator.'
    return
  }

  sending.value = true
  try {
    await emailjs.send(
      emailjsServiceId,
      emailjsTemplateId,
      {
        from_name: form.name.trim(),
        from_email: form.email.trim(),
        subject: form.subject.trim(),
        message: form.message.trim(),
      },
      emailjsPublicKey,
    )

    sent.value = true
    toast.success('Pesan berhasil dikirim!')

    // Reset form
    form.name = ''
    form.email = ''
    form.subject = ''
    form.message = ''
    clearFieldErrors()
  }
  catch {
    formError.value = 'Gagal mengirim pesan. Silakan coba lagi nanti.'
    toast.error('Gagal mengirim pesan.')
  }
  finally {
    sending.value = false
  }
}

function resetForm() {
  sent.value = false
  formError.value = ''
  clearFieldErrors()
}

const messageLength = computed(() => form.message.length)

const infoCards = computed(() => {
  if (!contactInfo.value) return []
  return [
    {
      icon: 'lucide:mail',
      label: 'Email',
      value: contactInfo.value.email,
      href: contactInfo.value.email ? `mailto:${contactInfo.value.email}` : null,
      color: 'bg-primary-50 text-primary-600',
    },
    {
      icon: 'lucide:phone',
      label: 'Telepon',
      value: contactInfo.value.phone,
      href: contactInfo.value.phone ? `tel:${contactInfo.value.phone}` : null,
      color: 'bg-success-50 text-success-600',
    },
    {
      icon: 'lucide:map-pin',
      label: 'Alamat',
      value: contactInfo.value.address,
      href: null,
      color: 'bg-warning-50 text-warning-600',
    },
    {
      icon: 'lucide:clock',
      label: 'Jam Operasional',
      value: 'Senin – Jumat, 08.00 – 17.00 WIB',
      href: null,
      color: 'bg-secondary-50 text-secondary-600',
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
      variant="pattern"
    />

    <!-- Section 1: Contact Info Cards -->
    <section class="bg-surface">
      <div class="container py-12 sm:py-16">
        <!-- Loading skeleton -->
        <template v-if="loading">
          <SkeletonCardGrid :count="4" :columns="4" variant="simple" class="max-w-5xl mx-auto" />
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
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 max-w-5xl mx-auto">
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
              <div
                :class="['w-11 h-11 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform duration-300', card.color]"
              >
                <Icon :name="card.icon" class="w-5 h-5" />
              </div>
              <p class="text-[11px] text-muted font-semibold uppercase tracking-wider">{{ card.label }}</p>
              <p class="text-sm text-heading font-medium mt-1.5 leading-relaxed">{{ card.value }}</p>
            </component>
          </div>
        </template>
      </div>
    </section>

    <!-- Section 2: Image + Form Card -->
    <section class="bg-surface-muted">
      <div class="container py-14 sm:py-20">
        <div class="max-w-5xl mx-auto">
          <!-- Section badge -->
          <div class="text-center mb-10">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary-50 border border-primary-100 mb-4">
              <Icon name="lucide:pen-line" class="w-3.5 h-3.5 text-primary-500" />
              <span class="text-xs font-semibold text-primary-600 uppercase tracking-wider">Kirim Pesan</span>
            </div>
            <h2 class="text-2xl sm:text-3xl font-bold text-heading">Sampaikan Pesan Anda</h2>
            <p class="mt-3 text-base text-body max-w-xl mx-auto">
              Isi formulir berikut dan tim kami akan segera merespons pesan Anda.
            </p>
          </div>

          <div class="rounded-2xl border border-border bg-surface shadow-soft overflow-hidden">
            <div class="grid grid-cols-1 lg:grid-cols-5">
              <!-- Left: Image + overlay info -->
              <div class="hidden lg:block lg:col-span-2 relative min-h-[560px]">
                <img
                  src="/assets/contact/contact-1.jpg"
                  alt="Hubungi PaudPedia"
                  class="absolute inset-0 w-full h-full object-cover"
                />
                <!-- Gradient overlay -->
                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent" />

                <!-- Bottom info -->
                <div class="absolute bottom-0 left-0 right-0 p-6">
                  <h3 class="text-lg font-semibold text-white mb-2">PaudPedia</h3>
                  <p class="text-sm text-white/80 leading-relaxed">
                    Platform edukasi untuk pendidikan anak usia dini terbaik di Indonesia.
                  </p>
                  <div v-if="contactInfo?.email" class="mt-4 flex items-center gap-2 text-white/70 text-xs">
                    <Icon name="lucide:mail" class="w-3.5 h-3.5" />
                    <span>{{ contactInfo.email }}</span>
                  </div>
                  <div v-if="contactInfo?.phone" class="mt-2 flex items-center gap-2 text-white/70 text-xs">
                    <Icon name="lucide:phone" class="w-3.5 h-3.5" />
                    <span>{{ contactInfo.phone }}</span>
                  </div>
                </div>
              </div>

              <!-- Right: Contact Form -->
              <div class="lg:col-span-3 p-6 sm:p-8 lg:p-10">
                <!-- Success State -->
                <div v-if="sent" class="flex flex-col items-center justify-center text-center py-16">
                  <div class="relative mb-6">
                    <div class="w-20 h-20 rounded-full bg-success-50 flex items-center justify-center">
                      <Icon name="lucide:check-circle" class="w-10 h-10 text-success-500" />
                    </div>
                    <div class="absolute -top-1 -right-1 w-6 h-6 rounded-full bg-success-100 flex items-center justify-center">
                      <Icon name="lucide:sparkles" class="w-3.5 h-3.5 text-success-600" />
                    </div>
                  </div>
                  <h3 class="text-xl font-bold text-heading mb-2">Pesan Terkirim!</h3>
                  <p class="text-sm text-body mb-8 max-w-sm leading-relaxed">
                    Terima kasih telah menghubungi kami. Kami akan merespons pesan Anda sesegera mungkin melalui email.
                  </p>
                  <button
                    type="button"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border border-border text-sm font-medium text-body hover:border-primary-300 hover:text-primary-600 hover:bg-primary-50/50 transition-all"
                    @click="resetForm"
                  >
                    <Icon name="lucide:plus" class="w-4 h-4" />
                    Kirim pesan lain
                  </button>
                </div>

                <!-- Form -->
                <form v-else class="space-y-5" @submit.prevent="handleSubmit">
                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <!-- Name -->
                    <div>
                      <label for="contact-name" class="block text-sm font-medium text-heading mb-1.5">
                        Nama Lengkap <span class="text-danger-500">*</span>
                      </label>
                      <div class="relative">
                        <Icon name="lucide:user" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-muted pointer-events-none" />
                        <input
                          id="contact-name"
                          v-model="form.name"
                          type="text"
                          placeholder="Masukkan nama lengkap"
                          class="w-full pl-10 pr-4 py-2.5 rounded-xl border text-sm text-heading placeholder:text-muted/50 focus:outline-none focus:ring-2 focus:ring-primary-200 focus:border-primary-400 transition-all"
                          :class="fieldErrors.name ? 'border-danger-300 bg-danger-50/30' : 'border-border bg-surface'"
                          @input="fieldErrors.name = ''"
                        />
                      </div>
                      <p v-if="fieldErrors.name" class="mt-1 text-xs text-danger-500 flex items-center gap-1">
                        <Icon name="lucide:alert-circle" class="w-3 h-3 shrink-0" />
                        {{ fieldErrors.name }}
                      </p>
                    </div>

                    <!-- Email -->
                    <div>
                      <label for="contact-email" class="block text-sm font-medium text-heading mb-1.5">
                        Email <span class="text-danger-500">*</span>
                      </label>
                      <div class="relative">
                        <Icon name="lucide:mail" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-muted pointer-events-none" />
                        <input
                          id="contact-email"
                          v-model="form.email"
                          type="email"
                          placeholder="contoh@email.com"
                          class="w-full pl-10 pr-4 py-2.5 rounded-xl border text-sm text-heading placeholder:text-muted/50 focus:outline-none focus:ring-2 focus:ring-primary-200 focus:border-primary-400 transition-all"
                          :class="fieldErrors.email ? 'border-danger-300 bg-danger-50/30' : 'border-border bg-surface'"
                          @input="fieldErrors.email = ''"
                        />
                      </div>
                      <p v-if="fieldErrors.email" class="mt-1 text-xs text-danger-500 flex items-center gap-1">
                        <Icon name="lucide:alert-circle" class="w-3 h-3 shrink-0" />
                        {{ fieldErrors.email }}
                      </p>
                    </div>
                  </div>

                  <!-- Subject -->
                  <div>
                    <label for="contact-subject" class="block text-sm font-medium text-heading mb-1.5">
                      Subjek <span class="text-danger-500">*</span>
                    </label>
                    <div class="relative">
                      <Icon name="lucide:bookmark" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-muted pointer-events-none" />
                      <input
                        id="contact-subject"
                        v-model="form.subject"
                        type="text"
                        placeholder="Masukkan subjek pesan"
                        class="w-full pl-10 pr-4 py-2.5 rounded-xl border text-sm text-heading placeholder:text-muted/50 focus:outline-none focus:ring-2 focus:ring-primary-200 focus:border-primary-400 transition-all"
                        :class="fieldErrors.subject ? 'border-danger-300 bg-danger-50/30' : 'border-border bg-surface'"
                        @input="fieldErrors.subject = ''"
                      />
                    </div>
                    <p v-if="fieldErrors.subject" class="mt-1 text-xs text-danger-500 flex items-center gap-1">
                      <Icon name="lucide:alert-circle" class="w-3 h-3 shrink-0" />
                      {{ fieldErrors.subject }}
                    </p>
                  </div>

                  <!-- Message -->
                  <div>
                    <label for="contact-message" class="block text-sm font-medium text-heading mb-1.5">
                      Pesan <span class="text-danger-500">*</span>
                    </label>
                    <div class="relative">
                      <textarea
                        id="contact-message"
                        v-model="form.message"
                        rows="5"
                        placeholder="Tulis pesan Anda di sini..."
                        class="w-full px-4 py-3 rounded-xl border text-sm text-heading placeholder:text-muted/50 focus:outline-none focus:ring-2 focus:ring-primary-200 focus:border-primary-400 transition-all resize-none"
                        :class="fieldErrors.message ? 'border-danger-300 bg-danger-50/30' : 'border-border bg-surface'"
                        :maxlength="MESSAGE_MAX"
                        @input="fieldErrors.message = ''"
                      />
                    </div>
                    <div class="mt-1 flex items-center justify-between">
                      <p v-if="fieldErrors.message" class="text-xs text-danger-500 flex items-center gap-1">
                        <Icon name="lucide:alert-circle" class="w-3 h-3 shrink-0" />
                        {{ fieldErrors.message }}
                      </p>
                      <span v-else />
                      <span
                        class="text-[11px] tabular-nums"
                        :class="messageLength > MESSAGE_MAX * 0.9 ? 'text-warning-500' : 'text-muted'"
                      >
                        {{ messageLength }}/{{ MESSAGE_MAX }}
                      </span>
                    </div>
                  </div>

                  <!-- General Error -->
                  <div v-if="formError" class="flex items-start gap-2.5 p-3.5 rounded-xl bg-danger-50 border border-danger-200">
                    <Icon name="lucide:alert-triangle" class="w-4 h-4 text-danger-500 shrink-0 mt-0.5" />
                    <p class="text-sm text-danger-600">{{ formError }}</p>
                  </div>

                  <!-- Submit -->
                  <button
                    type="submit"
                    :disabled="sending"
                    class="w-full inline-flex items-center justify-center gap-2.5 px-6 py-3 rounded-xl bg-primary-500 text-white text-sm font-semibold shadow-sm hover:bg-primary-600 hover:shadow-md focus:ring-2 focus:ring-primary-300 focus:ring-offset-2 transition-all disabled:opacity-60 disabled:cursor-not-allowed disabled:hover:shadow-sm"
                  >
                    <Icon v-if="sending" name="lucide:loader-2" class="w-4 h-4 animate-spin" />
                    <Icon v-else name="lucide:send" class="w-4 h-4" />
                    {{ sending ? 'Mengirim pesan...' : 'Kirim Pesan' }}
                  </button>

                  <!-- Privacy notice -->
                  <p class="text-[11px] text-muted text-center leading-relaxed">
                    Dengan mengirim pesan, Anda menyetujui bahwa informasi yang diberikan akan digunakan untuk merespons pertanyaan Anda.
                  </p>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Section 3: Social Media -->
    <section
      v-if="!loading && !error && contactInfo?.social_media && Object.keys(contactInfo.social_media).length"
      class="bg-surface"
    >
      <div class="container py-12 sm:py-16">
        <div class="max-w-xl mx-auto text-center">
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary-50 border border-primary-100 mb-4">
            <Icon name="lucide:share-2" class="w-3.5 h-3.5 text-primary-500" />
            <span class="text-xs font-semibold text-primary-600 uppercase tracking-wider">Media Sosial</span>
          </div>
          <h2 class="text-xl sm:text-2xl font-bold text-heading mb-2">Ikuti Kami</h2>
          <p class="text-sm text-body mb-8">Tetap terhubung melalui media sosial kami.</p>

          <div class="flex flex-wrap justify-center gap-3">
            <template v-for="(url, platform) in contactInfo.social_media" :key="platform">
              <a
                v-if="url"
                :href="String(url)"
                target="_blank"
                rel="noopener noreferrer"
                class="group w-12 h-12 rounded-xl bg-surface border border-border flex items-center justify-center text-muted hover:text-primary-600 hover:bg-primary-50 hover:border-primary-200 hover:-translate-y-0.5 hover:shadow-sm transition-all duration-200"
                :aria-label="String(platform)"
              >
                <Icon :name="socialIcons[String(platform)] || 'lucide:link'" class="w-5 h-5" />
              </a>
            </template>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

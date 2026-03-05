<script setup lang="ts">
/**
 * FAQ Page
 * Route: /faq
 *
 * FR-PP-09: FAQ accordion — Static content.
 */
useSeo({
  title: 'Pertanyaan Umum (FAQ)',
  description: 'Temukan jawaban atas pertanyaan yang sering diajukan tentang PaudPedia.',
})

interface FaqItem {
  question: string
  answer: string
}

interface FaqCategory {
  title: string
  icon: string
  items: FaqItem[]
}

const faqCategories: FaqCategory[] = [
  {
    title: 'Umum',
    icon: 'lucide:help-circle',
    items: [
      {
        question: 'Apa itu PaudPedia?',
        answer: 'PaudPedia adalah platform e-learning dan marketplace yang didedikasikan untuk pendidikan anak usia dini (PAUD). Kami menyediakan kursus online, webinar, produk edukasi, dan artikel untuk pendidik dan orang tua.',
      },
      {
        question: 'Siapa saja yang bisa menggunakan PaudPedia?',
        answer: 'PaudPedia dirancang untuk guru/pendidik PAUD, orang tua, mahasiswa pendidikan PAUD, dan siapapun yang tertarik dengan pendidikan anak usia dini.',
      },
      {
        question: 'Apakah PaudPedia gratis?',
        answer: 'Anda bisa mendaftar dan mengakses beberapa konten secara gratis. Untuk kursus premium, webinar berbayar, dan produk digital, tersedia harga yang terjangkau.',
      },
    ],
  },
  {
    title: 'Kursus & Pembelajaran',
    icon: 'lucide:graduation-cap',
    items: [
      {
        question: 'Bagaimana cara mendaftar kursus?',
        answer: 'Cukup buat akun, pilih kursus yang diminati, lakukan pembayaran (untuk yang berbayar), dan Anda bisa mulai belajar langsung.',
      },
      {
        question: 'Apakah ada sertifikat setelah menyelesaikan kursus?',
        answer: 'Ya, setiap kursus yang berhasil diselesaikan akan mendapatkan sertifikat digital yang bisa diunduh dan diverifikasi.',
      },
      {
        question: 'Bisa belajar di perangkat apa saja?',
        answer: 'PaudPedia bisa diakses di smartphone, tablet, laptop, dan desktop melalui web browser. Platform kami dioptimalkan untuk semua ukuran layar.',
      },
    ],
  },
  {
    title: 'Webinar',
    icon: 'lucide:video',
    items: [
      {
        question: 'Apa bedanya webinar gratis dan berbayar?',
        answer: 'Webinar gratis biasanya merupakan sesi pengenalan atau diskusi umum. Webinar berbayar menawarkan materi yang lebih mendalam, interaksi langsung dengan narasumber, dan materi yang bisa diunduh.',
      },
      {
        question: 'Apakah webinar bisa ditonton ulang?',
        answer: 'Tergantung pada tipe webinar. Beberapa webinar menyediakan rekaman yang bisa ditonton ulang, sementara yang lain hanya bisa diakses secara langsung (live).',
      },
    ],
  },
  {
    title: 'Pembayaran & Akun',
    icon: 'lucide:credit-card',
    items: [
      {
        question: 'Metode pembayaran apa saja yang tersedia?',
        answer: 'Kami mendukung berbagai metode pembayaran termasuk Transfer Bank, E-Wallet (GoPay, OVO, Dana), dan kartu kredit/debit.',
      },
      {
        question: 'Bagaimana cara mengganti kata sandi?',
        answer: 'Masuk ke akun Anda, buka Pengaturan Profil, lalu pilih menu Keamanan. Di sana Anda bisa mengubah kata sandi.',
      },
      {
        question: 'Bisakah meminta pengembalian dana (refund)?',
        answer: 'Ya, pengembalian dana bisa dilakukan dalam waktu 7 hari setelah pembelian dengan syarat dan ketentuan yang berlaku. Hubungi tim kami melalui halaman Kontak.',
      },
    ],
  },
]

const openItems = ref<Set<string>>(new Set())

function toggleItem(key: string) {
  if (openItems.value.has(key)) {
    openItems.value.delete(key)
  }
  else {
    openItems.value.add(key)
  }
}
</script>

<template>
  <div>
    <!-- Hero -->
    <section class="bg-gradient-to-br from-primary-50 to-surface">
      <div class="container py-14 sm:py-20 text-center">
        <h1 class="text-3xl sm:text-4xl font-bold text-heading">Pertanyaan Umum (FAQ)</h1>
        <p class="mt-4 text-base text-body max-w-2xl mx-auto leading-relaxed">
          Temukan jawaban atas pertanyaan yang sering diajukan tentang PaudPedia.
        </p>
      </div>
    </section>

    <!-- FAQ Sections -->
    <section class="bg-surface">
      <div class="container py-14 sm:py-20">
        <div class="max-w-3xl mx-auto space-y-10">
          <div v-for="(category, catIdx) in faqCategories" :key="catIdx">
            <!-- Category Header -->
            <div class="flex items-center gap-3 mb-4">
              <div class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-primary-100">
                <Icon :name="category.icon" class="w-5 h-5 text-primary-600" />
              </div>
              <h2 class="text-lg font-semibold text-heading">{{ category.title }}</h2>
            </div>

            <!-- Accordion Items -->
            <div class="space-y-3">
              <div
                v-for="(item, itemIdx) in category.items"
                :key="itemIdx"
                class="rounded-xl border border-border overflow-hidden transition-all"
                :class="openItems.has(`${catIdx}-${itemIdx}`) ? 'bg-primary-50/50' : 'bg-surface'"
              >
                <button
                  type="button"
                  class="w-full flex items-center justify-between gap-4 p-4 text-left"
                  @click="toggleItem(`${catIdx}-${itemIdx}`)"
                >
                  <span class="text-sm font-medium text-heading">{{ item.question }}</span>
                  <Icon
                    name="lucide:chevron-down"
                    class="w-5 h-5 text-muted shrink-0 transition-transform duration-200"
                    :class="{ 'rotate-180': openItems.has(`${catIdx}-${itemIdx}`) }"
                  />
                </button>
                <Transition
                  enter-active-class="transition-all duration-200 ease-out"
                  leave-active-class="transition-all duration-150 ease-in"
                  enter-from-class="opacity-0 max-h-0"
                  enter-to-class="opacity-100 max-h-40"
                  leave-from-class="opacity-100 max-h-40"
                  leave-to-class="opacity-0 max-h-0"
                >
                  <div v-if="openItems.has(`${catIdx}-${itemIdx}`)" class="overflow-hidden">
                    <p class="px-4 pb-4 text-sm text-body leading-relaxed">{{ item.answer }}</p>
                  </div>
                </Transition>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA -->
    <section class="bg-surface-muted">
      <div class="container py-10 sm:py-14 text-center">
        <p class="text-sm text-body mb-4">Masih punya pertanyaan?</p>
        <NuxtLink
          to="/contact"
          class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-primary-500 text-white text-sm font-medium hover:bg-primary-600 transition-colors"
        >
          <Icon name="lucide:mail" class="w-4 h-4" />
          Hubungi Kami
        </NuxtLink>
      </div>
    </section>
  </div>
</template>

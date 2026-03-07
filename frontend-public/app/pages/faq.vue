<script setup lang="ts">
/**
 * FAQ Page
 * Route: /faq
 *
 * FR-PP-09: FAQ accordion — Static content.
 * Uses reusable PageHero, SidebarLayout, and ContactCta components.
 */
import type { SidebarSection } from '~~/types'

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

/** Map faqCategories → SidebarSection[] for SidebarLayout */
const sidebarSections = computed<SidebarSection[]>(() =>
  faqCategories.map((cat, idx) => ({
    id: `faq-${idx}`,
    icon: cat.icon,
    title: cat.title,
    subtitle: `${cat.items.length} pertanyaan`,
  })),
)

const openItems = ref<Set<string>>(new Set())

function toggleItem(key: string) {
  if (openItems.value.has(key)) {
    openItems.value.delete(key)
  }
  else {
    openItems.value.add(key)
  }
}

/** Get FAQ items for a given section index */
function getItems(index: number): FaqItem[] {
  return faqCategories[index]?.items ?? []
}
</script>

<template>
  <div>
    <PageHero
      badge="Pusat Bantuan"
      badge-icon="lucide:message-circle-question"
      title="Pertanyaan yang"
      highlight="Sering Diajukan"
      description="Temukan jawaban atas pertanyaan umum tentang PaudPedia. Tidak menemukan yang Anda cari? Jangan ragu untuk menghubungi kami."
    />

    <SidebarLayout :sections="sidebarSections" sidebar-label="Kategori">
      <template #default="{ index }">
        <div class="space-y-3">
          <div
            v-for="(item, itemIdx) in getItems(index)"
            :key="`${index}-${itemIdx}`"
            class="rounded-2xl border overflow-hidden transition-all duration-300"
            :class="openItems.has(`${index}-${itemIdx}`) ? 'border-primary-200 bg-primary-50/30 shadow-sm' : 'border-border bg-surface hover:border-primary-100'"
          >
            <button
              type="button"
              class="w-full flex items-center justify-between gap-4 p-5 text-left group"
              @click="toggleItem(`${index}-${itemIdx}`)"
            >
              <div class="flex items-center gap-3">
                <span
                  class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-xs font-semibold shrink-0 transition-colors"
                  :class="openItems.has(`${index}-${itemIdx}`) ? 'bg-primary-100 text-primary-700' : 'bg-surface-muted text-muted group-hover:bg-primary-50 group-hover:text-primary-600'"
                >
                  {{ itemIdx + 1 }}
                </span>
                <span class="text-sm font-medium text-heading">{{ item.question }}</span>
              </div>
              <Icon
                name="lucide:chevron-down"
                class="w-5 h-5 text-muted shrink-0 transition-transform duration-300"
                :class="{ 'rotate-180 text-primary-500': openItems.has(`${index}-${itemIdx}`) }"
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
              <div v-if="openItems.has(`${index}-${itemIdx}`)" class="overflow-hidden">
                <div class="px-5 pb-5 pl-[3.75rem]">
                  <p class="text-sm text-body leading-relaxed">{{ item.answer }}</p>
                </div>
              </div>
            </Transition>
          </div>
        </div>
      </template>
    </SidebarLayout>

    <ContactCta />
  </div>
</template>

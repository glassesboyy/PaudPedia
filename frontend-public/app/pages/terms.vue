<script setup lang="ts">
/**
 * Terms & Conditions Page
 * Route: /terms
 *
 * FR-PP-07: Syarat & Ketentuan — Static content.
 * Uses reusable PageHero, PolicyLayout, and ContactCta components.
 */
import type { SidebarSection } from '~~/types';

useSeo({
  title: 'Syarat & Ketentuan',
  description: 'Syarat dan ketentuan penggunaan platform PaudPedia.',
})

const lastUpdated = '1 Januari 2025'

const sections: SidebarSection[] = [
  {
    id: 'ketentuan-umum',
    icon: 'lucide:scroll-text',
    title: '1. Ketentuan Umum',
    content: 'Dengan mengakses dan menggunakan platform PaudPedia, Anda menyetujui untuk terikat dengan Syarat & Ketentuan ini. Jika Anda tidak menyetujui ketentuan ini, harap jangan menggunakan layanan kami.',
  },
  {
    id: 'definisi',
    icon: 'lucide:book-open',
    title: '2. Definisi',
    content: 'Berikut adalah definisi istilah yang digunakan dalam dokumen ini:',
    list: [
      '<strong>"Platform"</strong> mengacu pada situs web dan aplikasi PaudPedia.',
      '<strong>"Pengguna"</strong> mengacu pada siapapun yang mendaftar dan menggunakan layanan PaudPedia.',
      '<strong>"Konten"</strong> mengacu pada semua materi pembelajaran, video, artikel, dan produk digital yang tersedia di platform.',
      '<strong>"Mentor"</strong> mengacu pada instruktur atau pembuat konten di PaudPedia.',
    ],
  },
  {
    id: 'pendaftaran-akun',
    icon: 'lucide:user-plus',
    title: '3. Pendaftaran Akun',
    content: 'Untuk mengakses fitur lengkap PaudPedia, Anda perlu membuat akun dengan ketentuan:',
    list: [
      'Memberikan informasi yang akurat dan lengkap saat pendaftaran.',
      'Menjaga kerahasiaan kredensial akun Anda.',
      'Bertanggung jawab atas semua aktivitas yang dilakukan melalui akun Anda.',
      'Memberitahu kami segera jika ada penggunaan tidak sah atas akun Anda.',
    ],
  },
  {
    id: 'penggunaan-layanan',
    icon: 'lucide:laptop',
    title: '4. Penggunaan Layanan',
    content: 'Anda setuju untuk menggunakan platform ini hanya untuk tujuan yang sah dan tidak akan:',
    list: [
      'Menyalin, mendistribusikan, atau membagikan konten berbayar tanpa izin.',
      'Menggunakan sistem otomatis (bot) untuk mengakses platform.',
      'Mengunggah konten yang melanggar hukum, bersifat menyesatkan, atau tidak pantas.',
      'Berupaya mengakses area platform yang tidak diperuntukkan bagi Anda.',
    ],
  },
  {
    id: 'kursus-konten',
    icon: 'lucide:graduation-cap',
    title: '5. Kursus & Konten Pembelajaran',
    content: 'Kursus yang tersedia di PaudPedia dibuat oleh mentor yang telah terverifikasi. Kami berupaya memastikan kualitas konten, namun:',
    list: [
      'Konten bersifat edukatif dan tidak dimaksudkan sebagai saran profesional.',
      'Akses ke kursus berbayar bersifat personal dan tidak dapat dipindahtangankan.',
      'Sertifikat yang diterbitkan merupakan bukti penyelesaian pembelajaran di platform PaudPedia.',
    ],
  },
  {
    id: 'pembayaran-refund',
    icon: 'lucide:credit-card',
    title: '6. Pembayaran & Refund',
    content: 'Terkait transaksi di PaudPedia:',
    list: [
      'Harga yang ditampilkan sudah termasuk pajak yang berlaku.',
      'Pembayaran diproses melalui payment gateway yang terpercaya.',
      'Permintaan refund dapat diajukan dalam waktu 7 hari setelah pembelian.',
      'Refund tidak berlaku untuk kursus yang sudah selesai lebih dari 30% progress-nya.',
    ],
  },
  {
    id: 'hak-kekayaan-intelektual',
    icon: 'lucide:badge-check',
    title: '7. Hak Kekayaan Intelektual',
    content: 'Semua konten di PaudPedia, termasuk teks, gambar, video, logo, dan desain, dilindungi oleh hak cipta dan hak kekayaan intelektual. Anda tidak diperkenankan menggunakan, menyalin, atau memodifikasi konten tanpa izin tertulis dari kami.',
  },
  {
    id: 'batasan-tanggung-jawab',
    icon: 'lucide:alert-triangle',
    title: '8. Batasan Tanggung Jawab',
    content: 'PaudPedia menyediakan layanan "sebagaimana adanya". Kami tidak bertanggung jawab atas kerugian langsung atau tidak langsung yang timbul dari penggunaan platform, termasuk namun tidak terbatas pada kerusakan data atau gangguan layanan.',
  },
  {
    id: 'pengakhiran',
    icon: 'lucide:log-out',
    title: '9. Pengakhiran',
    content: 'Kami berhak menangguhkan atau menghentikan akun Anda jika Anda melanggar Syarat & Ketentuan ini. Anda juga dapat menghapus akun Anda kapan saja melalui pengaturan profil.',
  },
  {
    id: 'perubahan-ketentuan',
    icon: 'lucide:refresh-cw',
    title: '10. Perubahan Ketentuan',
    content: 'Kami dapat mengubah Syarat & Ketentuan ini sewaktu-waktu. Perubahan material akan diberitahukan melalui email atau notifikasi di platform. Penggunaan platform setelah perubahan dianggap sebagai persetujuan Anda terhadap ketentuan baru.',
  },
  {
    id: 'hubungi-kami',
    icon: 'lucide:mail',
    title: '11. Hubungi Kami',
    content: 'Jika Anda memiliki pertanyaan tentang Syarat & Ketentuan ini, silakan hubungi kami melalui halaman',
    contactLink: true,
  },
]
</script>

<template>
  <div>
    <PageHero
      badge="Syarat & Ketentuan"
      badge-icon="lucide:file-text"
      title="Ketentuan Penggunaan"
      highlight="PaudPedia"
      description="Harap baca syarat dan ketentuan berikut dengan saksama sebelum menggunakan layanan kami."
      :last-updated="lastUpdated"
      variant="pattern"
    />

    <SidebarLayout :sections="sections" />

    <ContactCta
      title="Ada pertanyaan tentang ketentuan?"
      description="Jika Anda memiliki pertanyaan terkait syarat & ketentuan kami, jangan ragu untuk menghubungi tim kami."
      icon="lucide:file-text"
    />
  </div>
</template>

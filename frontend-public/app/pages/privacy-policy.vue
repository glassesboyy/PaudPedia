<script setup lang="ts">
/**
 * Privacy Policy Page
 * Route: /privacy-policy
 *
 * FR-PP-06: Kebijakan Privasi — Static content.
 * Uses reusable PageHero, PolicyLayout, and ContactCta components.
 */
import type { SidebarSection } from '~~/types';

useSeo({
  title: 'Kebijakan Privasi',
  description: 'Kebijakan privasi PaudPedia menjelaskan bagaimana kami mengumpulkan, menggunakan, dan melindungi data pribadi Anda.',
})

const lastUpdated = '1 Januari 2025'

const sections: SidebarSection[] = [
  {
    id: 'pendahuluan',
    icon: 'lucide:info',
    title: '1. Pendahuluan',
    content: 'PaudPedia ("kami", "kita", atau "milik kami") menghargai privasi Anda. Kebijakan Privasi ini menjelaskan bagaimana kami mengumpulkan, menggunakan, menyimpan, dan melindungi informasi pribadi Anda ketika Anda menggunakan platform PaudPedia.',
  },
  {
    id: 'informasi-dikumpulkan',
    icon: 'lucide:database',
    title: '2. Informasi yang Kami Kumpulkan',
    content: 'Kami mengumpulkan informasi berikut saat Anda menggunakan layanan kami:',
    list: [
      '<strong>Informasi Akun:</strong> Nama, alamat email, nomor telepon, dan foto profil saat Anda mendaftar.',
      '<strong>Informasi Pembayaran:</strong> Data transaksi yang diperlukan untuk memproses pembelian kursus, webinar, atau produk.',
      '<strong>Data Penggunaan:</strong> Informasi tentang bagaimana Anda berinteraksi dengan platform, termasuk progress belajar dan riwayat aktivitas.',
      '<strong>Informasi Perangkat:</strong> Jenis browser, sistem operasi, dan alamat IP untuk memastikan keamanan dan kompatibilitas layanan.',
    ],
  },
  {
    id: 'penggunaan-informasi',
    icon: 'lucide:settings',
    title: '3. Penggunaan Informasi',
    content: 'Informasi yang kami kumpulkan digunakan untuk:',
    list: [
      'Menyediakan dan mengelola layanan platform e-learning.',
      'Memproses transaksi dan pembayaran.',
      'Mengirimkan notifikasi terkait kursus, webinar, dan pesanan.',
      'Meningkatkan pengalaman pengguna dan mengembangkan fitur baru.',
      'Menjaga keamanan platform dan mencegah penyalahgunaan.',
    ],
  },
  {
    id: 'penyimpanan-keamanan',
    icon: 'lucide:shield',
    title: '4. Penyimpanan dan Keamanan Data',
    content: 'Kami menyimpan data pribadi Anda di server yang aman dan menerapkan langkah-langkah keamanan teknis dan organisasional yang sesuai untuk melindungi informasi Anda dari akses tidak sah, pengubahan, pengungkapan, atau penghancuran.',
  },
  {
    id: 'berbagi-informasi',
    icon: 'lucide:share-2',
    title: '5. Berbagi Informasi',
    content: 'Kami tidak menjual data pribadi Anda kepada pihak ketiga. Kami hanya membagikan informasi Anda dalam keadaan berikut:',
    list: [
      '<strong>Penyedia Layanan:</strong> Dengan mitra pembayaran dan infrastruktur yang membantu kami mengoperasikan platform.',
      '<strong>Kewajiban Hukum:</strong> Jika diwajibkan oleh hukum atau peraturan yang berlaku.',
      '<strong>Persetujuan Anda:</strong> Dengan persetujuan eksplisit dari Anda.',
    ],
  },
  {
    id: 'hak-anda',
    icon: 'lucide:user-check',
    title: '6. Hak Anda',
    content: 'Sebagai pengguna, Anda memiliki hak untuk:',
    list: [
      'Mengakses dan melihat data pribadi Anda yang kami simpan.',
      'Memperbarui atau memperbaiki informasi pribadi Anda.',
      'Meminta penghapusan akun dan data pribadi Anda.',
      'Membatalkan langganan email atau notifikasi.',
    ],
  },
  {
    id: 'cookies',
    icon: 'lucide:cookie',
    title: '7. Cookies',
    content: 'PaudPedia menggunakan cookies dan teknologi serupa untuk meningkatkan pengalaman Anda, menganalisis penggunaan platform, dan menyimpan preferensi Anda. Anda dapat mengelola pengaturan cookies melalui browser Anda.',
  },
  {
    id: 'perubahan-kebijakan',
    icon: 'lucide:refresh-cw',
    title: '8. Perubahan Kebijakan',
    content: 'Kami dapat memperbarui Kebijakan Privasi ini dari waktu ke waktu. Perubahan signifikan akan diberitahukan melalui email atau notifikasi di platform. Penggunaan berkelanjutan atas layanan kami setelah perubahan dianggap sebagai persetujuan Anda.',
  },
  {
    id: 'hubungi-kami',
    icon: 'lucide:mail',
    title: '9. Hubungi Kami',
    content: 'Jika Anda memiliki pertanyaan tentang Kebijakan Privasi ini, silakan hubungi kami melalui halaman',
    contactLink: true,
  },
]
</script>

<template>
  <div>
    <PageHero
      badge="Kebijakan Privasi"
      badge-icon="lucide:shield-check"
      title="Privasi Anda adalah"
      highlight="Prioritas Kami"
      description="Kami berkomitmen untuk melindungi data pribadi Anda dan menjaga transparansi dalam setiap proses."
      :last-updated="lastUpdated"
      variant="pattern"
    />

    <SidebarLayout :sections="sections" />

    <ContactCta
      title="Ada pertanyaan tentang privasi?"
      description="Jika Anda memiliki pertanyaan terkait kebijakan privasi kami, jangan ragu untuk menghubungi tim kami."
      icon="lucide:shield"
    />
  </div>
</template>

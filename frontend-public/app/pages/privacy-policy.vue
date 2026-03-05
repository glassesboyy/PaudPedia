<script setup lang="ts">
/**
 * Privacy Policy Page
 * Route: /privacy-policy
 *
 * FR-PP-06: Kebijakan Privasi — Static content.
 */
useSeo({
  title: 'Kebijakan Privasi',
  description: 'Kebijakan privasi PaudPedia menjelaskan bagaimana kami mengumpulkan, menggunakan, dan melindungi data pribadi Anda.',
})

const lastUpdated = '1 Januari 2025'
</script>

<template>
  <div>
    <!-- Hero -->
    <section class="bg-gradient-to-br from-primary-50 to-surface">
      <div class="container py-14 sm:py-20 text-center">
        <h1 class="text-3xl sm:text-4xl font-bold text-heading">Kebijakan Privasi</h1>
        <p class="mt-3 text-sm text-muted">
          Terakhir diperbarui: {{ lastUpdated }}
        </p>
      </div>
    </section>

    <!-- Content -->
    <section class="bg-surface">
      <div class="container py-14 sm:py-20">
        <div class="max-w-3xl mx-auto prose-paud">
          <h2>1. Pendahuluan</h2>
          <p>
            PaudPedia ("kami", "kita", atau "milik kami") menghargai privasi Anda. Kebijakan Privasi ini
            menjelaskan bagaimana kami mengumpulkan, menggunakan, menyimpan, dan melindungi informasi
            pribadi Anda ketika Anda menggunakan platform PaudPedia.
          </p>

          <h2>2. Informasi yang Kami Kumpulkan</h2>
          <p>Kami mengumpulkan informasi berikut saat Anda menggunakan layanan kami:</p>
          <ul>
            <li><strong>Informasi Akun:</strong> Nama, alamat email, nomor telepon, dan foto profil saat Anda mendaftar.</li>
            <li><strong>Informasi Pembayaran:</strong> Data transaksi yang diperlukan untuk memproses pembelian kursus, webinar, atau produk.</li>
            <li><strong>Data Penggunaan:</strong> Informasi tentang bagaimana Anda berinteraksi dengan platform, termasuk progress belajar dan riwayat aktivitas.</li>
            <li><strong>Informasi Perangkat:</strong> Jenis browser, sistem operasi, dan alamat IP untuk memastikan keamanan dan kompatibilitas layanan.</li>
          </ul>

          <h2>3. Penggunaan Informasi</h2>
          <p>Informasi yang kami kumpulkan digunakan untuk:</p>
          <ul>
            <li>Menyediakan dan mengelola layanan platform e-learning.</li>
            <li>Memproses transaksi dan pembayaran.</li>
            <li>Mengirimkan notifikasi terkait kursus, webinar, dan pesanan.</li>
            <li>Meningkatkan pengalaman pengguna dan mengembangkan fitur baru.</li>
            <li>Menjaga keamanan platform dan mencegah penyalahgunaan.</li>
          </ul>

          <h2>4. Penyimpanan dan Keamanan Data</h2>
          <p>
            Kami menyimpan data pribadi Anda di server yang aman dan menerapkan langkah-langkah
            keamanan teknis dan organisasional yang sesuai untuk melindungi informasi Anda dari
            akses tidak sah, pengubahan, pengungkapan, atau penghancuran.
          </p>

          <h2>5. Berbagi Informasi</h2>
          <p>Kami tidak menjual data pribadi Anda kepada pihak ketiga. Kami hanya membagikan informasi Anda dalam keadaan berikut:</p>
          <ul>
            <li><strong>Penyedia Layanan:</strong> Dengan mitra pembayaran dan infrastruktur yang membantu kami mengoperasikan platform.</li>
            <li><strong>Kewajiban Hukum:</strong> Jika diwajibkan oleh hukum atau peraturan yang berlaku.</li>
            <li><strong>Persetujuan Anda:</strong> Dengan persetujuan eksplisit dari Anda.</li>
          </ul>

          <h2>6. Hak Anda</h2>
          <p>Sebagai pengguna, Anda memiliki hak untuk:</p>
          <ul>
            <li>Mengakses dan melihat data pribadi Anda yang kami simpan.</li>
            <li>Memperbarui atau memperbaiki informasi pribadi Anda.</li>
            <li>Meminta penghapusan akun dan data pribadi Anda.</li>
            <li>Membatalkan langganan email atau notifikasi.</li>
          </ul>

          <h2>7. Cookies</h2>
          <p>
            PaudPedia menggunakan cookies dan teknologi serupa untuk meningkatkan pengalaman Anda,
            menganalisis penggunaan platform, dan menyimpan preferensi Anda. Anda dapat mengelola
            pengaturan cookies melalui browser Anda.
          </p>

          <h2>8. Perubahan Kebijakan</h2>
          <p>
            Kami dapat memperbarui Kebijakan Privasi ini dari waktu ke waktu. Perubahan signifikan
            akan diberitahukan melalui email atau notifikasi di platform. Penggunaan berkelanjutan
            atas layanan kami setelah perubahan dianggap sebagai persetujuan Anda.
          </p>

          <h2>9. Hubungi Kami</h2>
          <p>
            Jika Anda memiliki pertanyaan tentang Kebijakan Privasi ini, silakan hubungi kami
            melalui halaman <NuxtLink to="/contact" class="text-primary-600 hover:underline">Kontak</NuxtLink>.
          </p>
        </div>
      </div>
    </section>
  </div>
</template>

<style scoped>
.prose-paud h2 {
  font-size: 1.125rem;
  line-height: 1.75rem;
  font-weight: 600;
  color: var(--color-heading);
  margin-top: 2rem;
  margin-bottom: 0.75rem;
}
.prose-paud h2:first-child {
  margin-top: 0;
}
.prose-paud p {
  font-size: 0.875rem;
  line-height: 1.625;
  color: var(--color-body);
  margin-bottom: 1rem;
}
.prose-paud ul {
  font-size: 0.875rem;
  line-height: 1.625;
  color: var(--color-body);
  margin-bottom: 1rem;
  padding-left: 1.25rem;
  list-style-type: disc;
}
.prose-paud ul > * + * {
  margin-top: 0.5rem;
}
.prose-paud li {
  font-size: 0.875rem;
  line-height: 1.625;
  color: var(--color-body);
}
</style>

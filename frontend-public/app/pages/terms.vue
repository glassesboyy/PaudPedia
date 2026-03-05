<script setup lang="ts">
/**
 * Terms & Conditions Page
 * Route: /terms
 *
 * FR-PP-07: Syarat & Ketentuan — Static content.
 */
useSeo({
  title: 'Syarat & Ketentuan',
  description: 'Syarat dan ketentuan penggunaan platform PaudPedia.',
})

const lastUpdated = '1 Januari 2025'
</script>

<template>
  <div>
    <!-- Hero -->
    <section class="bg-gradient-to-br from-primary-50 to-surface">
      <div class="container py-14 sm:py-20 text-center">
        <h1 class="text-3xl sm:text-4xl font-bold text-heading">Syarat & Ketentuan</h1>
        <p class="mt-3 text-sm text-muted">
          Terakhir diperbarui: {{ lastUpdated }}
        </p>
      </div>
    </section>

    <!-- Content -->
    <section class="bg-surface">
      <div class="container py-14 sm:py-20">
        <div class="max-w-3xl mx-auto prose-paud">
          <h2>1. Ketentuan Umum</h2>
          <p>
            Dengan mengakses dan menggunakan platform PaudPedia, Anda menyetujui untuk terikat
            dengan Syarat & Ketentuan ini. Jika Anda tidak menyetujui ketentuan ini,
            harap jangan menggunakan layanan kami.
          </p>

          <h2>2. Definisi</h2>
          <ul>
            <li><strong>"Platform"</strong> mengacu pada situs web dan aplikasi PaudPedia.</li>
            <li><strong>"Pengguna"</strong> mengacu pada siapapun yang mendaftar dan menggunakan layanan PaudPedia.</li>
            <li><strong>"Konten"</strong> mengacu pada semua materi pembelajaran, video, artikel, dan produk digital yang tersedia di platform.</li>
            <li><strong>"Mentor"</strong> mengacu pada instruktur atau pembuat konten di PaudPedia.</li>
          </ul>

          <h2>3. Pendaftaran Akun</h2>
          <p>Untuk mengakses fitur lengkap PaudPedia, Anda perlu membuat akun dengan ketentuan:</p>
          <ul>
            <li>Memberikan informasi yang akurat dan lengkap saat pendaftaran.</li>
            <li>Menjaga kerahasiaan kredensial akun Anda.</li>
            <li>Bertanggung jawab atas semua aktivitas yang dilakukan melalui akun Anda.</li>
            <li>Memberitahu kami segera jika ada penggunaan tidak sah atas akun Anda.</li>
          </ul>

          <h2>4. Penggunaan Layanan</h2>
          <p>Anda setuju untuk menggunakan platform ini hanya untuk tujuan yang sah dan tidak akan:</p>
          <ul>
            <li>Menyalin, mendistribusikan, atau membagikan konten berbayar tanpa izin.</li>
            <li>Menggunakan sistem otomatis (bot) untuk mengakses platform.</li>
            <li>Mengunggah konten yang melanggar hukum, bersifat menyesatkan, atau tidak pantas.</li>
            <li>Berupaya mengakses area platform yang tidak diperuntukkan bagi Anda.</li>
          </ul>

          <h2>5. Kursus & Konten Pembelajaran</h2>
          <p>
            Kursus yang tersedia di PaudPedia dibuat oleh mentor yang telah terverifikasi.
            Kami berupaya memastikan kualitas konten, namun:
          </p>
          <ul>
            <li>Konten bersifat edukatif dan tidak dimaksudkan sebagai saran profesional.</li>
            <li>Akses ke kursus berbayar bersifat personal dan tidak dapat dipindahtangankan.</li>
            <li>Sertifikat yang diterbitkan merupakan bukti penyelesaian pembelajaran di platform PaudPedia.</li>
          </ul>

          <h2>6. Pembayaran & Refund</h2>
          <p>Terkait transaksi di PaudPedia:</p>
          <ul>
            <li>Harga yang ditampilkan sudah termasuk pajak yang berlaku.</li>
            <li>Pembayaran diproses melalui payment gateway yang terpercaya.</li>
            <li>Permintaan refund dapat diajukan dalam waktu 7 hari setelah pembelian.</li>
            <li>Refund tidak berlaku untuk kursus yang sudah selesai lebih dari 30% progress-nya.</li>
          </ul>

          <h2>7. Hak Kekayaan Intelektual</h2>
          <p>
            Semua konten di PaudPedia, termasuk teks, gambar, video, logo, dan desain, dilindungi
            oleh hak cipta dan hak kekayaan intelektual. Anda tidak diperkenankan menggunakan,
            menyalin, atau memodifikasi konten tanpa izin tertulis dari kami.
          </p>

          <h2>8. Batasan Tanggung Jawab</h2>
          <p>
            PaudPedia menyediakan layanan "sebagaimana adanya". Kami tidak bertanggung jawab atas
            kerugian langsung atau tidak langsung yang timbul dari penggunaan platform, termasuk
            namun tidak terbatas pada kerusakan data atau gangguan layanan.
          </p>

          <h2>9. Pengakhiran</h2>
          <p>
            Kami berhak menangguhkan atau menghentikan akun Anda jika Anda melanggar Syarat & Ketentuan
            ini. Anda juga dapat menghapus akun Anda kapan saja melalui pengaturan profil.
          </p>

          <h2>10. Perubahan Ketentuan</h2>
          <p>
            Kami dapat mengubah Syarat & Ketentuan ini sewaktu-waktu. Perubahan material akan
            diberitahukan melalui email atau notifikasi di platform. Penggunaan platform setelah
            perubahan dianggap sebagai persetujuan Anda terhadap ketentuan baru.
          </p>

          <h2>11. Hubungi Kami</h2>
          <p>
            Jika Anda memiliki pertanyaan tentang Syarat & Ketentuan ini, silakan hubungi kami
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

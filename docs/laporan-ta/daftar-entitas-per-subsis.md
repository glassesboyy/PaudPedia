# Daftar Entitas (Tabel/Model) Database per Subsistem

Berikut adalah rincian daftar entitas database (total 38 Model) pada sistem PaudPedia yang dikelompokkan berdasarkan ketiga subsistem utama untuk kebutuhan referensi pembahasan pada Bab 4 (khususnya sub-bagian Implementasi Database / Tabel Inti Terkait pada Database):

---

## 1. Daftar Entitas Terkait Subsistem SIAKAD (Sekolah)
Entitas pada subsistem ini berfokus pada arsitektur *multi-tenant* sekolah, manajemen data akademik anak usia dini, catatan operasional sekolah, hingga pengelolaan paket berlangganan institusi:
1. `schools` (Tabel Institusi / *Tenant* Sekolah)
2. `school_members` (Tabel Pivot Keanggotaan & Peran: *Headmaster, Operator, Teacher, Parent*)
3. `teachers` (Tabel Profil Spesifik Guru / NIP / Spesialisasi)
4. `parent_profiles` (Tabel Profil Wali Murid / Orang Tua)
5. `classes` / `class_rooms` (Tabel Rombongan Belajar / Kelas)
6. `students` (Tabel Data Induk Siswa PAUD)
7. `attendances` (Tabel Log Presensi Harian Siswa)
8. `development_programs` (Tabel Master Katalog Program Perkembangan / Aspek Penilaian)
9. `development_indicators` (Tabel Master Katalog Indikator Penilaian PAUD dengan filter *is_active* / *Soft Deletes*)
10. `assessments` (Tabel Log Penilaian Observasi Kualitatif Bulanan Siswa dengan filter *Temporal Assessment Integrity*)
11. `finances` (Tabel Pembukuan Buku Besar / *Ledger* Administratif: SPP & Tabungan Siswa)
12. `student_reports` (Tabel Draf & Header Rapor Naratif Semester Siswa dengan proteksi draf)
13. `student_report_details` (Tabel Rincian Narasi Rapor per Aspek Perkembangan berfitur *Smart Omission*)
14. `school_transfer_requests` (Tabel Permohonan Perpindahan / Mutasi Siswa & Anggota Sekolah antar-*Tenant*)
15. `subscription_orders` (Tabel Transaksi Pemesanan / Perpanjangan Paket Berlangganan *Pro Plan* Institusi Sekolah)

---

## 2. Daftar Entitas Terkait Subsistem Publik (B2C & LMS)
Entitas pada subsistem ini memfasilitasi transaksi *e-commerce* produk digital, konsumsi konten pembelajaran asinkron, interaksi publik, hingga evaluasi kuis:
1. `users` (Tabel Akun Pengguna / Pelanggan B2C)
2. `categories` (Tabel Taksonomi Pengelompokan Konten B2C)
3. `products` (Tabel Katalog Produk Digital / *E-book*)
4. `webinars` (Tabel Katalog Acara Webinar / Session dengan penyensoran kredensial sensitif di publik)
5. `courses` (Tabel Katalog Kelas Kursus Asinkron)
6. `modules` (Tabel Silabus Modul Pembelajaran Kursus)
7. `lessons` (Tabel Materi Video / Dokumen PDF Kursus)
8. `quizzes` (Tabel Wadah Evaluasi Kuis Kursus)
9. `quiz_questions` (Tabel Butir Soal Pertanyaan Kuis)
10. `quiz_answers` (Tabel Opsi Jawaban Pilihan Ganda & Kunci Jawaban)
11. `quiz_attempt_answers` (Tabel Log Rincian Pilihan Jawaban Pelanggan pada Setiap Butir Soal Kuis)
12. `carts` (Tabel Dompet Keranjang Belanja Pengguna)
13. `cart_items` (Tabel Rincian Item di dalam Keranjang)
14. `promo_codes` (Tabel Kupon Diskon & Potongan Harga)
15. `orders` (Tabel Transaksi Pesanan B2C & Pembayaran Midtrans)
16. `order_items` (Tabel Rincian Item yang Dipesan / Relasi *Polymorphic*)
17. `course_enrollments` (Tabel Lisensi Kepemilikan, Persentase Progres, & Tautan Sertifikat Kursus)
18. `lesson_progress` (Tabel Log Penyelesaian Materi Pembelajaran)
19. `quiz_attempts` (Tabel Log Riwayat Pengerjaan Kuis & Skor Kelulusan)
20. `articles` (Tabel Katalog Artikel & Blog Edukasi dengan perhitungan *reading_time* otomatis)
21. `mentors` (Tabel Profil Portofolio Mentor / Pembicara)
22. `testimonials` (Tabel Ulasan & Rating dari Pelanggan)

---

## 3. Daftar Entitas Terkait Subsistem Admin Panel (18 Entitas untuk ERD Admin Panel)
Mengingat subsistem ini bertindak sebagai pusat kontrol pengawasan (*Super Admin*) serta manajemen editorial *back-office* (*Moderator Panel*), entitas-entitas berikut wajib dihadirkan dan digambarkan secara utuh pada Diagram ERD ke-3 (ERD Admin Panel):
1. `site_settings` (Tabel Konfigurasi Universal Situs / *Key-Value Store* untuk Logo, Kontak, & Metadata SEO)
2. `users` (Tabel Manajemen Akun Pengguna Global & Pengaturan Hak Akses Peran *Super Admin/Moderator*)
3. `schools` (Target Inspeksi Manajemen *Tenant*, Pengawasan Limit Siswa Paket *Free*, & Intervensi Masa Berlangganan Pro)
4. `subscription_orders` (Target Pengawasan, Verifikasi, & Konfirmasi Pembayaran Berlangganan Sekolah *Pro* oleh Super Admin)
5. `orders` (Target Agregasi Laporan Penjualan B2C & Grafik Analitik Omset Platform)
6. `categories` (Tabel Manajemen Taksonomi Pengelompokan Konten & Katalog B2C)
7. `courses` (Tabel Manajemen Katalog Kelas Kursus Asinkron oleh Moderator)
8. `modules` (Tabel Manajemen Silabus & Bab Kursus)
9. `lessons` (Tabel Manajemen Materi Video & Dokumen PDF Kursus)
10. `quizzes` (Tabel Manajemen Evaluasi Kuis Kursus)
11. `quiz_questions` (Tabel Manajemen Butir Soal Kuis)
12. `quiz_answers` (Tabel Manajemen Opsi & Kunci Jawaban Kuis)
13. `webinars` (Tabel Manajemen Jadwal Acara Webinar & Kredensial Rapat Zoom)
14. `products` (Tabel Manajemen Katalog Produk Digital / *E-book*)
15. `articles` (Tabel Manajemen Redaksi Artikel & Blog Edukasi)
16. `mentors` (Tabel Manajemen Direktori Profil Mentor / Pembicara)
17. `promo_codes` (Tabel Pembuatan & Pengaturan Kupon Diskon)
18. `testimonials` (Tabel Moderasi & Kurasi Ulasan Pelanggan)

# Daftar Entitas (Tabel/Model) Database per Subsistem

Berikut adalah rincian daftar entitas database pada sistem PaudPedia yang dikelompokkan berdasarkan ketiga subsistem utama untuk kebutuhan referensi pembahasan pada Bab 4 (khususnya sub-bagian Implementasi Database / Tabel Inti Terkait pada Database):

---

## 1. Daftar Entitas Terkait Subsistem SIAKAD (Sekolah)
Entitas pada subsistem ini berfokus pada arsitektur *multi-tenant* sekolah, manajemen data akademik anak usia dini, serta catatan operasional sekolah:
1. `schools` (Tabel Institusi / *Tenant* Sekolah)
2. `school_members` (Tabel Pivot Keanggotaan & Peran: *Headmaster, Operator, Teacher, Parent*)
3. `teachers` (Tabel Profil Spesifik Guru / NIP / Spesialisasi)
4. `parent_profiles` (Tabel Profil Wali Murid / Orang Tua)
5. `classes` / `class_rooms` (Tabel Rombongan Belajar / Kelas)
6. `students` (Tabel Data Induk Siswa PAUD)
7. `attendances` (Tabel Log Presensi Harian Siswa)
8. `development_programs` (Tabel Master Katalog Program Perkembangan / Aspek Penilaian)
9. `development_indicators` (Tabel Master Katalog Indikator Penilaian PAUD)
10. `assessments` (Tabel Log Penilaian Observasi Kualitatif Bulanan Siswa)
11. `finances` (Tabel Pembukuan Keuangan: SPP & Tabungan Siswa)
12. `student_reports` (Tabel Draf & Header Rapor Naratif Semester Siswa)
13. `student_report_details` (Tabel Rincian Narasi Rapor per Aspek Perkembangan)

---

## 2. Daftar Entitas Terkait Subsistem Publik (B2C & LMS)
Entitas pada subsistem ini memfasilitasi transaksi *e-commerce* produk digital, konsumsi konten pembelajaran asinkron, serta interaksi publik:
1. `users` (Tabel Akun Pengguna / Pelanggan B2C)
2. `categories` (Tabel Taksonomi Pengelompokan Konten B2C)
3. `products` (Tabel Katalog Produk Digital / *E-book*)
4. `webinars` (Tabel Katalog Acara Webinar / Session)
5. `courses` (Tabel Katalog Kelas Kursus Asinkron)
6. `modules` (Tabel Silabus Modul Pembelajaran Kursus)
7. `lessons` (Tabel Materi Video / Dokumen PDF Kursus)
8. `quizzes` (Tabel Wadah Evaluasi Kuis Kursus)
9. `quiz_questions` (Tabel Butir Soal Pertanyaan Kuis)
10. `quiz_answers` (Tabel Opsi Jawaban Pilihan Ganda & Kunci Jawaban)
11. `carts` (Tabel Dompet Keranjang Belanja Pengguna)
12. `cart_items` (Tabel Rincian Item di dalam Keranjang)
13. `promo_codes` (Tabel Kupon Diskon & Potongan Harga)
14. `orders` (Tabel Transaksi Pesanan & Pembayaran Midtrans)
15. `order_items` (Tabel Rincian Item yang Dipesan / *Polymorphic*)
16. `course_enrollments` (Tabel Lisensi Kepemilikan & Persentase Progres Kursus)
17. `lesson_progress` (Tabel Log Penyelesaian Materi Pembelajaran)
18. `quiz_attempts` (Tabel Log Riwayat Pengerjaan Kuis & Nilai Kelulusan)
19. `articles` (Tabel Katalog Artikel & Blog Edukasi)
20. `mentors` (Tabel Profil Portofolio Mentor / Pembicara)
21. `testimonials` (Tabel Ulasan & Rating dari Pelanggan)

---

## 3. Daftar Entitas Terkait Subsistem Admin Panel
Entitas pada subsistem ini bertindak sebagai pusat kontrol pengawasan (*Super Admin*) serta manajemen konten *back-office* (*Moderator*):
1. `site_settings` (Tabel Konfigurasi Universal Situs / *Key-Value Logo, Kontak, & SEO*)
2. `users` (Tabel Manajemen Akun Pengguna & Hak Akses Peran Admin/Moderator)
3. `schools` (Target Inspeksi Manajemen Tenant & Intervensi Masa Berlangganan Pro)
4. `orders` (Target Agregasi Laporan Penjualan & Grafik Analitik Omset Platform)
5. *Serta seluruh entitas Katalog & Konten B2C yang dikelola penuh oleh peran Moderator (`webinars`, `courses`, `modules`, `lessons`, `quizzes`, `products`, `articles`, `mentors`, `categories`, `testimonials`, `promo_codes`).*

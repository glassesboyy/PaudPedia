# Arsitektur Entitas dan Model Sistem Paudpedia

Dokumen ini menjelaskan seluruh entitas (model) yang digunakan di dalam ekosistem sistem **Paudpedia**, beserta keterhubungannya. Untuk mempermudah pemetaan _Database Schema_ (ERD), entitas dibagi ke dalam 3 subsistem utama berdasarkan **domain kepemilikan dan tempat pengelolaan utama (Master Data vs Data Operasional/Transaksional)**:

---

## 1. Entitas Subsistem Admin & Moderator Panel (Filament)
Subsistem ini bertindak sebagai _Content Management System_ (CMS) dan pengelola **Master Data**. Entitas di sini adalah data referensi inti yang dibuat, disunting, dan dikelola secara terpusat oleh Admin atau Moderator sebelum bisa dikonsumsi oleh subsistem lain.

### Master Data Global & Akses
- **`User`**: Menyimpan seluruh kredensial akun. Merupakan pusat (SSO) bagi Admin, Guru, Siswa, dan Pengguna Umum.
- **`SiteSetting`**: Konfigurasi global aplikasi (logo, kontak, SEO).

### Master Data Konten & Informasi
- **`Category`**: Klasifikasi untuk artikel, kursus, produk, dan webinar.
- **`Article`**: Konten blog/berita edukasi yang dipublikasikan oleh moderator/admin.
- **`Webinar`**: Data jadwal, narasumber, dan kuota pendaftaran seminar online.

### Master Data LMS (Materi Pembelajaran)
- **`Mentor`**: Profil instruktur/pengajar yang ditugaskan ke sebuah kursus.
- **`Course`**: Entitas induk untuk kelas online/kursus yang ditawarkan.
- **`Module`**: Bab atau pembagian materi di dalam sebuah `Course`.
- **`Lesson`**: Konten materi spesifik (video/teks/dokumen) di dalam `Module`.
- **`Quiz` & `QuizQuestion` & `QuizAnswer`**: Bank soal, kuis, dan opsi jawaban yang melekat pada modul/kursus.

### Master Data E-Commerce & Institusi
- **`Product`**: Data barang fisik/digital (selain kursus) yang dijual ke publik.
- **`PromoCode`**: Kode voucher diskon untuk e-commerce.
- **`School`**: Data master institusi PAUD yang didaftarkan dan divalidasi oleh Admin.

*(Tempatkan Gambar Diagram Schema/ERD Admin Panel di sini)*

**Deskripsi Diagram Admin & Moderator Panel:**
Diagram di atas mengilustrasikan struktur Master Data yang dikelola secara terpusat melalui panel Filament. Entitas `User` bertindak sebagai inti identitas bagi seluruh aktor dalam ekosistem. Tabel-tabel master seperti `Category`, `Course`, `Product`, dan `School` berdiri sebagai referensi utama yang memegang _Primary Key_ untuk didistribusikan sebagai _Foreign Key_ ke tabel-tabel operasional di subsistem LMS dan SIAKAD. Melalui skema ini, terlihat jelas bahwa Admin memiliki kontrol penuh dalam mendefinisikan "bahan baku" sebelum data tersebut bisa digunakan oleh _end-user_ atau operator sekolah.

---

## 2. Entitas Subsistem Public Site & LMS
Entitas dalam subsistem ini bersifat **Transaksional dan Operasional**. Data ini sebagian besar di-generate secara dinamis dari aktivitas _end-user_ (pengunjung/peserta didik) saat mereka berinteraksi dengan layanan di website publik.

### Transaksi E-Commerce & Keuangan
- **`Cart` & `CartItem`**: Data keranjang belanja sementara _user_ sebelum checkout.
- **`Order` & `OrderItem`**: Rekap transaksi pembayaran final untuk kursus/produk.
- **`Finance`**: Catatan riwayat mutasi kas atau pembayaran yang telah selesai.

### Aktivitas Pembelajaran LMS
- **`CourseEnrollment`**: Hak akses (_ownership_) pengguna terhadap kursus yang telah mereka beli.
- **`LessonProgress`**: Rekaman sejauh mana _user_ telah menyelesaikan materi (`Lesson`) tertentu.
- **`QuizAttempt` & `QuizAttemptAnswer`**: Riwayat sesi ujian/kuis pengguna beserta detail jawaban yang mereka pilih (untuk skoring otomatis).
- **`Testimonial`**: Ulasan dan rating yang diberikan pengguna setelah menyelesaikan sebuah kursus.

*(Tempatkan Gambar Diagram Schema/ERD Public Site & LMS di sini)*

**Deskripsi Diagram Public Site & LMS:**
Diagram ini memetakan alur data yang berorientasi pada transaksi dan aktivitas _end-user_ (pengunjung website). Entitas pada subsistem ini sangat bergantung pada relasi data dari Master Data (misal: entitas `Order` yang menjembatani `User` dengan entitas `Course` atau `Product`). Proses bisnis e-learning terlihat dari alur di mana _checkout_ melahirkan `CourseEnrollment`, yang kemudian memberikan hak akses kepada _user_ untuk menghasilkan data operasional berupa `LessonProgress` dan `QuizAttempt`. Skema ini dirancang dengan penekanan pada pencatatan riwayat (log transaksi dan progres belajar).

---

## 3. Entitas Subsistem SIAKAD
Entitas ini berfokus pada **Manajemen Operasional Akademik** di masing-masing sekolah PAUD. Data di sini dikelola secara spesifik oleh operator sekolah, guru, dan diakses oleh wali murid.

### Struktur Sekolah
- **`SchoolMember`**: Penugasan pengguna (`User`) sebagai staf/admin pengelola sebuah `School`.
- **`ClassRoom`**: Rombongan belajar (kelas) aktif di suatu sekolah.

### Profil Akademik
- **`Teacher`**: Profil detail guru yang ditugaskan di suatu institusi dan mengampu `ClassRoom`.
- **`Student`**: Data anak didik yang tergabung ke dalam `ClassRoom`.
- **`ParentProfile`**: Profil wali murid yang direlasikan ke data `Student` untuk memantau progres anak.

### Operasional Harian Sekolah
- **`Attendance`**: Catatan absensi / kehadiran harian siswa di kelas.
- **`Assessment`**: Rekapitulasi nilai atau evaluasi perkembangan anak didik secara kognitif, motorik, dan sosial.

*(Tempatkan Gambar Diagram Schema/ERD SIAKAD di sini)*

**Deskripsi Diagram SIAKAD:**
Diagram skema SIAKAD secara khusus menyoroti struktur hierarki dan operasional akademik harian di masing-masing PAUD. Entitas `School` bertindak sebagai akar (_root_) yang mengisolasi data masing-masing institusi. Dari `School`, skema bercabang ke `ClassRoom` (rombongan belajar) yang mempertemukan entitas profil `Teacher` dengan `Student`. Diagram ini juga memperlihatkan bahwa rutinitas harian seperti entitas `Attendance` (kehadiran) dan `Assessment` (penilaian) menempel pada entitas `Student`, yang pada akhirnya dapat diakses (di-_query_) oleh akun `User` yang direlasikan melalui `ParentProfile` (Wali Murid).

---

## Alur Relasi dalam Database Schema (Gambaran ERD)

1. **Pusat Relasi (Core):** 
   Tabel `User` menjadi sentral seluruh relasi. Tabel operasional seperti `Order`, `CourseEnrollment`, dan `SchoolMember` memiliki _foreign key_ yang merujuk langsung ke tabel `User`.

2. **Relasi Master ke Transaksi (Admin $\rightarrow$ LMS):**
   Tabel _Master Data_ di Admin Panel akan menjadi referensi (_foreign key_) bagi tabel transaksional LMS. Contohnya: `Course_id` digunakan oleh `CourseEnrollment`, dan `Product_id` / `Course_id` digunakan oleh `OrderItem`.

3. **Relasi Master ke Operasional (Admin $\rightarrow$ SIAKAD):**
   Tabel master `School` adalah akar dari subsistem SIAKAD. Tabel operasional `ClassRoom` memiliki `school_id`, dan secara berantai, `Student` memiliki `classroom_id`.

---
_Dokumen ini disusun untuk memudahkan pemetaan desain ERD berdasarkan domain fungsionalitas: Admin Panel (CMS & Master Data), LMS (Transaksi Pembelajaran), dan SIAKAD (Operasional Akademik)._

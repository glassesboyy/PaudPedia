# 📄 USE CASE DOKUMENTASI (VERSI PENYEDERHANAAN)
## Platform PaudPedia - Per Role

Dokumentasi ini menyajikan daftar Use Case sistem yang diringkas dan dikelompokkan berdasarkan peran pengguna (Role).

### 1. Guest (Pengunjung)
Pengguna publik yang belum terautentikasi (belum login).

| ID | Use Case | Deskripsi | Subsystem |
|---|---|---|---|
| UC-G-01 | View Landing Page | Melihat beranda utama, fitur, dan statistik platform | Public Platform |
| UC-G-02 | Browse Artikel | Menjelajahi dan membaca blog atau artikel edukasi | Public Platform |
| UC-G-04 | Browse Marketplace | Mencari produk digital, webinar, dan kursus | Public Platform |
| UC-G-06 | Register | Pendaftaran akun baru bagi pengguna publik | Auth |
| UC-G-07 | Login | Masuk ke sistem menggunakan email dan password | Auth |

### 2. User (Pengguna Terdaftar)
Pengguna yang sudah memiliki akun untuk aktivitas belajar dan belanja.

| ID | Use Case | Deskripsi | Subsystem |
|---|---|---|---|
| UC-U-01 | Manage Cart | Menambah, mengubah, atau menghapus item keranjang | E-Commerce |
| UC-U-03 | Checkout & Payment | Konfirmasi pesanan dan pembayaran via Midtrans | E-Commerce |
| UC-U-07 | Take Courses/Quiz | Proses pembelajaran dan evaluasi kuis di modul LMS | LMS |
| UC-U-08 | Track Progress | Pantau persentase penyelesaian materi belajar | LMS |
| UC-U-09 | Download Certificate | Unduh sertifikat otomatis setelah lulus kursus | LMS |
| UC-U-10 | Edit Profile | Memperbarui data profil dan keamanan akun | Profile |

### 3. Parent (Orang Tua)
Wali murid yang memiliki akses khusus untuk memantau data anak di sekolah.

| ID | Use Case | Deskripsi | Subsystem |
|---|---|---|---|
| UC-P-01 | Monitor Anak | Melihat profil, data absensi, dan nilai perkembangan anak | SIAKAD |
| UC-P-04 | Download Rapor | Mengunduh laporan hasil belajar anak dalam format PDF | SIAKAD |

### 4. Teacher (Guru)
Staf pengajar yang bertanggung jawab atas pengelolaan data akademis harian.

| ID | Use Case | Deskripsi | Subsystem |
|---|---|---|---|
| UC-T-03 | Input Absensi | Mencatat kehadiran harian siswa di dalam kelas | SIAKAD |
| UC-T-04 | Input Nilai | Mengisi penilaian asesmen harian atau bulanan siswa | SIAKAD |
| UC-P-04 | View Rapor | Melihat rekapitulasi laporan hasil belajar siswa | SIAKAD |

### 5. Headmaster (Kepala Sekolah)
Pengelola utama sekolah dengan kontrol operasional institusi secara menyeluruh.

| ID | Use Case | Deskripsi | Subsystem |
|---|---|---|---|
| UC-H-01 | Register School | Pendaftaran institusi sekolah baru ke platform | School MGMT |
| UC-H-05 | Manage Teachers | Kelola data guru, wali kelas, dan staf sekolah | School MGMT |
| UC-H-06 | Manage Students | Kelola database siswa dan proses pendaftaran siswa | School MGMT |
| UC-H-12 | Manage Class | Pengaturan struktur kelas dan periode tahun ajaran | School MGMT |
| UC-T-03 | Input Absensi/Nilai | Melakukan input data harian (sama seperti akses Guru) | SIAKAD |

### 6. Moderator & Admin
Pengelola konten platform secara global dan administrator sistem.

| ID | Use Case | Deskripsi | Subsystem | Role |
|---|---|---|---|---|
| UC-MOD-01 | Manage Content | Kelola data Kursus, Webinar, Produk, dan Artikel | Admin Panel | Moderator |
| UC-A-02 | User Management | Kelola akun pengguna, sekolah, dan hak akses | Admin Panel | Admin |
| UC-A-04 | Site Settings | Konfigurasi visual situs, logo, dan informasi kontak | Admin Panel | Admin |
| UC-A-08 | View Analytics | Pantau statistik penjualan, transaksi, dan pertumbuhan | Analytics | Admin |
| UC-A-10 | Manage Promo | Membuat dan mengelola kode promo diskon belanja | E-Commerce | Admin |

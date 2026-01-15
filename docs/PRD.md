# PRODUCT REQUIREMENTS DOCUMENT (PRD)
## Platform Paud Pedia - Multi-Tenant SIAKAD & E-Learning Platform

**Tech Stack:**
- Backend: Laravel 12 (REST API) + MySQL 8.0
- Admin Panel: Laravel Filament (Admin & Moderator Dashboard)
- Public Frontend: Next.js/Nuxt.js (E-Learning & Marketplace)
- SIAKAD Frontend: React/Vue + Vite (Multi-Tenant School Management)

---

## 📋 Gambaran Umum Dokumen

### Tujuan
Dokumen ini mendefinisikan Product Vision, features, requirements, dan success criteria untuk Platform Paud Pedia - sebuah platform dual-purpose yang menggabungkan:
1. **SIAKAD (Multi-Tenant School Management System)** untuk institusi PAUD
2. **Public E-Learning & Marketplace** untuk edukasi parenting

### Scope
- ✅ Product Vision & objectives
- ✅ Target pengguna & persona
- ✅ Requirement fitur (dipisah berdasarkan user roles)
- ✅ User story
- ✅ Metrik kesuksesan
- ✅ Kriteria peluncuran
- ❌ Skema database (see ERD.md/CLASS_DIAGRAM.md)
- ❌ Flow detail (see FLOWS.md)

### Struktur Requirements
Requirements diorganisir berdasarkan **User Roles & Access Level**:

1. **🏫 SIAKAD Multi-Tenant** - Internal school features (Headmaster, Teacher, Parent)
2. **🌐 Platform Publik (B2C) - Public Features** - User-facing marketplace (Guest, Registered User)
3. **🔧 Fitur Admin & Moderator** - Backend management (Admin, Moderator)

**Rationale:**
- ✅ **Clarity:** Jelas siapa yang mengakses fitur apa
- ✅ **Tech Stack Alignment:** Public Features (Next.js/Nuxt) vs Admin Features (Laravel Filament)
- ✅ **Development Focus:** Frontend team fokus pada public features, Backend team fokus pada admin/moderator features
### Struktur Requirements
Requirements diorganisir berdasarkan **User Roles & Access Level**:

1. **🏫 SIAKAD Multi-Tenant** - Internal school features (Headmaster, Teacher, Parent)
2. **🌐 Platform Publik (B2C) - Public Features** - User-facing marketplace (Guest, Registered User)
3. **🔧 Fitur Admin & Moderator** - Backend management (Admin, Moderator)

**Rationale:**
- ✅ **Clarity:** Jelas siapa yang mengakses fitur apa
- ✅ **Tech Stack Alignment:** Public Features (Next.js/Nuxt) vs Admin Features (Laravel Filament)
- ✅ **Development Focus:** Frontend team fokus pada public features, Backend team fokus pada admin/moderator features

---

## 📋 Daftar Isi

1. [Ringkasan Eksekutif](#-ringkasan-eksekutif)
   - [Nama Produk](#nama-produk)
   - [Tipe Produk](#tipe-produk)
   - [Target Market](#target-market)
   - [Key Differentiators](#key-differentiators)
   - [Business Model](#business-model)
2. [Product Vision](#-product-vision)
   - [Pernyataan Visi](#pernyataan-visi)
   - [Misi](#misi)
   - [Core Values](#core-values)
3. [Target Pasar](#-target-pasar)
   - [Pasar Utama: Lembaga PAUD](#pasar-utama-lembaga-paud)
   - [Pasar Sekunder: Orang Tua & Pendidik](#pasar-sekunder-orang-tua--pendidik)
4. [Persona Pengguna](#-persona-pengguna)
   - [Persona 1: Bu Sari – Kepala PAUD](#persona-1-bu-sari--kepala-paud-usia-38-tahun)
   - [Persona 2: Bu Ani – Guru PAUD](#persona-2-bu-ani--guru-paud-usia-28-tahun)
   - [Persona 3: Ibu Rina – Orang Tua Murid](#persona-3-ibu-rina--orang-tua-murid-usia-32-tahun)
   - [Persona 4: Pak Budi – Moderator / Pembuat Konten](#persona-4-pak-budi--moderator--pembuat-konten-usia-35-tahun)
   - [Persona 5: Admin – Administrator Platform](#persona-5-admin--administrator-platform-usia-30-tahun)
5. [Requirement Fitur](#-requirement-fitur)
   - [🏫 SIAKAD Multi-Tenant](#-siakad-multi-tenant)
     - [1. Manajemen Sekolah](#1-manajemen-sekolah)
     - [2. Manajemen Pengguna](#2-manajemen-pengguna)
     - [3. Manajemen Kelas](#3-manajemen-kelas)
     - [4. Manajemen Orang Tua](#4-manajemen-orang-tua)
     - [5. Manajemen Siswa](#5-manajemen-siswa)
     - [6. Manajemen Absensi](#6-manajemen-absensi)
     - [7. Manajemen Penilaian (PAUD)](#7-manajemen-penilaian-paud)
     - [8. Manajemen Keuangan (Pro Plan)](#8-manajemen-keuangan-khusus-paket-pro)
     - [9. Laporan (Pro Plan)](#9-laporan-khusus-paket-pro)
   - [🌐 Platform Publik (B2C) - Public Features](#-platform-publik-b2c---public-features)
     - [10. Browse & Beli Webinar](#10-browse--beli-webinar)
     - [11. Browse & Beli Kursus](#11-browse--beli-kursus)
     - [12. Browse & Beli Produk Digital](#12-browse--beli-produk-digital)
     - [13. Baca Artikel](#13-baca-artikel)
     - [14. Lihat Profil Mentor](#14-lihat-profil-mentor)
     - [15. Fitur Pencarian & Filter](#15-fitur-pencarian--filter)
     - [16. E-Commerce (Keranjang & Checkout)](#16-e-commerce-keranjang--checkout)
     - [17. Learning Management System (LMS)](#17-learning-management-system-lms)
     - [18. Halaman Publik](#18-halaman-publik)
     - [19. Akun Pengguna](#19-akun-pengguna)
   - [🔧 Fitur Admin & Moderator](#-fitur-admin--moderator)
     - [20. Manajemen Konten (Moderator Only)](#20-manajemen-konten-moderator-only-)
       - [20.1 CRUD Webinar](#201-crud-webinar)
       - [20.2 CRUD Kursus](#202-crud-kursus)
       - [20.3 CRUD Produk Digital](#203-crud-produk-digital)
       - [20.4 CRUD Artikel](#204-crud-artikel)
       - [20.5 CRUD Mentor](#205-crud-mentor)
       - [20.6 Manajemen Kategori](#206-manajemen-kategori)
     - [21. Administrasi Platform (Admin Only)](#21-administrasi-platform-admin-only-)
   - [Kebutuhan Non-Fungsional](#kebutuhan-non-fungsional)
6. [User Story](#-user-story)
   - [Epic 1: Onboarding Sekolah](#epic-1-onboarding-sekolah)
   - [Epic 2: Manajemen Siswa & Orang Tua](#epic-2-manajemen-siswa--orang-tua)
   - [Epic 3: Operasional Harian](#epic-3-operasional-harian)
   - [Epic 4: Langganan & Pembayaran](#epic-4-langganan--pembayaran)
   - [Epic 5: Marketplace Publik](#epic-5-marketplace-publik)
   - [Epic 6: Pembuatan Konten](#epic-6-pembuatan-konten)

---

## 🎯 Ringkasan Eksekutif

### Nama Produk
**Platform Paud Pedia** (Paud Pedia Platform)

### Tipe Produk
Solusi SaaS Dual-Platform:
- **Platform A:** Multi-Tenant SIAKAD for PAUD Schools (B2B SaaS)
- **Platform B:** Public E-Learning & Digital Marketplace (B2C)

### Target Market
- **Primer:** PAUD (Pendidikan Anak Usia Dini) institusi di Indonesia - usia 0-6 years
- **Sekunder:** Orang tua yang mencari edukasi parenting & sumber daya

### Key Differentiators
1. **Spesifik PAUD:** Dirancang khusus untuk pendidikan anak usia dini (usia 0-6), bukan adaptasi dari sistem SD/SMP
2. **Monitoring Khusus Orang Tua:** Tanpa login siswa (desain sesuai usia)
3. **Model Pendapatan Ganda:** Langganan sekolah (B2B) + Marketplace publik (B2C)
4. **Freemium Model:** Gratis tier (20 siswa + 5 guru) → Tier Pro (unlimited + fitur premium)
5. **Ekosistem Terintegrasi:** Manajemen sekolah + edukasi parenting dalam satu platform

### Business Model
**B2B (SIAKAD):**
- **Paket Gratis:** Rp 0/bulan
  - Batas maksimal: 20 siswa
  - Batas maksimal: 5 guru/teacher
  - Fitur dasar: Manajemen siswa, absensi, penilaian
  - Tidak termasuk: Generate PDF rapor, manajemen keuangan
  
- **Paket Pro:** Rp 200,000/bulan
  - Siswa unlimited
  - Guru/teacher unlimited
  - Semua fitur: Generate PDF rapor, manajemen keuangan (SPP & Tabungan)
  - Priority support

**B2C (Public Platform):**
- Webinar: Rp 50,000 - 200,000/event
- Courses: Rp 100,000 - 500,000/kursus
- Produk Digital: Rp 25,000 - 150,000/produk
- Artikel: Gratis (SEO & engagement)

**Rasionale Batasan Paket Gratis:**
- **20 Siswa:** Sesuai dengan ukuran PAUD kecil (1-2 kelas)
- **5 Guru:** Reasonable untuk sekolah kecil (1 kepala sekolah + 2-4 guru)
- **Mencegah Abuse:** Batasan guru mencegah sekolah besar memakai paket gratis selamanya
- **Upgrade Incentive:** Sekolah yang berkembang (>20 siswa atau >5 guru) akan upgrade ke Pro

---

## 🚀 Product Vision

### Pernyataan Visi
*"Memberdayakan setiap sekolah PAUD di Indonesia dengan teknologi yang mudah digunakan, sekaligus menjadi sumber terpercaya untuk edukasi parenting dan perkembangan anak usia dini."*

(Memberdayakan setiap sekolah PAUD di Indonesia dengan teknologi yang mudah digunakan, sekaligus menjadi sumber terpercaya untuk edukasi parenting dan perkembangan anak usia dini.)

### Misi
1. **Menyederhanakan Operasional Sekolah:** Mengurangi beban administratif untuk sekolah PAUD melalui alat digital yang intuitif
2. **Memberdayakan Orang Tua:** Menyediakan monitoring transparan untuk perkembangan anak dan akses ke sumber daya parenting berkualitas
3. **Mendukung Guru:** Melengkapi pendidik dengan alat yang efisien untuk absensi, penilaian, dan komunikasi
4. **Mendorong Pendidikan Berkualitas:** Memungkinkan hasil pendidikan yang lebih baik melalui insight berbasis data dan pengembangan profesional

### Core Values
- **Kesederhanaan:** Mudah digunakan oleh guru dan orang tua meskipun tidak terbiasa dengan teknologi
- **Aksesibilitas:** Tier gratis memastikan small schools can benefit
- **Privasi:** Penanganan data anak yang aman
- **Sesuai Usia:** Dirancang untuk konteks PAUD (usia 0-6)
- **Pembelajaran Berkelanjutan:** Mendukung pembelajaran sepanjang hayat untuk orang tua dan pendidik

---

## 🎯 Target Pasar

### Pasar Utama: Lembaga PAUD

**Ukuran Pasar (Indonesia):**
- Total lembaga PAUD: ±200.000+ (237.751 lembaga pada tahun 2024)
- Segmen target: PAUD di wilayah perkotaan dan semi-perkotaan dengan 20–100 siswa

**Permasalahan Utama:**
1. Pencatatan absensi masih manual (menggunakan kertas)
2. Komunikasi dengan orang tua tidak teratur (tersebar di banyak grup WhatsApp)
3. Belum memiliki data dan arsip siswa dalam bentuk digital
4. Proses pembuatan laporan memakan waktu lama
5. Keterbatasan anggaran untuk menggunakan aplikasi atau SaaS yang mahal

### Pasar Sekunder: Orang Tua & Pendidik

**Karakteristik Pengguna:**
- Orang tua dengan anak usia 0–6 tahun
- Mayoritas ibu (70–80% sebagai pengambil keputusan)
- Tinggal di wilayah perkotaan dan semi-perkotaan, kelas menengah
- Pengguna smartphone
- Aktif menggunakan media sosial

**Permasalahan Utama:**
1. Kurangnya transparansi terhadap perkembangan anak
2. Akses terbatas ke edukasi parenting yang berkualitas
3. Kesulitan menemukan sumber belajar yang terpercaya
4. Membutuhkan tips praktis dan template yang siap digunakan
5. Keinginan untuk meningkatkan kompetensi dan pengembangan profesional (bagi pendidik)

---

## 👥 Persona Pengguna

### Persona 1: Bu Sari – Kepala PAUD (Usia 38 Tahun)

**Latar Belakang:**
- Pemilik TK kecil dengan 35 siswa
- Pengalaman 8 tahun di bidang pendidikan anak usia dini
- Kemampuan teknologi terbatas
- Menggunakan WhatsApp untuk komunikasi dengan orang tua
- Absensi siswa masih dicatat secara manual (kertas)

**Tujuan:**
- Mengurangi waktu untuk pekerjaan administrasi
- Meningkatkan citra sekolah agar terlihat lebih profesional
- Meningkatkan kepuasan orang tua
- Mencatat dan memantau perkembangan siswa secara digital

**Kendala:**
- Menghabiskan lebih dari 3 jam per minggu untuk membuat laporan manual
- Data dan arsip kertas sering hilang
- Keluhan orang tua karena kurangnya informasi perkembangan anak
- Tidak mampu membeli sistem manajemen sekolah yang mahal

**Skenario Penggunaan:**
- Mendaftarkan sekolah (Paket Gratis)
- Menambahkan data guru dan siswa
- Memantau absensi harian
- Menghasilkan laporan perkembangan siswa
- Meningkatkan paket ke Pro ketika jumlah siswa bertambah

---

### Persona 2: Bu Ani – Guru PAUD (Usia 28 Tahun)

**Latar Belakang:**
- Guru di TK Melati dengan total 60 siswa
- Mengajar Kelas A dengan 20 siswa
- Menggunakan smartphone setiap hari
- Kemampuan teknologi tingkat menengah

**Tujuan:**
- Mengisi absensi dengan cepat (kurang dari 5 menit per hari)
- Pencatatan penilaian siswa yang mudah
- Melihat perkembangan siswa dari waktu ke waktu
- Mengikuti pengembangan kompetensi dan pelatihan profesional

**Kendala:**
- Data absensi berbasis kertas sering hilang
- Pengisian data yang berulang dan memakan waktu
- Sulit memantau perkembangan siswa secara berkelanjutan
- Akses terbatas ke pelatihan guru

**Skenario Penggunaan:**
- Mengisi absensi harian
- Mencatat penilaian siswa
- Melihat daftar siswa dalam kelas
- Mengakses kursus edukasi parenting untuk pengembangan profesional

---

### Persona 3: Ibu Rina – Orang Tua Murid (Usia 32 Tahun)

**Latar Belakang:**
- Ibu dengan dua anak (usia 4 dan 6 tahun)
- Kedua anak bersekolah di TK Melati
- Bekerja penuh waktu
- Aktif menggunakan Instagram dan WhatsApp

**Tujuan:**
- Memantau perkembangan anak secara rutin
- Mendapatkan informasi kegiatan sekolah
- Mempelajari pola asuh yang efektif
- Mengakses sumber edukasi anak yang berkualitas

**Kendala:**
- Komunikasi dari sekolah sangat terbatas
- Tidak mengetahui aktivitas anak sehari-hari di sekolah
- Informasi parenting tersebar dan tidak selalu terpercaya
- Biaya seminar atau workshop parenting cukup mahal

**Skenario Penggunaan:**
- Melihat absensi dan penilaian anak
- Mengunduh laporan perkembangan anak
- Membeli webinar tentang tumbuh kembang anak
- Membaca artikel parenting gratis

---

### Persona 4: Pak Budi – Moderator / Pembuat Konten (Usia 35 Tahun)

**Latar Belakang:**
- Pakar parenting dan psikolog anak
- Membuat konten edukasi
- Ingin menjangkau audiens yang lebih luas
- Kemampuan teknis web terbatas

**Tujuan:**
- Membagikan pengetahuan kepada orang tua
- Memonetisasi keahlian melalui webinar dan kursus
- Membangun personal branding
- Mengelola konten dengan mudah

**Kendala:**
- Platform LMS yang rumit
- Biaya platform yang tinggi
- Hambatan teknis dalam proses pengaturan

**Skenario Penggunaan:**
- Membuat dan menerbitkan webinar
- Mengelola kursus online
- Menulis artikel blog
- Mengelola profil sebagai mentor

---

### Persona 5: Admin – Administrator Platform (Usia 30 Tahun)

**Latar Belakang:**
- Mengelola seluruh sistem platform
- Memiliki latar belakang teknis
- Berorientasi pada pengembangan bisnis

**Tujuan:**
- Menjaga pertumbuhan dan kestabilan platform
- Mengoptimalkan pendapatan
- Menjaga kepuasan pengguna
- Memastikan kualitas dan moderasi konten

**Skenario Penggunaan:**
- Mengelola seluruh pengguna dan data sekolah
- Mengatur konfigurasi sistem
- Melihat analitik dan laporan
- Menangani dukungan pelanggan
- Menyetujui konten dan testimoni

---

## 📦 Requirement Fitur

#### 🏫 SIAKAD Multi-Tenant
> **Tech Stack:** React/Vue + Vite (Frontend) | Laravel API (Backend)  
> **User Roles:** Headmaster, Teacher, Parent

**1. Manajemen Sekolah**
- [ ] FR-SM-01: Pendaftaran sekolah (kepala sekolah daftar mandiri)
- [ ] FR-SM-02: Pengelolaan profil sekolah (nama, alamat, logo)
- [ ] FR-SM-03: Tampilan paket berlangganan (Gratis vs Pro)
- [ ] FR-SM-04: Upgrade ke Paket Pro (pembayaran via Midtrans)
- [ ] FR-SM-05: Pembatasan jumlah siswa (Gratis: 20 siswa, Pro: unlimited)
- [ ] FR-SM-06: Pembatasan jumlah guru (Gratis: 5 guru, Pro: unlimited)
- [ ] FR-SM-07: Notifikasi saat mendekati batas (siswa atau guru)
- [ ] FR-SM-08: Block penambahan siswa/guru baru saat limit tercapai (Free Plan)

**2. Manajemen Pengguna**
- [ ] FR-UM-01: Pendaftaran guru oleh kepala sekolah
- [ ] FR-UM-02: Pengiriman otomatis kredensial login ke email guru
- [ ] FR-UM-03: Akses multi-sekolah (1 akun pengguna untuk beberapa sekolah)
- [ ] FR-UM-04: Dashboard berdasarkan peran (Kepala Sekolah, Guru, Orang Tua)

**3. Manajemen Kelas**
- [ ] FR-CM-01: Membuat, mengubah, dan menghapus kelas
- [ ] FR-CM-02: Menetapkan wali kelas pada setiap kelas
- [ ] FR-CM-03: Dukungan satu guru mengajar beberapa kelas (Many-to-One)
- [ ] FR-CM-04: Pengaturan kapasitas kelas (opsional)

**4. Manajemen Orang Tua**
- [ ] FR-PM-01: Membuat profil orang tua secara terpisah (waiting list)
- [ ] FR-PM-02: Email orang tua harus unik per sekolah
- [ ] FR-PM-03: Pembuatan akun pengguna secara otomatis beserta kredensial
- [ ] FR-PM-04: Pengiriman email sambutan kepada orang tua
- [ ] FR-PM-05: Melihat database orang tua dengan fitur pencarian

**5. Manajemen Siswa**
- [ ] FR-STM-01: Menambahkan data siswa dengan form 2 tab (Data Siswa & Data Orang Tua)
- [ ] FR-STM-02: Pilihan orang tua dari data yang sudah ada atau membuat data baru
- [ ] FR-STM-03: Otomatis menghubungkan data siswa dengan profil orang tua
- [ ] FR-STM-04: Mengubah dan menghapus data siswa (khusus Kepala Sekolah)
- [ ] FR-STM-05: Melihat daftar siswa (Guru: hanya baca)
- [ ] FR-STM-06: Profil siswa dengan fitur unggah foto
- [ ] FR-STM-07: Tanpa akun login untuk siswa

**6. Manajemen Absensi**
- [ ] FR-AT-01: Pengisian absensi harian oleh guru
- [ ] FR-AT-02: Status absensi: Hadir, Sakit, Izin, Alpha
- [ ] FR-AT-03: Filter berdasarkan kelas (hanya kelas yang diampu guru)
- [ ] FR-AT-04: Tampilan absensi untuk orang tua (riwayat & ringkasan)
- [ ] FR-AT-05: Tampilan kalender absensi untuk orang tua
- [ ] FR-AT-06: Perhitungan persentase kehadiran siswa

**7. Manajemen Penilaian (PAUD)**
- [ ] FR-AS-01: Input penilaian berdasarkan kategori (Kognitif, Motorik, Sosial-Emosional, Etc)
- [ ] FR-AS-02: Skala penilaian PAUD: BB, MB, BSH, BSB
- [ ] FR-AS-03: Penilaian berbasis semester (Semester 1, 2, 3, 4)
- [ ] FR-AS-04: Tampilan riwayat penilaian untuk orang tua
- [ ] FR-AS-05: Catatan guru pada setiap penilaian

**8. Manajemen Keuangan (khusus Paket Pro)**
- [ ] FR-FN-01: Pencatatan pembayaran SPP
- [ ] FR-FN-02: Pencatatan tabungan siswa
- [ ] FR-FN-03: Metode pembayaran: Tunai dan Transfer
- [ ] FR-FN-04: Tampilan riwayat pembayaran untuk orang tua (hanya baca)
- [ ] FR-FN-05: Ringkasan keuangan per siswa

**9. Laporan (khusus Paket Pro)**
- [ ] FR-RP-01: Generate laporan perkembangan siswa dalam format PDF (rapor)
- [ ] FR-RP-02: Memuat data absensi, penilaian, dan foto siswa
- [ ] FR-RP-03: Orang tua dapat mengunduh laporan PDF
- [ ] FR-RP-04: Logo sekolah ditampilkan pada header laporan

---

#### 🌐 Platform Publik (B2C) - Public Features
> **Tech Stack:** Next.js/Nuxt.js (Frontend) | Laravel API (Backend)  
> **User Roles:** Guest, Registered User

**10. Browse & Beli Webinar**
- [ ] FR-WB-P01: Menjelajahi daftar webinar (guest & user)
- [ ] FR-WB-P02: Lihat detail webinar (judul, deskripsi, mentor, harga, jadwal, durasi)
- [ ] FR-WB-P03: Filter webinar berdasarkan kategori, harga, tanggal
- [ ] FR-WB-P04: Search webinar by keyword
- [ ] FR-WB-P05: Tambah webinar ke keranjang (user only)
- [ ] FR-WB-P06: Akses link Zoom webinar setelah pembayaran berhasil
- [ ] FR-WB-P07: Lihat daftar webinar yang diikuti di "Akun Saya → Webinar Saya"
- [ ] FR-WB-P08: Notifikasi email reminder H-1 webinar

**11. Browse & Beli Kursus**
- [ ] FR-CR-P01: Menjelajahi daftar kursus (guest & user)
- [ ] FR-CR-P02: Lihat detail kursus (silabus, mentor, harga, tingkat kesulitan)
- [ ] FR-CR-P03: Preview lesson gratis (video/PDF yang di-set preview)
- [ ] FR-CR-P04: Filter kursus berdasarkan kategori, harga, tingkat kesulitan
- [ ] FR-CR-P05: Search kursus by keyword
- [ ] FR-CR-P06: Tambah kursus ke keranjang (user only)
- [ ] FR-CR-P07: Otomatis terdaftar ke kursus setelah pembayaran berhasil
- [ ] FR-CR-P08: Lihat daftar kursus yang diikuti di "Akun Saya → Kursus Saya"

**12. Browse & Beli Produk Digital**
- [ ] FR-PR-P01: Menjelajahi daftar produk digital (guest & user)
- [ ] FR-PR-P02: Lihat detail produk (deskripsi, harga, tipe file, ukuran file)
- [ ] FR-PR-P03: Filter produk berdasarkan kategori, harga, tipe file
- [ ] FR-PR-P04: Search produk by keyword
- [ ] FR-PR-P05: Tambah produk ke keranjang (user only)
- [ ] FR-PR-P06: Download produk setelah pembayaran berhasil
- [ ] FR-PR-P07: Lihat daftar produk yang dibeli di "Akun Saya → Produk Saya"
- [ ] FR-PR-P08: Akses unlimited download (tidak ada expiry link)

**13. Baca Artikel**
- [ ] FR-AR-P01: Menjelajahi daftar artikel (guest & user)
- [ ] FR-AR-P02: Baca artikel lengkap (akses gratis tanpa login)
- [ ] FR-AR-P03: Filter artikel berdasarkan kategori, tags
- [ ] FR-AR-P04: Search artikel by keyword
- [ ] FR-AR-P05: Lihat artikel featured di homepage
- [ ] FR-AR-P06: Tampilkan reading time otomatis
- [ ] FR-AR-P07: Navigasi artikel terkait (related articles)

**14. Lihat Profil Mentor**
- [ ] FR-MN-P01: Browse daftar mentor (guest & user)
- [ ] FR-MN-P02: Lihat profil mentor (bio, foto, keahlian, social media links)
- [ ] FR-MN-P03: Lihat webinar & kursus yang diampu mentor
- [ ] FR-MN-P04: Filter mentor berdasarkan keahlian

**15. Fitur Pencarian & Filter**
- [ ] FR-SF-01: Search bar global (cari webinar, kursus, produk, artikel)
- [ ] FR-SF-02: Filter konten berdasarkan kategori
- [ ] FR-SF-03: Filter konten berdasarkan harga (gratis, berbayar, range harga) -> khusus yang memiliki harga
- [ ] FR-SF-04: Sort konten (terbaru, terpopuler, termurah, termahal)

**16. E-Commerce (Keranjang & Checkout)**
- [ ] FR-EC-01: Tambah item ke keranjang (webinar, kursus, produk)
- [ ] FR-EC-02: Update quantity di keranjang
- [ ] FR-EC-03: Hapus item dari keranjang
- [ ] FR-EC-04: Tampilkan ringkasan order (subtotal, diskon, total)
- [ ] FR-EC-05: Proses checkout dengan pembayaran melalui Midtrans
- [ ] FR-EC-06: Penggunaan kode promo saat checkout
- [ ] FR-EC-07: Riwayat pesanan di "Akun Saya → Riwayat Transaksi"
- [ ] FR-EC-08: Detail status pembayaran (pending, paid, failed)
- [ ] FR-EC-09: Email konfirmasi setelah pembayaran berhasil
- [ ] FR-EC-10: Email berisi link Zoom (webinar), link kursus, atau link download (produk)

**17. Learning Management System (LMS)**
- [ ] FR-LM-01: Pemutar kursus (video player untuk YouTube embed)
- [ ] FR-LM-02: PDF viewer untuk lesson tipe PDF
- [ ] FR-LM-03: Pelacakan penyelesaian lesson (mark as complete)
- [ ] FR-LM-04: Perhitungan persentase progres belajar otomatis
- [ ] FR-LM-05: Progress bar per kursus (0-100%)
- [ ] FR-LM-06: Lock lesson berikutnya sampai lesson sebelumnya selesai (opsional)
- [ ] FR-LM-07: Pembuatan sertifikat otomatis setelah 100% penyelesaian
- [ ] FR-LM-08: Download sertifikat dalam format PDF
- [ ] FR-LM-09: Tampilkan sertifikat di "Akun Saya → Sertifikat Saya"
- [ ] FR-LM-10: Share sertifikat ke social media (opsional)

**18. Halaman Publik**
- [ ] FR-PP-01: Halaman landing dengan hero section, fitur unggulan, dan statistik platform
- [ ] FR-PP-02: Section featured courses/webinars/products
- [ ] FR-PP-03: Section testimonials (rotating atau grid)
- [ ] FR-PP-04: Halaman Tentang Kami (visi, misi, tim)
- [ ] FR-PP-05: Halaman Kontak (form kontak + info)
- [ ] FR-PP-06: Halaman Kebijakan Privasi
- [ ] FR-PP-07: Halaman Syarat & Ketentuan
- [ ] FR-PP-08: Halaman blog/artikel (akses gratis, SEO-friendly)
- [ ] FR-PP-09: Halaman FAQ (Frequently Asked Questions)
- [ ] FR-PP-10: Footer dengan links & social media

**19. Akun Pengguna**
- [ ] FR-UA-01: Pendaftaran pengguna (email & password)
- [ ] FR-UA-02: Login pengguna (email & password)
- [ ] FR-UA-03: Verifikasi email setelah registrasi
- [ ] FR-UA-04: Reset kata sandi (forgot password)
- [ ] FR-UA-05: Dashboard "Akun Saya" (overview)
- [ ] FR-UA-06: Edit profil pengguna (nama, email, foto, bio)
- [ ] FR-UA-07: Ubah password
- [ ] FR-UA-08: Tab "Kursus Saya" (kursus yang diikuti + progress)
- [ ] FR-UA-09: Tab "Produk Saya" (produk yang dibeli + download link)
- [ ] FR-UA-10: Tab "Webinar Saya" (webinar yang diikuti + Zoom link)
- [ ] FR-UA-11: Tab "Sertifikat Saya" (semua sertifikat yang didapat)
- [ ] FR-UA-12: Tab "Riwayat Transaksi" (order history)
- [ ] FR-UA-13: Logout

---

#### 🔧 Fitur Admin & Moderator
> **Tech Stack:** Laravel Filament (Admin Panel)  
> **User Roles:** Admin, Moderator

**20. Manajemen Konten (Moderator Only)** 🔒

**20.1 CRUD Webinar**
- [ ] FR-WB-M01: Membuat webinar baru (Moderator)
- [ ] FR-WB-M02: Mengubah detail webinar (judul, deskripsi, harga, jadwal)
- [ ] FR-WB-M03: Menghapus webinar (Moderator)
- [ ] FR-WB-M04: Input link Zoom secara manual (meeting ID & passcode)
- [ ] FR-WB-M05: Menetapkan mentor pada webinar
- [ ] FR-WB-M06: Upload thumbnail webinar
- [ ] FR-WB-M07: Set harga & harga original (untuk tampilan diskon)
- [ ] FR-WB-M08: Publish/unpublish webinar
- [ ] FR-WB-M09: Set jadwal webinar (tanggal & waktu)
- [ ] FR-WB-M10: Set durasi webinar (dalam menit)

**20.2 CRUD Kursus**
- [ ] FR-CR-M01: Membuat kursus baru (Moderator)
- [ ] FR-CR-M02: Mengubah detail kursus (judul, deskripsi, harga, kategori)
- [ ] FR-CR-M03: Menghapus kursus (Moderator)
- [ ] FR-CR-M04: Membuat dan mengelola modul kursus
- [ ] FR-CR-M05: Membuat dan mengelola lesson dalam modul
- [ ] FR-CR-M06: Mendukung berbagai tipe konten lesson (video YouTube embed, PDF, kuis, teks)
- [ ] FR-CR-M07: Mengatur urutan modul dan lesson (drag & drop atau numbering)
- [ ] FR-CR-M08: Set lesson sebagai preview gratis
- [ ] FR-CR-M09: Upload thumbnail kursus
- [ ] FR-CR-M10: Set tingkat kesulitan (Beginner, Intermediate, Advanced)
- [ ] FR-CR-M11: Menetapkan mentor pada kursus
- [ ] FR-CR-M12: Publish/unpublish kursus

**20.3 CRUD Produk Digital**
- [ ] FR-PR-M01: Membuat produk digital baru (Moderator)
- [ ] FR-PR-M02: Mengubah detail produk (judul, deskripsi, harga, kategori)
- [ ] FR-PR-M03: Menghapus produk digital (Moderator)
- [ ] FR-PR-M04: Upload file produk (PDF, ZIP, DOC, PPT, maksimal 50MB)
- [ ] FR-PR-M05: Upload thumbnail produk
- [ ] FR-PR-M06: Set harga & harga original (untuk tampilan diskon)
- [ ] FR-PR-M07: Tampilkan informasi file (tipe & ukuran file)
- [ ] FR-PR-M08: Active/inactive produk
- [ ] FR-PR-M09: Set kategori produk

**20.4 CRUD Artikel**
- [ ] FR-AR-M01: Membuat artikel baru (Moderator)
- [ ] FR-AR-M02: Mengubah artikel (Moderator)
- [ ] FR-AR-M03: Menghapus artikel (Moderator)
- [ ] FR-AR-M04: Editor teks kaya (rich text editor) dengan support gambar
- [ ] FR-AR-M05: Upload featured image
- [ ] FR-AR-M06: Auto-generate slug dari judul (editable)
- [ ] FR-AR-M07: Set meta description untuk SEO
- [ ] FR-AR-M08: Tambah tags (multi-select atau comma-separated)
- [ ] FR-AR-M09: Save as draft atau publish langsung
- [ ] FR-AR-M10: Set artikel sebagai featured
- [ ] FR-AR-M11: Auto-calculate reading time

**20.5 CRUD Mentor**
- [ ] FR-MN-M01: Membuat profil mentor baru (Moderator)
- [ ] FR-MN-M02: Mengubah profil mentor (Moderator)
- [ ] FR-MN-M03: Menghapus profil mentor (Moderator)
- [ ] FR-MN-M04: Upload foto profil mentor
- [ ] FR-MN-M05: Informasi mentor (nama, title/gelar, bio, keahlian)
- [ ] FR-MN-M06: Tambah social media links (Instagram, LinkedIn, dll)
- [ ] FR-MN-M07: Set mentor sebagai active/inactive
- [ ] FR-MN-M08: Assign mentor ke webinar dan kursus

**20.6 Manajemen Kategori**
- [ ] FR-CG-M01: Membuat kategori untuk kursus, produk, dan artikel (Moderator)
- [ ] FR-CG-M02: Edit kategori (Moderator)
- [ ] FR-CG-M03: Hapus kategori (Moderator)
- [ ] FR-CG-M04: Set konten sebagai featured (ditampilkan di halaman utama)

**21. Administrasi Platform (Admin Only)** 🔒
- [ ] FR-AD-01: Melihat seluruh pengguna dan data sekolah (Admin)
- [ ] FR-AD-02: Mengelola pengaturan situs (logo, kontak, media sosial)
- [ ] FR-AD-03: Melihat analitik penjualan (pendapatan, transaksi)
- [ ] FR-AD-04: Melihat statistik platform (total user, total kursus, revenue)
- [ ] FR-AD-05: Pendaftaran kursus secara manual (untuk keperluan troubleshooting)
- [ ] FR-AD-06: Pembuatan sertifikat secara manual (untuk keperluan troubleshooting)
- [ ] FR-AD-07: Mengelola kode promo (create, edit, delete, activate/deactivate)
- [ ] FR-AD-08: Approve/reject testimoni user
- [ ] FR-AD-09: Manage user roles & permissions (Admin, Moderator)

---

### Kebutuhan Non-Fungsional

**Performa:**
- [ ] NFR-PF-01: Waktu muat halaman kurang lebih 10 detik
- [ ] NFR-PF-02: Mendukung kurang lebih 100 pengguna aktif secara bersamaan
- [ ] NFR-PF-03: Waktu respons API kurang lebih 500 ms

**Keamanan:**
- [ ] NFR-SC-01: Menggunakan enkripsi HTTPS
- [ ] NFR-SC-02: Penerapan Row Level Security (RLS) untuk isolasi data multi-tenant
- [ ] NFR-SC-03: Enkripsi kata sandi (password hashing)
- [ ] NFR-SC-04: Verifikasi email wajib untuk pengguna

**Kemudahan Penggunaan (Usability):**
- [ ] NFR-US-01: Desain responsif untuk perangkat mobile
- [ ] NFR-US-02: Antarmuka menggunakan Bahasa Indonesia
- [ ] NFR-US-04: Validasi form dengan pesan kesalahan yang jelas dan membantu

---

## 📖 User Story

### Epic 1: Onboarding Sekolah

**US-1.1: Pendaftaran Sekolah (Kepala Sekolah)**
- **Sebagai** kepala PAUD
- **Saya ingin** mendaftarkan sekolah dengan informasi dasar
- **Sehingga** saya dapat mulai menggunakan platform dengan Paket Gratis

**Kriteria Penerimaan (Acceptance Criteria):**
- [ ] Form berisi: nama sekolah, NPSN (opsional), alamat, nama kepala sekolah, email, kata sandi
- [ ] Verifikasi email dikirim secara otomatis
- [ ] Sekolah dibuat dengan Paket Gratis (batas 20 siswa + 5 guru)
- [ ] Akun kepala sekolah otomatis dibuat dengan peran “Kepala Sekolah”
- [ ] Dialihkan ke dashboard setelah email terverifikasi

---

**US-1.2: Undangan Guru (Kepala Sekolah)**
- **Sebagai** kepala sekolah
- **Saya ingin** menambahkan guru ke dalam sekolah
- **Sehingga** guru dapat membantu mengelola siswa dan menginput data

**Kriteria Penerimaan:**
- [ ] Form berisi: nama guru, email, nomor telepon (opsional)
- [ ] Kata sandi dibuat otomatis atau diisi manual
- [ ] Email berisi kredensial login dikirim secara otomatis
- [ ] Guru dapat login dan mengakses dashboard sekolah
- [ ] Kepala sekolah dapat mengubah atau menghapus data guru

---

### Epic 2: Manajemen Siswa & Orang Tua

**US-2.1: Pembuatan Profil Orang Tua (Kepala Sekolah)**
- **Sebagai** kepala sekolah
- **Saya ingin** membuat profil orang tua secara terpisah
- **Sehingga** data orang tua dapat disiapkan sebelum pendaftaran siswa

**Kriteria Penerimaan:**
- [ ] Form berisi: email (unik per sekolah), nama ayah, nama ibu, nomor telepon, alamat, dan lainnya jika diperlukan
- [ ] Validasi email: harus unik dalam satu sekolah
- [ ] Akun pengguna dibuat otomatis beserta kredensial
- [ ] Email dikirim ke orang tua berisi detail login
- [ ] Orang tua dapat login meskipun belum memiliki anak terdaftar (waiting list)

---

**US-2.2: Pendaftaran Siswa dengan Pemilihan Orang Tua (Kepala Sekolah)**
- **Sebagai** kepala sekolah
- **Saya ingin** mendaftarkan siswa dan menghubungkannya dengan data orang tua (yang sudah ada atau baru)
- **Sehingga** orang tua dapat memantau perkembangan anak

**Kriteria Penerimaan:**
- [ ] Tab 1: Data siswa (nama, tanggal lahir, jenis kelamin, kelas)
- [ ] Tab 2: Pemilihan orang tua (dropdown data yang sudah ada atau tambah baru)
- [ ] Jika orang tua sudah ada: tampilkan pratinjau (email, nama, daftar anak)
- [ ] Jika orang tua baru: tampilkan form orang tua (email, nama, nomor telepon, dan lainnya yang diperlukan)
- [ ] Email dikirim ke orang tua (orang tua lama: “anak ditambahkan”, orang tua baru: “akun dibuat”)
- [ ] Siswa terhubung ke `parent_profile_id` (wajib)
- [ ] Tidak ada akun login untuk siswa

---

### Epic 3: Operasional Harian

**US-3.1: Pengisian Absensi Harian (Guru)**
- **Sebagai** guru
- **Saya ingin** mengisi absensi harian kelas kurang dari 5 menit
- **Sehingga** orang tua dapat melihat absensi secara real-time

**Kriteria Penerimaan:**
- [ ] Pilih tanggal (default: hari ini)
- [ ] Pilih kelas (dropdown kelas yang diampu sebagai wali kelas)
- [ ] Daftar siswa dengan opsi: Hadir, Sakit, Izin, Alpha
- [ ] Tombol simpan memperbarui seluruh data siswa sekaligus
- [ ] Notifikasi berhasil ditampilkan
- [ ] Data absensi tidak boleh duplikat (kombinasi student_id + tanggal unik)

---

**US-3.2: Pemantauan Absensi (Orang Tua)**
- **Sebagai** orang tua
- **Saya ingin** melihat riwayat absensi anak
- **Sehingga** saya dapat memantau kehadiran anak di sekolah

**Kriteria Penerimaan:**
- [ ] Pilih anak (dropdown jika memiliki lebih dari satu anak)
- [ ] Pilih periode (Bulan Ini, Semester Ini, Kustom)
- [ ] Ringkasan ditampilkan: total Hadir, Sakit, Izin, Alpha, persentase kehadiran
- [ ] Tampilan kalender dengan kode warna absensi (opsional/good to be true)
- [ ] Ekspor ke PDF (opsional/good to be true)

---

**US-3.3: Penilaian Siswa (Guru)**
- **Sebagai** guru
- **Saya ingin** mencatat penilaian siswa berdasarkan kategori
- **Sehingga** perkembangan siswa dapat dipantau

**Kriteria Penerimaan:**
- [ ] Pilih siswa dan kategori (Kognitif, Motorik, Sosial-Emosional, dan lainnya)
- [ ] Input indikator (kemampuan atau perilaku tertentu)
- [ ] Pilih nilai: BB, MB, BSH, BSB
- [ ] Tambahkan catatan (opsional)
- [ ] Pilih semester (1, 2, 3, atau 4)
- [ ] Simpan dan lanjut ke siswa berikutnya

---

### Epic 4: Langganan & Pembayaran

**US-4.1: Upgrade ke Paket Pro (Kepala Sekolah)**
- **Sebagai** kepala sekolah
- **Saya ingin** meningkatkan paket ke Pro ketika jumlah siswa melebihi 20 dan guru melebihi 5
- **Sehingga** saya dapat menambah siswa dan guru tanpa batas dan mengakses fitur premium

**Kriteria Penerimaan:**
- [ ] Tombol upgrade terlihat pada dashboard Paket Gratis
- [ ] Halaman harga menampilkan perbandingan Paket Gratis dan Pro
- [ ] Alur checkout: Rp200.000/bulan
- [ ] Pembayaran melalui Midtrans (berbagai metode pembayaran)
- [ ] Setelah pembayaran berhasil: paket = Pro, batas siswa = tanpa batas
- [ ] Email konfirmasi dikirim
- [ ] Fitur Pro langsung aktif

---

**US-4.2: Unduh Laporan PDF (Paket Pro – Orang Tua)**
- **Sebagai** orang tua dari sekolah dengan Paket Pro
- **Saya ingin** mengunduh laporan perkembangan anak dalam bentuk PDF
- **Sehingga** saya dapat menyimpannya sebagai arsip

**Kriteria Penerimaan:**
- [ ] Tombol “Unduh PDF” tersedia (khusus Paket Pro)
- [ ] PDF memuat: logo sekolah, foto siswa, ringkasan absensi, dan penilaian
- [ ] Format PDF profesional (ukuran A4)
- [ ] Proses unduh berjalan langsung
- [ ] Paket Gratis: tombol nonaktif dengan label “🔒 khusus Paket Pro”

---

### Epic 5: Marketplace Publik

**US-5.1: Pembelian Webinar (Pengguna)**
- **Sebagai** pengguna biasa/publik
- **Saya ingin** membeli webinar tentang perkembangan anak atau lainnya yang diinginkan
- **Sehingga** saya dapat belajar dari para ahli

**Kriteria Penerimaan:**
- [ ] Menjelajahi daftar webinar (judul, mentor, harga, jadwal)
- [ ] Klik “Daftar” → diarahkan ke halaman checkout
- [ ] Menggunakan kode promo (opsional)
- [ ] Pembayaran melalui Midtrans
- [ ] Setelah pembayaran: email dikirim berisi link Zoom
- [ ] Link Zoom tersedia di Akun Saya → tab Webinar

---

**US-5.2: Pendaftaran & Penyelesaian Kursus (Pengguna)**
- **Sebagai** pengguna biasa/publik
- **Saya ingin** mengikuti kursus online dan memantau progres belajar
- **Sehingga** saya dapat belajar sesuai waktu sendiri

**Kriteria Penerimaan:**
- [ ] Menjelajahi kursus dan melihat silabus
- [ ] Membeli kursus → otomatis terdaftar setelah pembayaran
- [ ] Pemutar kursus: video, PDF, kuis, dan konten kursus lainnya sesuai yang dibuat
- [ ] Progress bar diperbarui otomatis (% selesai)
- [ ] Sertifikat dibuat otomatis saat progres 100%
- [ ] Sertifikat dapat diunduh dari Akun Saya

---

**US-5.3: Pembelian Produk Digital (Pengguna)**
- **Sebagai** pengguna biasa/publik
- **Saya ingin** membeli e-book parenting
- **Sehingga** saya dapat membaca tips praktis

**Kriteria Penerimaan:**
- [ ] Menjelajahi produk (judul, harga, tipe file, ukuran file)
- [ ] Tambah ke keranjang atau langsung checkout
- [ ] Pembayaran melalui Midtrans
- [ ] Setelah pembayaran: email dikirim berisi link unduhan
- [ ] Tombol unduh tersedia di Akun Saya → tab Produk
- [ ] Link unduhan tidak kedaluwarsa (akses permanen)

---

### Epic 6: Pembuatan Konten

**US-6.1: Pembuatan Kursus (Moderator)**
- **Sebagai** moderator konten
- **Saya ingin** membuat kursus online dengan beberapa modul
- **Sehingga** saya dapat membagikan materi secara terstruktur

**Kriteria Penerimaan:**
- [ ] Membuat kursus: judul, deskripsi, mentor, kategori, harga
- [ ] Menambahkan modul dengan urutan
- [ ] Menambahkan materi: video (URL YouTube), PDF (unggah), kuis
- [ ] Mengatur urutan materi dalam modul
- [ ] Pratinjau kursus sebelum dipublikasikan
- [ ] Tombol publish / unpublish
- [ ] Kursus tampil di marketplace publik setelah dipublikasikan

---

**US-6.2: Penulisan Artikel (Moderator)**
- **Sebagai** moderator
- **Saya ingin** menulis dan mempublikasikan artikel blog
- **Sehingga** dapat meningkatkan SEO dan keterlibatan pengunjung

**Kriteria Penerimaan:**
- [ ] Editor teks lengkap dengan opsi pemformatan
- [ ] Unggah gambar di dalam konten
- [ ] Mengatur gambar utama, judul, slug, dan deskripsi meta
- [ ] Menambahkan tag (dipisahkan dengan koma)
- [ ] Simpan sebagai draft atau langsung publikasikan
- [ ] Artikel yang dipublikasikan dapat diakses di /artikel/[slug]
- [ ] Akses publik tanpa perlu login

---
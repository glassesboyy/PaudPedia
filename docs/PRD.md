# PRODUCT REQUIREMENTS DOCUMENT (PRD)
## Platform Paud Pedia - Multi-Tenant SIAKAD & E-Learning Platform

---

## 📋 Gambaran Umum Dokumen

### Tujuan
This PRD defines the Product Vision, features, requirements, and success criteria for Platform Paud Pedia - a dual-dual-purpose platform combining:
1. **SIAKAD (Multi-Tenant School Management System)** untuk institusi PAUD
2. **Public E-Learning & Marketplace** untuk edukasi parenting

### Scope
- ✅ Product Vision & objectives
- ✅ Target pengguna & persona
- ✅ Requirement fitur
- ✅ User story
- ✅ Metrik kesuksesan
- ✅ Kriteria peluncuran
- ❌ Implementasi teknis (lihat dokumen teknis)
- ❌ Skema database (see ERD.md/CLASS_DIAGRAM.md)
- ❌ Flow detail (see FLOWS.md)

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
   - [🌐 Platform Publik (B2C)](#-platform-publik-b2c)
   - [🔧 Fitur Admin](#-fitur-admin)
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
4. **Freemium Model:** Gratis tier (20 siswa) → Tier Pro (unlimited + fitur premium)
5. **Ekosistem Terintegrasi:** Manajemen sekolah + edukasi parenting dalam satu platform

### Business Model
**B2B (SIAKAD):**
- Paket Gratis: Rp 0/bulan (20 siswa maksimal, fitur dasar)
- Paket Pro: Rp 200,000/bulan (siswa unlimited, laporan PDF, manajemen keuangan)

**B2C (Public Platform):**
- Webinar: Rp 50,000 - 200,000/event
- Courses: Rp 100,000 - 500,000/kursus
- Produk Digital: Rp 25,000 - 150,000/produk
- Artikel: Gratis (SEO & engagement)

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

## 📦 Requirement fitur

#### 🏫 SIAKAD Multi-Tenant

**1. Manajemen Sekolah**
- [ ] FR-SM-01: Pendaftaran sekolah (kepala sekolah daftar mandiri)
- [ ] FR-SM-02: Pengelolaan profil sekolah (nama, alamat, logo)
- [ ] FR-SM-03: Tampilan paket berlangganan (Gratis vs Pro)
- [ ] FR-SM-04: Upgrade ke Paket Pro (pembayaran via Midtrans)
- [ ] FR-SM-05: Pembatasan jumlah siswa (Gratis: 20 siswa, Pro: tanpa batas)

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
- [ ] FR-AS-01: Input penilaian berdasarkan kategori (Kognitif, Motorik, Sosial-Emosional)
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

#### 🌐 Platform Publik (B2C)

**10. Manajemen Konten Webinar**
- [ ] FR-CT-01: Membuat, mengubah, dan menghapus webinar (Moderator)
- [ ] FR-CT-02: Input link Zoom secara manual
- [ ] FR-CT-03: Membuat, mengubah, dan menghapus kursus dengan modul dan materi (Moderator)
- [ ] FR-CT-04: Mendukung video (embed YouTube), PDF, dan kuis
- [ ] FR-CT-05: Membuat, mengubah, dan menghapus produk digital (Moderator)
- [ ] FR-CT-06: Unggah file produk (PDF, ZIP, maksimal 50MB)
- [ ] FR-CT-07: Membuat, mengubah, dan menghapus artikel (Moderator)
- [ ] FR-CT-08: Editor teks kaya (rich text editor) untuk artikel
- [ ] FR-CT-09: URL ramah SEO (slug)

**11. Manajemen Mentor**
- [ ] FR-MN-01: Membuat, mengubah, dan menghapus profil mentor
- [ ] FR-MN-02: Menetapkan mentor pada webinar dan kursus
- [ ] FR-MN-03: Informasi mentor meliputi bio, foto, dan keahlian

**12. Kategori & Pengelompokan Konten**
- [ ] FR-CG-01: Pengelompokan kategori untuk kursus, produk, dan artikel
- [ ] FR-CG-02: Konten unggulan (ditampilkan di halaman utama)
- [ ] FR-CG-03: Fitur pencarian dan filter konten

**13. E-Commerce**
- [ ] FR-EC-01: Menjelajahi webinar, kursus, dan produk digital
- [ ] FR-EC-02: Menambahkan item ke keranjang
- [ ] FR-EC-03: Proses checkout dengan pembayaran melalui Midtrans
- [ ] FR-EC-04: Penggunaan kode promo
- [ ] FR-EC-05: Riwayat pesanan di halaman Akun Saya
- [ ] FR-EC-06: Otomatis terdaftar ke kursus setelah pembayaran berhasil
- [ ] FR-EC-07: Pengiriman email berisi link Zoom (webinar), link unduhan (produk) atau link kursus (kursus)

**14. Manajemen Pembelajaran (LMS)**
- [ ] FR-LM-01: Pemutar kursus (video / penampil PDF)
- [ ] FR-LM-02: Pelacakan penyelesaian materi
- [ ] FR-LM-03: Perhitungan persentase progres belajar
- [ ] FR-LM-04: Pembuatan sertifikat otomatis setelah 100% penyelesaian
- [ ] FR-LM-05: Unduh sertifikat

**15. Halaman Publik**
- [ ] FR-PP-01: Halaman landing dengan hero, fitur, dan statistik
- [ ] FR-PP-02: Halaman Tentang Kami
- [ ] FR-PP-03: Halaman Kontak
- [ ] FR-PP-04: Halaman Kebijakan Privasi
- [ ] FR-PP-05: Halaman blog / artikel (akses gratis)
- [ ] FR-PP-06: Bagian testimoni pengguna

**16. Akun Pengguna**
- [ ] FR-UA-01: Pendaftaran dan login pengguna
- [ ] FR-UA-02: Verifikasi email
- [ ] FR-UA-03: Reset kata sandi
- [ ] FR-UA-04: Dashboard Akun Saya
- [ ] FR-UA-05: Pengelolaan profil pengguna
- [ ] FR-UA-06: Kursus Saya (kursus yang diikuti)
- [ ] FR-UA-07: Produk Saya (produk yang dibeli)
- [ ] FR-UA-08: Webinar Saya (webinar yang diikuti)

---

#### 🔧 Fitur Admin

**17. Administrasi Platform**
- [ ] FR-AD-01: Melihat seluruh pengguna dan data sekolah
- [ ] FR-AD-02: Mengelola pengaturan situs (logo, kontak, media sosial)
- [ ] FR-AD-03: Melihat analitik penjualan (pendapatan, transaksi)
- [ ] FR-AD-04: Pendaftaran kursus secara manual (untuk keperluan troubleshooting)
- [ ] FR-AD-05: Pembuatan sertifikat secara manual (untuk keperluan troubleshooting)
- [ ] FR-AD-06: Mengelola kode promo

---

### Kebutuhan Non-Fungsional

**Performa:**
- [ ] NFR-PF-01: Waktu muat halaman kurang dari 10 detik
- [ ] NFR-PF-02: Mendukung lebih dari 1.000 pengguna aktif secara bersamaan
- [ ] NFR-PF-03: Waktu respons API kurang dari 500 ms

**Keamanan:**
- [ ] NFR-SC-01: Menggunakan enkripsi HTTPS
- [ ] NFR-SC-02: Penerapan Row Level Security (RLS) untuk isolasi data multi-tenant
- [ ] NFR-SC-03: Penyimpanan file yang aman (menggunakan signed URL)
- [ ] NFR-SC-04: Enkripsi kata sandi (password hashing)
- [ ] NFR-SC-05: Verifikasi email wajib untuk pengguna

**Kemudahan Penggunaan (Usability):**
- [ ] NFR-US-01: Desain responsif untuk perangkat mobile (±80% pengguna menggunakan mobile)
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
- [ ] Sekolah dibuat dengan Paket Gratis (batas 20 siswa)
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
- **Saya ingin** meningkatkan paket ke Pro ketika jumlah siswa melebihi 20
- **Sehingga** saya dapat menambah siswa tanpa batas dan mengakses fitur premium

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
- [ ] Pemutar kursus: video, PDF, dan kuis
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

*Terakhir Diperbarui: January 14, 2026*
*Versi: 1.0 - Initial PRD*
*Document Status: Draft*
*Next Review: February 1, 2026*

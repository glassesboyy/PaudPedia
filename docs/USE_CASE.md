# USE CASE DOKUMENTASI
## Platform Paud Pedia - Per Role

**Tech Stack:**
- Backend: Laravel 12 (REST API) + MySQL 8.0
- Admin Panel: Laravel Filament (Admin & Moderator Dashboard)
- Public Frontend: Next.js/Nuxt.js (Guest & User roles - E-Learning & Marketplace)
- SIAKAD Frontend: React/Vue + Vite (Parent, Teacher, Headmaster roles - School Management)

> **Catatan:** Dokumentasi ini fokus pada use cases per role. Untuk flow detail sistem, lihat [FLOWS.md](./FLOWS.md)

---

## 📋 Daftar Isi

1. [Roles Overview](#roles-overview)
2. [Use Cases Per Role](#use-cases-per-role)
   - [1. Guest (Pengunjung)](#1-guest-pengunjung)
   - [2. User (Pengguna Terdaftar)](#2-user-pengguna-terdaftar)
   - [3. Parent (Orang Tua)](#3-parent-orang-tua)
   - [4. Teacher (Guru)](#4-teacher-guru)
   - [5. Headmaster (Kepala Sekolah)](#5-headmaster-kepala-sekolah)
   - [6. Moderator (Content Manager)](#6-moderator-content-manager)
   - [7. Admin (Super Admin)](#7-admin-super-admin)
3. [Key Differences](#key-differences)
   - [🏫 SIAKAD Roles Comparison](#-siakad-roles-comparison)
   - [🌐 Public Platform Roles Comparison](#-public-platform-roles-comparison)
   - [💳 Subscription Impact](#-subscription-impact)

---

## Roles Overview

Platform ini memiliki **7 roles** dengan tanggung jawab berbeda:

| Role | Akses | Primary Function |
|------|-------|------------------|
| **Guest** | Public | Browse tanpa login |
| **User** | Authenticated | Shopping & learning |
| **Parent** | School-based | Monitor anak (PAUD - usia 0-6 tahun) |
| **Teacher** | School-based | Input data siswa (read-only view) |
| **Headmaster** | School-owner | Manage school (full CRUD) |
| **Moderator** | Admin panel | Content creation |
| **Admin** | Full access | System management |

---

## Use Cases Per Role

### 1. Guest (Pengunjung)

**Karakteristik:** Belum register/login

**Use Cases:**

| ID | Use Case | Deskripsi |
|----|----------|-----------|
| UC-G-01 | View landing page | Lihat homepage dengan hero, features, stats, testimonials |
| UC-G-02 | Browse artikel | Baca blog/artikel pendidikan |
| UC-G-03 | View mentor profiles | Lihat profil mentor |
| UC-G-04 | Browse marketplace | Lihat produk digital, webinar, kursus |
| UC-G-05 | View product detail | Lihat detail produk/webinar/kursus |
| UC-G-06 | Register | Daftar akun baru |
| UC-G-07 | Login | Masuk dengan email/password |

**Restrictions:**
- ❌ Tidak bisa add to cart
- ❌ Tidak bisa checkout
- ❌ Tidak bisa akses dashboard

---

### 2. User (Pengguna Terdaftar)

**Karakteristik:** Sudah login, belum terikat sekolah

**Use Cases:**

| ID | Use Case | Deskripsi |
|----|----------|-----------|
| UC-U-01 | Add to cart | Tambah produk/webinar/kursus ke cart |
| UC-U-02 | Apply promo code | Pakai kode promo saat checkout |
| UC-U-03 | Checkout & payment | Bayar via Midtrans |
| UC-U-04 | View my purchases | Lihat riwayat pembelian |
| UC-U-05 | Access webinar | Klik Zoom link di My Account |
| UC-U-06 | Download products | Download produk digital yang dibeli |
| UC-U-07 | Take courses | Belajar kursus yang dibeli |
| UC-U-08 | View course progress | Lihat progress kursus (%, modules completed) |
| UC-U-09 | Download certificate | Download certificate setelah kursus selesai 100% |
| UC-U-10 | Edit profile | Update nama, email, foto profil |

**Key Features:**
- ✅ Shopping cart & checkout
- ✅ My Account dashboard (Webinars, Products, Courses, Certificates)
- ✅ Certificate auto-generated saat kursus 100%

---

### 3. Parent (Orang Tua)

**Karakteristik:** Orang tua siswa PAUD (usia 0-6 tahun)

**Important Notes (UPDATED in v5.0):**
- **PAUD Target Market:** Anak usia 0-6 tahun (belum bisa login sendiri)
- **Parent Monitoring Only:** Semua monitoring dilakukan oleh orang tua
- **Account Creation:** Bisa dibuat standalone (waiting list) atau auto via student registration
- **Email Unique:** Email unique per school (same email bisa di sekolah berbeda)
- **Multiple Children:** Bisa monitor banyak anak dalam 1 account

**Use Cases:**

| ID | Use Case | Deskripsi |
|----|----------|-----------|
| UC-P-01 | View children data | Lihat data anak (nama, kelas, absensi, nilai) |
| UC-P-02 | View children absensi | Lihat rekap kehadiran anak |
| UC-P-03 | View children nilai | Lihat nilai asesmen anak |
| UC-P-04 | Download children rapor | Download PDF rapor anak (jika sekolah Pro Plan) |
| UC-P-05 | View SPP status | Lihat status pembayaran SPP (jika sekolah Pro Plan) |
| UC-P-06 | View tabungan anak | Lihat saldo tabungan anak (jika sekolah Pro Plan) |
| UC-P-07 | Manage own profile | Manage data pribadi (nama, HP, alamat, dll) |

**Key Features:**
- ✅ Account bisa dibuat sebelum student terdaftar (waiting list)
- ✅ Login dengan email orang tua (bukan email siswa)
- ✅ Bisa monitor multiple children (1 email, banyak anak)
- ✅ Multi-school support (jika anak sekolah di tempat berbeda)
- ✅ Multi-role support (misal: Parent di sekolah A + Teacher di sekolah B)

**Login Credentials:**
- Email: Email yang diinput Headmaster (unique per school)
- Password: Random-generated, dikirim via email
- Bisa ubah password setelah first login

**Example Scenarios:**

**Scenario A: Waiting List**
```
1. Headmaster create parent profile: ibuani@gmail.com
2. Status: 0 anak (waiting list)
3. Email sent: Welcome + credentials
4. Parent bisa login tapi belum ada data anak
5. Saat anak diterima: Headmaster add student + pilih parent dari dropdown
6. Parent refresh dashboard → muncul data anak
```

**Scenario B: Multiple Children**
```
Parent: Ibu Ani (email: ibuani@gmail.com)
Children:
  - Budi Santoso (Kelas A) - added by Headmaster
  - Siti Aminah (Kelas B)  - added by Headmaster (same parent)

Login: ibuani@gmail.com
Dashboard: Show both children (Budi & Siti)
```

**Scenario C: Multi-School**
```
Parent: Ibu Ani
- TK Melati: Parent untuk Budi (email: ibuani@gmail.com)
- SD Harapan: Parent untuk Siti (email: ibuani@gmail.com)

Login: ibuani@gmail.com
Dashboard: Show school selector
  - TK Melati (1 anak: Budi)
  - SD Harapan (1 anak: Siti)
```

---

### 4. Teacher (Guru)

**Karakteristik:** Guru yang mengajar di sekolah

**Use Cases:**

| ID | Use Case | Deskripsi |
|----|----------|-----------|
| UC-T-01 | View school students | Lihat semua siswa di sekolah |
| UC-T-02 | View student details | Lihat detail data siswa (read-only) |
| UC-T-03 | Input absensi | Input kehadiran harian siswa |
| UC-T-04 | Input nilai asesmen | Input nilai (Checklist, Anekdot, Portfolio, Foto Berseri) |
| UC-T-05 | View rapor siswa | Lihat rapor siswa (read-only) |
| UC-T-06 | Generate PDF rapor | Generate & download PDF rapor (Pro Plan only) |
| UC-T-07 | Input SPP payment | Input pembayaran SPP siswa (Pro Plan only) |
| UC-T-08 | Input tabungan siswa | Input setoran/tarik tabungan (Pro Plan only) |
| UC-T-09 | View own class | Filter siswa by kelas yang diajar |

**Key Features:**
- ✅ Read access ke semua data siswa di sekolah
- ✅ Input access untuk teaching activities (absensi, nilai, keuangan)
- ✅ Generate rapor PDF (Pro Plan)

**Restrictions:**
- ❌ Tidak bisa add/edit/delete siswa (hanya Headmaster)
- ❌ Tidak bisa manage guru lain
- ❌ Tidak bisa manage kelas
- ❌ Tidak bisa upgrade subscription

---

### 5. Headmaster (Kepala Sekolah)

**Karakteristik:** Owner sekolah, yang daftar pertama kali

**Use Cases:**

| ID | Use Case | Deskripsi |
|----|----------|-----------|
| UC-H-01 | Register school | Daftar sekolah baru (Free Plan) |
| UC-H-02 | Edit school profile | Edit nama, alamat, logo sekolah |
| UC-H-03 | View subscription status | Lihat status Free/Pro Plan |
| UC-H-04 | Upgrade to Pro | Upgrade dari Free ke Pro Plan |
| UC-H-05 | Manage teachers | Add/edit/remove guru |
| UC-H-06 | Manage students | Add/edit/delete siswa (dengan dropdown parent selection) |
| UC-H-07 | Input absensi | Input kehadiran (sama seperti Teacher) |
| UC-H-08 | Input nilai asesmen | Input nilai (sama seperti Teacher) |
| UC-H-09 | Generate PDF rapor | Generate rapor (Pro Plan only) |
| UC-H-10 | Manage keuangan | Input SPP & Tabungan (Pro Plan only) |
| UC-H-11 | View school analytics | Lihat statistik siswa, keuangan |
| UC-H-12 | Manage kelas | Create/edit/delete kelas, assign wali kelas (1 teacher bisa handle multiple kelas) |
| UC-H-13 | Manage parent profiles | Add/edit/delete parent database (independent entity) |

**Key Differences vs Teacher:**
- ✅ Bisa manage siswa (add/edit/delete) - Teacher hanya bisa view
- ✅ Bisa manage guru (add/remove)
- ✅ Bisa manage kelas (create/assign)
- ✅ Bisa manage parent profiles (standalone database)
- ✅ Bisa upgrade subscription
- ✅ Bisa edit school profile
- ✅ Full access ke semua fitur sekolah

**Restrictions:**
- ❌ Tidak ada pengaturan tahun ajaran (simplified)
- ❌ Tidak ada branding website sekolah

---

### 6. Moderator (Content Manager)

**Karakteristik:** Content creator untuk platform public (B2C)

**Use Cases:**

| ID | Use Case | Deskripsi |
|----|----------|-----------|
| UC-MOD-01 | Create webinar | Buat webinar dengan Zoom link manual |
| UC-MOD-02 | Edit webinar | Edit detail webinar (title, mentor, harga, Zoom link) |
| UC-MOD-03 | Delete webinar | Hapus webinar |
| UC-MOD-04 | Create course | Buat kursus baru dengan modules/lessons |
| UC-MOD-05 | Edit course | Edit course content (modules, videos, PDFs) |
| UC-MOD-06 | Delete course | Hapus course |
| UC-MOD-07 | Create product | Buat produk digital (e-book, template, dll) |
| UC-MOD-08 | Edit product | Edit detail & upload file produk |
| UC-MOD-09 | Delete product | Hapus produk |
| UC-MOD-10 | Create artikel | Tulis artikel/blog |
| UC-MOD-11 | Edit artikel | Edit artikel |
| UC-MOD-12 | Delete artikel | Hapus artikel |
| UC-MOD-13 | Manage mentor profiles | Add/edit/delete mentor |
| UC-MOD-14 | Manage testimonials | Add/edit/delete testimonial |

**Key Features:**
- ✅ Full content creation access
- ✅ Manage semua content public (webinar, course, product, artikel)
- ✅ Webinar: Input Zoom link manual (create di Zoom dulu, lalu paste link)

**Restrictions:**
- ❌ Tidak bisa akses SIAKAD (school data)
- ❌ Tidak bisa site settings
---

### 7. Admin (Super Admin)

**Karakteristik:** Super admin dengan full access

**Use Cases:**

| ID | Use Case | Deskripsi |
|----|----------|-----------|
| UC-A-01 | All Moderator use cases | Semua yang bisa Moderator lakukan |
| UC-A-02 | Manage users | View/edit/delete semua user |
| UC-A-03 | Manage schools | View semua sekolah, edit subscription manual |
| UC-A-04 | Site settings | Edit logo, kontak, social media platform |
| UC-A-05 | View all orders | Lihat semua transaksi |
| UC-A-06 | Manual enroll | Enroll user ke kursus secara manual (troubleshooting) |
| UC-A-07 | Manual certificate | Generate certificate manual (troubleshooting) |
| UC-A-08 | View sales analytics | Lihat statistik penjualan (webinar, course, product) |
| UC-A-09 | Manage promo codes | Create/edit/delete kode promo |

**Key Differences vs Moderator:**
- ✅ Full system access (users, schools, settings)
- ✅ Manual enrollment & certificate generation (untuk troubleshooting)
- ✅ Site settings (logo, footer, contact info)
- ✅ View sales analytics (revenue, transactions)
- ✅ View semua data (cross-tenant)

**Note on Certificate:**
- **Normal flow:** User complete course 100% → System auto-generate certificate
- **Admin manual:** Hanya untuk troubleshooting (misal: user komplain certificate tidak generate)

---

## Key Differences

### 🏫 SIAKAD Roles Comparison

| Feature | Parent | Teacher | Headmaster |
|---------|--------|---------|------------|
| **Data Access** |
| View children data | ✅ | - | - |
| View school students | - | ✅ | ✅ |
| View student details | - | ✅ | ✅ |
| **Data Input** |
| Add/edit/delete students | - | ❌ | ✅ |
| Input absensi | - | ✅ | ✅ |
| Input nilai | - | ✅ | ✅ |
| **Management** |
| Manage teachers | - | - | ✅ |
| Manage kelas | - | - | ✅ |
| Edit school profile | - | - | ✅ |
| Upgrade subscription | - | - | ✅ |
| **Pro Features** |
| Generate PDF rapor | Download | Generate | Generate |
| Manage keuangan | View only | ✅ (Pro) | ✅ (Pro) |

### 🌐 Public Platform Roles Comparison

| Feature | Guest | User | Moderator | Admin |
|---------|-------|------|-----------|-------|
| **Shopping** |
| Browse products | ✅ | ✅ | ✅ | ✅ |
| Add to cart | - | ✅ | ✅ | ✅ |
| Checkout | - | ✅ | ✅ | ✅ |
| **Learning** |
| Take courses | - | ✅ | ✅ | ✅ |
| Get certificate | - | Auto | Auto | Auto + Manual |
| **Content Management** |
| Create webinar | - | - | ✅ | ✅ |
| Create course | - | - | ✅ | ✅ |
| Create artikel | - | - | ✅ | ✅ |
| **System Admin** |
| Site settings | - | - | - | ✅ |
| User management | - | - | - | ✅ |
| View sales analytics | - | - | - | ✅ |
| Manual enroll | - | - | - | ✅ |

### 💳 Subscription Impact

| Feature | Free Plan | Pro Plan |
|---------|-----------|----------|
| **Limits** |
| Student limit | 20 max | Unlimited |
| Teacher limit | 5 max | Unlimited |
| **Features** |
| Input siswa & absensi | ✅ | ✅ |
| Input nilai asesmen | ✅ | ✅ |
| Generate PDF rapor | ❌ | ✅ |
| Keuangan (SPP) | ❌ | ✅ |
| Keuangan (Tabungan) | ❌ | ✅ |
| **Pricing** |
| Cost | Free | Rp 200.000/bulan |

**Free Plan Restrictions:**
- Saat add student ke-21: Error → "Upgrade ke Pro untuk unlimited siswa"
- Saat add teacher ke-6: Error → "Upgrade ke Pro untuk unlimited guru"
- Saat klik "Generate PDF": Disabled button → "🔒 Pro Plan Only"
- Saat akses menu Keuangan: Redirect → Upgrade page

---

# FLOWS DOKUMENTASI
## Platform Paud Pedia - System Flows & Process

**Tech Stack:**
- Backend: Laravel 12 (REST API) + MySQL 8.0
- Admin Panel: Laravel Filament (Admin & Moderator)
- Public Frontend: Next.js/Nuxt.js (Guest, User - Shopping & LMS)
- SIAKAD Frontend: React/Vue + Vite (Parent, Teacher, Headmaster - School Management)

**Tujuan:** Dokumentasi alur sistem secara detail untuk setiap fitur

> **Catatan:** Flow ini menjelaskan interaksi antara frontend dan backend API

---

## 📋 Daftar Isi

1. [Multi-Tenant Flows](#-multi-tenant-flows)
   - [Flow 1: School Registration (Headmaster Onboarding)](#flow-1-school-registration-headmaster-onboarding)
   - [Flow 2: Teacher Registration (Manual by Headmaster)](#flow-2-teacher-registration-manual-by-headmaster)
   - [Flow 3: Kelas Management](#flow-3-kelas-management)
   - [Flow 4: Parent Management (Independent)](#flow-4-parent-management-independent)
   - [Flow 5: Student Registration (by Headmaster)](#flow-5-student-registration-by-headmaster)
   - [Flow 6: Multi-School Access (One User, Multiple Roles)](#flow-6-multi-school-access-one-user-multiple-roles)
2. [Subscription Flows](#-subscription-flows)
   - [Flow 1: New School Registration (Free Plan)](#flow-1-new-school-registration-free-plan)
   - [Flow 2: Upgrade to Pro Plan](#flow-2-upgrade-to-pro-plan)
   - [Flow 3: Feature Gating (Free vs Pro)](#flow-3-feature-gating-free-vs-pro)
3. [Payment Flows](#-payment-flows)
   - [Flow 1: Webinar Purchase](#flow-1-webinar-purchase)
   - [Flow 2: Product Digital Purchase](#flow-2-product-digital-purchase)
   - [Flow 3: Course Enrollment](#flow-3-course-enrollment)
   - [Flow 4: Cart to Checkout (Multiple Items)](#flow-4-cart-to-checkout-multiple-items)
4. [Content Management Flows](#-content-management-flows)
   - [Flow 1: Absensi Input & View](#flow-1-absensi-input--view)
   - [Flow 2: Moderator Buat Webinar](#flow-2-moderator-buat-webinar)
   - [Flow 3: Moderator Buat Kursus](#flow-3-moderator-buat-kursus)
   - [Flow 4: User Complete Course & Get Certificate](#flow-4-user-complete-course--get-certificate)
   - [Flow 5: Quiz Attempt & Auto-Grading](#flow-5-quiz-attempt--auto-grading)
   - [Flow 6: Moderator Buat Produk Digital](#flow-6-moderator-buat-produk-digital)
   - [Flow 7: Moderator Buat Artikel](#flow-7-moderator-buat-artikel)
5. [Email Notification Flows](#-email-notification-flows)

---

## 🏫 Multi-Tenant Flows

### Flow 1: School Registration (Headmaster Onboarding)

```
1. Calon Headmaster akses /daftar-sekolah
2. Fill form:
   - Nama sekolah, NPSN, Alamat
   - Nama headmaster, Email, Password
3. Submit → System creates:
   - schools record (plan: 'free', limit: 20)
   - auth.users record
   - school_members (role: 'headmaster')
4. Email verifikasi dikirim
5. Headmaster verify email
6. Login → Dashboard sekolah (Free Plan)
7. Banner: "Upgrade ke Pro untuk unlimited siswa + PDF Rapor + Keuangan"
```

---

### Flow 2: Teacher Registration (Manual by Headmaster)

```
1. Headmaster login → Dashboard → Menu "Guru"
2. Klik "Tambah Guru"
3. Fill form:
   - Nama Lengkap
   - Email
   - Password (auto-generate atau manual input)
   - Nomor HP (optional)
4. Submit
5. System creates:
   - auth.users record
   - school_members (role: 'teacher', school_id)
   - teachers record (basic profile)
6. Note: Assignment teacher ke kelas dilakukan di Kelas Management
7. Email credentials dikirim ke guru:
   "Akun Anda telah dibuat oleh [Nama Sekolah]
    Email: guru.a@gmail.com
    Password: xxxxx
    Login di: https://sikola.paudceria.com/login"
7. Guru login → Dashboard Guru
```

---

### Flow 3: Kelas Management

**Business Rules:**
- ✅ 1 Kelas hanya punya 1 Wali Kelas (homeroom teacher)
- ✅ 1 Teacher BOLEH jadi Wali Kelas di multiple kelas
- ✅ Contoh: Bu Ika bisa jadi wali kelas A dan B sekaligus
- ✅ Kelas bisa tanpa wali kelas (optional assignment)

```
1. Headmaster login → Dashboard → Menu "Kelas"
2. Klik "Tambah Kelas"
3. Fill form:
   - Nama Kelas (misal: "Kelas A", "Kelompok Bermain")
   - Tingkat (misal: "TK A", "TK B", "KB")
   - Wali Kelas (pilih dari daftar teacher - optional)
     * Dropdown menampilkan semua teacher di sekolah
     * Teacher yang sama bisa dipilih di multiple kelas
     * Contoh: Bu Ika → Kelas A (pagi), Kelas B (siang)
   - Kapasitas (optional)
4. Submit → Kelas created dengan homeroom_teacher_id
5. Edit kelas (jika perlu):
   - Ubah wali kelas
   - System update classes.homeroom_teacher_id
6. Teacher bisa filter siswa by kelas yang dia handle sebagai wali kelas
```

---

### Flow 4: Parent Management (Independent)

**Purpose:** 
- Maintain database orang tua
- Prevent duplicate parent accounts
- Support waiting list (parent registered before student enrolled)
- Easier data management & updates

```
1. Headmaster login → Dashboard → Menu "Orang Tua"

2. View Parent Database:
   ┌────────────────────────────────────────────┐
   │ 🔍 Search: [_____________] [+ Tambah Ortu] │
   ├────────────────────────────────────────────┤
   │ Email              │ Nama       │ Anak     │
   │ ibuani@gmail.com   │ Ibu Ani    │ 2 siswa  │
   │ bapakbudi@mail.com │ Bpk Budi   │ 1 siswa  │
   │ siti@email.com     │ Ibu Siti   │ 0 siswa  │ ← Waiting list
   └────────────────────────────────────────────┘

3. Klik [+ Tambah Ortu]:
   ┌─────────────────────────────────────┐
   │ Tambah Orang Tua                    │
   ├─────────────────────────────────────┤
   │ Email Orang Tua * (untuk login)     │
   │ [____________________]              │
   │                                     │
   │ Nama Ayah                           │
   │ [____________________]              │
   │                                     │
   │ Nama Ibu                            │
   │ [____________________]              │
   │                                     │
   │ No. HP                              │
   │ [____________________]              │
   │                                     │
   │ Pekerjaan Ayah (optional)           │
   │ [____________________]              │
   │                                     │
   │ Pekerjaan Ibu (optional)            │
   │ [____________________]              │
   │                                     │
   │ Alamat (optional)                   │
   │ [____________________]              │
   │                                     │
   │ [Batal]  [Simpan]                   │
   └─────────────────────────────────────┘

4. System validate:
   - Email must be unique per school ✅
   - Email format validation ✅

5. IF email already exists:
   ┌─────────────────────────────────────┐
   │ ⚠️ Email Sudah Terdaftar            │
   ├─────────────────────────────────────┤
   │ Email: ibuani@gmail.com             │
   │ sudah digunakan oleh:               │
   │                                     │
   │ Nama: Ibu Ani                       │
   │ Anak: Siti (Kelas B)                │
   │                                     │
   │ [OK]                                │
   └─────────────────────────────────────┘

6. IF email unique:
   - Create auth.users account
   - Create parent_profiles record
   - Send welcome email with credentials
   - Status: 0 anak (waiting list)

7. Parent saved → Redirect to parent list

8. Parent bisa langsung dipilih saat student registration
```

---

### Flow 5: Student Registration (by Headmaster)

**REVISED in v3.0:** Simplified - Parent-only monitoring (Student login removed)

**Important:** Hanya Headmaster yang bisa add/edit/delete siswa (Teacher hanya read-only).

**Concept:** 
- Student = Data master siswa (nama, kelas, dll)
- Student WAJIB link ke Parent Profile (parent monitoring)
- **NO Student Login** - Sesuai target market PAUD (usia 0-6 tahun)

```
1. Headmaster login → Dashboard → Menu "Siswa"
2. Klik "Tambah Siswa"
3. Fill form:
   
   Tab 1: Data Siswa
   - Nama Lengkap Siswa
   - Tanggal Lahir
   - Jenis Kelamin
   - NISN (optional)
   - Kelas (pilih dari kelas yang sudah dibuat)
   - Alamat
   
   Tab 2: Data Orang Tua (REQUIRED) ⭐ PARENT MONITORING
   ┌──────────────────────────────────────────────────┐
   │ Pilih Orang Tua:                                 │
   │                                                  │
   │ ◉ Pilih dari daftar existing                     │
   │   ┌─────────────────────────────────────────┐    │
   │   │ 🔍 Search email or nama...              │    │
   │   │                                         │    │
   │   │ [Dropdown: Pilih Orang Tua ▼]           │    │
   │   │                                         │    │
   │   │ Options:                                │    │
   │   │ ✓ Ibu Ani (ibuani@gmail.com)            │    │
   │   │   Bpk Budi (bapakbudi@mail.com)         │    │
   │   │   Ibu Siti (siti@email.com)             │    │
   │   └─────────────────────────────────────────┘    │
   │                                                  │
   │   Preview Data Terpilih:                         │
   │   ┌─────────────────────────────────────────┐    │
   │   │ 📧 Email: ibuani@gmail.com              │    │
   │   │ 👨 Ayah: Budi Santoso                   │    │
   │   │ 👩 Ibu: Ani Wijaya                      │    │
   │   │ 📱 HP: 0812-3456-7890                   │    │
   │   │ 👶 Anak terdaftar: Siti (Kelas B)       │    │
   │   └─────────────────────────────────────────┘    │
   │                                                  │
   │ ○ Buat orang tua baru                            │
   │   Email Orang Tua * [____________________]       │
   │   Nama Ayah        [____________________]       │
   │   Nama Ibu         [____________________]       │
   │   No. HP           [____________________]       │
   │   Pekerjaan Ayah   [____________________]       │
   │   Pekerjaan Ibu    [____________________]       │
   │   Alamat           [____________________]       │
   │                                                  │
   └──────────────────────────────────────────────────┘
   
4. Submit → Process based on selection:
   
   IF "Pilih dari daftar existing":
     - Get parent_profile_id from selected parent
     - Link student ke parent existing
     - Send email: "Anak baru ditambahkan"
   
   IF "Buat orang tua baru":
     - Validate email unique
     - Create parent_profile (Flow 4)
     - Link student ke parent baru
     - Send email: "Akun dibuat + anak terdaftar"

5. System creates student record with parent link

6. Redirect ke daftar siswa
```

**Email Templates:**

**Email 1: Parent Existing (Child Added)**
```html
Subject: Anak Baru Ditambahkan - TK Melati

Hai Ibu Ani,

Anak baru telah ditambahkan ke akun Anda:
• Budi Santoso (Kelas A) ✨ NEW

Sekarang Anda dapat memantau:
• Anak 1: Siti (Kelas B)
• Anak 2: Budi (Kelas A)

Login: https://sikola.paudceria.com/login
Email: ibuani@gmail.com

Terima kasih,
TK Melati
```

**Email 2: Parent Baru (Account Created)**
```html
Subject: Akun Orang Tua & Pendaftaran Siswa - TK Melati

Hai Ibu Y,

Akun Anda telah dibuat untuk memantau perkembangan anak:
• Budi Santoso (Kelas A)

Kredensial Login:
📧 Email: newemail@gmail.com
🔑 Password: RandomPass
🔗 Login: https://sikola.paudceria.com/login

Silakan login dan ubah password Anda.

Terima kasih,
TK Melati
```

**Important Notes:**
- **Parent Monitoring Only:** Sesuai target market PAUD (anak usia 0-6 tahun tidak bisa login)
- **Parent Selection = REQUIRED:** Pilih existing atau create new
- **Email Validation:** Prevent duplicate parent accounts
- **Multiple Children:** Same parent, different students (auto-handled)
- **Waiting List Support:** Parent bisa exist tanpa anak (0 students)

---

### Flow 6: Multi-School Access (One User, Multiple Roles)

**Scenario:** Bu Ani adalah Teacher di TK Melati, tapi anaknya sekolah di TK Mawar (Bu Ani jadi Parent)

```
Data Structure:
school_members {
  { school_id: 'A', user_id: 'ani', role: 'teacher' },  // TK Melati
  { school_id: 'B', user_id: 'ani', role: 'parent' }    // TK Mawar (via parent_profiles)
}

Login Flow:
1. Bu Ani login
2. System detect: User punya akses ke 2 sekolah
3. Dashboard show: "Pilih Sekolah"
   - TK Melati (sebagai Guru)
   - TK Mawar (sebagai Orang Tua)
4. Bu Ani pilih TK Melati → Role: Teacher
5. Bu Ani bisa switch ke TK Mawar → Role: Parent

Context Switching:
- Frontend store: currentSchoolId + currentRole
- Setiap API call kirim: school_id header
- RLS filter data by school_id
```

---

## 💳 Subscription Flows

### Flow 1: New School Registration (Free Plan)

```
1. Visit /daftar-sekolah
2. Fill form → Submit
3. System creates:
   - School (subscription_plan: 'free', student_limit: 20, teacher_limit: 5)
   - Headmaster account
4. Email aktivasi
5. Login → Dashboard (Free Plan)
6. Status: Free Plan (20 siswa max, 5 guru max)
```

---

### Flow 2: Upgrade to Pro Plan

```
1. Headmaster klik "Upgrade ke Pro"
2. Redirect ke /billing/upgrade
3. Konfirmasi:
   ┌────────────────────────────────────────┐
   │  Upgrade ke Pro Plan                   │
   ├────────────────────────────────────────┤
   │  Sekolah: TK Melati                    │
   │  Paket: Pro Plan                       │
   │  Harga: Rp 200.000/bulan               │
   │                                        │
   │  Fitur yang unlock:                    │
   │  ✓ Unlimited Siswa                     │
   │  ✓ Unlimited Guru                      │
   │  ✓ Generate PDF Rapor                  │
   │  ✓ Keuangan (SPP & Tabungan)           │
   │                                        │
   │  [Bayar Sekarang]                      │
   └────────────────────────────────────────┘

4. Klik "Bayar Sekarang"
5. Pilih metode pembayaran (Midtrans)
6. Selesaikan pembayaran
7. Midtrans webhook → Update schools:
   - subscription_plan = 'pro'
   - student_limit = NULL (unlimited)
   - teacher_limit = NULL (unlimited)
8. Email konfirmasi
9. Dashboard: Pro features unlocked
```

---

### Flow 3: Feature Gating (Free vs Pro)

```
Frontend: Disable Pro features untuk Free Plan

IF school.plan === 'free':
  - Generate PDF button → Disabled dengan label "🔒 Pro Only"
  - Menu Keuangan → Show upgrade prompt
  
IF school.plan === 'pro':
  - All features enabled

Backend: Validate Pro access
  - Check subscription sebelum generate PDF
  - Return error 403 jika Free Plan akses Pro feature
  - Show upgrade URL
```

---

## 💰 Payment Flows

### Flow 1: Webinar Purchase

```
1. User browse /webinar
2. Klik webinar detail
3. Add to cart / Direct checkout
4. Apply promo code (optional)
5. Click "Checkout"
6. Midtrans payment gateway
7. User bayar
8. Midtrans webhook → Update order status
9. Email sent dengan Zoom link
10. User akses My Account → tab Webinars → klik Zoom link
```

---

### Flow 2: Product Digital Purchase

```
1. User browse /marketplace
2. Klik produk detail
3. Add to cart / Direct checkout
4. Apply promo code (optional)
5. Click "Checkout"
6. Midtrans payment gateway
7. User bayar
8. Midtrans webhook → Update order status
9. Email sent dengan download link
10. User akses My Account → tab Products → download file
```

---

### Flow 3: Course Enrollment

```
1. User browse /courses
2. Klik course detail
3. Add to cart / Direct checkout
4. Apply promo code (optional)
5. Click "Checkout"
6. Midtrans payment gateway
7. User bayar
8. Midtrans webhook → Update order status
9. Auto-enroll ke course
10. Email sent dengan course link
11. User akses My Account → tab Courses → mulai belajar
12. Progress tracked per module
13. Setelah 100% → Auto-generate certificate
```

---

### Flow 4: Cart to Checkout (Multiple Items)

```
1. User browse berbagai produk (Webinar, Course, Product Digital)
2. User klik "Add to Cart" pada beberapa item
3. Item tersimpan di database `carts` dan `cart_items`
4. User buka halaman Cart (/cart)
5. User review list item, kuantitas, dan subtotal
6. User input Promo Code (optional) → System validasi promo & hitung ulang total
7. Klik "Proceed to Checkout"
8. System generate Order dengan OrderItems polimorfik
9. Redirect ke Midtrans payment gateway
10. Midtrans webhook mengirim notifikasi (pending -> paid)
11. System proses masing-masing item sesuai tipenya (enroll course, kirim download link, dsb)
```

---

## 🎓 Content Management Flows

### Flow 1: Absensi Input & View

```
Teacher Flow:
1. Teacher login → Dashboard → Menu "Absensi"
2. Pilih tanggal (default: hari ini)
3. Pilih kelas (dropdown kelas yang teacher handle sebagai wali kelas)
   - Jika Bu Ika handle Kelas A dan B → Dropdown: [Kelas A ▼] [Kelas B ▼]
   - Jika teacher belum assigned ke kelas → Show empty state
4. List siswa muncul (filtered by selected class)
5. Input status per siswa:
   - ✅ Hadir
   - 🤒 Sakit
   - 📝 Izin
   - ❌ Alpha
6. Save → Data saved to attendance table

Parent Flow:
1. Parent login → Dashboard → Tab "Absensi"
2. Pilih anak (jika punya multiple children)
3. Pilih periode (Bulan ini, Semester ini, Custom)
4. View rekap:
   - Total Hadir: 15 hari
   - Sakit: 2 hari
   - Izin: 1 hari
   - Alpha: 0 hari
   - Persentase kehadiran: 83%
5. View detail per tanggal
```

---

### Flow 2: Moderator Buat Webinar

```
1. Moderator login → /admin/webinars
2. Klik "Tambah Webinar"
3. Fill form:
   - Title, Description
   - Mentor (pilih dari mentor_profiles)
   - Tanggal & Waktu
   - Zoom Link (manual input - create di Zoom dulu)
   - Zoom Meeting ID, Passcode (manual input - create di Zoom dulu)
   - Harga
   - Upload thumbnail
4. Toggle "Active" → Publish
5. Save
6. Webinar muncul di /webinar
```

---

### Flow 3: Moderator Buat Kursus

```
1. Moderator login → /admin/courses
2. Klik "Tambah Kursus"
3. Fill basic info:
   - Title, Description
   - Category, Level (Beginner/Intermediate/Advanced)
   - Mentor (pilih dari mentor_profiles)
   - Harga
   - Upload thumbnail
4. Save draft
5. Tambah Modul:
   - Modul 1: Introduction
     - Lesson 1: Video (YouTube embed URL)
     - Lesson 2: PDF (upload file)
   - Modul 2: Main Content
     - Lesson 1: Video
     - Lesson 2: Quiz (create questions)
6. Set urutan modul (drag & drop / cara lainnya)
7. Preview kursus
8. Toggle "Published"
9. Kursus muncul di /courses
```

---

### Flow 4: User Complete Course & Get Certificate

```
1. User enrolled di course (via payment)
2. User akses course → mulai lesson pertama
3. Setiap lesson selesai:
   - Mark as completed
   - Progress updated: 10%, 20%, ...
4. User selesaikan quiz → auto-grading
5. Progress: 100% (all modules completed)
6. System trigger: Generate Certificate
   - Template PDF dengan:
     - Nama user
     - Judul course
     - Tanggal selesai
     - Certificate ID (unique)
7. Certificate saved ke database
8. Email sent: "Selamat! Anda telah menyelesaikan [Course]"
9. User download certificate dari My Account
```

---

### Flow 5: Quiz Attempt & Auto-Grading

```
1. User akses lesson dengan tipe konten 'quiz'
2. User klik "Mulai Kuis"
3. System buat record di tabel `quiz_attempts` (status: ongoing)
4. Tampil soal-soal kuis (`quiz_questions`)
5. User pilih jawaban, system simpan ke `quiz_attempt_answers`
6. User klik "Submit Kuis"
7. System hitung nilai berdasarkan `is_correct` di `quiz_answers`
8. Update `quiz_attempts` (status: completed, score: calculated)
9. Jika nilai mencukupi (passing grade), lesson di-mark completed
10. Lanjut ke materi berikutnya atau generate sertifikat jika ini materi terakhir
```

---

### Flow 6: Moderator Buat Produk Digital

```
1. Moderator login → /admin/products
2. Klik "Tambah Produk"
3. Fill form:
   - Judul Produk
   - Deskripsi (rich text editor)
   - Category (E-book, Template, Worksheet, Poster, dll)
   - Harga
   - Upload link produk (jika ada/opsional)
   - Upload thumbnail
   - Upload file produk (PDF, ZIP, etc)
     * Max file size: 50MB
     * Allowed: .pdf, .zip, .doc, .docx, .ppt, .pptx
4. Preview produk
5. Toggle "Active" → Publish
6. Save
7. Produk muncul di /marketplace

Product Download Flow:
1. User beli produk → Payment success
2. System saves to user_products table
3. Email sent dengan download link
4. User akses My Account → tab "My Products"
5. Klik "Download" → File downloaded
6. Download link valid selamanya (no expiry)
```

---

### Flow 7: Moderator Buat Artikel

```
1. Moderator login → /admin/articles
2. Klik "Tambah Artikel"
3. Fill form:
   - Judul Artikel
   - Slug (auto-generated dari judul, editable)
   - Category (Tips Parenting, Pendidikan PAUD, Kesehatan Anak, dll)
   - Content (rich text editor dengan image upload)
   - Meta Description (SEO)
   - Upload featured image
   - Tags (optional)
4. Save as Draft atau Publish
5. Preview artikel
6. IF Published:
   - Artikel muncul di /artikel
   - SEO-friendly URL: /artikel/[slug]
7. Public dapat akses tanpa login (free content)

Article Management:
- Featured Article: Toggle untuk homepage display
- View Counter: Track berapa kali dibaca
- Share Buttons: Facebook, Twitter, WhatsApp
```

---

## 📧 Email Notification Flows

1. **School Registration** → Email verifikasi
2. **Teacher Account Created** → Login credentials
3. **Parent Profile Created (standalone)** → Welcome email + credentials (waiting list)
4. **Parent Account Created (via student reg)** → Login credentials + child info
5. **Child Added to Existing Parent** → Notification email (child added)
6. **Payment Success (Webinar)** → Email dengan Zoom link
7. **Payment Success (Product)** → Email dengan download link
8. **Payment Success (Course)** → Email dengan course link
9. **Course Completed** → Email dengan certificate
10. **Subscription Upgraded** → Email konfirmasi Pro Plan

---
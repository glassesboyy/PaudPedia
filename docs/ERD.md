# ENTITY RELATIONSHIP DIAGRAM (ERD)
## Platform Paud Pedia - Skema & Struktur Database

**Database:** MySQL 8.0  
**Backend:** Laravel 11+  
**Tujuan:** Spesifikasi skema database lengkap - Tabel, Kolom, Relasi, Constraint

**Cakupan:**
- ✅ Definisi tabel
- ✅ Spesifikasi kolom (nama, tipe, constraint)
- ✅ Primary Key & Foreign Key
- ✅ Unique constraint
- ✅ Index
- ✅ Tipe enum
- ✅ Kardinalitas relasi

---

## 📋 Daftar Isi

1. [Database Overview](#️-gambaran-umum-database)
   - [Tipe Database](#tipe-database)
   - [Konvensi Penamaan](#konvensi-penamaan)
   - [Kolom Standar](#kolom-standar)
2. [Core Schema](#-skema-inti-core-schema)
   - [Tabel: users](#tabel-users-supabase-auth)
   - [Tabel: user_profiles](#tabel-user_profiles)
3. [Multi-Tenant Schema](#-skema-multi-tenant)
   - [Tabel: schools](#tabel-schools)
   - [Tabel: school_members](#tabel-school_members)
   - [Tabel: teachers](#tabel-teachers)
   - [Tabel: classes](#tabel-classes)
   - [Tabel: parent_profiles](#tabel-parent_profiles)
   - [Tabel: students](#tabel-students)
   - [Tabel: attendance](#tabel-attendance)
   - [Tabel: assessments](#tabel-assessments)
   - [Tabel: finances](#tabel-finances)
4. [Content Management Schema](#-content-management-schema)
   - [Tabel: mentors](#tabel-mentors)
   - [Tabel: categories](#tabel-categories)
   - [Tabel: webinars](#tabel-webinars)
   - [Tabel: courses](#tabel-courses)
   - [Tabel: modules](#tabel-modules)
   - [Tabel: lessons](#tabel-lessons)
   - [Tabel: course_enrollments](#tabel-course_enrollments)
   - [Tabel: lesson_Progress](#tabel-lesson_progress)
   - [Tabel: products](#tabel-products)
   - [Tabel: articles](#tabel-articles)
   - [Tabel: testimonials](#tabel-testimonials)
5. [Commerce Schema](#-commerce-schema)
   - [Tabel: orders](#tabel-orders)
   - [Tabel: order_items](#tabel-order_items)
   - [Tabel: promo_codes](#tabel-promo_codes)
   - [Tabel: site_settings](#tabel-site_settings)
6. [Indexes & Constraints](#-indexes--constraints)
   - [Index Performa](#index-performa)
   - [Ringkasan Unique Constraint](#ringkasan-unique-constraint)
7. [Peta Relasi ERD](#️-peta-relasi-erd)
   - [Domain Multi-Tenant](#domain-multi-tenant)
   - [Domain Manajemen Konten](#domain-manajemen-konten)
   - [Domain Perdagangan](#domain-perdagangan)
   - [Lintas Domain](#lintas-domain)
8. [Statistik Database](#-statistik-database)
9. [Pertimbangan Keamanan](#-pertimbangan-keamanan)
   - [Row Tingkat Security (RLS)](#row-tingkat-security-rls-rls)
   - [Data Sensitif](#data-sensitif)
10. [Notes](#-notes)
    - [Strategi Migrasi](#strategi-migrasi)
    - [Integritas Data](#integritas-data)
    - [Tips Performa](#tips-performa)
    - [Skalabilitas](#skalabilitas)

---

## 🗄️ Gambaran Umum Database

### Tipe Database
- **MySQL 8.0** (Production)
- **Engine:** InnoDB (untuk transaction support & foreign key)
- **Character Set:** utf8mb4 (untuk emoji & multilingual support)
- **Collation:** utf8mb4_unicode_ci

### Konvensi Penamaan
- **Tabel:** Plural, snake_case (contoh: `users`, `school_members`)
- **Kolom:** snake_case (contoh: `user_id`, `created_at`)
- **Foreign Key:** `{referenced_table_singular}_id` (contoh: `school_id`, `user_id`)
- **Index:** `idx_{table}_{columns}` (contoh: `idx_students_school_id`)
- **Unique Constraint:** `uq_{table}_{columns}` (contoh: `uq_parent_profiles_school_email`)

### Kolom Standar
Semua tabel memiliki (Laravel conventions):
- `id` : BIGINT UNSIGNED (Primary Key, Auto Increment) atau CHAR(36) untuk UUID
- `created_at` : TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
- `updated_at` : TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

### ID Strategy
**Option 1: Auto-Increment (Recommended for MySQL)**
- `id` : BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
- Lebih performa untuk indexing & joins
- Native MySQL support

**Option 2: UUID (Alternative)**
- `id` : CHAR(36) PRIMARY KEY
- Better untuk distributed systems
- Laravel UUID support dengan package

**Pilihan:** Gunakan **BIGINT UNSIGNED** untuk performa optimal di MySQL

---

## 🔑 Skema Inti (Core Schema)

### Tabel: `users` (Laravel Auth)
**Deskripsi:** Tabel autentikasi dasar (Laravel default users table)

| Kolom | Tipe | Constraint | Deskripsi |
|--------|------|-------------|-------------|
| `id` | BIGINT UNSIGNED | PK, AUTO_INCREMENT | ID pengguna |
| `name` | VARCHAR(255) | NOT NULL | Nama lengkap |
| `email` | VARCHAR(255) | UNIQUE, NOT NULL | Alamat email |
| `email_verified_at` | TIMESTAMP | NULL | Waktu verifikasi email |
| `password` | VARCHAR(255) | NOT NULL | Password terenkripsi (bcrypt) |
| `phone` | VARCHAR(20) | NULL | Nomor telepon |
| `remember_token` | VARCHAR(100) | NULL | Token remember me |
| `created_at` | TIMESTAMP | NULL | Waktu pembuatan akun |
| `updated_at` | TIMESTAMP | NULL | Waktu update terakhir |

**Index:**
- `PRIMARY KEY` (`id`)
- `UNIQUE KEY` (`email`)

**Catatan:**
- Dikelola oleh Laravel Authentication
- Extended oleh tabel `teachers`, `parent_profiles`
- Password hashing menggunakan bcrypt via Laravel

---

### Tabel: `user_profiles`
**Deskripsi:** Profil extended untuk semua pengguna (data publik)

| Kolom | Tipe | Constraint | Deskripsi |
|--------|------|-------------|-------------|
| `id` | BIGINT UNSIGNED | PK, AUTO_INCREMENT | ID profil |
| `user_id` | BIGINT UNSIGNED | FK → users.id, UNIQUE, NOT NULL | Referensi ke user |
| `full_name` | VARCHAR(255) | NOT NULL | Nama lengkap |
| `avatar_url` | TEXT | NULL | URL gambar avatar |
| `bio` | TEXT | NULL | Biografi singkat |
| `created_at` | TIMESTAMP | NULL | Waktu dibuat |
| `updated_at` | TIMESTAMP | NULL | Waktu diupdate |

**Foreign Key:**
- `FOREIGN KEY` (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE

**Index:**
- `PRIMARY KEY` (`id`)
- `UNIQUE KEY` (`user_id`)
- `INDEX` `idx_user_profiles_user_id` (`user_id`)

---

## 🏫 Skema Multi-Tenant

### Tabel: `schools`
**Deskripsi:** Entitas sekolah/institusi (root multi-tenant)

| Kolom | Tipe | Constraint | Deskripsi |
|--------|------|-------------|-------------|
| `id` | BIGINT UNSIGNED | PK, AUTO_INCREMENT | ID sekolah |
| `name` | VARCHAR(255) | NOT NULL | Nama sekolah |
| `npsn` | VARCHAR(20) | UNIQUE, NULL | Nomor Pokok Sekolah Nasional |
| `address` | TEXT | NULL | Alamat lengkap |
| `phone` | VARCHAR(20) | NULL | Telepon kontak |
| `email` | VARCHAR(255) | NULL | Email kontak |
| `logo_url` | TEXT | NULL | URL logo sekolah |
| `subscription_plan` | ENUM | NOT NULL, DEFAULT 'free' | Tipe paket langganan |
| `student_limit` | INTEGER | NOT NULL, DEFAULT 20 | Batas maksimal siswa |
| `subscription_expires_at` | TIMESTAMP | NULL | Waktu kadaluarsa paket Pro |
| `is_active` | BOOLEAN | NOT NULL, DEFAULT true | Status aktif |
| `created_at` | TIMESTAMP | NOT NULL | Waktu dibuat |
| `updated_at` | TIMESTAMP | NOT NULL | Waktu diupdate |

**Enum:**
- `subscription_plan`: 'free', 'pro'

**Index:**
- `idx_schools_npsn` (UNIQUE, WHERE npsn IS NOT NULL)
- `idx_schools_subscription_plan`

**Constraint:**
- CHECK: `student_limit >= 0`
- CHECK: `subscription_plan = 'free' AND student_limit <= 20 OR subscription_plan = 'pro'`

---

### Tabel: `school_members`
**Deskripsi:** Tabel junction untuk relasi user-school-role (multi-tenancy)

| Kolom | Tipe | Constraint | Deskripsi |
|--------|------|-------------|-------------|
| `id` | UUID | PK, NOT NULL | ID anggota |
| `school_id` | UUID | FK → schools.id, NOT NULL | Referensi sekolah |
| `user_id` | UUID | FK → users.id, NOT NULL | Referensi user |
| `role` | ENUM | NOT NULL | Peran di sekolah |
| `is_active` | BOOLEAN | NOT NULL, DEFAULT true | Status aktif |
| `joined_at` | TIMESTAMP | NOT NULL, DEFAULT now() | Waktu bergabung |
| `created_at` | TIMESTAMP | NOT NULL | Waktu dibuat |

**Enum:**
- `role`: 'headmaster', 'teacher', 'parent'

**Foreign Key:**
- `school_id` → `schools.id` ON DELETE CASCADE
- `user_id` → `users.id` ON DELETE CASCADE

**Index:**
- `idx_school_members_school_id`
- `idx_school_members_user_id`
- `idx_school_members_role`

**Unique Constraint:**
- `uq_school_members_school_user_role` (school_id, user_id, role)

**Catatan:**
- 1 User bisa menjadi anggota dari multiple sekolah dengan role berbeda
- Contoh: Bu Ani = Teacher di School A, Parent di School B

---

### Tabel: `teachers`
**Deskripsi:** Profil extended untuk user dengan role teacher

| Kolom | Tipe | Constraint | Deskripsi |
|--------|------|-------------|-------------|
| `id` | UUID | PK, NOT NULL | ID Guru |
| `user_id` | UUID | FK → users.id, UNIQUE, NOT NULL | Referensi user |
| `school_id` | UUID | FK → schools.id, NOT NULL | Referensi sekolah |
| `employee_id` | VARCHAR(50) | NULL | Nomor pegawai |
| `specialization` | VARCHAR(255) | NULL | Spesialisasi mengajar |
| `bio` | TEXT | NULL | Biografi |
| `created_at` | TIMESTAMP | NOT NULL | Waktu dibuat |
| `updated_at` | TIMESTAMP | NOT NULL | Waktu diupdate |

**Foreign Key:**
- `user_id` → `users.id` ON DELETE CASCADE
- `school_id` → `schools.id` ON DELETE CASCADE

**Index:**
- `idx_teachers_user_id` (UNIQUE)
- `idx_teachers_school_id`

---

### Tabel: `classes`
**Deskripsi:** Entitas kelas (Kelas A, Kelas B, etc)

| Kolom | Tipe | Constraint | Deskripsi |
|--------|------|-------------|-------------|
| `id` | UUID | PK, NOT NULL | ID Kelas |
| `school_id` | UUID | FK → schools.id, NOT NULL | Referensi sekolah |
| `homeroom_teacher_id` | UUID | FK → teachers.id, NULL | Wali kelas |
| `name` | VARCHAR(100) | NOT NULL | Nama kelas |
| `level` | VARCHAR(50) | NULL | Tingkat (TK A, TK B, KB) |
| `capacity` | INTEGER | NULL | Kapasitas maksimal |
| `academic_year` | VARCHAR(20) | NULL | Tahun ajaran |
| `created_at` | TIMESTAMP | NOT NULL | Waktu dibuat |
| `updated_at` | TIMESTAMP | NOT NULL | Waktu diupdate |

**Foreign Key:**
- `school_id` → `schools.id` ON DELETE CASCADE
- `homeroom_teacher_id` → `teachers.id` ON DELETE SET NULL

**Index:**
- `idx_classes_school_id`
- `idx_classes_homeroom_teacher_id`

**Unique Constraint:**
- `uq_classes_school_name` (school_id, name)

**Catatan:**
- Many-to-One: 1 Teacher bisa menangani multiple Kelas
- homeroom_teacher_id bersifat nullable (kelas bisa ada tanpa guru)

---

### Tabel: `parent_profiles`
**Deskripsi:** Entitas profil orang tua (independen dari siswa - v3.0)

| Kolom | Tipe | Constraint | Deskripsi |
|--------|------|-------------|-------------|
| `id` | UUID | PK, NOT NULL | ID profil orang tua |
| `school_id` | UUID | FK → schools.id, NOT NULL | Referensi sekolah |
| `user_id` | UUID | FK → users.id, NOT NULL | Referensi user |
| `email` | VARCHAR(255) | NOT NULL | Email orang tua (untuk login) |
| `father_name` | VARCHAR(255) | NULL | Nama ayah |
| `mother_name` | VARCHAR(255) | NULL | Nama ibu |
| `phone` | VARCHAR(20) | NOT NULL | Telepon kontak |
| `father_occupation` | VARCHAR(255) | NULL | Pekerjaan ayah |
| `mother_occupation` | VARCHAR(255) | NULL | Pekerjaan ibu |
| `address` | TEXT | NULL | Alamat rumah |
| `created_at` | TIMESTAMP | NOT NULL | Waktu dibuat |
| `updated_at` | TIMESTAMP | NOT NULL | Waktu diupdate |

**Foreign Key:**
- `school_id` → `schools.id` ON DELETE CASCADE
- `user_id` → `users.id` ON DELETE CASCADE

**Index:**
- `idx_parent_profiles_school_id`
- `idx_parent_profiles_user_id`
- `idx_parent_profiles_email`

**Unique Constraint:**
- `uq_parent_profiles_school_email` (school_id, email)

**Catatan:**
- Email must be unique per school (email yang sama bisa ada di sekolah berbeda)
- Can exist without students (skenario waiting list)

---

### Tabel: `students`
**Deskripsi:** Entitas siswa/anak (usia 0-6, TANPA login)

| Kolom | Tipe | Constraint | Deskripsi |
|--------|------|-------------|-------------|
| `id` | UUID | PK, NOT NULL | ID Siswa |
| `school_id` | UUID | FK → schools.id, NOT NULL | Referensi sekolah |
| `class_id` | UUID | FK → classes.id, NULL | Referensi kelas |
| `parent_profile_id` | UUID | FK → parent_profiles.id, NOT NULL | Referensi orang tua |
| `name` | VARCHAR(255) | NOT NULL | Nama lengkap |
| `nisn` | VARCHAR(20) | NULL | Nomor induk siswa nasional |
| `birth_date` | DATE | NOT NULL | Tanggal lahir |
| `Jenis kelamin` | ENUM | NOT NULL | Jenis kelamin |
| `address` | TEXT | NULL | Alamat rumah |
| `photo_url` | TEXT | NULL | Student URL foto |
| `enrollment_date` | DATE | NOT NULL | Tanggal pendaftaran |
| `status` | ENUM | NOT NULL, DEFAULT 'active' | Status siswa |
| `created_at` | TIMESTAMP | NOT NULL | Waktu dibuat |
| `updated_at` | TIMESTAMP | NOT NULL | Waktu diupdate |

**Enum:**
- `Jenis kelamin`: 'male', 'female'
- `status`: 'active', 'graduated', 'transferred'

**Foreign Key:**
- `school_id` → `schools.id` ON DELETE CASCADE
- `class_id` → `classes.id` ON DELETE SET NULL
- `parent_profile_id` → `parent_profiles.id` ON DELETE RESTRICT

**Index:**
- `idx_students_school_id`
- `idx_students_class_id`
- `idx_students_parent_profile_id`
- `idx_students_status`

**Catatan:**
- Must be linked to parent_profile (RESTRICT deletion jika siswa ada)
- NO student_user_id (v3.0 - Role Student dihapus)

---

### Tabel: `attendance`
**Deskripsi:** Catatan absensi harian

| Kolom | Tipe | Constraint | Deskripsi |
|--------|------|-------------|-------------|
| `id` | UUID | PK, NOT NULL | Attendance ID |
| `school_id` | UUID | FK → schools.id, NOT NULL | Referensi sekolah |
| `student_id` | UUID | FK → students.id, NOT NULL | Referensi siswa |
| `class_id` | UUID | FK → classes.id, NOT NULL | Referensi kelas |
| `teacher_id` | UUID | FK → teachers.id, NOT NULL | Guru yang menginput |
| `date` | DATE | NOT NULL | Tanggal absensi |
| `status` | ENUM | NOT NULL | Status kehadiran |
| `notes` | TEXT | NULL | Catatan tambahan |
| `created_at` | TIMESTAMP | NOT NULL | Waktu dibuat |
| `updated_at` | TIMESTAMP | NOT NULL | Waktu diupdate |

**Enum:**
- `status`: 'present', 'sick', 'permission', 'absent'

**Foreign Key:**
- `school_id` → `schools.id` ON DELETE CASCADE
- `student_id` → `students.id` ON DELETE CASCADE
- `class_id` → `classes.id` ON DELETE CASCADE
- `teacher_id` → `teachers.id` ON DELETE RESTRICT

**Index:**
- `idx_attendance_school_id`
- `idx_attendance_student_id`
- `idx_attendance_date`
- `idx_attendance_class_id`

**Unique Constraint:**
- `uq_attendance_student_date` (student_id, date)

---

### Tabel: `assessments`
**Deskripsi:** Penilaian siswa/catatan nilai (gaya PAUD)

| Kolom | Tipe | Constraint | Deskripsi |
|--------|------|-------------|-------------|
| `id` | UUID | PK, NOT NULL | Assessment ID |
| `school_id` | UUID | FK → schools.id, NOT NULL | Referensi sekolah |
| `student_id` | UUID | FK → students.id, NOT NULL | Referensi siswa |
| `class_id` | UUID | FK → classes.id, NOT NULL | Referensi kelas |
| `teacher_id` | UUID | FK → teachers.id, NOT NULL | Guru yang menginput |
| `category` | VARCHAR(100) | NOT NULL | Kategori penilaian |
| `indicator` | VARCHAR(255) | NOT NULL | Indikator spesifik |
| `score` | ENUM | NOT NULL | PAUD score |
| `notes` | TEXT | NULL | Catatan guru |
| `assessment_date` | DATE | NOT NULL | Tanggal penilaian |
| `semester` | ENUM | NOT NULL | Semester |
| `created_at` | TIMESTAMP | NOT NULL | Waktu dibuat |
| `updated_at` | TIMESTAMP | NOT NULL | Waktu diupdate |

**Enum:**
- `score`: 'BB', 'MB', 'BSH', 'BSB' (Belum Berkembang, Mulai Berkembang, Berkembang Sesuai Harapan, Berkembang Sangat Baik)
- `semester`: '1', '2', '3', '4'

**Foreign Key:**
- `school_id` → `schools.id` ON DELETE CASCADE
- `student_id` → `students.id` ON DELETE CASCADE
- `class_id` → `classes.id` ON DELETE CASCADE
- `teacher_id` → `teachers.id` ON DELETE RESTRICT

**Index:**
- `idx_assessments_school_id`
- `idx_assessments_student_id`
- `idx_assessments_semester`

---

### Tabel: `finances`
**Deskripsi:** Catatan keuangan (SPP & Tabungan) - khusus Pro Plan

| Kolom | Tipe | Constraint | Deskripsi |
|--------|------|-------------|-------------|
| `id` | UUID | PK, NOT NULL | Finance ID |
| `school_id` | UUID | FK → schools.id, NOT NULL | Referensi sekolah |
| `student_id` | UUID | FK → students.id, NOT NULL | Referensi siswa |
| `type` | ENUM | NOT NULL | Tipe transaksi |
| `Jumlah` | DECIMAL(10,2) | NOT NULL | Jumlah |
| `description` | VARCHAR(255) | NULL | Deskripsi |
| `transaction_date` | DATE | NOT NULL | Tanggal transaksi |
| `payment_method` | ENUM | NULL | Metode pembayaran |
| `created_by` | UUID | FK → users.id, NOT NULL | User yang membuat |
| `created_at` | TIMESTAMP | NOT NULL | Waktu dibuat |

**Enum:**
- `type`: 'spp', 'tabungan'
- `payment_method`: 'cash', 'transfer'

**Foreign Key:**
- `school_id` → `schools.id` ON DELETE CASCADE
- `student_id` → `students.id` ON DELETE CASCADE
- `created_by` → `users.id` ON DELETE RESTRICT

**Index:**
- `idx_finances_school_id`
- `idx_finances_student_id`
- `idx_finances_transaction_date`
- `idx_finances_type`

**Constraint:**
- CHECK: `Jumlah >= 0`

---

## 📚 Content Management Schema

### Tabel: `mentors`
**Deskripsi:** Mentor/instruktur untuk webinar & kursus

| Kolom | Tipe | Constraint | Deskripsi |
|--------|------|-------------|-------------|
| `id` | UUID | PK, NOT NULL | Mentor ID |
| `name` | VARCHAR(255) | NOT NULL | Nama lengkap |
| `title` | VARCHAR(255) | NULL | Jabatan profesional |
| `bio` | TEXT | NULL | Biografi |
| `photo_url` | TEXT | NULL | Profile URL foto |
| `expertise` | VARCHAR(255) | NULL | Bidang keahlian |
| `social_media` | JSONB | NULL | Link media sosial |
| `is_active` | BOOLEAN | NOT NULL, DEFAULT true | Status aktif |
| `created_at` | TIMESTAMP | NOT NULL | Waktu dibuat |
| `updated_at` | TIMESTAMP | NOT NULL | Waktu diupdate |

**Index:**
- `idx_mentors_is_active`

---

### Tabel: `categories`
**Deskripsi:** Kategori untuk kursus, produk, artikel

| Kolom | Tipe | Constraint | Deskripsi |
|--------|------|-------------|-------------|
| `id` | UUID | PK, NOT NULL | Category ID |
| `name` | VARCHAR(100) | NOT NULL | Category name |
| `slug` | VARCHAR(100) | UNIQUE, NOT NULL | URL-friendly slug |
| `description` | TEXT | NULL | Deskripsi |
| `type` | ENUM | NOT NULL | Tipe konten |
| `created_at` | TIMESTAMP | NOT NULL | Waktu dibuat |

**Enum:**
- `type`: 'course', 'product', 'article'

**Index:**
- `idx_categories_slug` (UNIQUE)
- `idx_categories_type`

---

### Tabel: `webinars`
**Deskripsi:** Event webinar live

| Kolom | Tipe | Constraint | Deskripsi |
|--------|------|-------------|-------------|
| `id` | UUID | PK, NOT NULL | Webinar ID |
| `mentor_id` | UUID | FK → mentors.id, NOT NULL | Referensi mentor |
| `title` | VARCHAR(255) | NOT NULL | Judul webinar |
| `slug` | VARCHAR(255) | UNIQUE, NOT NULL | Slug URL |
| `description` | TEXT | NULL | Deskripsi lengkap |
| `thumbnail_url` | TEXT | NULL | URL gambar thumbnail |
| `Harga` | DECIMAL(10,2) | NOT NULL | Harga |
| `original_Harga` | DECIMAL(10,2) | NULL | Original Harga (untuk diskon) |
| `zoom_link` | TEXT | NOT NULL | URL meeting Zoom |
| `zoom_meeting_id` | VARCHAR(50) | NULL | ID meeting Zoom |
| `zoom_passcode` | VARCHAR(50) | NULL | Passcode Zoom |
| `scheduled_at` | TIMESTAMP | NOT NULL | Waktu jadwal |
| `Durasi_minutes` | INTEGER | NULL | Durasi in minutes |
| `max_participants` | INTEGER | NULL | Maksimal peserta |
| `is_active` | BOOLEAN | NOT NULL, DEFAULT true | Status aktif |
| `created_at` | TIMESTAMP | NOT NULL | Waktu dibuat |
| `updated_at` | TIMESTAMP | NOT NULL | Waktu diupdate |

**Foreign Key:**
- `mentor_id` → `mentors.id` ON DELETE RESTRICT

**Index:**
- `idx_webinars_slug` (UNIQUE)
- `idx_webinars_mentor_id`
- `idx_webinars_scheduled_at`
- `idx_webinars_is_active`

**Constraint:**
- CHECK: `Harga >= 0`
- CHECK: `original_Harga IS NULL OR original_Harga >= Harga`

---

### Tabel: `courses`
**Deskripsi:** Kursus online (LMS)

| Kolom | Tipe | Constraint | Deskripsi |
|--------|------|-------------|-------------|
| `id` | UUID | PK, NOT NULL | Course ID |
| `mentor_id` | UUID | FK → mentors.id, NOT NULL | Referensi mentor |
| `category_id` | UUID | FK → categories.id, NULL | Referensi kategori |
| `title` | VARCHAR(255) | NOT NULL | Judul kursus |
| `slug` | VARCHAR(255) | UNIQUE, NOT NULL | Slug URL |
| `description` | TEXT | NULL | Deskripsi lengkap |
| `thumbnail_url` | TEXT | NULL | URL gambar thumbnail |
| `Harga` | DECIMAL(10,2) | NOT NULL | Harga |
| `original_Harga` | DECIMAL(10,2) | NULL | Original Harga |
| `Tingkat` | ENUM | NOT NULL | Difficulty Tingkat |
| `Durasi_hours` | INTEGER | NULL | Estimated Durasi |
| `is_published` | BOOLEAN | NOT NULL, DEFAULT false | Status publikasi |
| `created_at` | TIMESTAMP | NOT NULL | Waktu dibuat |
| `updated_at` | TIMESTAMP | NOT NULL | Waktu diupdate |

**Enum:**
- `Tingkat`: 'beginner', 'intermediate', 'advanced'

**Foreign Key:**
- `mentor_id` → `mentors.id` ON DELETE RESTRICT
- `category_id` → `categories.id` ON DELETE SET NULL

**Index:**
- `idx_courses_slug` (UNIQUE)
- `idx_courses_mentor_id`
- `idx_courses_category_id`
- `idx_courses_is_published`

**Constraint:**
- CHECK: `Harga >= 0`

---

### Tabel: `modules`
**Deskripsi:** Modul kursus (chapters)

| Kolom | Tipe | Constraint | Deskripsi |
|--------|------|-------------|-------------|
| `id` | UUID | PK, NOT NULL | Module ID |
| `course_id` | UUID | FK → courses.id, NOT NULL | Referensi kursus |
| `title` | VARCHAR(255) | NOT NULL | Judul modul |
| `description` | TEXT | NULL | Deskripsi modul |
| `order` | INTEGER | NOT NULL | Urutan tampilan |
| `created_at` | TIMESTAMP | NOT NULL | Waktu dibuat |
| `updated_at` | TIMESTAMP | NOT NULL | Waktu diupdate |

**Foreign Key:**
- `course_id` → `courses.id` ON DELETE CASCADE

**Index:**
- `idx_modules_course_id`
- `idx_modules_order`

**Unique Constraint:**
- `uq_modules_course_order` (course_id, order)

---

### Tabel: `lessons`
**Deskripsi:** Pelajaran dalam modul (video, PDF, quiz)

| Kolom | Tipe | Constraint | Deskripsi |
|--------|------|-------------|-------------|
| `id` | UUID | PK, NOT NULL | Lesson ID |
| `module_id` | UUID | FK → modules.id, NOT NULL | Referensi modul |
| `title` | VARCHAR(255) | NOT NULL | Judul pelajaran |
| `content_type` | ENUM | NOT NULL | Tipe konten |
| `content_url` | TEXT | NULL | URL konten |
| `Durasi_minutes` | INTEGER | NULL | Durasi |
| `order` | INTEGER | NOT NULL | Urutan tampilan |
| `is_preview` | BOOLEAN | NOT NULL, DEFAULT false | Preview gratis |
| `created_at` | TIMESTAMP | NOT NULL | Waktu dibuat |
| `updated_at` | TIMESTAMP | NOT NULL | Waktu diupdate |

**Enum:**
- `content_type`: 'video', 'pdf', 'quiz', 'text'

**Foreign Key:**
- `module_id` → `modules.id` ON DELETE CASCADE

**Index:**
- `idx_lessons_module_id`
- `idx_lessons_order`

**Unique Constraint:**
- `uq_lessons_module_order` (module_id, order)

---

### Tabel: `course_enrollments`
**Deskripsi:** Pendaftaran pengguna in courses

| Kolom | Tipe | Constraint | Deskripsi |
|--------|------|-------------|-------------|
| `id` | UUID | PK, NOT NULL | ID enrollment |
| `course_id` | UUID | FK → courses.id, NOT NULL | Referensi kursus |
| `user_id` | UUID | FK → users.id, NOT NULL | Referensi user |
| `enrolled_at` | TIMESTAMP | NOT NULL, DEFAULT now() | Waktu enrollment |
| `Progress_percentage` | INTEGER | NOT NULL, DEFAULT 0 | Progress (0-100) |
| `completed_at` | TIMESTAMP | NULL | Waktu penyelesaian |
| `certificate_url` | TEXT | NULL | URL sertifikat |
| `created_at` | TIMESTAMP | NOT NULL | Waktu dibuat |

**Foreign Key:**
- `course_id` → `courses.id` ON DELETE RESTRICT
- `user_id` → `users.id` ON DELETE CASCADE

**Index:**
- `idx_course_enrollments_course_id`
- `idx_course_enrollments_user_id`

**Unique Constraint:**
- `uq_course_enrollments_course_user` (course_id, user_id)

**Constraint:**
- CHECK: `Progress_percentage BETWEEN 0 AND 100`

---

### Tabel: `lesson_Progress`
**Deskripsi:** Pelacakan penyelesaian pelajaran per user

| Kolom | Tipe | Constraint | Deskripsi |
|--------|------|-------------|-------------|
| `id` | UUID | PK, NOT NULL | ID progress |
| `enrollment_id` | UUID | FK → course_enrollments.id, NOT NULL | Referensi enrollment |
| `lesson_id` | UUID | FK → lessons.id, NOT NULL | Referensi pelajaran |
| `is_completed` | BOOLEAN | NOT NULL, DEFAULT false | Status penyelesaian |
| `completed_at` | TIMESTAMP | NULL | Waktu penyelesaian |
| `created_at` | TIMESTAMP | NOT NULL | Waktu dibuat |

**Foreign Key:**
- `enrollment_id` → `course_enrollments.id` ON DELETE CASCADE
- `lesson_id` → `lessons.id` ON DELETE CASCADE

**Index:**
- `idx_lesson_Progress_enrollment_id`
- `idx_lesson_Progress_lesson_id`

**Unique Constraint:**
- `uq_lesson_Progress_enrollment_lesson` (enrollment_id, lesson_id)

---

### Tabel: `products`
**Deskripsi:** Produk digital (e-book, template, dll)

| Kolom | Tipe | Constraint | Deskripsi |
|--------|------|-------------|-------------|
| `id` | UUID | PK, NOT NULL | ID produk |
| `category_id` | UUID | FK → categories.id, NULL | Referensi kategori |
| `title` | VARCHAR(255) | NOT NULL | Judul produk |
| `slug` | VARCHAR(255) | UNIQUE, NOT NULL | Slug URL |
| `description` | TEXT | NULL | Deskripsi lengkap |
| `thumbnail_url` | TEXT | NULL | URL gambar thumbnail |
| `file_url` | TEXT | NOT NULL | URL file download |
| `file_type` | VARCHAR(50) | NULL | Tipe file (PDF, ZIP) |
| `file_size` | BIGINT | NULL | Ukuran file dalam byte |
| `Harga` | DECIMAL(10,2) | NOT NULL | Harga |
| `original_Harga` | DECIMAL(10,2) | NULL | Original Harga |
| `is_active` | BOOLEAN | NOT NULL, DEFAULT true | Status aktif |
| `created_at` | TIMESTAMP | NOT NULL | Waktu dibuat |
| `updated_at` | TIMESTAMP | NOT NULL | Waktu diupdate |

**Foreign Key:**
- `category_id` → `categories.id` ON DELETE SET NULL

**Index:**
- `idx_products_slug` (UNIQUE)
- `idx_products_category_id`
- `idx_products_is_active`

**Constraint:**
- CHECK: `Harga >= 0`

---

### Tabel: `articles`
**Deskripsi:** Artikel blog (konten gratis untuk SEO)

| Kolom | Tipe | Constraint | Deskripsi |
|--------|------|-------------|-------------|
| `id` | UUID | PK, NOT NULL | ID artikel |
| `category_id` | UUID | FK → categories.id, NULL | Referensi kategori |
| `author_id` | UUID | FK → users.id, NOT NULL | Referensi penulis |
| `title` | VARCHAR(255) | NOT NULL | Judul artikel |
| `slug` | VARCHAR(255) | UNIQUE, NOT NULL | Slug URL |
| `content` | TEXT | NOT NULL | Konten artikel |
| `excerpt` | VARCHAR(500) | NULL | Kutipan singkat |
| `featured_image_url` | TEXT | NULL | URL gambar unggulan |
| `tags` | JSONB | NULL | Array tag |
| `reading_time_minutes` | INTEGER | NULL | Waktu baca |
| `view_count` | INTEGER | NOT NULL, DEFAULT 0 | Penghitung tampilan |
| `is_featured` | BOOLEAN | NOT NULL, DEFAULT false | Status unggulan |
| `is_published` | BOOLEAN | NOT NULL, DEFAULT false | Status publikasi |
| `published_at` | TIMESTAMP | NULL | Waktu publikasi |
| `created_at` | TIMESTAMP | NOT NULL | Waktu dibuat |
| `updated_at` | TIMESTAMP | NOT NULL | Waktu diupdate |

**Foreign Key:**
- `category_id` → `categories.id` ON DELETE SET NULL
- `author_id` → `users.id` ON DELETE RESTRICT

**Index:**
- `idx_articles_slug` (UNIQUE)
- `idx_articles_category_id`
- `idx_articles_author_id`
- `idx_articles_is_published`
- `idx_articles_published_at`

**Full-text Search:**
- `idx_articles_fts` ON (title, content) USING GIN

---

### Tabel: `testimonials`
**Deskripsi:** Testimoni pengguna/ulasan

| Kolom | Tipe | Constraint | Deskripsi |
|--------|------|-------------|-------------|
| `id` | UUID | PK, NOT NULL | ID testimoni |
| `user_id` | UUID | FK → users.id, NULL | Referensi user |
| `name` | VARCHAR(255) | NOT NULL | Nama tampilan |
| `title` | VARCHAR(255) | NULL | Jabatan profesional |
| `content` | TEXT | NOT NULL | Konten testimoni |
| `Rating` | INTEGER | NOT NULL | Rating (1-5) |
| `photo_url` | TEXT | NULL | URL foto |
| `is_featured` | BOOLEAN | NOT NULL, DEFAULT false | Status unggulan |
| `is_approved` | BOOLEAN | NOT NULL, DEFAULT false | Status persetujuan |
| `created_at` | TIMESTAMP | NOT NULL | Waktu dibuat |

**Foreign Key:**
- `user_id` → `users.id` ON DELETE SET NULL

**Index:**
- `idx_testimonials_user_id`
- `idx_testimonials_is_featured`
- `idx_testimonials_is_approved`

**Constraint:**
- CHECK: `Rating BETWEEN 1 AND 5`

---

## 💰 Commerce Schema

### Tabel: `orders`
**Deskripsi:** Order pembelian/transaksi

| Kolom | Tipe | Constraint | Deskripsi |
|--------|------|-------------|-------------|
| `id` | UUID | PK, NOT NULL | ID order |
| `user_id` | UUID | FK → users.id, NOT NULL | Referensi user |
| `order_number` | VARCHAR(50) | UNIQUE, NOT NULL | Nomor order |
| `total_Jumlah` | DECIMAL(10,2) | NOT NULL | Total Jumlah |
| `discount_Jumlah` | DECIMAL(10,2) | NOT NULL, DEFAULT 0 | Discount Jumlah |
| `final_Jumlah` | DECIMAL(10,2) | NOT NULL | Final Jumlah |
| `promo_code` | VARCHAR(50) | NULL | Applied Kode promo |
| `status` | ENUM | NOT NULL, DEFAULT 'pending' | Status order |
| `payment_method` | VARCHAR(50) | NULL | Metode pembayaran |
| `payment_url` | TEXT | NULL | URL snap Midtrans |
| `paid_at` | TIMESTAMP | NULL | Waktu pembayaran |
| `created_at` | TIMESTAMP | NOT NULL | Waktu dibuat |
| `updated_at` | TIMESTAMP | NOT NULL | Waktu diupdate |

**Enum:**
- `status`: 'pending', 'paid', 'failed', 'cancelled'

**Foreign Key:**
- `user_id` → `users.id` ON DELETE RESTRICT

**Index:**
- `idx_orders_user_id`
- `idx_orders_order_number` (UNIQUE)
- `idx_orders_status`
- `idx_orders_created_at`

**Constraint:**
- CHECK: `total_Jumlah >= 0`
- CHECK: `discount_Jumlah >= 0`
- CHECK: `final_Jumlah >= 0`
- CHECK: `final_Jumlah = total_Jumlah - discount_Jumlah`

---

### Tabel: `order_items`
**Deskripsi:** Item dalam order (relasi polimorfik)

| Kolom | Tipe | Constraint | Deskripsi |
|--------|------|-------------|-------------|
| `id` | UUID | PK, NOT NULL | ID item order |
| `order_id` | UUID | FK → orders.id, NOT NULL | Referensi order |
| `item_type` | ENUM | NOT NULL | Tipe item |
| `item_id` | UUID | NOT NULL | Referensi item |
| `item_title` | VARCHAR(255) | NOT NULL | Judul item (snapshot) |
| `item_Harga` | DECIMAL(10,2) | NOT NULL | Item Harga (snapshot) |
| `Kuantitas` | INTEGER | NOT NULL, DEFAULT 1 | Kuantitas |
| `Subtotal` | DECIMAL(10,2) | NOT NULL | Subtotal |
| `created_at` | TIMESTAMP | NOT NULL | Waktu dibuat |

**Enum:**
- `item_type`: 'webinar', 'course', 'product'

**Foreign Key:**
- `order_id` → `orders.id` ON DELETE CASCADE

**Index:**
- `idx_order_items_order_id`
- `idx_order_items_item_type_id` (item_type, item_id)

**Constraint:**
- CHECK: `Kuantitas > 0`
- CHECK: `item_Harga >= 0`
- CHECK: `Subtotal = item_Harga * Kuantitas`

**Catatan:**
- Polimorfik: item_id mereferensi webinars.id | courses.id | products.id berdasarkan item_type
- Snapshot: item_title and item_Harga disimpan untuk mempertahankan data jika sumber dihapus/diupdate

---

### Tabel: `promo_codes`
**Deskripsi:** Kode diskon/promo

| Kolom | Tipe | Constraint | Deskripsi |
|--------|------|-------------|-------------|
| `id` | UUID | PK, NOT NULL | ID kode promo |
| `code` | VARCHAR(50) | UNIQUE, NOT NULL | Kode promo |
| `discount_type` | ENUM | NOT NULL | Tipe diskon |
| `discount_value` | DECIMAL(10,2) | NOT NULL | Nilai diskon |
| `min_purchase` | DECIMAL(10,2) | NULL | Pembelian minimum |
| `max_discount` | DECIMAL(10,2) | NULL | Diskon maksimal (untuk %) |
| `usage_limit` | INTEGER | NULL | Batas penggunaan |
| `used_count` | INTEGER | NOT NULL, DEFAULT 0 | Jumlah penggunaan |
| `valid_from` | TIMESTAMP | NOT NULL | Berlaku dari |
| `valid_until` | TIMESTAMP | NOT NULL | Berlaku sampai |
| `is_active` | BOOLEAN | NOT NULL, DEFAULT true | Status aktif |
| `created_at` | TIMESTAMP | NOT NULL | Waktu dibuat |

**Enum:**
- `discount_type`: 'percentage', 'fixed'

**Index:**
- `idx_promo_codes_code` (UNIQUE)
- `idx_promo_codes_is_active`

**Constraint:**
- CHECK: `discount_value > 0`
- CHECK: `discount_type = 'percentage' AND discount_value <= 100 OR discount_type = 'fixed'`
- CHECK: `usage_limit IS NULL OR usage_limit > 0`
- CHECK: `valid_until > valid_from`

---

### Tabel: `site_settings`
**Deskripsi:** Pengaturan platform global

| Kolom | Tipe | Constraint | Deskripsi |
|--------|------|-------------|-------------|
| `id` | UUID | PK, NOT NULL | ID pengaturan |
| `key` | VARCHAR(100) | UNIQUE, NOT NULL | Key pengaturan |
| `value` | TEXT | NULL | Value pengaturan (JSON) |
| `description` | VARCHAR(255) | NULL | Deskripsi |
| `updated_at` | TIMESTAMP | NOT NULL | Waktu diupdate |

**Index:**
- `idx_site_settings_key` (UNIQUE)

**Contoh:**
- key: 'site_logo', value: 'https://...'
- key: 'contact_email', value: 'info@paudceria.com'
- key: 'social_media', value: '{"facebook": "...", "instagram": "..."}'

---

## 🔍 Indexes & Constraints

### Index Performa

**Multi-Tenant Queries:**
```sql
CREATE INDEX idx_students_school_class ON students(school_id, class_id);
CREATE INDEX idx_attendance_school_date ON attendance(school_id, date);
CREATE INDEX idx_assessments_student_semester ON assessments(student_id, semester);
CREATE INDEX idx_classes_school_homeroom ON classes(school_id, homeroom_teacher_id);
```

**Commerce Queries:**
```sql
CREATE INDEX idx_orders_user_status ON orders(user_id, status);
CREATE INDEX idx_course_enrollments_user_Progress ON course_enrollments(user_id, Progress_percentage);
```

**Content Queries:**
```sql
CREATE INDEX idx_courses_published_category ON courses(is_published, category_id);
CREATE INDEX idx_articles_published_featured ON articles(is_published, is_featured);
```

### Ringkasan Unique Constraint

| Table | Constraint | Columns |
|-------|------------|---------|
| `users` | `uq_users_email` | `email` |
| `schools` | `uq_schools_npsn` | `npsn` |
| `school_members` | `uq_school_members_school_user_role` | `school_id, user_id, role` |
| `teachers` | `uq_teachers_user_id` | `user_id` |
| `classes` | `uq_classes_school_name` | `school_id, name` |
| `parent_profiles` | `uq_parent_profiles_school_email` | `school_id, email` |
| `attendance` | `uq_attendance_student_date` | `student_id, date` |
| `course_enrollments` | `uq_course_enrollments_course_user` | `course_id, user_id` |
| `lesson_Progress` | `uq_lesson_Progress_enrollment_lesson` | `enrollment_id, lesson_id` |
| `modules` | `uq_modules_course_order` | `course_id, order` |
| `lessons` | `uq_lessons_module_order` | `module_id, order` |
| `orders` | `uq_orders_number` | `order_number` |
| `promo_codes` | `uq_promo_codes_code` | `code` |
| `site_settings` | `uq_site_settings_key` | `key` |

---

## 🗺️ Peta Relasi ERD

### Domain Multi-Tenant

```
users (1) ──────< school_members (M) >────── (1) schools
                       │
                       └─ role: headmaster | teacher | parent

users (1) ──── (1) teachers
users (1) ──── (1) parent_profiles (per school)

schools (1) ────< classes (M)
schools (1) ────< students (M)
schools (1) ────< parent_profiles (M)
schools (1) ────< attendance (M)
schools (1) ────< assessments (M)
schools (1) ────< finances (M)

teachers (1) ────< classes (M) [FK: homeroom_teacher_id]
   └─ Many-to-One: 1 Teacher bisa menangani multiple Kelas

classes (M) >──── (0..1) teachers [FK: homeroom_teacher_id, nullable]
   └─ Opsional: 1 Class can have 0 or 1 Wali kelas

parent_profiles (1) ────< students (M) [FK: parent_profile_id, required]
   └─ Wajib: Setiap Siswa harus memiliki Parent

students (M) >──── (0..1) classes [FK: class_id, nullable]
students (1) ────< attendance (M)
students (1) ────< assessments (M)
students (1) ────< finances (M)
```

### Domain Manajemen Konten

```
mentors (1) ────< webinars (M)
mentors (1) ────< courses (M)

categories (1) ────< courses (M)
categories (1) ────< products (M)
categories (1) ────< articles (M)

courses (1) ────< modules (M)
modules (1) ────< lessons (M)

users (1) ────< course_enrollments (M) >──── (1) courses
course_enrollments (1) ────< lesson_Progress (M) >──── (1) lessons

users (1) ────< articles (M) [as author]
users (0..1) ────< testimonials (M)
```

### Domain Perdagangan

```
users (1) ────< orders (M)
orders (1) ────< order_items (M)

order_items (M) >──── (1) webinars | courses | products [polymorphic]
   └─ Polimorfik: item_type + item_id

promo_codes ← referenced by orders.promo_code (no FK)
```

### Lintas Domain

```
users → school_members → schools [Multi-Tenant Access]
users → teachers [Profil extended]
users → parent_profiles [Profil extended per School]
users → orders [Commerce]
users → course_enrollments [Learning]
users → articles [Content Creation]
```

---

## 📊 Statistik Database

**Total Tabel:** 25

**By Domain:**
- Core: 2 tables (users, user_profiles)
- Multi-Tenant: 9 tables (schools, school_members, teachers, classes, parent_profiles, students, attendance, assessments, finances)
- Content: 11 tables (mentors, categories, webinars, courses, modules, lessons, course_enrollments, lesson_Progress, products, articles, testimonials)
- Commerce: 3 tables (orders, order_items, promo_codes, site_settings)

**Relationship Types:**
- One-to-One: 3 (users→teachers, users→parent_profiles per school, users→user_profiles)
- One-to-Many: 45+
- Many-to-Many: 2 (via Tabel junctions: school_members, lesson_Progress)
- Polimorfik: 1 (order_items)

**Index:**
- Primary Keys: 25
- Foreign Keys: 50+
- Unique Constraints: 14
- Index Performa: 20+
- Full-text Search: 1

---

# CLASS DIAGRAM
## Platform Paud Pedia - Entity Relationship & Structure

**Tech Stack:**
- Backend: Laravel 12 (REST API)
- Database: MySQL 8.0
- Admin Panel: Laravel Filament
- Frontend: Next.js/Nuxt.js (Public) + React/Vue+Vite (SIAKAD)

**Tujuan:** Blueprint untuk class diagram - Entity definitions, attributes, dan relationships

**Cakupan:**
- ✅ Entity/Class definitions
- ✅ Attributes per entity
- ✅ Relationships & Cardinality
- ✅ Primary Keys & Foreign Keys

> **Catatan:** Untuk skema database detail dengan tipe data MySQL, lihat [ERD.md](./ERD.md)

---

## 📋 Daftar Isi

1. [Core Entities](#-core-entities)
   - [1. User (Auth)](#1-user-auth)
2. [Multi-Tenant Entities](#-multi-tenant-entities)
   - [2. School](#2-school)
   - [3. SchoolMember](#3-schoolmember)
   - [4. Teacher](#4-teacher)
   - [5. Class](#5-class)
   - [6. ParentProfile](#6-parentprofile)
   - [7. Student](#7-student)
   - [8. Attendance](#8-attendance)
   - [9. Assessment](#9-assessment)
   - [10. Finance](#10-finance-khusus-pro-plan)
3. [Content Management Entities](#-content-management-entities)
   - [11. Mentor](#11-mentor)
   - [12. Webinar](#12-webinar)
   - [13. Course](#13-course)
   - [14. Module](#14-module)
   - [15. Lesson](#15-lesson)
   - [16. CourseEnrollment](#16-courseenrollment)
   - [17. LessonProgress](#17-lessonprogress)
   - [18. Product](#18-product)
   - [19. Article](#19-article)
   - [20. Category](#20-category)
   - [21. Testimonial](#21-testimonial)
4. [Commerce Entities](#-commerce-entities)
   - [22. Order](#22-order)
   - [23. OrderItem](#23-orderitem)
   - [24. PromoCode](#24-promocode)
   - [25. SiteSettings](#25-sitesettings)
5. [Relationship Summary](#-relationship-summary)
   - [Domain Multi-Tenant](#domain-multi-tenant)
   - [Domain Manajemen Konten](#domain-manajemen-konten)
   - [Domain Perdagangan](#domain-perdagangan)
   - [Lintas Domain Relationships](#lintas-domain-relationships)
6. [Key Design Patterns](#-key-design-patterns)
7. [Entity Count Summary](#-entity-count-summary)

---

## 🔑 Core Entities

### 1. User (Auth)
**Deskripsi:** Autentikasi dasar entity untuk semua user di platform

**Atribut:**
- `id` : UUID (PK)
- `email` : String (unique)
- `password` : String (hashed)
- `full_name` : String
- `phone` : String (optional)
- `avatar_url` : String (optional)
- `email_verified` : Boolean
- `created_at` : Timestamp
- `updated_at` : Timestamp

**Relasi:**
- Has Many → SchoolMember (multi-tenant roles)
- Has Many → Order (shopping transaksi)
- Has Many → CourseEnrollment (course access)
- Has One → ParentProfile (if role = parent)
- Has One → Teacher (if role = teacher)

---

## 🏫 Multi-Tenant Entities

### 2. School
**Deskripsi:** Sekolah/institution entity (multi-tenant)

**Atribut:**
- `id` : UUID (PK)
- `name` : String
- `npsn` : String (unique, optional)
- `address` : Text
- `phone` : String
- `email` : String
- `logo_url` : String (optional)
- `subscription_plan` : Enum ('free', 'pro')
- `student_limit` : Integer (20 for free, unlimited for pro)
- `teacher_limit` : Integer (5 for free, unlimited for pro)
- `subscription_expires_at` : Timestamp (nullable)
- `created_at` : Timestamp
- `updated_at` : Timestamp

**Relasi:**
- Has Many → SchoolMember (teachers, headmaster, parents)
- Has Many → Class (kelas-kelas di sekolah)
- Has Many → Student (siswa-siswa di sekolah)
- Has Many → ParentProfile (orang tua di sekolah)
- Has Many → Attendance (absensi records)
- Has Many → Assessment (nilai records)

**Catatan:**
- Free Plan: student_limit = 20, teacher_limit = 5
- Pro Plan: student_limit & teacher_limit = NULL atau unlimited

---

### 3. SchoolMember
**Deskripsi:** Tabel junction untuk user-school-role relationship (multi-tenant access)

**Atribut:**
- `id` : UUID (PK)
- `school_id` : UUID (FK → School)
- `user_id` : UUID (FK → User)
- `role` : Enum ('headmaster', 'teacher', 'parent')
- `is_active` : Boolean
- `joined_at` : Timestamp
- `created_at` : Timestamp

**Relasi:**
- Belongs To → School
- Belongs To → User

**Catatan:**
- 1 User bisa jadi member di multiple schools dengan role berbeda
- Contoh: Bu Ani = Teacher di School A, Parent di School B

---

### 4. Teacher
**Deskripsi:** Profil extended untuk user dengan role teacher

**Atribut:**
- `id` : UUID (PK)
- `user_id` : UUID (FK → User, unique)
- `school_id` : UUID (FK → School)
- `nip` : String (optional)
- `specialization` : String (optional)
- `bio` : Text (optional)
- `created_at` : Timestamp
- `updated_at` : Timestamp

**Relasi:**
- Belongs To → User
- Belongs To → School
- Has Many → Class (as Wali kelas - via Class.homeroom_teacher_id)

**Catatan:**
- 1 Teacher bisa handle multiple classes (Many-to-One relationship)

---

### 5. Class
**Deskripsi:** Kelas di sekolah (TK A, TK B, KB, dll)

**Atribut:**
- `id` : UUID (PK)
- `school_id` : UUID (FK → School)
- `homeroom_teacher_id` : UUID (FK → Teacher, nullable)
- `name` : String (ex: "Kelas A", "Kelas B")
- `Tingkat` : String (ex: "TK A", "TK B", "KB")
- `capacity` : Integer (optional)
- `academic_year` : String (optional, simplified for MVP)
- `created_at` : Timestamp
- `updated_at` : Timestamp

**Relasi:**
- Belongs To → School
- Belongs To → Teacher (Wali kelas - optional)
- Has Many → Student

**Catatan:**
- 1 Class = 1 Wali kelas (nullable)
- 1 Teacher bisa menangani multiple Kelas (Many-to-One)

---

### 6. ParentProfile
**Deskripsi:** Independent parent entity (v3.0 - not tied to student creation)

**Atribut:**
- `id` : UUID (PK)
- `school_id` : UUID (FK → School)
- `user_id` : UUID (FK → User, unique per school)
- `email` : String (unique per school)
- `father_name` : String (optional)
- `mother_name` : String (optional)
- `phone` : String
- `father_occupation` : String (optional)
- `mother_occupation` : String (optional)
- `address` : Text (optional)
- `created_at` : Timestamp
- `updated_at` : Timestamp

**Relasi:**
- Belongs To → School
- Belongs To → User
- Has Many → Student (children)

**Catatan:**
- Email must be unique per school
- Can exist without students (skenario waiting list)
- 1 Parent can have multiple children

---

### 7. Student
**Deskripsi:** Siswa/murid entity (PAUD target: ages 0-6)

**Atribut:**
- `id` : UUID (PK)
- `school_id` : UUID (FK → School)
- `class_id` : UUID (FK → Class, nullable)
- `parent_profile_id` : UUID (FK → ParentProfile, required)
- `name` : String
- `nisn` : String (optional)
- `birth_date` : Date
- `Jenis kelamin` : Enum ('male', 'female')
- `address` : Text
- `photo_url` : String (optional)
- `enrollment_date` : Date
- `status` : Enum ('active', 'graduated', 'transferred')
- `created_at` : Timestamp
- `updated_at` : Timestamp

**Relasi:**
- Belongs To → School
- Belongs To → Class (optional)
- Belongs To → ParentProfile (required)
- Has Many → Attendance
- Has Many → Assessment

**Catatan:**
- NO student login (v3.0 - removed Student role)
- Parent monitoring only (ages 0-6 cannot login)
- Must be linked to ParentProfile

---

### 8. Attendance
**Deskripsi:** Absensi/kehadiran siswa

**Atribut:**
- `id` : UUID (PK)
- `school_id` : UUID (FK → School)
- `student_id` : UUID (FK → Student)
- `class_id` : UUID (FK → Class)
- `teacher_id` : UUID (FK → Teacher)
- `date` : Date
- `status` : Enum ('present', 'sick', 'permission', 'absent')
- `notes` : Text (optional)
- `created_at` : Timestamp
- `updated_at` : Timestamp

**Relasi:**
- Belongs To → School
- Belongs To → Student
- Belongs To → Class
- Belongs To → Teacher (who input the attendance)

**Catatan:**
- Unique constraint: (student_id, date)
- Teacher can only input for their homeroom classes

---

### 9. Assessment
**Deskripsi:** Nilai/penilaian siswa

**Atribut:**
- `id` : UUID (PK)
- `school_id` : UUID (FK → School)
- `student_id` : UUID (FK → Student)
- `class_id` : UUID (FK → Class)
- `teacher_id` : UUID (FK → Teacher)
- `category` : String (ex: "Kognitif", "Motorik", "Sosial Emosional")
- `indicator` : String (ex: "Mengenal angka 1-10")
- `score` : String (ex: "BB", "MB", "BSH", "BSB")
- `notes` : Text (optional)
- `assessment_date` : Date
- `semester` : Enum ('1', '2', '3', '4')
- `created_at` : Timestamp
- `updated_at` : Timestamp

**Relasi:**
- Belongs To → School
- Belongs To → Student
- Belongs To → Class
- Belongs To → Teacher (who input the assessment)

**Catatan:**
- PAUD assessment: BB (Belum Berkembang), MB (Mulai Berkembang), BSH (Berkembang Sesuai Harapan), BSB (Berkembang Sangat Baik)

---

### 10. Finance (khusus Pro Plan)
**Deskripsi:** Keuangan siswa (SPP, Tabungan) - Pro Plan feature

**Atribut:**
- `id` : UUID (PK)
- `school_id` : UUID (FK → School)
- `student_id` : UUID (FK → Student)
- `type` : Enum ('spp', 'tabungan')
- `Jumlah` : Decimal
- `description` : String
- `transaction_date` : Date
- `payment_method` : Enum ('cash', 'transfer') (optional)
- `created_by` : UUID (FK → User)
- `created_at` : Timestamp

**Relasi:**
- Belongs To → School
- Belongs To → Student
- Belongs To → User (creator)

**Catatan:**
- Only accessible for Pro Plan schools
- SPP: monthly tuition payment
- Tabungan: savings tracking

---

## 📚 Content Management Entities

### 11. Mentor
**Deskripsi:** Mentor/instructor untuk webinar & course

**Atribut:**
- `id` : UUID (PK)
- `name` : String
- `title` : String (ex: "M.Psi, Psikolog")
- `bio` : Text
- `photo_url` : String
- `expertise` : String (ex: "Parenting Expert")
- `social_media` : JSON (optional - instagram, linkedin, etc)
- `is_active` : Boolean
- `created_at` : Timestamp
- `updated_at` : Timestamp

**Relasi:**
- Has Many → Webinar
- Has Many → Course

---

### 12. Webinar
**Deskripsi:** Live webinar/workshop event

**Atribut:**
- `id` : UUID (PK)
- `mentor_id` : UUID (FK → Mentor)
- `title` : String
- `slug` : String (unique)
- `description` : Text
- `thumbnail_url` : String
- `price` : Decimal
- `original_price` : Decimal (untuk diskon display)
- `zoom_link` : String
- `zoom_meeting_id` : String
- `zoom_passcode` : String (optional)
- `scheduled_at` : Timestamp
- `duration_minutes` : Integer
- `max_participants` : Integer (optional)
- `is_active` : Boolean
- `created_at` : Timestamp
- `updated_at` : Timestamp

**Relasi:**
- Belongs To → Mentor
- Has Many → Order (via OrderItem)

**Catatan:**
- Manual Zoom integration (no API)
- One-time event

---

### 13. Course
**Deskripsi:** Online course/kursus dengan modules & lessons

**Atribut:**
- `id` : UUID (PK)
- `mentor_id` : UUID (FK → Mentor)
- `category_id` : UUID (FK → Category, optional)
- `title` : String
- `slug` : String (unique)
- `description` : Text
- `thumbnail_url` : String
- `price` : Decimal
- `price_Harga` : Decimal (optional)
- `Tingkat` : Enum ('beginner', 'intermediate', 'advanced')
- `duration_hours` : Integer (estimated)
- `is_published` : Boolean
- `created_at` : Timestamp
- `updated_at` : Timestamp

**Relasi:**
- Belongs To → Mentor
- Belongs To → Category (optional)
- Has Many → Module
- Has Many → CourseEnrollment
- Has Many → Order (via OrderItem)

---

### 14. Module
**Deskripsi:** Module/bab dalam course

**Atribut:**
- `id` : UUID (PK)
- `course_id` : UUID (FK → Course)
- `title` : String
- `description` : Text (optional)
- `order` : Integer (sequence)
- `created_at` : Timestamp
- `updated_at` : Timestamp

**Relasi:**
- Belongs To → Course
- Has Many → Lesson

---

### 15. Lesson
**Deskripsi:** Lesson/materi dalam module (video, PDF, quiz)

**Atribut:**
- `id` : UUID (PK)
- `module_id` : UUID (FK → Module)
- `title` : String
- `content_type` : Enum ('video', 'pdf', 'quiz', 'text')
- `content_url` : String (YouTube embed URL or file URL)
- `duration_minutes` : Integer (optional)
- `order` : Integer (sequence)
- `is_preview` : Boolean (Preview gratis lesson)
- `created_at` : Timestamp
- `updated_at` : Timestamp

**Relasi:**
- Belongs To → Module
- Has Many → LessonProgress

---

### 16. CourseEnrollment
**Deskripsi:** Pendaftaran pengguna ke course (after payment)

**Atribut:**
- `id` : UUID (PK)
- `course_id` : UUID (FK → Course)
- `user_id` : UUID (FK → User)
- `enrolled_at` : Timestamp
- `progress_percentage` : Integer (0-100)
- `completed_at` : Timestamp (nullable)
- `certificate_url` : String (nullable)
- `created_at` : Timestamp

**Relasi:**
- Belongs To → Course
- Belongs To → User
- Has Many → LessonProgress

**Catatan:**
- Auto-created after payment success
- Progress tracked automatically

---

### 17. LessonProgress
**Deskripsi:** Tracking Progress per lesson per user

**Atribut:**
- `id` : UUID (PK)
- `enrollment_id` : UUID (FK → CourseEnrollment)
- `lesson_id` : UUID (FK → Lesson)
- `is_completed` : Boolean
- `completed_at` : Timestamp (nullable)
- `created_at` : Timestamp

**Relasi:**
- Belongs To → CourseEnrollment
- Belongs To → Lesson

**Catatan:**
- Unique constraint: (enrollment_id, lesson_id)
- When all lessons completed → trigger certificate generation

---

### 18. Product
**Deskripsi:** Produk digital (e-book, template, worksheet, etc)

**Atribut:**
- `id` : UUID (PK)
- `category_id` : UUID (FK → Category, optional)
- `title` : String
- `slug` : String (unique)
- `description` : Text
- `thumbnail_url` : String
- `file_url` : String (download link)
- `file_type` : String (ex: "PDF", "ZIP")
- `file_size` : Integer (in bytes)
- `Harga` : Decimal
- `original_Harga` : Decimal (optional)
- `is_active` : Boolean
- `created_at` : Timestamp
- `updated_at` : Timestamp

**Relasi:**
- Belongs To → Category (optional)
- Has Many → Order (via OrderItem)

**Catatan:**
- One-time download (no expiry)
- File stored securely (signed URL)

---

### 19. Article
**Deskripsi:** Blog/artikel (free content untuk SEO)

**Atribut:**
- `id` : UUID (PK)
- `category_id` : UUID (FK → Category, optional)
- `author_id` : UUID (FK → User)
- `title` : String
- `slug` : String (unique)
- `content` : Text (rich text/markdown)
- `excerpt` : String (meta description)
- `featured_image_url` : String
- `tags` : JSON (array of strings)
- `reading_time_minutes` : Integer (auto-calculated)
- `view_count` : Integer (default: 0)
- `is_featured` : Boolean
- `is_published` : Boolean
- `published_at` : Timestamp (nullable)
- `created_at` : Timestamp
- `updated_at` : Timestamp

**Relasi:**
- Belongs To → Category (optional)
- Belongs To → User (author - Moderator or Admin)

**Catatan:**
- Public access (no login required)
- SEO-friendly URLs

---

### 20. Category
**Deskripsi:** Category untuk Course, Product, Article

**Atribut:**
- `id` : UUID (PK)
- `name` : String
- `slug` : String (unique)
- `description` : Text (optional)
- `type` : Enum ('course', 'product', 'article')
- `created_at` : Timestamp

**Relasi:**
- Has Many → Course
- Has Many → Product
- Has Many → Article

---

### 21. Testimonial
**Deskripsi:** Testimonial/review dari user

**Atribut:**
- `id` : UUID (PK)
- `user_id` : UUID (FK → User, nullable)
- `name` : String (if user_id null)
- `title` : String (ex: "Kepala Sekolah TK Melati")
- `content` : Text
- `Rating` : Integer (1-5)
- `photo_url` : String (optional)
- `is_featured` : Boolean
- `is_approved` : Boolean
- `created_at` : Timestamp

**Relasi:**
- Belongs To → User (optional)

**Catatan:**
- Can be from registered users or manual input by admin

---

## 💰 Commerce Entities

### 22. Order
**Deskripsi:** Transaction/order untuk webinar, course, product

**Atribut:**
- `id` : UUID (PK)
- `user_id` : UUID (FK → User)
- `order_number` : String (unique, auto-generated)
- `total_Jumlah` : Decimal
- `discount_Jumlah` : Decimal (default: 0)
- `final_Jumlah` : Decimal
- `promo_code` : String (nullable)
- `status` : Enum ('pending', 'paid', 'failed', 'cancelled')
- `payment_method` : String (from Midtrans)
- `payment_url` : String (URL snap Midtrans)
- `paid_at` : Timestamp (nullable)
- `created_at` : Timestamp
- `updated_at` : Timestamp

**Relasi:**
- Belongs To → User
- Has Many → OrderItem

**Catatan:**
- Integrated with Midtrans payment gateway
- Webhook updates status

---

### 23. OrderItem
**Deskripsi:** Item dalam order (polymorphic - webinar/course/product)

**Atribut:**
- `id` : UUID (PK)
- `order_id` : UUID (FK → Order)
- `item_type` : Enum ('webinar', 'course', 'product')
- `item_id` : UUID (polymorphic FK)
- `item_title` : String (snapshot)
- `item_Harga` : Decimal (snapshot)
- `Kuantitas` : Integer (default: 1)
- `Subtotal` : Decimal
- `created_at` : Timestamp

**Relasi:**
- Belongs To → Order
- Belongs To → Webinar | Course | Product (polymorphic)

**Catatan:**
- relasi polimorfik (item_type + item_id)
- Harga snapshot (jika harga berubah, order tetap pakai harga lama)

---

### 24. PromoCode
**Deskripsi:** Discount/Kode promo

**Atribut:**
- `id` : UUID (PK)
- `code` : String (unique)
- `discount_type` : Enum ('percentage', 'fixed')
- `discount_value` : Decimal
- `min_purchase` : Decimal (optional)
- `max_discount` : Decimal (optional - for percentage)
- `usage_limit` : Integer (optional)
- `used_count` : Integer (default: 0)
- `valid_from` : Timestamp
- `valid_until` : Timestamp
- `is_active` : Boolean
- `created_at` : Timestamp

**Relasi:**
- None (referenced by Order via promo_code string)

---

### 25. SiteSettings
**Deskripsi:** Pengaturan platform global (logo, contact, social media)

**Atribut:**
- `id` : UUID (PK)
- `key` : String (unique - ex: "site_logo", "contact_email")
- `value` : Text (JSON for complex values)
- `description` : String (optional)
- `updated_at` : Timestamp

**Relasi:**
- None (singleton pattern per key)

**Catatan:**
- Only Admin can edit
- Examples: site_logo, site_name, contact_email, facebook_url, instagram_url

---

## 📊 Relationship Summary

### Domain Multi-Tenant

```
User (1) ────< SchoolMember (M) >──── (M) School
                     │
                     └─ role: headmaster | teacher | parent

School (1) ────< Class (M)
School (1) ────< Student (M)
School (1) ────< ParentProfile (M)
School (1) ────< Attendance (M)
School (1) ────< Assessment (M)
School (1) ────< Finance (M)

Teacher (1) ────< Class (M) [homeroom_teacher_id]
   └─ 1 Teacher bisa menangani multiple Kelas (Many-to-One)

Class (M) >──── (1) Teacher [nullable]
   └─ 1 Class has 1 Wali kelas (optional)

ParentProfile (1) ────< Student (M)
   └─ 1 Parent can have Multiple Children

Student (M) >──── (1) Class [nullable]
Student (M) >──── (1) ParentProfile [required]
Student (1) ────< Attendance (M)
Student (1) ────< Assessment (M)
Student (1) ────< Finance (M)
```

### Domain Manajemen Konten

```
Mentor (1) ────< Webinar (M)
Mentor (1) ────< Course (M)

Category (1) ────< Course (M)
Category (1) ────< Product (M)
Category (1) ────< Article (M)

Course (1) ────< Module (M)
Module (1) ────< Lesson (M)

Course (1) ────< CourseEnrollment (M) >──── (1) User
CourseEnrollment (1) ────< LessonProgress (M) >──── (1) Lesson
```

### Domain Perdagangan

```
User (1) ────< Order (M)
Order (1) ────< OrderItem (M)

OrderItem (M) >──── (1) Webinar | Course | Product [polymorphic]

CourseEnrollment ← Auto-created after Order.status = 'paid'
```

### Lintas Domain Relationships

```
User (1) ────< Order (M)              [Commerce]
User (1) ────< CourseEnrollment (M)   [Content Management]
User (1) ────< SchoolMember (M)       [Multi-Tenant]
User (1) ──── (1) ParentProfile       [Multi-Tenant]
User (1) ──── (1) Teacher             [Multi-Tenant]
User (1) ────< Article (M)            [Content - as author]
```

---

## 🎯 Key Design Patterns

### 1. Multi-Tenancy Pattern
```
Every SIAKAD entity has school_id
- School isolation via school_id
- User can belong to multiple schools (via SchoolMember)
- Role-based per school (Bu Ani = Teacher di School A, Parent di School B)
```

### 2. relasi polimorfik
```
OrderItem can reference:
- Webinar (item_type = 'webinar', item_id = webinar.id)
- Course (item_type = 'course', item_id = course.id)
- Product (item_type = 'product', item_id = product.id)
```

### 3. Soft Delete Pattern (Recommended)
```
Entities that should support soft delete:
- Student (status: active | graduated | transferred)
- User (deleted_at timestamp)
- School (is_active boolean)
```

### 4. snapshot Pattern
```
OrderItem stores snapshot of:
- item_title (jika product dihapus)
- item_Harga (jika harga berubah)
Prevents data loss from deleted or updated items
```

### 5. Audit Trail Pattern (Recommended)
```
Critical entities should track:
- created_by (user_id)
- updated_by (user_id)
- deleted_by (user_id)

Examples: Finance, Assessment, Attendance
```

---

## 📝 Entity Count Summary

**Domain Multi-Tenant:** 10 entities
- User, School, SchoolMember, Teacher, Class, ParentProfile, Student, Attendance, Assessment, Finance

**Domain Manajemen Konten:** 11 entities
- Mentor, Webinar, Course, Module, Lesson, CourseEnrollment, LessonProgress, Product, Article, Category, Testimonial

**Domain Perdagangan:** 4 entities
- Order, OrderItem, PromoCode, SiteSettings

**Total:** 25 entities

---

## 🔗 Cardinality Legend

```
(1)      = One (exactly one)
(M)      = Many (zero or more)
(1..*)   = One to Many (at least one)
(0..1)   = Zero or One (optional)
─────    = Relationship line
>────    = Foreign Key direction
<────>   = Bidirectional relationship
[nullable] = Optional relationship
[required] = Mandatory relationship
```

---

## 📌 Notes for Diagram Creation

1. **Grouping Recommendation:**
   - Group 1: Multi-Tenant (School, Class, Student, Teacher, Parent)
   - Group 2: Content (Course, Webinar, Product, Article, Mentor)
   - Group 3: Commerce (Order, OrderItem, Payment)
   - Group 4: Core (User, Auth)

2. **Color Coding Suggestion:**
   - Blue: Multi-Tenant entities
   - Green: Content Management entities
   - Orange: Commerce entities
   - Gray: Core/Shared entities

3. **Key Relationships to Highlight:**
   - User ↔ SchoolMember ↔ School (multi-tenancy)
   - Teacher → Class (Many-to-One) **IMPORTANT**
   - ParentProfile → Student (One-to-Many)
   - Order → OrderItem → Polymorphic (Webinar|Course|Product)

4. **Inheritance/Extension Pattern:**
   - User is extended by Teacher (1-to-1)
   - User is extended by ParentProfile (1-to-1 per school)

5. **Critical Constraints:**
   - ParentProfile.email unique per school_id
   - Teacher bisa menangani multiple Kelas
   - Class has max 1 homeroom_teacher_id
   - Student must have parent_profile_id (NO student login)

---

*Terakhir Diperbarui: January 14, 2026*
*Versi: 1.0 - Initial Class Diagram Specification*

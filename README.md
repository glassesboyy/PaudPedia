# 📚 PAUDPEDIA - Dokumentasi Lengkap

Selamat datang di dokumentasi lengkap **Platform PaudPedia** - Multi-Tenant SIAKAD & E-Learning Platform untuk institusi PAUD (Pendidikan Anak Usia Dini) di Indonesia.

---

## 🛠️ Tech Stack

### Backend
- **Laravel 12** - REST API, Business Logic, Authentication
- **MySQL 8.0** - Primary Database

### Frontend
| Platform | Technology | Purpose |
|----------|------------|---------|
| **Admin Panel** | Laravel Filament | Dashboard untuk Admin & Moderator (manajemen konten, user, analytics) |
| **Public Site** | Next.js / Nuxt.js | Marketing pages, E-Commerce, LMS, User Dashboard |
| **SIAKAD** | React/Vue + Vite | Multi-tenant school management (Parent, Teacher, Headmaster dashboards) |

### Integrasi
- **Payment Gateway:** Midtrans (Indonesia)
- **Authentication:** Laravel Sanctum (Token-based API Auth)
- **PDF Generation:** DomPDF / Snappy
- **Email:** Laravel Mail + Queue

---

## 📋 Struktur Dokumentasi

### 1. [PRD.md](/docs/PRD.md) - Product Requirements Document
**Isi:**
- Product vision & objectives
- Target market & user personas
- Feature requirements (functional & non-functional)
- User stories & acceptance criteria

---

### 2. [ERD.md](/docs/ERD.md) - Entity Relationship Diagram
**Isi:**
- Skema database lengkap (25 tables)
- Tipe data MySQL untuk setiap kolom
- Foreign keys & constraints
- Indexes & optimization strategy
- Laravel migration examples

---

### 3. [CLASS_DIAGRAM.md](/docs/CLASS_DIAGRAM.md) - Class Diagram
**Isi:**
- Entity definitions (attributes & methods)
- Relationships & cardinality
- Business logic structure
- Design patterns

---

### 4. [USE_CASE.md](/docs/USE_CASE.md) - Use Cases Per Role
**Isi:**
- 7 roles: Guest, User, Parent, Teacher, Headmaster, Moderator, Admin
- Use cases per role (what they can do)
- Permission & access control matrix
- Subscription impact (Free vs Pro)

---

### 5. [FLOWS.md](/docs/FLOWS.md) - System Flows
**Isi:**
- Multi-tenant flows (school registration, user management)
- Subscription flows (Free to Pro upgrade)
- Payment flows (Midtrans integration)
- Content management flows
- Email notification flows

---

## 🎯 Quick Start untuk Developer

### Backend Developer (Laravel)
2. Baca: **ERD.md** → Database Schema
3. Baca: **CLASS_DIAGRAM.md** → Laravel Models & Relationships
4. Baca: **FLOWS.md** → API Endpoints & Business Logic
5. Implementasi: Migration, Models, Controllers, API Routes

### Frontend Developer (Next.js/Nuxt - Public Site)
1. Baca: **PRD.md** → Feature Requirements
2. Baca: **USE_CASE.md** → Guest & User roles
3. Baca: **FLOWS.md** → Shopping & LMS flows
4. Implementasi: Pages, Components, API Integration

### Frontend Developer (React/Vue - SIAKAD)
1. Baca: **PRD.md** → SIAKAD Feature Requirements
2. Baca: **USE_CASE.md** → Parent, Teacher, Headmaster roles
3. Baca: **FLOWS.md** → Multi-tenant & School Management flows
4. Implementasi: Dashboards, Forms, Multi-tenancy logic

### QA Tester
1. Baca: **USE_CASE.md** → Semua use cases per role
2. Baca: **FLOWS.md** → Expected flows & happy/unhappy paths
3. Baca: **PRD.md** → Acceptance criteria
4. Test: Functional testing per role & feature

---

## 🌐 Domain Structure

```
paudpedia.com           → Next.js/Nuxt (Public Site & LMS)
├── / (landing page)
├── /artikel (blog)
├── /webinar (marketplace)
├── /kursus (courses)
├── /login & /register
└── /akun-saya (user dashboard)

sikola.paudpedia.com    → React/Vue+Vite (SIAKAD)
├── /login
├── /dashboard (role-based)
├── /siswa (students)
├── /absensi (attendance)
├── /nilai (assessment)
└── /laporan (reports)

admin.paudpedia.com     → Laravel Filament
├── /admin (super admin)
├── /moderator (content manager)
├── /users
├── /schools
└── /analytics

api.paudpedia.com       → Laravel API
└── /api/v1/*
```

---

## 🔐 Multi-Tenancy Strategy

**Isolasi Data:**
- Setiap sekolah memiliki `school_id` unik
- Semua data SIAKAD di-filter berdasarkan `school_id`
- Laravel Global Scope untuk auto-filter queries
- Middleware untuk verify school access

**Role Management:**
- User dapat memiliki multiple roles di multiple schools
- Contoh: Bu Ani = Teacher di School A, Parent di School B
- Tabel `school_members` menyimpan relasi user-school-role

---

## 📊 Database Overview

**Total Tables:** 25

**By Domain:**
- **Core:** 2 tables (users, user_profiles)
- **Multi-Tenant:** 9 tables (schools, school_members, teachers, classes, parent_profiles, students, attendance, assessments, finances)
- **Content:** 11 tables (mentors, categories, webinars, courses, modules, lessons, enrollments, progress, products, articles, testimonials)
- **Commerce:** 3 tables (orders, order_items, promo_codes, site_settings)

**ID Strategy:** BIGINT UNSIGNED AUTO_INCREMENT (MySQL optimized)

---

## 📖 Cara Membaca Dokumentasi

### Untuk Memahami Sistem Secara Keseluruhan
1. **PRD.md** → Product vision & requirements
3. **USE_CASE.md** → User perspective

### Untuk Development
1. **ERD.md** → Database structure (WAJIB BACA PERTAMA)
2. **CLASS_DIAGRAM.md** → Laravel Models
3. **FLOWS.md** → Implementation flows
4. **USE_CASE.md** → Feature checklist

### Untuk Testing
1. **USE_CASE.md** → Test scenarios per role
2. **FLOWS.md** → Expected behavior
3. **PRD.md** → Acceptance criteria

---

## ⚡ Quick Links

- [Product Requirements](./PRD.md)
- [Database Schema](./ERD.md)
- [Class Diagram](./CLASS_DIAGRAM.md)
- [Use Cases](./USE_CASE.md)
- [System Flows](./FLOWS.md)

---

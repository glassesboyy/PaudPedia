# Test User Credentials

Semua password default: **password**

## 🔐 Admin Panel Users (Laravel Filament)

### 1. Super Admin (Full System Access)
```
Email: admin@paudpedia.com
Password: password
Role: admin
Access: Full system management, site settings, all content
```

### 2. Content Moderator (Content Manager)
```
Email: moderator@paudpedia.com
Password: password
Role: moderator
Access: Manage webinar, course, product, artikel, mentor, testimonial
```

---

## 🏫 SIAKAD Users (School Management System)

### 3. Headmaster (Kepala Sekolah)

**School 1: TK Harapan Bangsa (Pro Plan)**
```
Email: headmaster1@test.com
Password: password
Role: headmaster
Access: Full school management, manage students/teachers/classes, upgrade subscription
```

**School 2: TK Ceria Cendekia (Free Plan)**
```
Email: headmaster2@test.com
Password: password
Role: headmaster
Access: Full school management (with Free Plan limitations)
```

**School 3: TK Mutiara Hati (Pro Plan)**
```
Email: headmaster3@test.com
Password: password
Role: headmaster
Access: Full school management
```

### 4. Teacher (Guru)

```
Email: teacher1@test.com
Password: password
Role: teacher
Name: Dewi Kusuma, S.Pd
Access: View students, input absensi, input nilai, generate rapor (Pro only)
```

```
Email: teacher2@test.com
Password: password
Role: teacher
Name: Rina Puspita, S.Pd
Access: Same as teacher1
```

```
Email: teacher3@test.com
Password: password
Role: teacher
Name: Budi Santoso, S.Pd
Access: Same as teacher1
```

### 5. Parent (Orang Tua)

```
Email: parent1@test.com
Password: password
Role: parent
Name: Ibu Ani Wijaya
Access: View children data, absensi, nilai, download rapor, view SPP/tabungan (Pro only)
```

```
Email: parent2@test.com
Password: password
Role: parent
Name: Bapak Joko Prasetyo
Access: Same as parent1
```

```
Email: parent3@test.com
Password: password
Role: parent
Name: Ibu Fitri Handayani
Access: Same as parent1
```

---

## 🌐 E-Learning Users (Public Platform)

### 6. Regular User (Shopping & Learning)

```
Email: user1@test.com
Password: password
Role: user
Name: Lisa Pratiwi
Access: Browse, shopping cart, checkout, take courses, download certificate
```

```
Email: user2@test.com
Password: password
Role: user
Name: Rizky Ramadhan
Access: Same as user1
```

```
Email: user3@test.com
Password: password
Role: user
Name: Dina Mariana
Access: Same as user1
```

---

## 📊 Test Data Summary

### Created Records:
- ✅ **Users**: 27 total
  - 1 Admin
  - 1 Moderator
  - 3 Headmasters (1 per school)
  - 3 Teachers
  - 3 Parents
  - 3 Regular Users
  - 10 Random Users (via Factory)

- ✅ **Schools**: 3
  - TK Harapan Bangsa (Pro Plan)
  - TK Ceria Cendekia (Free Plan)
  - TK Mutiara Hati (Pro Plan)

- ✅ **Content**:
  - 3 Mentors
  - 13 Categories
  - 3 Webinars
  - 4 Courses
  - Modules & Lessons (auto-generated per course)
  - 4 Products
  - 4 Articles
  - 5 Testimonials

- ✅ **School Data**:
  - 4 Classes per school
  - ~10 Students per class
  - 30 days attendance history per student
  - Assessments (2 semesters)
  - Finance records (Pro schools only)

- ✅ **Commerce**:
  - 4 Promo Codes
  - Random Orders with items
  - Course Enrollments with progress

---

## 🧪 Testing Scenarios

### Scenario 1: Admin Management
```
Login: admin@paudpedia.com
Test: Create webinar, view all schools, edit site settings
```

### Scenario 2: Content Creation
```
Login: moderator@paudpedia.com
Test: Create course with modules, upload product, publish artikel
```

### Scenario 3: School Management (Pro)
```
Login: headmaster1@test.com
School: TK Harapan Bangsa (Pro Plan)
Test: Add student, generate PDF rapor, input SPP
```

### Scenario 4: School Management (Free)
```
Login: headmaster2@test.com
School: TK Ceria Cendekia (Free Plan)
Test: Try add 21st student (should fail), try access keuangan (should redirect)
```

### Scenario 5: Teacher Activity
```
Login: teacher1@test.com
Test: Input absensi, input nilai, view rapor
Limitation: Cannot add/edit/delete students
```

### Scenario 6: Parent Monitoring
```
Login: parent1@test.com
Test: View children data, check attendance percentage, view assessments
```

### Scenario 7: E-Learning User
```
Login: user1@test.com
Test: Browse courses, add to cart, checkout, take course, get certificate
```

---

## 🔄 Re-seeding Database

Untuk reset dan seed ulang database:

```bash
# Windows (cmd)
cd c:\laragon\www\paudpedia\backend-api-admin
php artisan migrate:fresh --seed

# Linux/Mac
php artisan migrate:fresh --seed
```

**Warning:** Ini akan menghapus semua data dan membuat ulang dari awal!

---

## 📝 Notes

1. **Multi-Role Support**: User bisa punya multiple roles (contoh: Parent di sekolah A + Teacher di sekolah B)
2. **Email Unique per School**: Parent bisa punya email sama di sekolah berbeda
3. **Random Data**: StudentSeeder & OrderSeeder menggunakan faker untuk variasi data
4. **Auto-generated**: Certificate auto-generate saat course progress 100%
5. **Subscription Impact**: Finance features hanya tersedia untuk Pro Plan schools

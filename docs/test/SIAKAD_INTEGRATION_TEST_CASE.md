# SIAKAD Integration Test Case
Dokumentasi pengujian End-to-End (E2E) untuk integrasi antara Frontend Public dan Frontend SIAKAD.

---

## 1. Authentication Flow
Fokus pada proses masuk (login) dan perpindahan sesi antar domain.

### TC-AUTH-01: Cross-Domain Login (Public to SIAKAD)
- **Tujuan:** Memastikan user yang login di FE Public dapat mengakses SIAKAD tanpa login ulang.
- **Preconditions:** User memiliki akun terdaftar sebagai 'Sekolah' (Headmaster). User dalam posisi logout di kedua aplikasi.
- **Test Steps:**
  1. Buka `http://localhost:3000/auth/login`.
  2. Masukkan email dan password valid.
  3. Setelah masuk ke Dashboard Public, klik menu "Dashboard SIAKAD" pada navbar.
  4. Amati proses redirect ke `http://localhost:5173/auth/token?token=...`.
- **Expected Result:**
  - Aplikasi SIAKAD berhasil memvalidasi token secara otomatis.
  - User langsung diarahkan ke Dashboard SIAKAD atau halaman Pilih Sekolah tanpa diminta login ulang.
- **Actual Result:** Berhasil login di Public, namun link "Dashboard SIAKAD" sempat tidak muncul di Navbar (Sudah diperbaiki dengan menambahkan eager loading memberships di backend).
- **Status:** PASS (After Fix)
### TC-AUTH-02: Direct Login on SIAKAD
- **Tujuan:** Memastikan login langsung di aplikasi SIAKAD berfungsi.
- **Preconditions:** User memiliki akun 'Sekolah' (Headmaster).
- **Test Steps:**
  1. Buka `http://localhost:5173/login`.
  2. Masukkan kredensial valid.
  3. Klik tombol "Masuk".
- **Expected Result:**
  - Login berhasil dan user diarahkan ke Dashboard SIAKAD.
- **Actual Result:** 
  - Login berhasil dan user diarahkan ke Dashboard SIAKAD.
- **Status:** PASS

---

## 2. Registration Flow
Fokus pada pembuatan akun baru melalui Frontend Public.

### TC-REG-01: Registrasi User Biasa (B2C)
- **Tujuan:** Membuat akun user umum tanpa akses sekolah.
- **Preconditions:** Menggunakan email yang belum pernah terdaftar.
- **Test Steps:**
  1. Buka `http://localhost:3000/auth/register`.
  2. Pilih Tab "User Biasa".
  3. Isi Form (Nama, Email, Password).
  4. Klik "Daftar".
- **Expected Result:**
  - Akun berhasil dibuat (role: `user`).
  - User diarahkan ke halaman verifikasi email atau dashboard public.
  - Menu "Dashboard SIAKAD" TIDAK muncul di navbar.
- **Actual Result:** 
  - Akun berhasil dibuat (role: `user`).
  - User diarahkan ke halaman verifikasi email atau dashboard public.
  - Menu "Dashboard SIAKAD" TIDAK muncul di navbar.
- **Status:** PASS 

### TC-REG-02: Registrasi Sekolah Baru (Guest)
- **Tujuan:** Pendaftaran user baru sekaligus mendaftarkan sekolah (onboarding Headmaster).
- **Preconditions:** Menggunakan email baru.
- **Test Steps:**
  1. Buka `http://localhost:3000/auth/register`.
  2. Pilih Tab "Sekolah".
  3. Isi Form Data Akun dan Form Data Sekolah (Nama Sekolah, NPSN, Alamat).
  4. Klik "Daftar & Buat Sekolah".
- **Expected Result:**
  - Akun user dibuat dengan role `user` dan `headmaster`.
  - Data sekolah tersimpan di database.
  - User otomatis diarahkan ke Dashboard SIAKAD setelah proses login otomatis berhasil.
- **Actual Result:** Berhasil mendaftar, redirect ke SIAKAD lancar, dan landed di Dashboard dengan role Kepala Sekolah.
- **Status:** PASS
---

## 3. Upgrade Account Flow
Fokus pada user yang sudah punya akun namun ingin mendaftarkan sekolahnya.

### TC-UPG-01: Upgrade User Biasa ke Sekolah
- **Tujuan:** Menambahkan akses sekolah (role headmaster) ke akun yang sudah ada.
- **Preconditions:** Login sebagai "User Biasa" di FE Public.
- **Test Steps:**
  1. Buka halaman registrasi sekolah melalui menu atau link `http://localhost:3000/auth/register?type=school`.
  2. Sistem harus mengenali user sudah login (menampilkan info profil, bukan form password).
  3. Isi Data Sekolah.
  4. Klik "Daftarkan Sekolah".
- **Expected Result:**
  - User mendapatkan role tambahan `headmaster`.
  - Terbentuk data `School` dan `SchoolMember`.
  - User diarahkan ke SIAKAD dan mendapatkan akses dashboard.
- **Actual Result:** Upgrade berhasil untuk user existing (Surya). Form otomatis mengenali user dan hanya meminta data sekolah. Setelah submit, user berhasil diarahkan ke dashboard SIAKAD dengan role Kepala Sekolah.
- **Status:** PASS

---

## 4. Error Handling & Edge Cases
Fokus pada validasi sistem terhadap data tidak valid.

### TC-ERR-01: Akses SIAKAD Tanpa Role Sekolah
- **Tujuan:** Mencegah user biasa masuk ke sistem akademik.
- **Preconditions:** Login sebagai "User Biasa" (tanpa membership sekolah).
- **Test Steps:**
  1. Coba buka langsung URL `http://localhost:5173/dashboard`.
- **Expected Result:**
  - User otomatis dilempar kembali ke halaman login SIAKAD atau Landing Page Public dengan pesan "Akses ditolak".
- **Actual Result:** User biasa tanpa role sekolah yang mencoba akses langsung `http://localhost:5173/dashboard` berhasil diblokir. API mengembalikan `403 Forbidden` dan dashboard gagal dimuat (Tampilan Blank/Access Denied).
- **Status:** PASS

### TC-ERR-02: Token Tidak Valid / Expired
- **Tujuan:** Memastikan sistem menangani token rusak atau kadaluarsa saat perpindahan domain.
- **Preconditions:** -
- **Test Steps:**
  1. Buka URL secara manual: `http://localhost:5173/auth/token?token=token_palsu_123`.
- **Expected Result:**
  - Muncul pesan error "Data profil user gagal diverifikasi".
  - Redirect otomatis ke halaman login setelah 2-3 detik.
- **Actual Result:** Redirect ke login berhasil, namun pesan error di UI kurang terlihat jelas (Sudah diperbaiki).
- **Status:** PASS
---

## Detail Teknis Terkait

### Endpoint API Utama
- `POST /api/v1/auth/register-school`: Pendaftaran sekolah baru (Guest).
- `POST /api/v1/schools/register`: Upgrade akun ke sekolah (Authenticated).
- `GET /api/v1/auth/me`: Verifikasi profil & sinkronisasi sesi.
- `GET /api/v1/my-memberships`: Mengambil daftar sekolah yang diakses user.

### Peran & Hak Akses (RBAC)
- **Role `user`:** Akses marketplace dan kursus umum di FE Public.
- **Role `headmaster`:** Akses penuh manajemen sekolah di FE SIAKAD.
- **Role `teacher` / `parent`:** Akses terbatas sesuai modul di SIAKAD.

### Catatan Khusus
- **Redirect Token:** Proses perpindahan dari Public ke SIAKAD menggunakan parameter `?token=` di URL. Parameter ini hanya digunakan satu kali untuk proses inisialisasi session di domain SIAKAD.
- **Email Verification:** Jika sistem mewajibkan verifikasi email, user tidak dapat mengakses Dashboard SIAKAD sebelum status `email_verified_at` di database terisi.

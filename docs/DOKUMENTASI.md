Tentu, ini adalah **Project Brief & Technical Architecture Blueprint** yang lengkap dan telah disempurnakan untuk ekosistem **Paudpedia**.

Dokumen ini dirancang sebagai panduan tunggal ("Single Source of Truth") untuk developer (Backend & Frontend) dalam membangun sistem menggunakan **Laravel (API) + MySQL** dan **React**.

---

# 📘 BLUEPRINT TEKNIS: PAUDPEDIA ECOSYSTEM

**Stack:** Laravel 12 (API) | MySQL 8.0 | React (Nuxt/Next/Vite) | Filament (Admin)

---

## 1. ARSITEKTUR SISTEM (High Level)

Sistem ini menggunakan pendekatan **Headless Monolith**. Satu Backend Laravel melayani semua kebutuhan data melalui API, namun Frontend dipisahkan berdasarkan target pengguna untuk performa dan keamanan.

### 🌐 Peta Ekosistem

1. **Backend Core (API):** `api.paudpedia.com` (Laravel)
* Pusat logika, database, job queue, dan integrasi payment.


2. **Frontend Public & LMS:** `paudpedia.com` (Nuxt/React)
* Landing page, pembelian webinar, marketplace, dan kelas online.


3. **Frontend SaaS (SIAKAD):** `sikola.paudpedia.com` (React)
* Dashboard khusus Sekolah (Guru, Kepsek) untuk manajemen siswa & asesmen.


4. **Super Admin:** `admin.paudpedia.com` (FilamentPHP)
* Panel internal untuk manajemen user, validasi pembayaran, dan konten global.

---

## 2. LOGIKA SISTEM (Business Logic)

### A. Logic Asesmen (Kurikulum Merdeka)

Karena asesmen PAUD bersifat kualitatif (Narasi), bukan angka, backend harus menangani ini:

1. **Input:** Guru input data via Form React.
2. **Processing:** Laravel menyimpan data mentah ke kolom JSON.
3. **Output (Rapor):** Saat generate Rapor Semester, sistem akan menarik semua data JSON selama 6 bulan, lalu menyajikannya dalam format PDF yang rapi per elemen capaian (Agama, Jati Diri, STEAM).

### B. Logic Subscription (SaaS)

1. **Free Plan:** Sekolah bisa input data siswa & absensi. Limit 20 Siswa.
2. **Pro Plan:** Unlock fitur "Generate PDF Rapor" dan "Keuangan".
3. **Middleware:** Di Laravel, buat middleware `CheckSubscription`. Jika masa aktif habis, API akses ke fitur Pro akan return `403 Forbidden`.

---

## 3. KEBUTUHAN SERVER (Deployment)

Untuk menjalankan ekosistem ini dengan lancar:

* **VPS:** Min. 2 Core CPU, 4GB RAM (Ubuntu 22.04).
* **Web Server:** Nginx (sebagai Reverse Proxy).
* **Database:** MySQL 8.0 (Optimized configuration).
* **Storage:** AWS S3 / MinIO (Wajib untuk menyimpan foto-foto kegiatan siswa agar server tidak penuh).
* **Domain Management:**
* `paudpedia.com` (Main)
* `api.paudpedia.com` (Backend)
* `app.paudpedia.com` (SaaS Dashboard)


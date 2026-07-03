# Arsitektur Skema Database Platform Paudpedia

## 1. Pendahuluan

Arsitektur basis data pada platform Paudpedia dirancang dengan menggunakan pendekatan relasional yang menitikberatkan pada normalisasi serta integritas referensial. Untuk menampung berbagai fitur kompleks mulai dari *Learning Management System* (LMS), *e-commerce*, hingga manajemen sekolah *multi-tenant*, keseluruhan tabel di dalam platform dipetakan menjadi beberapa subsistem logis. Pembagian subsistem ini bertujuan untuk memberikan gambaran tingkat tinggi (*helicopter view*) mengenai struktur fungsional database, sehingga mempermudah tahapan analisis operasional serta memetakan batasan logika bisnis tanpa harus menyelami struktur kolom secara langsung.

Meskipun klasifikasi ini membagi tabel-tabel tersebut ke dalam kelompok Subsistem SIAKAD, Subsistem Public B2C, dan Subsistem Admin Panel, perlu digarisbawahi bahwa seluruh tabel secara fisik tetap berada di dalam satu basis data tunggal. Pembagian ini semata-mata bersifat konseptual dan struktural. Tabel-tabel antar subsistem tetap saling terintegrasi secara teknis menggunakan *foreign key* dan *constraint* tingkat basis data untuk memastikan bahwa data pengguna dan rekam jejak transaksi di satu modul dapat secara transparan diakses dari modul lainnya.

## 2. Subsistem SIAKAD

Subsistem SIAKAD menangani seluruh beban kerja terkait kegiatan operasional dan administratif lembaga pendidikan usia dini. Tabel-tabel pada kelompok ini berpusat di sekitar entitas sekolah sebagai *tenant* (pemilik data) untuk memastikan bahwa rekam jejak siswa dan laporan keuangan sebuah institusi tidak saling bercampur dengan institusi lain.

Berikut adalah daftar tabel yang beroperasi di dalam Subsistem SIAKAD:
- `schools`
- `school_members`
- `school_transfer_requests`
- `classes`
- `teachers`
- `parent_profiles`
- `students`
- `attendance`
- `assessments`
- `development_programs`
- `development_indicators`
- `student_reports`
- `student_report_details`
- `finances`

Keseluruhan kumpulan tabel di atas saling berkolaborasi untuk membentuk mesin operasional pendidikan yang utuh. Mulai dari pendaftaran (*enrollment*) dan pengelompokan kelas fisik, hingga rekam jejak evaluasi rapor, pencatatan indikator perkembangan, dan manajemen keuangan tagihan, semuanya dikunci secara referensial oleh identitas sekolah (menggunakan kolom relasional seperti *school_id*). Skema ketat ini menjamin bahwa seluruh antarmuka SIAKAD dapat melakukan pembatasan (*scoping*) data secara otomatis, sehingga kepala sekolah atau guru hanya diberi kapabilitas untuk membaca serta mengelola data yang bernaung di bawah wilayah institusinya masing-masing.

## 3. Subsistem Public B2C

Subsistem Public B2C dikhususkan untuk menampung lalu lintas interaksi masyarakat umum (*traffic*) secara terbuka. Kumpulan tabel pada subsistem ini mencakup aktivitas *e-commerce*, manajemen konten portal (*Content Management System*/CMS), serta jejak rekam belajar mandiri jarak jauh (LMS). Modul ini bekerja di luar batas administrasi sekolah mana pun, sehingga setiap pengguna publik (*guest* atau konsumen) memiliki kebebasan penuh dalam mencari dan bertransaksi.

Berikut adalah daftar tabel yang beroperasi di dalam Subsistem Public B2C:
- `categories`
- `mentors`
- `courses`
- `modules`
- `lessons`
- `lesson_progress`
- `quizzes`
- `quiz_questions`
- `quiz_answers`
- `quiz_attempts`
- `quiz_attempt_answers`
- `course_enrollments`
- `webinars`
- `products`
- `carts`
- `cart_items`
- `promo_codes`
- `orders`
- `order_items`
- `articles`
- `testimonials`

Fungsi utama dari jajaran tabel publik ini adalah merajut kelancaran siklus niaga hingga pemberian lisensi atau hak akses produk digital secara otomatis. Alur bisnis diinisiasi dari penampungan data di keranjang belanja (`carts`) dan pembentukan pesanan akhir (`orders`), kemudian disinkronisasi dengan persediaan aset digital beserta potongan harga promo. Pasca transaksi dinyatakan berhasil (*paid*), distribusi data dilanjutkan ke arah kumpulan tabel aktivitas LMS (seperti `course_enrollments` dan catatan `lesson_progress`) untuk merekap jejak pelatihan milik publik secara mandiri tanpa memerlukan campur tangan birokrasi lembaga PAUD.

## 4. Subsistem Admin Panel

Subsistem Admin Panel merangkum jajaran tabel yang menempati posisi teratas pada hierarki tata kelola platform. Tabel-tabel pada kelompok ini menguasai hak dan wewenang administratif mutlak (*superadmin*), sekaligus memainkan peran sebagai tabel berbagi (*shared tables*) yang mengaitkan kredensial global dengan kelancaran aktivitas sistem langganan B2B (*Business-to-Business*).

Berikut adalah daftar tabel sentral yang ditinjau dan dikendalikan melalui Subsistem Admin Panel:
- `users`
- `site_settings`
- `subscription_orders`
- `roles`
- `permissions`
- `model_has_permissions`
- `model_has_roles`
- `role_has_permissions`

Kumpulan tabel di atas mengemban tugas krusial sebagai pilar keamanan aplikasi serta wadah konfigurasi parameter keseluruhan (*system settings*). Tabel sentral `users` dipadukan dengan struktur matriks tabel *roles* dan *permissions* (bawaan modul Spatie) untuk menyaring secara presisi siapa saja figur yang memiliki otorisasi melangkah masuk ke sudut-sudut platform. Sementara itu, tabel `subscription_orders` disediakan khusus bagi staf internal guna memonitor deretan faktur arus kas perpanjangan perangkat lunak (*renewal*) yang diajukan oleh unit sekolah (tenant), menjadikannya muara pengawasan (*helicopter view*) yang mengamankan kelangsungan model bisnis platform ini ke depannya.

## 5. Kesimpulan

Pembagian skema basis data ke dalam tiga kelompok subsistem konseptual—yakni SIAKAD, Public B2C, dan Admin Panel—merepresentasikan segregasi (pemisahan) fungsional yang sangat sistematis di dalam arsitektur platform Paudpedia. Desain konseptual ini tidak hanya menunjang kelancaran dokumentasi serta pemahaman struktural tingkat tinggi bagi tim perancang sistem, melainkan turut serta menyempurnakan keamanan logika bisnis dengan mendesentralisasikan ruang gerak data per modul, sekaligus menjaga agar seluruh referensi entitas tetap terikat kokoh di dalam satu repositori basis data tunggal.

3.7	Rancangan Portal Informasi dan Konten Statis (Modul 16, 17, 18)
Portal informasi merupakan lapisan terdepan atau antarmuka tamu (*Guest*) dari sisi aplikasi Public B2C. Portal ini menyajikan berbagai profil, informasi esensial, maupun konten statis layaknya *company profile* untuk menarik *leads* atau calon konsumen. Pada modul ini, sistem menampilkan artikel/blog edukasi, daftar profil ahli (*mentors*), serta ulasan dan kepuasan pelanggan (*testimonials*). Karena sifatnya yang statis dan bebas akses (publik), platform difokuskan pada pengoptimalan *Search Engine Optimization* (SEO) sehingga konten-konten tersebut sangat mudah diindeks oleh mesin pencari web.

3.7.1	Wireframe
Tampilan yang berkaitan dengan fitur ini meliputi:
- Halaman Utama atau Landing Page (/)
- Halaman Artikel & Edukasi (/articles)
- Halaman Direktori Profil Mentor (/mentors)
- Halaman Tanya Jawab atau FAQ (/faq)
- Halaman Kontak & Bantuan (/contact)

3.7.2	Entity Relationship Diagram (ERD)

**Entitas: articles**
Atribut: title, slug, content, is_published, published_at
Relasi:
- Many-to-One ke entitas `categories` | Kata Relasi: "Berkategori"
- Many-to-One ke entitas `users` | Kata Relasi: "Ditulis Oleh"

**Entitas: mentors**
Atribut: name, title, bio, expertise, is_active
Relasi:
- One-to-Many ke entitas `courses` dan `webinars` | Kata Relasi: "Mengajar Kelas"

**Entitas: testimonials**
Atribut: name, content, rating, is_approved
Relasi:
- Many-to-One ke entitas `users` | Kata Relasi: "Diberikan Oleh"

**Entitas: categories**
Atribut: name, slug, type
Relasi:
- One-to-Many ke entitas `articles`, `products`, dan `courses` | Kata Relasi: "Mengelompokkan"

*Penjelasan Keseluruhan:*
Diagram ERD pada modul portal publik disusun lebih longgar karena sifat informasinya yang diperuntukkan bagi tamu (*Guest*) tanpa perlu proses otentikasi ketat. Entitas `articles` (berita/blog edukasi), `mentors` (data rekam jejak tenaga pengajar), dan `testimonials` (ulasan kepuasan pengguna) utamanya berdiri mendampingi halaman utama (*landing page*) agar tampil interaktif. Entitas `articles` memiliki fiksasi relasi yang merujuk pada `categories` guna memudahkan pengunjung menyaring topik bacaan (misalnya khusus membaca 'Tips Parenting'), serta menyertakan *Foreign Key* ke akun `users` staf yang mengarang tulisan tersebut. Di sisi lain, profil pengajar di entitas `mentors` disiapkan sebagai data mandiri yang nantinya direferensikan silang secara luas (*one-to-many*) ke entitas kelas kursus atau acara *webinar* sebagai figur narasumbernya. Pola ini memisahkan secara elegan antara sistem identitas login tertutup (*users*) dan sistem identitas tampilan publik (*mentors*).

3.7.3	Database Schema
- articles
- mentors
- testimonials
- categories

Tabel `articles` memegang struktur informasi seperti `title`, `slug` (untuk ramah SEO), `content` (berupa HTML/Rich Text), serta `published_at`. Tabel `mentors` memiliki struktur biodata meliputi nama, `expertise` (bidang keahlian), `bio`, serta `social_media`. Tabel `testimonials` menyimpan ulasan teks dari pengunjung. Tabel `categories` menyimpan tipe kategori dengan format `slug` untuk filter kategori konten.

3.7.4	Activity Diagram
```text
[Start]
   |
   v
Pengunjung (Guest) Membuka Website Public
   |
   v
Sistem Menampilkan Landing Page Utama
   |
   v
Navigasi ke Menu Informasi (Artikel/Mentor)
   |
   v
Pilih Artikel atau Profil Spesifik
   |
   v
Kueri Database Berdasarkan 'Slug'
   |
   +-- [Data Tidak Ada] --> Tampilkan Halaman 404 (Not Found)
   |
   +-- [Data Ada]
           |
           v
    Sistem Me-Render Konten Detail
           |
           v
         [End]
```
Diagram ini menjabarkan interaksi pengunjung biasa yang meramban aplikasi untuk tujuan mencari literasi atau membaca *company profile* tanpa diwajibkan untuk membuat akun (*login*) terlebih dahulu. Kueri data dilakukan secara reaktif berdasarkan URL (*slug*).

3.7.5	Use Case Diagram
1. Use Case: View Landing Page & Static Content
   Role: Guest, User
   Deskripsi: Menjelajahi beranda utama, FAQ, dan kontak platform.

2. Use Case: Browse Artikel & Mentor
   Role: Guest, User
   Deskripsi: Mengakses literasi artikel blog, serta menelusuri daftar rekam jejak tenaga pendidik/mentor yang terdaftar.

3.8	Rancangan Manajemen Konten dan Katalog Item (Modul 19, 20, 21, 22)
Sistem Manajemen Konten (CMS) ini berada terisolasi pada area *Admin Panel* dan dijalankan sepenuhnya oleh struktur peranan *back-office* (Moderator/Admin). Modul ini bertanggung jawab atas kreasi dan pengunggahan produk baru ke pasar *marketplace* B2C (seperti draf kelas kursus, tautan *zoom* webinar, serta dokumen E-book). Fasilitas yang didukung tidak hanya terbatas pada formulir spesifikasi produk saja, melainkan juga menyokong pembuatan hierarki kurikulum LMS (pemecahan silabus menjadi modul dan *lessons*) dan sistem pembuatan kuis (*quiz builder*).

3.8.1	Wireframe
Tampilan yang berkaitan dengan fitur ini meliputi:
- Dasbor Manajemen Konten Kursus (Admin Panel) (/admin)
- Formulir Buat/Edit Produk Digital (/admin/products/create)
- Formulir Buat/Edit Kursus (/admin/courses/builder)
- Formulir Buat/Edit Webinar (/admin/webinars/create)
- Formulir Buat/Edit Artikel (/admin/articles/create)

3.8.2	Entity Relationship Diagram (ERD)

**Entitas: products, webinars, courses**
Atribut: title, price, is_published
Relasi:
- Many-to-One ke entitas `categories` | Kata Relasi: "Berkategori"
- Many-to-One ke entitas `mentors` | Kata Relasi: "Diajarkan Oleh"

**Entitas: modules**
Atribut: title, order
Relasi:
- Many-to-One ke entitas `courses` | Kata Relasi: "Bagian Dari Kelas"
- One-to-Many ke entitas `lessons` | Kata Relasi: "Berisi Materi"

**Entitas: quizzes**
Atribut: title
Relasi:
- Many-to-One ke entitas `modules` | Kata Relasi: "Ujian Untuk Modul"
- One-to-Many ke entitas `quiz_questions` | Kata Relasi: "Mempunyai Pertanyaan"

**Entitas: quiz_questions & quiz_answers**
Atribut quiz_questions: question
Atribut quiz_answers: answer, is_correct
Relasi:
- `quiz_questions` One-to-Many ke `quiz_answers` | Kata Relasi: "Memiliki Opsi Jawaban"

*Penjelasan Keseluruhan:*
Area operasional Manajemen Konten pada Admin Panel ini mengorkestrasi hampir separuh dari total entitas basis data platform. Rancangan ERD menempatkan tabel katalog penawaran digital (seperti `products`, `webinars`, dan `courses`) di pusat diagram agar berelasi dinamis dengan entitas referensial pendukung, yakni `mentors` (guna menampilkan detail wajah dan bio narasumber) serta `categories` (untuk taksonomi). Khusus pada produk berupa kursus mandiri (`courses`), arsitektur databasenya mengekspansi relasi secara vertikal ke bawah yang menyambungkan baris draf kursus tersebut kepada rentetan silabus di dalamnya. Rangkaian ini dimulai dari pembagian `modules` (Bab), yang kemudian rinciannya dipecah ke unit terkecil yakni entitas materi pasif (`lessons`) dan entitas tantangan evaluasi (`quizzes`). Semuanya ini diselaraskan di belakang panggung (*back-stage*) oleh peran *Moderator* sebelum nilai boolean pengontrol publisitas diubah agar katalog tayang.

3.8.3	Database Schema
- products, webinars, courses
- modules, lessons
- quizzes, quiz_questions, quiz_answers
- mentors, categories

Tabel `courses` bertumpu pada indikator *Harga*, *Tingkat* (kesulitan), dan rujukan `mentor_id`. Tabel `webinars` menambahkan kolom esensial penunjang acara *live* seperti `zoom_link`, `scheduled_at`, dan kuota `max_participants`. Untuk menyokong pembentukan kuis yang interaktif, skema merajut tabel `quizzes` kepada `quiz_questions` yang berisi untaian teks tanya-jawab, di mana relasinya diselesaikan ke pilihan ganda di tabel `quiz_answers` beserta sebuah flag indikator (*is_correct*) penentu jawaban kunci.

3.8.4	Activity Diagram
```text
[Start]
   |
   v
Moderator Login ke Admin Panel (CMS)
   |
   v
Buka Menu "Manajemen Kursus"
   |
   vSchoolMember
Pilih "Buat Kursus Baru" & Isi Detail (Judul, Harga, Mentor)
   |
   v
Simpan Draf Kursus Pertama
   |
   v
Masuk ke Fitur "Syllabus Builder"
   |
   v
Tambah Modul Baru -> Tambah Materi (Lesson) / Kuis
   |
   v
Sistem Mengunggah Video/File ke Storage
   |
   v
Ubah Status Publikasi (is_published = true)
   |
   v
Katalog Muncul di Halaman Public B2C
   |
   v
         [End]
```
Diagram alur pembuatan silabus memberikan visualisasi atas pekerjaan seorang staf CMS yang menyusun materi secara *back-stage*. Publik sama sekali tidak akan menemui materi tersebut hingga sang *moderator* benar-benar merilis (*publish*) komponen draf ke sistem *live*.

3.8.5	Use Case Diagram
1. Use Case: Create & Edit Course/Webinar/Product
   Role: Moderator, Admin
   Deskripsi: Membuat penawaran barang dan edukasi digital baru untuk dilepas ke pasar (*Marketplace*).

2. Use Case: Build Syllabus & Quiz
   Role: Moderator, Admin
   Deskripsi: Menginjeksi urutan struktur video edukasi, modul, serta soal kuis komprehensif ke sebuah draf kelas.

3. Use Case: Manage Artikel & Mentor Profiles
   Role: Moderator, Admin
   Deskripsi: Mengunggah publikasi buletin blog serta mendaftarkan profil portofolio mentor.

3.9	Rancangan Administrasi Platform dan Monitoring (Modul 23, 24, 25, 26)
*(Catatan: Bagian ini merupakan interpretasi kontrol sentral dari Admin Panel berdasarkan diagram arsitektur awal)*

Modul fungsionalitas ini adalah jantung pengawasan atau konfigurasi tertinggi platform (Super Admin). Modul Administrasi memberikan akses penuh tak terbatas (lintas-*tenant*) guna menyelia ekosistem aplikasi agar tetap tertata. Operasionalitasnya mencakup pemblokiran/manajemen basis data seluruh *Users*, pendaftaran maupun perpanjangan secara manual (*bypass*) pada paket langganan *Schools*, konfigurasi pengaturan universal *website* (logo, teks kontak situs), hingga penyediaan grafik analitik tingkat dewa menyangkut statistik laba lintas instrumen B2C.

3.9.1	Wireframe
Tampilan yang berkaitan dengan fitur pengawasan ini meliputi:
- Dasbor Analitik & Grafik Statistik Utama (/admin/analytics)
- Halaman Manajemen Pengguna (/admin/users)
- Halaman Manajemen Tenant Sekolah (/admin/schools)
- Halaman Laporan & Analitik Keuangan Penjualan (/admin/analytics)
- Halaman Pengaturan Sistem (/admin/settings)

3.9.2	Entity Relationship Diagram (ERD)

**Entitas: users**
Atribut: name, email, role, is_active
Relasi:
- One-to-Many ke seluruh interaksi transaksional | Kata Relasi: "Melakukan Interaksi"

**Entitas: schools**
Atribut: name, npsn, subscription_plan, subscription_expires_at
Relasi:
- One-to-Many ke tabel SIAKAD | Kata Relasi: "Menaungi Tenant"

**Entitas: orders**
Atribut: order_number, total_amount, status
Relasi:
- Many-to-One ke entitas `users` | Kata Relasi: "Dibayar Oleh"

**Entitas: site_settings**
Atribut: key, value, description
Relasi:
- Independen | Kata Relasi: "Berdiri Sendiri (Tanpa FK)"

*Penjelasan Keseluruhan:*
Pada modul fungsi administrasi Super Admin, entitas di wilayah ini dikonstruksikan bukan untuk merajut kerangka relasi baru di dalam sistem, melainkan diperlakukan sebagai basis sumber data mentah (*raw source*) yang memiliki hak akses sentral melampaui sekat (*helicopter view*). Entitas tabel pusat `users` (keseluruhan basis pendaftar publik dan staf), `schools` (direktori institusi yang berlangganan), serta `orders` (catatan omset transaksi platform komersial) diperlakukan sebagai entitas tulang punggung yang senantiasa diawasi, diagregasi ke dalam grafik analitik *dashboard*, maupun di-intervensi manual atribut esensialnya bila diperlukan (seperti saat admin hendak memperpanjang masa *subscription* sekolah secara paksa dari *backend*). Di bagian tepi skema, terdapat entitas tunggal yang unik yakni `site_settings`. Tabel konfigurasi dengan pola penyimpanan *key-value* (JSON) ini tidak memiliki keterikatan relasional ke tabel mana pun. Datanya semata-mata diinjeksi secara bebas untuk menyuplai variabel dinamis lingkungan *frontend* (seperti konfigurasi teks SEO global, penggantian URL logo aplikasi, atau penyesuaian nomor kontak bantuan pelanggan).

3.9.3	Database Schema
- users
- schools
- orders
- site_settings

Tabel `users` dapat di-*query* secara universal tanpa memperdulikan `school_id`. Tabel `schools` pada instrumen ini diawasi terkhusus pada atribut kolom pemantauan paket seperti `subscription_plan` dan *timestamp* `subscription_expires_at` untuk mengecek kapan sewaktu-waktu sekolah kehilangan akses pro. Tabel parameter global `site_settings` memiliki format unik *key-value* (JSON) guna penyimpanan berbagai atribut global perihal aset dinamis.

3.9.4	Activity Diagram
```text
[Start]
   |
   v
Super Admin Login ke Admin Panel
   |
   v
Sistem Mengkalkulasi Data Global & Menampilkan Grafik
   |
   v
Buka Menu "Manajemen Institusi (Schools)"
   |
   v
Inspeksi Profil Salah Satu Sekolah (Tenant)
   |
   v
Lakukan Pembaruan Paket Langganan Manual
   |
   v
Simpan Konfigurasi Data (Update schools)
   |
   v
Sistem Membuka Kunci Limitasi Fitur pada SIAKAD Sekolah Tersebut
   |
   v
         [End]
```
Diagram skenario tersebut melambangkan betapa fleksibelnya level Super Admin. Admin berwenang mengubah atribut limitasi sebuah institusi secara mutlak (*bypass*) melalui instrumen operasionalnya (misalnya ketika transaksi eror dan perlu aktivasi Pro via *back-office*).

3.9.5	Use Case Diagram
1. Use Case: View Platform & Sales Analytics
   Role: Admin
   Deskripsi: Membaca rekapan agregat jumlah transaksi, lonjakan *user*, serta pertumbuhan sekolah (tenant) baru.

2. Use Case: Manage Users & Schools
   Role: Admin
   Deskripsi: Mencari, merombak detil privasi, serta mengaktivasi paket (*subscription*) langganan sekolah secara manual.

3. Use Case: Site Settings Configuration
   Role: Admin
   Deskripsi: Menentukan variabel publik seperti ikon grafis, logo layanan, *hyperlink* rujukan *footer*, dan *social media* situs.

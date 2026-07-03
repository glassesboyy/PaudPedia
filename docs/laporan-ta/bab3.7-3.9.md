> **Catatan Pembaruan Dokumen (Sinkronisasi Codebase):**
> Seluruh isi pada dokumen ini telah mengalami perubahan arsitektur dokumen dan pembaruan substansi. Bagian yang diubah secara masif meliputi:
> - **3.7.1 - 3.7.4** (Restrukturisasi urutan, penghapusan ERD, penambahan Class Diagram untuk Portal Informasi).
> - **3.8.1 - 3.8.4** (Restrukturisasi urutan, penghapusan ERD, penambahan Class Diagram untuk Manajemen Konten CMS).
> - **3.9.1 - 3.9.4** (Restrukturisasi urutan, penghapusan ERD, penambahan Class Diagram untuk Administrasi & Monitoring).
> 
> *Catatan ini dapat Anda hapus saat dokumen akan dicetak.*

---

3.7	Rancangan Portal Informasi dan Konten Statis (Modul 16, 17, 18)
Portal informasi merupakan lapisan terdepan atau antarmuka tamu (*Guest*) dari sisi aplikasi Public B2C. Portal ini menyajikan berbagai profil, informasi esensial, maupun konten statis layaknya *company profile* untuk menarik *leads* atau calon konsumen. Pada modul ini, sistem menampilkan artikel/blog edukasi, daftar profil ahli (*mentors*), serta ulasan dan kepuasan pelanggan (*testimonials*). Karena sifatnya yang statis dan bebas akses (publik), platform difokuskan pada pengoptimalan *Search Engine Optimization* (SEO) sehingga konten-konten tersebut sangat mudah diindeks oleh mesin pencari web.

3.7.1	Activity Diagram
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

3.7.2	Use Case Diagram
1. Use Case: View Landing Page & Static Content
   Role: Guest, User
   Deskripsi: Menjelajahi beranda utama, FAQ, dan kontak platform.

2. Use Case: Browse Artikel & Mentor
   Role: Guest, User
   Deskripsi: Mengakses literasi artikel blog, serta menelusuri daftar rekam jejak mentor yang terdaftar.

3.7.3	Class Diagram
Pemodelan *Object-Oriented* pada modul portal publik disusun lebih longgar karena sifat informasinya yang diperuntukkan bagi tamu (*Guest*) tanpa perlu proses otentikasi ketat. Sistem mengelola kolaborasi kelas-kelas berikut:
- **Class `User`**: Berfungsi secara *read-only* sebagai entitas identitas rujukan. Profil penulis/pembuat *Article* dan pemberi *Testimonial* direlasikan ke akun `User` agar nama asli mereka terekspos valid di halaman publik.
- **Class `Article`**: Merepresentasikan entitas berita atau blog edukasi. Memuat atribut seperti `title`, `slug` (ramah SEO), `content`, dan `published_at`. Secara struktural merelasikan kategorisasi bacaan via metode `category()` serta merujuk staf penulis melalui tautan `author()`.
- **Class `Mentor`**: Mengelola data rekam jejak guru. Properti biodatanya memuat `name`, `expertise` (bidang keahlian), `bio`, dan tautan gambar profil `photo_url`. Profil pengajar ini berstatus mandiri yang diintegrasikan silang ke entitas kelas melalui fungsi kolektif `courses()`.
- **Class `Testimonial`**: Menyimpan ulasan pengguna (`content`, `rating`). Kelas ini berfungsi mendukung antarmuka halaman utama agar interaktif, dilengkapi atribut kontrol moderasi `is_approved`.
- **Class `Category`**: Berfungsi sebagai unit pengelompokan (*taxonomy*) multi-tipe (untuk artikel, produk, dsb). Mengandalkan metode identifikasi properti `slug` sebagai kunci rujukan rute dinamis (URL) dalam web.

3.7.4	Wireframe
Tampilan yang berkaitan dengan fitur ini meliputi:
- Halaman Utama atau Landing Page (/)
- Halaman Artikel & Edukasi (/articles)
- Halaman Direktori Profil Mentor (/mentors)
- Halaman Tanya Jawab atau FAQ (/faq)
- Halaman Kontak & Bantuan (/contact)

3.8	Rancangan Manajemen Konten dan Katalog Item (Modul 19, 20, 21, 22)
Sistem Manajemen Konten (CMS) ini berada terisolasi pada area *Admin Panel* dan dijalankan sepenuhnya oleh struktur peranan *back-office* (Moderator/Admin). Modul ini bertanggung jawab atas kreasi dan pengunggahan produk baru ke pasar *marketplace* B2C (seperti draf kelas kursus, tautan *zoom* webinar, serta dokumen E-book). Fasilitas yang didukung tidak hanya terbatas pada formulir spesifikasi produk saja, melainkan juga menyokong pembuatan hierarki kurikulum LMS (pemecahan silabus menjadi modul dan *lessons*) dan sistem pembuatan kuis (*quiz builder*).

3.8.1	Activity Diagram
```text
[Start]
   |
   v
Moderator Login ke Admin Panel (CMS)
   |
   v
Buka Menu "Manajemen Kursus"
   |
   v
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

3.8.2	Use Case Diagram
1. Use Case: Create & Edit Course/Webinar/Product
   Role: Moderator, Admin
   Deskripsi: Membuat penawaran barang dan edukasi digital baru untuk dilepas ke pasar (*Marketplace*).

2. Use Case: Build Syllabus & Quiz
   Role: Moderator, Admin
   Deskripsi: Menginjeksi urutan struktur video edukasi, modul, serta soal kuis komprehensif ke sebuah draf kelas.

3. Use Case: Manage Artikel & Mentor Profiles
   Role: Moderator, Admin
   Deskripsi: Mengunggah publikasi buletin blog serta mendaftarkan profil portofolio mentor.

3.8.3	Class Diagram
Area operasional Manajemen Konten (*Content Management*) mengorkestrasi kendali terhadap berbagai instansiasi penawaran digital dan entitas pendukung perakitannya:
- **Class Katalog Dasar (`Product`, `Webinar`)**: Direpresentasikan untuk manajemen harga dan kontrol properti publikasi (lewat atribut boolean `is_published`). Model `Webinar` mencakup penambahan kolom fungsional acara *live* seperti `zoom_link`, `scheduled_at`, dan penetapan `max_participants`.
- **Class Komposisi Kursus (`Course`, `Module`, `Lesson`)**: Membangun relasi konstruksi bertumpuk. Objek sentral `Course` membawahi koleksi bab (metode `modules()`). Setiap instansi *Module* menyimpan satuan bacaan statis `Lesson` yang diperkaya tautan media fisik (storage file materi).
- **Class Evaluasi (`Quiz`, `QuizQuestion`, `QuizAnswer`)**: Didesain merajut skema pengujian bersarang (nested). Kelas `Quiz` melampirkan himpunan baris `QuizQuestion` yang berisi konteks tanya jawab, di mana validasinya diproyeksikan (di-relasikan) pada ragam *multiple choice* di `QuizAnswer` berbekal penanda boolean `is_correct`.
- **Class `Category`**: Berperan sebagai entitas taksonomi payung yang membantu mengelompokkan `Course`, `Webinar`, dan `Product` agar mudah dicari di *Marketplace*.
- **Class `Mentor`**: Menyediakan relasi portofolio pemateri. Katalog `Course` maupun `Webinar` masing-masing memegang atribut rujukan ke objek `Mentor` untuk memunculkan foto dan spesialisasi sang pembicara.

3.8.4	Wireframe
Tampilan yang berkaitan dengan fitur ini meliputi:
- Dasbor Manajemen Konten Kursus (Admin Panel) (/admin)
- Formulir Buat/Edit Produk Digital (/admin/products/create)
- Formulir Buat/Edit Kursus (/admin/courses/builder)
- Formulir Buat/Edit Webinar (/admin/webinars/create)
- Formulir Buat/Edit Artikel (/admin/articles/create)

3.9	Rancangan Administrasi Platform dan Monitoring (Modul 23, 24, 25, 26)
*(Catatan: Bagian ini merupakan interpretasi kontrol sentral dari Admin Panel berdasarkan diagram arsitektur awal)*

Modul fungsionalitas ini adalah jantung pengawasan atau konfigurasi tertinggi platform (Super Admin). Modul Administrasi memberikan akses penuh tak terbatas (lintas-*tenant*) guna menyelia ekosistem aplikasi agar tetap tertata. Operasionalitasnya mencakup pemblokiran/manajemen basis data seluruh *Users*, pendaftaran maupun perpanjangan secara manual (*bypass*) pada paket langganan *Schools*, konfigurasi pengaturan universal *website* (logo, teks kontak situs), hingga penyediaan grafik analitik tingkat dewa menyangkut statistik laba lintas instrumen B2C.

3.9.1	Activity Diagram
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

3.9.2	Use Case Diagram
1. Use Case: View Platform & Sales Analytics
   Role: Admin
   Deskripsi: Membaca rekapan agregat jumlah transaksi, lonjakan *user*, serta pertumbuhan sekolah (tenant) baru.

2. Use Case: Manage Users & Schools
   Role: Admin
   Deskripsi: Mencari, merombak detil privasi, serta mengaktivasi paket (*subscription*) langganan sekolah secara manual.

3. Use Case: Site Settings Configuration
   Role: Admin
   Deskripsi: Menentukan variabel publik seperti ikon grafis, logo layanan, *hyperlink* rujukan *footer*, dan *social media* situs.

3.9.3	Class Diagram
Berbeda dengan modul operasional lain, *Class Diagram* pada wilayah administrasi puncak ini memosisikan entitas basis data sebagai target inspeksi global dan injeksi dinamis:
- **Class `User`**: Diinspeksi untuk agregasi analitik jumlah pendaftar, manajemen pembatasan (*is_active*), maupun kontrol perombakan level *role* admin lintas-*tenant*.
- **Class `School`**: Model ini dipantau secara *helicopter-view* pada fungsi-fungsi penentuan status `subscription_plan`. Admin bisa melakukan intervensi paksa tenggat waktu di atribut `subscription_ended_at` untuk mengatasi kendala tagihan aktivasi manual.
- **Class `Order`**: Digunakan untuk agregasi laporan penjualan. Admin membaca koleksi riwayat pemasukan global B2C demi kalkulasi grafik omset tanpa merusak integritas log transaksi milik pelanggan individual.
- **Class `SiteSetting`**: Objek singular terisolasi yang diimplementasikan khusus memegang *record* parameter tata letak dinamis global *website* yang bertumpu pada format penyimpanan *key-value*. Kelas ini dioperasikan merdeka (*independent*) tanpa mengandalkan skema *Foreign Key*, berfungsi menampung injeksi atribut *frontend* tanpa batas (contoh: SEO tag, nomor telepon yayasan, maupun logo tajuk halaman utama).

3.9.4	Wireframe
Tampilan yang berkaitan dengan fitur pengawasan ini meliputi:
- Dasbor Analitik & Grafik Statistik Utama (/admin/analytics)
- Halaman Manajemen Pengguna (/admin/users)
- Halaman Manajemen Tenant Sekolah (/admin/schools)
- Halaman Laporan & Analitik Keuangan Penjualan (/admin/analytics)
- Halaman Pengaturan Sistem (/admin/settings)

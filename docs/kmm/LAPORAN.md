# LAPORAN KEGIATAN KULIAH MAGANG MAHASISWA (KMM)

## KATA PENGANTAR

Puji syukur kehadirat Tuhan Yang Maha Esa atas segala rahmat dan karunia-Nya, sehingga penulis dapat menyelesaikan laporan Kegiatan Kuliah Magang Mahasiswa (KMM) ini dengan baik dan tepat waktu. Laporan ini disusun sebagai salah satu syarat akademis dan bentuk pertanggungjawaban atas pelaksanaan kegiatan magang yang telah dilakukan.

Kegiatan Kuliah Magang Mahasiswa ini dilaksanakan selama kurang lebih satu semester (berakhir pada April 2026) di CV Webina Naramedia, sebuah institusi mitra yang bergerak di bidang Teknologi, Informasi, dan Media. Melalui kegiatan ini, penulis mendapatkan kesempatan berharga untuk terjun langsung ke dunia industri perangkat lunak, khususnya dalam pengembangan platform edukasi berbasis SaaS (Software as a Service).

Penulis menyadari bahwa penyusunan laporan dan pelaksanaan kegiatan magang ini tidak terlepas dari dukungan, bimbingan, dan bantuan dari berbagai pihak. Oleh karena itu, pada kesempatan ini penulis mengucapkan terima kasih yang sebesar-besarnya kepada:
1. Bapak/Ibu dosen pembimbing yang telah memberikan arahan dan masukan selama masa persiapan hingga penyelesaian laporan magang ini.
2. Pimpinan dan segenap tim CV Webina Naramedia yang telah memberikan kesempatan, bimbingan teknis, serta pengalaman berharga di lingkungan kerja profesional.
3. Seluruh pihak yang tidak dapat disebutkan satu per satu, yang telah memberikan dukungan moral maupun material.

Penulis berharap laporan ini dapat memberikan manfaat, baik bagi pengembangan ilmu pengetahuan di institusi pendidikan, maupun sebagai referensi bagi pengembangan sistem serupa di masa mendatang.

---

## A. PENDAHULUAN

### 1. Latar Belakang
Perkembangan teknologi informasi yang pesat menuntut mahasiswa program studi terkait teknologi untuk tidak hanya menguasai teori akademis, tetapi juga memiliki pengalaman praktis di dunia industri. Kegiatan Kuliah Magang Mahasiswa (KMM) merupakan wadah yang tepat untuk menjembatani kesenjangan antara kurikulum pendidikan dan kebutuhan industri. Melalui kegiatan ini, mahasiswa ditantang untuk menerapkan ilmu rekayasa perangkat lunak dalam menyelesaikan permasalahan dunia nyata, beradaptasi dengan lingkungan kerja profesional, dan berkolaborasi dalam tim pengembang.

### 2. Alasan Memilih Institusi Mitra
CV Webina Naramedia dipilih sebagai institusi mitra karena memiliki rekam jejak inovatif dalam pengembangan aset digital terintegrasi dan solusi teknologi (SaaS). Fokus perusahaan pada penciptaan platform yang dapat memecahkan masalah pasar nyata, khususnya di sektor pendidikan dan media, sangat relevan dengan minat penulis dalam pengembangan arsitektur perangkat lunak skala besar.

### 3. Ketertarikan Bidang
Penulis memiliki ketertarikan yang besar terhadap pengembangan *full-stack web application* menggunakan arsitektur modern seperti arsitektur *Service Layer* dan pendekatan *Modular Domain-Driven Design*. Mengembangkan sistem multi-tenant yang aman dan sistem pembelajaran elektronik (e-learning) terpadu memberikan tantangan intelektual sekaligus wadah untuk mendalami implementasi *framework* terkini.

### 4. Tujuan Kegiatan
**Tujuan Umum:**
Memperoleh pengalaman kerja praktis di industri teknologi informasi serta memahami alur pengembangan perangkat lunak secara profesional, mulai dari analisis kebutuhan hingga implementasi sistem.

**Tujuan Khusus:**
1. Menganalisis, merancang, dan mengimplementasikan arsitektur sistem PaudPedia yang mengintegrasikan SIAKAD (Sistem Informasi Akademik) Multi-Tenant dan platform Public E-Learning & Marketplace.
2. Mengembangkan logika sistem yang kompleks, seperti isolasi data multi-tenant (RLS), sistem keranjang belanja (e-commerce), dan manajemen konten dinamis (kuis dan sertifikat).
3. Menerapkan best practice dalam penulisan kode dan standardisasi arsitektur baik pada backend (Laravel) maupun frontend (Nuxt.js dan Vue.js).

### 5. Manfaat Kegiatan

**Bagi Mahasiswa:**
1. **Pengembangan Kompetensi Teknis:** Meningkatkan keterampilan dalam implementasi *tech stack* modern (Laravel 12, Nuxt 4, Vue 3) dan pemahaman mendalam tentang arsitektur perangkat lunak skala industri.
2. **Pengalaman Kerja Profesional:** Memberikan wawasan nyata mengenai etika kerja, kolaborasi tim pengembang, dan siklus pengembangan produk (SDLC) dalam lingkungan startup/agensi digital.
3. **Penyusunan Portofolio:** Menghasilkan bukti karya nyata berupa platform PaudPedia yang dapat digunakan sebagai nilai tambah dalam persiapan karier profesional.

**Bagi Institusi Pendidikan:**
1. **Penyelarasan Kurikulum:** Memberikan umpan balik mengenai relevansi materi perkuliahan dengan kebutuhan terkini di industri teknologi informasi.
2. **Kemitraan Strategis:** Memperkuat hubungan kerja sama antara universitas dengan sektor industri untuk program magang dan penempatan kerja di masa depan.
3. **Peningkatan Akreditasi:** Menambah catatan rekam jejak kolaborasi eksternal yang mendukung standarisasi kualitas lulusan.

**Bagi Mitra Magang (Institusi Mitra):**
1. **Akselerasi Pengembangan Produk:** Mendapatkan bantuan teknis dalam percepatan implementasi fitur-fitur pada platform PaudPedia (SIAKAD & LMS).
2. **Inovasi dan Riset:** Memperoleh perspektif baru dan solusi kreatif dari mahasiswa dalam menghadapi tantangan teknis pengembangan sistem.
3. **Efisiensi Sumber Daya:** Membangun jalur rekrutmen potensial dengan mengidentifikasi talenta muda yang telah memahami budaya dan alur kerja perusahaan.

---

## B. TATA PELAKSANAAN KEGIATAN

### 1. Waktu dan Tempat Pelaksanaan
Kegiatan Kuliah Magang Mahasiswa ini dilaksanakan di CV Webina Naramedia yang berkantor pusat di Jakarta Selatan, DKI Jakarta. Pelaksanaan magang dilakukan secara terstruktur sesuai dengan jam kerja operasional institusi selama periode yang telah ditentukan oleh pihak kampus (selesai pada April 2026).

### 2. Tata Laksana Kegiatan
Aktivitas utama selama magang berfokus pada pengembangan produk utama CV Webina Naramedia yang disebut "PaudPedia", yaitu sebuah platform *dual-purpose* yang menggabungkan SIAKAD (B2B) untuk sekolah Pendidikan Anak Usia Dini (PAUD) dan Public Marketplace (B2C).

Peran dan tanggung jawab penulis meliputi:
- Menganalisis spesifikasi kebutuhan sistem bersama pemangku kepentingan dan menerjemahkannya ke dalam desain arsitektur basis data serta diagram kelas.
- Mengembangkan modul backend menggunakan Laravel 12 dengan menerapkan arsitektur *Service Layer Pattern*.
- Berkolaborasi dalam integrasi sistem antara API Laravel dan antarmuka Nuxt.js 4 untuk portal publik, serta Vue.js 3 untuk portal manajemen sekolah multi-tenant.
- Mengaudit dan menyinkronkan seluruh dokumentasi teknis, memastikan kesesuaian antara implementasi kode (*as-is codebase*) dengan *Source of Truth* dokumen.

### 3. Metode Identifikasi Masalah
Dalam menjalankan tugas pengembangan sistem, penulis menggunakan beberapa metode identifikasi masalah, yaitu:
- **Observasi:** Mengamati alur operasional pengelolaan data PAUD konvensional yang masih bersifat manual dan rentan kesalahan.
- **Wawancara:** Melakukan diskusi dengan mentor industri mengenai ekspektasi terhadap integrasi sistem e-commerce (Midtrans) ke dalam ekosistem platform edukasi.
- **Studi Dokumentasi:** Mengkaji berkas kebutuhan produk (PRD), requirement, dan arsitektur yang sudah ada untuk menemukan *gap* implementasi fitur terbaru seperti modul e-learning dan sistem keranjang belanja.

---

## C. HASIL KEGIATAN DAN PEMBAHASAN

### a. Profil Institusi Mitra
CV Webina Naramedia merupakan perusahaan yang bergerak di bidang Teknologi, Informasi, dan Media, didirikan pada tahun 2025 dan berpusat di Jakarta Selatan. Perusahaan ini memiliki visi untuk menjadi pengelola aset digital terintegrasi yang menggabungkan kekuatan media, teknologi, dan layanan kreatif demi menciptakan arus pendapatan pasif melalui media dan model bisnis SaaS (Software as a Service), serta pendapatan aktif melalui agensi. Misinya mencakup akuisisi dan pengembangan aset media digital, penciptaan solusi SaaS yang memecahkan masalah pasar nyata, serta menyediakan wadah profesional bagi talenta digital.

### b. Hasil Kegiatan dan Pembahasan

#### 1. Deskripsi Produk
Produk yang dibangun selama masa magang adalah **Platform PaudPedia**. Platform ini adalah solusi SaaS Dual-Platform yang mengintegrasikan:
- **SIAKAD Multi-Tenant (B2B):** Sistem manajemen administrasi PAUD yang memungkinkan pengaturan multi-sekolah. Platform ini memberdayakan Kepala Sekolah dan Guru untuk mengelola data operasional (kelas, absensi, penilaian, keuangan) dan memungkinkan orang tua untuk memantau perkembangan anaknya melalui fitur *Parent Monitoring*.
- **Public E-Learning & Marketplace (B2C):** Portal bagi pengguna publik (guest dan pengguna terdaftar) untuk membeli dan mengakses webinar, kursus edukasi bersertifikat, dan produk digital, serta membaca artikel.

Sistem ini diciptakan untuk menyelesaikan dua permasalahan utama. Pertama, inefisiensi administratif di banyak sekolah PAUD yang masih menggunakan dokumentasi fisik. Kedua, minimnya akses bagi orang tua ke sumber edukasi dan media pengasuhan anak yang berkualitas, mudah diakses, dan terpusat dalam satu platform terintegrasi.

#### 2. Fungsional Produk
Berdasarkan dokumen *Product Requirements Document* (PRD) yang telah direalisasikan, berikut adalah rincian tingkat makro mengenai *Functional Requirements* sistem PaudPedia:

| ID | Modul Sistem | Fungsionalitas Utama | Peran (*Role*) |
|---|---|---|---|
| FR-01 | Manajemen Multi-Tenant | Registrasi institusi, pengelolaan langganan (Gratis/Pro), manajemen siswa dan wali murid. | Headmaster |
| FR-02 | Operasional SIAKAD | Input dan pantauan kehadiran, asesmen/penilaian siswa dengan standar PAUD, dan manajemen kelas. | Teacher, Headmaster |
| FR-03 | Parent Portal | Antarmuka monitoring absensi, penilaian kognitif/motorik, riwayat pembayaran, dan laporan rapor PDF anak. | Parent |
| FR-04 | E-Commerce Publik | Browsing katalog produk, pengelolaan keranjang belanja (*cart*), checkout multi-item, validasi kode promo, dan integrasi Midtrans. | User, Guest |
| FR-05 | LMS (E-Learning) | Akses pemutar video, dokumen PDF, sistem penilaian kuis terotomatisasi (*auto-grading*), pelacakan progres, dan unduh sertifikat. | User |
| FR-06 | Content Management | Operasi CRUD (Create, Read, Update, Delete) komprehensif atas Webinar, Kursus, Modul, Kuis, Produk Digital, dan Artikel blog. | Moderator, Admin |
| FR-07 | Analytics & Settings | Laporan ringkasan penjualan, data lintas institusi (cross-tenant), serta pengaturan situs dan portal global. | Admin |

#### 3. Lingkungan Operasi
Sistem PaudPedia dirancang dan dijalankan di dalam lingkungan operasional berteknologi modern:
- **Backend:** Framework PHP Laravel 12 yang dieksekusi menggunakan web server berkinerja tinggi, memanfaatkan *Service Layer Pattern* untuk isolasi bisnis logika.
- **Frontend Publik:** Framework Nuxt.js 4 (berbasis Vue 3) dengan pendekatan hybrid (SSR dan CSR) guna mengoptimalkan pencarian (SEO) untuk artikel dan katalog kursus.
- **Frontend SIAKAD:** Framework Vue.js 3 dikonfigurasi melalui *Vite builder* dengan arsitektur modular berbasis fitur (*Feature-Based Architecture*).
- **Admin Panel:** Menggunakan Laravel Filament v5.
- **Database:** MySQL 8.0, difungsikan dengan skema RDBMS kompleks, memfasilitasi arsitektur multi-tenant dengan menggunakan *foreign key* `school_id` sebagai batasan (RLS) serta *polymorphic relationships* pada modul e-commerce.

#### 4. Rancangan Produk
Desain produk dipetakan ke dalam beberapa abstraksi sistem secara naratif sebagai berikut:

**Use Case Terintegrasi:**
Sistem mengakomodasi 7 (tujuh) jenis *roles* (Guest, User, Parent, Teacher, Headmaster, Moderator, dan Admin). Sebagai contoh, alur Use Case *Parent Monitoring* mengharuskan peran *Parent* untuk dapat masuk (*login*) melalui email yang dikelola terpusat oleh *Headmaster*. *Parent* secara eksklusif dapat membaca grafik absensi, rekap nilai (asesmen), dan laporan keuangan dari satu atau beberapa anak yang terdaftar secara mandiri (karena ketiadaan sistem *login* untuk siswa balita). Di sisi platform publik, seorang *User* dapat menjelajahi kursus (*Browse Course*), menambahkannya ke keranjang belanja (*Add to Cart*), dan melakukan konfirmasi pesanan (*Checkout*) untuk mendapatkan akses ke dalam sistem Learning Management System.

**Class Diagram Naratif:**
Secara struktural, arsitektur *Class Diagram* dibagi ke dalam 3 domain utama:
1. *Multi-Tenant Domain*: Mendefinisikan relasi antara entitas `User`, `School`, dan ekstensi peran `SchoolMember`. Satu kelas sekolah dipimpin oleh seorang `Teacher` dengan banyak entitas `Student` (yang terhubung ke `ParentProfile`).
2. *Content Management Domain*: Entitas hierarkis dari `Course` yang memiliki banyak `Module`, serta `Lesson` dan `Quiz`. Modul penyelesaian pembelajaran dicatat oleh entitas `CourseEnrollment` dan `QuizAttempt` guna menentukan validasi sertifikasi kelulusan.
3. *Commerce Domain*: Keranjang belanja representatif di `Cart` memiliki banyak entitas `CartItem` polimorfik. Saat transisi dilakukan, item akan dimutasikan menjadi `Order` dan `OrderItem` untuk pencatatan historikal.

**ERD (Entity Relationship Diagram) Naratif:**
Skema basis data (*Database Schema*) mengimplementasikan rancangan normalisasi di atas 30 tabel. Implementasi kunci meliputi polimorfisme pada tabel `cart_items` dan `order_items` (`item_type` merujuk ke model Webinar, Course, atau Product) untuk efisiensi penyimpanan (*snapshot pattern*). Untuk isolasi data multi-tenant, tabel SIAKAD seperti `attendances`, `assessments`, dan `finances` diwajibkan memiliki rujukan langsung ke `school_id`, sehingga transaksi manipulasi data antar sekolah tidak saling bertabrakan pada lapisan API.

#### 5. Design Interface
Sistem antarmuka didesain mengikuti prinsip *Atomic Design* yang berorientasi pada kemudahan pengalaman pengguna. Desain *dashboard* mengusung navigasi panel yang intuitif dengan estetika bersih dan representasi data visual. 
*(Catatan: Gambar Wireframe dan Mockup antarmuka pengguna akan dilampirkan secara terpisah di bagian lampiran dokumen formal).*

#### 6. Pembahasan
**Alur Kerja Sistem (System Workflow):**
Secara holistik, alur kerja sistem PaudPedia dibagi menjadi dua domain operasional utama yang saling terintegrasi:

1. **Alur Operasional SIAKAD (B2B):**
   - **Onboarding:** Kepala Sekolah mendaftarkan sekolah (Free/Pro) dan mendapatkan akses dashboard manajemen.
   - **Manajemen Data:** Kepala Sekolah menginput data Guru, Kelas, dan Siswa (yang terhubung ke profil Orang Tua).
   - **Aktivitas Harian:** Guru melakukan input absensi harian dan penilaian perkembangan siswa berdasarkan kategori PAUD.
   - **Pelaporan:** Sistem mengakumulasi data tersebut menjadi rekapitulasi kehadiran dan penilaian yang dapat dipantau oleh Orang Tua secara real-time, serta diunduh dalam bentuk laporan rapor PDF (khusus paket Pro).

2. **Alur Marketplace & E-Learning (B2C):**
   - **E-Commerce:** Pengguna publik menjelajahi katalog webinar, kursus, dan produk digital. Pengguna dapat menambahkan item ke keranjang belanja (*Cart*) dan melakukan proses *Checkout*.
   - **Transaksi:** Pembayaran diproses melalui *gateway* Midtrans. Setelah status pembayaran divalidasi menjadi "Paid", sistem secara otomatis memberikan akses ke item yang dibeli.
   - **Pembelajaran (LMS):** Pengguna mengakses materi video/PDF di dalam portal belajar. Pada akhir materi, pengguna mengikuti kuis evaluasi. Jika kuis diselesaikan dengan nilai tuntas, sistem secara otomatis menerbitkan sertifikat digital yang dapat diunduh oleh pengguna.

Integrasi kedua alur ini memastikan PaudPedia berfungsi sebagai ekosistem digital yang utuh, mulai dari manajemen internal sekolah hingga penyediaan konten edukasi eksternal bagi masyarakat luas.

**Permasalahan dan Solusi:**
Salah satu permasalahan arsitektural yang ditemukan selama pengerjaan proyek adalah potensi duplikasi logika bisnis saat dihadapkan pada antarmuka ganda (SIAKAD Vue.js vs Nuxt.js Publik). Solusi konseptual yang penulis implementasikan adalah melalui arsitektur *Service Layer* (*Thin Controller, Fat Service*) pada backend Laravel. Layanan sentral seperti `CheckoutService` dan `LmsService` memusatkan perhitungan keranjang, logika kuis, dan pembuatan sertifikat tanpa menaruhnya di dalam layer Controller. 

Secara teoritis, hal ini sejalan dengan prinsip *Domain-Driven Design* (DDD) dan prinsip SOLID (khususnya *Single Responsibility Principle*), di mana pemisahan lapisan (layer separation) antara pengontrol data dan logika domain menjamin *codebase* dapat dengan mudah diperbarui, ditingkatkan skalabilitasnya, serta ditangani oleh banyak pengembang di masa depan tanpa menciptakan efek regresi silang.

---

## D. KESIMPULAN DAN SARAN

### a. Kesimpulan
Kegiatan Kuliah Magang Mahasiswa di CV Webina Naramedia telah memberikan capaian yang sangat positif bagi penulis. Proyek pengembangan Platform PaudPedia membuktikan kelayakan implementasi perangkat lunak berbasis SaaS *Dual-Platform* yang responsif, stabil, dan terukur. Selama kegiatan ini, penulis berhasil merancang dan merealisasikan komponen inti mulai dari manajemen multi-tenant, arsitektur basis data relasional kompleks, sistem transaksi polimorfik, hingga perancangan dokumentasi teknis yang mendalam. Pencapaian ini membawa dampak signifikan terhadap peningkatan kompetensi teknis di bidang *Software Engineering*, pemahaman akan siklus pengembangan produk (SDLC), serta internalisasi etos kerja profesional dalam lingkungan teknologi masa kini.

### b. Saran
- **Untuk Pengembangan Sistem:** Mengingat arsitektur saat ini telah menerapkan modul kuis dan kelas LMS dasar, pengembangan selanjutnya disarankan untuk menyertakan elemen analitik prediktif terkait progres belajar, atau otomasi deteksi dini berbasis *Machine Learning* bagi guru guna mengevaluasi hasil asesmen kognitif anak usia dini.
- **Untuk Institusi Mitra:** Disarankan untuk terus mengekspansi kolaborasi dengan institusi pendidikan guna mendorong inovasi produk digital yang semakin solutif terhadap kebutuhan administrasi sekolah PAUD di berbagai wilayah di Indonesia.
- **Untuk Mahasiswa Selanjutnya:** Mahasiswa yang akan melaksanakan program magang di masa depan sangat disarankan untuk membekali diri dengan dasar pemahaman pola desain (Design Patterns), prinsip arsitektur modular, dan praktik penulisan kode bersih (*Clean Code*) agar dapat beradaptasi secara optimal dengan skala proyek berskala industri.

# BAB 5 PENGUJIAN

## 5.1 Metode Pengujian
Pengujian sistem dilakukan untuk memastikan bahwa seluruh fitur, alur kerja, dan proses bisnis pada platform layanan Software as a Service (SaaS) PAUD ini dapat berjalan sesuai dengan spesifikasi rancangan arsitektur yang telah dibangun. Pengujian diimplementasikan pada fungsi-fungsi utama aplikasi, ketepatan pertukaran data antar-modul, serta keakuratan validasi aturan bisnis yang ditanamkan pada sistem manajemen akademik, tata kelola keuangan, marketplace, hingga Learning Management System (LMS).

Metode pengujian yang digunakan dalam penelitian ini sepenuhnya dilakukan melalui pendekatan Pengujian Fungsional (*Functional Testing*) berbasis simulasi langsung pada antarmuka pengguna (*User Interface*). Pengujian fungsional ini mengevaluasi sistem dari sudut pandang pengguna akhir (*end-user*) dengan memberikan berbagai variasi masukan (*input*) yang wajar maupun tidak wajar, kemudian mengamati respons keluaran (*output*), perubahan status data, dan pesan validasi sistem pada layar aplikasi (Howden, 1980).

Pengujian fungsional dalam penelitian ini mencakup tiga aspek evaluasi terpadu dalam satu tahapan pengujian yang komprehensif:

1. **Validasi Interaksi Fitur Antarmuka**: Memeriksa apakah setiap tombol, navigasi, formulir pengisian data (seperti formulir pembuatan rombel, pengisian biodata siswa oleh Operator Sekolah, atau penilaian observasi oleh Guru), serta respons visual dari sistem telah beroperasi normal tanpa menghasilkan error.
2. **Validasi Alur Integrasi Lintas Modul**: Memeriksa sinkronisasi aliran data antar-halaman dari hulu ke hilir (*end-to-end*). Misalnya, menguji bagaimana pendaftaran ruang kerja (*tenant*) sekolah baru langsung membentuk isolasi data, bagaimana konfirmasi pelunasan transaksi marketplace otomatis membuka gembok kelas di LMS, atau bagaimana pencatatan tabungan oleh Operator langsung memperbarui saldo di dasbor Orang Tua (Leotta et al., 2016).
3. **Validasi Aturan Logika Bisnis (*Business Rule Validation*)**: Memeriksa keandalan sistem dalam menolak tindakan pengguna yang melanggar ketentuan batas sistem atau logika institusi (Dietrich & Paschke, 2005). Contohnya adalah pencegahan input nilai diskon agar harga barang tidak menjadi negatif, serta pemblokiran pencetakan sertifikat jika progres materi LMS belum mencapai 100%.

Dengan menyatukan ketiga aspek evaluasi di atas ke dalam metode Pengujian Fungsional, hasil pengujian dapat memberikan gambaran empiris yang nyata bahwa perangkat lunak yang dibangun telah matang, aman, dan siap dioperasikan pada ekosistem pendidikan anak usia dini sesungguhnya.

## 5.2 Lingkungan Pengujian
Lingkungan pengujian merupakan kondisi ekosistem perangkat keras (*hardware*) dan perangkat lunak (*software*) terisolasi yang diatur sedemikian rupa untuk menjalankan, mengkompilasi, dan menguji aplikasi. Tahapan pengujian ini diinisialisasi sepenuhnya pada lingkungan lokal (*local development environment*) untuk memberikan ruang yang aman bagi pengembang guna melakukan eksperimen, mencari kutu program (*debugging*), dan memvalidasi keseluruhan kualitas aplikasi sebelum versi finalnya disalurkan (*deploy*) secara terbuka.

### 5.2.1 Perangkat Keras
Spesifikasi perangkat keras yang digunakan untuk mendukung proses pengembangan dan pengujian sistem disajikan pada Tabel 10.

**Tabel 10 Data Perangkat Keras**

| Komponen | Spesifikasi |
| :--- | :--- |
| Processor | 12th Gen Intel(R) Core(TM) i5-12500H, 2500 Mhz, 12 Core(s), 16 Logical Processor(s) |
| Memory (RAM) | 16 GB |
| Storage | 512 GB |
| GPU | NVIDIA GeForce GTX 1650 |
| Operating System | Microsoft Windows 11 |

### 5.2.2 Perangkat Lunak
Perangkat lunak yang digunakan untuk mendukung proses penulisan kode sumber, pengembangan, dan pengujian sistem disajikan pada Tabel 11.

**Tabel 11 Data Perangkat Lunak**

| Perangkat Lunak | Keterangan |
| :--- | :--- |
| Laravel 12 | Web framework & REST API |
| Laravel Sanctum | Autentikasi API |
| Laravel Scramble | Dokumentasi API |
| Laravel Spatie | Role base access control (RBAC) |
| Eloquent ORM | Abstraksi database |
| Laravel Filament | Admin & Moderator panel builder |
| MySQL 8.0 | Database |
| Laravel Queue | Async job processing |
| Laravel DomPDF | PDF generation |
| Midtrans SDK | Payment gateway |
| Nuxt.js | Public site & LMS |
| Vue | SIAKAD dashboard |
| Vite | Build tool |
| TailwindCSS | CSS framework |

### 5.2.3 Environment Pengujian
Pengujian operasional sistem dijalankan murni di atas lingkungan lokal (*local environment*). Arsitektur perancangan diatur dengan memisahkan area tugas antara layanan backend dan frontend. Aplikasi antarmuka sisi publik dan manajemen sisi pengguna berjalan secara mandiri, sementara pangkalan peladen API utama menggunakan kerangka kerja Laravel (PHP) yang menjadi otak pemrosesan basis data terpusatnya. Komunikasi lintas layanan di antara kedua sisi ini dijembatani menggunakan standar pertukaran data REST API (Masse, 2011).

### 5.2.4 Material Pengujian
Material pengujian merepresentasikan kerangka modul-modul sistem utama yang disorot dan diawasi kinerjanya selama fase pengujian berlangsung. Sesuai dengan spesifikasi arsitektur yang dirancang pada bab-bab sebelumnya, ruang lingkup pengujian dipadatkan ke dalam tiga pilar kelompok modul utama, berikut:

1. **Sistem Manajemen Akademik**
   Meliputi pengujian alur pendaftaran multi-school (pembuatan tenant), penataan kelengkapan data induk siswa dan wali, hingga simulasi pemrosesan asesmen bulanan dan rekapitulasi rapor akhir PAUD.
2. **E-Learning & Marketplace**
   Meliputi serangkaian pengujian simulasi transaksi komersial kelas online publik, pengecekan pemutaran video materi di ruang Learning Management System (LMS), pengerjaan kuis, dan alur penerbitan sertifikat.
3. **Administrasi Portal & Admin**
   Meliputi verifikasi modul pengaturan tata letak konten statis portal publik (laman artikel, FAQ), pembatasan delegasi hak akses kepemimpinan instansi, serta pemonitoran metrik global khusus bagi level hierarki Admin.

Setiap gugusan komponen aplikasi di atas menjalani proses validasi operasional yang menyeluruh guna meminimalisasi potensi celah anomali komputasi (*bug*) yang sanggup mengganggu ketertiban rantai proses kegiatan para pengguna di dunia nyata.

### 5.2.5 Pelaku Pengujian
Seluruh proses pelaksanaan skenario pengujian fungsional dan teknis ini dieksekusi secara mandiri oleh penulis yang bertindak langsung sebagai perancang sekaligus pengembang sistem (*System Developer*). Pemusatan peranan penguji ini ditujukan untuk mengakselerasi penemuan kesalahan logika pemrograman, karena penulis memegang pemahaman yang paling holistik mengenai seluk-beluk pemetaan data hingga arsitektur lapisan keamanan dari aplikasi yang dibangun.

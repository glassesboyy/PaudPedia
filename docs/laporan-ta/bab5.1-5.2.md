# BAB 5	PENGUJIAN

## 5.1 Metode Pengujian
Pengujian sistem dilakukan untuk memastikan bahwa seluruh fitur, alur kerja, dan proses bisnis pada platform layanan *Software as a Service* (SaaS) PAUD ini dapat berjalan sesuai dengan spesifikasi rancangan arsitektur yang telah dibangun. Pengujian diimplementasikan pada fungsi-fungsi utama aplikasi, ketepatan pertukaran data antar-modul, serta keakuratan validasi aturan bisnis yang ditanamkan pada sistem manajemen akademik, tata kelola keuangan, *marketplace*, hingga *Learning Management System* (LMS).

Metode pengujian yang digunakan dalam penelitian ini sepenuhnya dilakukan melalui pendekatan **Pengujian Fungsional (*Functional Testing*)** berbasis simulasi langsung pada antarmuka pengguna (*User Interface*), tanpa melakukan pengujian otomatis berbasis kode (*Automated Code Testing*). Pengujian fungsional ini mengevaluasi sistem dari sudut pandang pengguna akhir (*end-user*) dengan memberikan berbagai variasi masukan (*input*) yang wajar maupun tidak wajar, kemudian mengamati respons keluaran (*output*), perubahan status data, dan pesan validasi sistem pada layar aplikasi.

Pengujian fungsional dalam penelitian ini mencakup tiga aspek evaluasi terpadu dalam satu tahapan pengujian yang komprehensif:
1. **Validasi Interaksi Fitur Antarmuka:** Memeriksa apakah setiap tombol, navigasi, formulir pengisian data (seperti formulir pembuatan rombel, pengisian biodata siswa oleh Operator Sekolah, atau penilaian observasi oleh Guru), serta respons visual dari sistem telah beroperasi normal tanpa menghasilkan galat (*error*).
2. **Validasi Alur Integrasi Lintas Modul:** Memeriksa sinkronisasi aliran data antar-halaman dari hulu ke hilir (*end-to-end*). Misalnya, menguji bagaimana pendaftaran ruang kerja (*tenant*) sekolah baru langsung membentuk isolasi data, bagaimana konfirmasi pelunasan transaksi *marketplace* otomatis membuka gembok kelas di LMS, atau bagaimana pencatatan tabungan oleh Operator langsung memperbarui saldo di dasbor Orang Tua.
3. **Validasi Aturan Logika Bisnis (*Business Rule Validation*):** Memeriksa keandalan sistem dalam menolak tindakan pengguna yang melanggar ketentuan batas sistem atau logika institusi. Contohnya adalah penolakan penambahan siswa baru saat kuota langganan gratis (*Free*) sekolah telah habis, pencegahan input nilai diskon agar harga barang tidak menjadi negatif, serta pemblokiran pencetakan sertifikat jika progres materi LMS belum mencapai 100%.

Dengan menyatukan ketiga aspek evaluasi di atas ke dalam metode **Pengujian Fungsional**, hasil pengujian dapat memberikan gambaran empiris yang nyata bahwa perangkat lunak yang dibangun telah matang, aman, dan siap dioperasikan pada ekosistem pendidikan anak usia dini sesungguhnya.

## 5.2 Lingkungan Pengujian
Lingkungan pengujian merupakan kondisi ekosistem perangkat keras (*hardware*) dan perangkat lunak (*software*) terisolasi yang diatur sedemikian rupa untuk menjalankan, mengkompilasi, dan menguji aplikasi. Tahapan pengujian ini diinisialisasi sepenuhnya pada lingkungan lokal (*local development environment*) untuk memberikan ruang yang aman bagi pengembang guna melakukan eksperimen, mencari kutu program (*debugging*), dan memvalidasi keseluruhan kualitas aplikasi sebelum versi finalnya disalurkan (*deploy*) secara terbuka ke peladen produksi.

### 5.2.1 Perangkat Keras
Perangkat keras yang dilibatkan dalam memfasilitasi proses penulisan kode sumber sekaligus pengujian kinerja sistem dapat dijabarkan pada tabel berikut:

| Komponen | Spesifikasi |
| :--- | :--- |
| Processor | 12th Gen Intel® Core™ i5-12450HX |
| Memory (RAM) | 12 GB |
| Storage | SSD |
| GPU | NVIDIA GeForce RTX 2050 |
| Operating System | Windows 11 Home Single Language 64-bit |

### 5.2.2 Perangkat Lunak
Perangkat lunak pendukung yang digunakan dalam membangun, menjalankan layanan peladen, dan mengeksekusi pengujian fungsional sistem disajikan pada tabel berikut:

| Perangkat Lunak | Versi / Spesifikasi | Fungsi dan Kegunaan |
| :--- | :--- | :--- |
| **PHP / Laravel** | PHP 8.2 / Laravel 12 | Bahasa pemrograman dan *framework* utama untuk pengembangan *Backend API* dan *Admin Panel* (Filament). |
| **Vue.js** | Vue.js versi 3 | *Framework JavaScript* untuk pengembangan antarmuka portal SIAKAD Sekolah. |
| **Nuxt.js** | Nuxt.js versi 4 | *Framework Vue.js* untuk pengembangan antarmuka portal publik (*Marketplace* & LMS B2C). |
| **MySQL / MariaDB** | MariaDB 10.10+ | Sistem manajemen basis data relasional (*RDBMS*) untuk penyimpan data *multi-tenant*. |
| **Laragon** | Laragon Full / WAMP | Local server environment untuk memfasilitasi peladen web Apache/Nginx, PHP, dan MySQL. |
| **Visual Studio Code** | Versi Terbaru | Code editor (*IDE*) utama untuk penulisan dan modifikasi baris kode sistem. |
| **Git & GitHub** | Git 2.40+ | Sistem kontrol versi (*Version Control System*) dan manajemen repositori proyek. |
| **Web Browser** | Google Chrome / Microsoft Edge | Peramban web untuk menjalankan tampilan antarmuka dan mengeksekusi pengujian fungsional UI. |

### 5.2.3 Environment Pengujian
Pengujian operasional sistem dijalankan murni di atas lingkungan lokal (*local environment*). Arsitektur perancangan diatur dengan memisahkan area tugas antara layanan *backend* dan *frontend*. 

Aplikasi antarmuka sisi publik dan manajemen sisi pengguna berjalan secara mandiri, sementara pangkalan peladen API utama menggunakan kerangka kerja Laravel (PHP) yang menjadi otak pemrosesan basis data terpusatnya. Komunikasi lintas layanan di antara kedua sisi ini dijembatani menggunakan standar pertukaran data *REST API*.

### 5.2.4 Material Pengujian
Material pengujian merepresentasikan kerangka modul-modul sistem utama yang disorot dan diawasi kinerjanya selama fase pengujian berlangsung. Sesuai dengan spesifikasi arsitektur terkini yang telah diperbarui, ruang lingkup pengujian dipadatkan ke dalam tiga pilar kelompok modul utama:

1. **Sistem Manajemen Akademik & Keuangan SIAKAD**: Meliputi pengujian alur pendaftaran *multi-school* (pembuatan *tenant*), pengelolaan pembagian hak akses (khususnya peran **Operator Sekolah** dalam mengelola CRUD biodata siswa, kelas rombel, dan relasi wali murid), pencatatan keuangan SPP dan Tabungan sebagai buku besar administratif (*ledger*) oleh Operator dan Guru, hingga simulasi pemrosesan asesmen bulanan berfilter *Temporal Assessment Integrity* dan pencetakan rapor naratif berfitur *Smart Omission*.
2. **E-Learning & Marketplace B2C**: Meliputi serangkaian pengujian simulasi transaksi komersial kelas *online* publik, pengecekan validasi kode promo diskon, pembukaan gembok otomatis kelas LMS setelah *checkout* berhasil, pemutaran video materi di ruang *Learning Management System* (LMS), pengerjaan kuis, dan alur penerbitan dokumen kelulusan (*e-certificate*) otomatis saat progres mencapai 100%.
3. **Administrasi Portal & Super Admin**: Meliputi verifikasi modul pengaturan tata letak konten statis portal publik (laman artikel, FAQ), isolasi keamanan data *multi-tenant*, validasi pembatasan kuota registrasi siswa untuk paket *Free*, serta pemonitoran metrik global khusus bagi level hierarki *Super Admin*.

Setiap gugusan komponen aplikasi di atas menjalani proses validasi operasional yang menyeluruh guna meminimalisasi potensi celah anomali komputasi (*bug*) yang sanggup mengganggu ketertiban rantai proses kegiatan para pengguna di dunia nyata.

### 5.2.5 Pelaku Pengujian
Seluruh proses pelaksanaan skenario pengujian fungsional dan teknis ini dieksekusi secara mandiri oleh penulis yang bertindak langsung sebagai perancang sekaligus pengembang sistem (*System Developer*). Pemusatan peranan penguji ini ditujukan untuk mengakselerasi penemuan kesalahan logika pemrograman, karena penulis memegang pemahaman yang paling holistik mengenai seluk-beluk pemetaan data hingga arsitektur lapisan keamanan dari aplikasi yang dibangun.

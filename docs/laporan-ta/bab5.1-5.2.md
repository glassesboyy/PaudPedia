# BAB 5 PENGUJIAN

## 5.1 Metode Pengujian
Pengujian sistem dilakukan untuk memastikan bahwa seluruh fitur, alur kerja, dan proses bisnis pada platform layanan *Software as a Service* (SaaS) PAUD ini dapat berjalan sesuai dengan spesifikasi rancangan arsitektur yang telah dibangun. Pengujian diimplementasikan pada fungsi-fungsi utama aplikasi, ketepatan pertukaran data antar-modul, serta keakuratan validasi aturan bisnis yang ditanamkan pada sistem manajemen akademik, tata kelola keuangan, *marketplace*, hingga *Learning Management System* (LMS).

Metode pengujian yang digunakan dalam penelitian ini sepenuhnya dilakukan melalui pendekatan simulasi langsung pada antarmuka pengguna (*User Interface*), tanpa melakukan pengujian otomatis berbasis kode (*Automated Code Testing*). Oleh karena itu, metode yang paling representatif untuk digunakan adalah *Black Box Testing*, *End-to-End (E2E) Testing*, dan *Business Rule Validation Testing*. Ketiga metode ini saling melengkapi untuk memastikan bahwa fungsionalitas visual, alur integrasi antar-halaman, dan aturan logika bisnis dapat berjalan mulus saat dioperasikan secara nyata.

### 5.1.1 Black Box Testing (Pengujian Fungsional)
*Black Box Testing* merupakan metode pengujian perangkat lunak yang berfokus pada fungsionalitas sistem tanpa perlu mengetahui struktur kode internal di baliknya. Dalam konteks penelitian ini, pengujian dilakukan dengan cara berinteraksi langsung dengan elemen-elemen antarmuka situs. Pengujian ini bertujuan untuk memastikan bahwa tombol navigasi, pengisian formulir data (seperti formulir pembuatan kursus atau penambahan siswa), serta respon visual dari sistem telah berjalan secara wajar dan tidak menghasilkan pesan galat (*error*) saat digunakan.

### 5.1.2 End-to-End Testing (Pengujian Alur Integrasi)
Meskipun tidak menguji kode *backend* secara langsung, integrasi sistem tetap dievaluasi menggunakan metode *End-to-End Testing* (E2E). Pengujian E2E adalah proses pengujian perangkat lunak dari awal hingga akhir (*hulu ke hilir*) untuk memastikan bahwa aliran data antar berbagai antarmuka berjalan sinkron. Pada sistem ini, E2E dilakukan dengan menyimulasikan skenario perjalanan pengguna secara utuh, contohnya: menguji alur mulai dari seorang pengguna awam membuka katalog di halaman *landing page*, memasukkan kursus ke keranjang belanja, melakukan simulasi pembayaran, hingga akhirnya pengguna tersebut dapat mengakses modul video di ruang LMS.

### 5.1.3 Business Rule Validation Testing
*Business Rule Validation Testing* tetap relevan dan diterapkan melalui antarmuka, di mana pengujian difokuskan untuk mengevaluasi apakah sistem mampu menolak tindakan pengguna yang menyalahi aturan logika institusi. Pengujian ini dilaksanakan dengan menyuntikkan skenario ketidakwajaran saat pengisian formulir antarmuka untuk melihat apakah sistem memunculkan peringatan penolakan yang sesuai. Pada sistem PAUD ini, validasi yang diuji secara manual meliputi: percobaan menambahkan siswa saat kuota langganan gratis telah penuh, percobaan mencetak sertifikat saat progres kelas belum mencapai 100%, hingga percobaan menginput nilai diskon produk agar tidak menyebabkan harga akhir menjadi negatif.

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
[Placeholder untuk tabel/daftar Perangkat Lunak - Harap isi secara manual]

### 5.2.3 Environment Pengujian
Pengujian operasional sistem dijalankan murni di atas lingkungan lokal (*local environment*). Arsitektur perancangan diatur dengan memisahkan area tugas antara layanan *backend* dan *frontend*. 

Aplikasi antarmuka sisi publik dan manajemen sisi pengguna berjalan secara mandiri, sementara pangkalan peladen API utama menggunakan kerangka kerja Laravel (PHP) yang menjadi otak pemrosesan basis data terpusatnya. Komunikasi lintas layanan di antara kedua sisi ini dijembatani menggunakan standar pertukaran data *REST API*.

### 5.2.4 Material Pengujian
Material pengujian merepresentasikan kerangka modul-modul sistem utama yang disorot dan diawasi kinerjanya selama fase pengujian berlangsung. Sesuai dengan spesifikasi arsitektur yang dirancang pada bab-bab sebelumnya, ruang lingkup pengujian dipadatkan ke dalam tiga pilar kelompok modul utama:

1. **Sistem Manajemen Akademik**: Meliputi pengujian alur pendaftaran *multi-school* (pembuatan *tenant*), penataan kelengkapan data induk siswa dan wali, hingga simulasi pemrosesan asesmen harian dan rekapitulasi rapor akhir PAUD.
2. **E-Learning & Marketplace**: Meliputi serangkaian pengujian simulasi transaksi komersial kelas *online* publik, pengecekan pemutaran video materi di ruang *Learning Management System* (LMS), pengerjaan kuis, dan alur penerbitan dokumen kelulusan (sertifikasi).
3. **Administrasi Portal & Super Admin**: Meliputi verifikasi modul pengaturan tata letak konten statis portal publik (laman artikel, FAQ), pembatasan delegasi hak akses kepemimpinan instansi, serta pemonitoran metrik global khusus bagi level hierarki *Super Admin*.

Setiap gugusan komponen aplikasi di atas menjalani proses validasi operasional yang menyeluruh guna meminimalisasi potensi celah anomali komputasi (*bug*) yang sanggup mengganggu ketertiban rantai proses kegiatan para pengguna di dunia nyata.

### 5.2.5 Pelaku Pengujian
Seluruh proses pelaksanaan skenario pengujian fungsional dan teknis ini dieksekusi secara mandiri oleh penulis yang bertindak langsung sebagai perancang sekaligus pengembang sistem (*System Developer*). Pemusatan peranan penguji ini ditujukan untuk mengakselerasi penemuan kesalahan logika pemrograman, karena penulis memegang pemahaman yang paling holistik mengenai seluk-beluk pemetaan data hingga arsitektur lapisan keamanan dari aplikasi yang dibangun.

3.4	Rancangan Manajemen Keuangan: SPP dan Tabungan (Modul 8)
Modul ini dikhususkan untuk memfasilitasi administrasi finansial sekolah seperti pembayaran SPP bulanan dan setoran tabungan siswa, yang menjadi nilai tambah bagi sekolah dengan status berlangganan paket Pro. Sistem ini memungkinkan kepala sekolah maupun guru mencatat setiap aliran dana masuk dan keluar untuk siswa secara digital. Transparansi keuangan terjamin karena setiap data yang diinput oleh sekolah akan segera terpantul pada dasbor milik Orang Tua, memungkinkan mereka memantau riwayat pembayaran, sisa kewajiban, maupun saldo tabungan terkini sang anak tanpa perlu proses rekonsiliasi manual.

3.4.1	Wireframe
Tampilan yang berkaitan dengan fitur ini meliputi:
- Halaman Ringkasan Keuangan/Overview (/finances)  ------------ INI BELOM
- Halaman Manajemen Pembayaran SPP (/finances/spp)  ------------ INI BELOM
- Halaman Manajemen Saldo Tabungan (/finances/savings)  ------------ INI BELOM
- Halaman Riwayat Keuangan Siswa (di dalam /students/:id)  ------------ INI BELOM
- Dasbor Pantauan Keuangan Orang Tua (di dalam /children/:id)  ------------ INI BELOM

3.4.2	Entity Relationship Diagram (ERD)

**Entitas: finances**
Atribut: type, amount, description, transaction_date, payment_method
Relasi:
- Many-to-One ke entitas `schools` | Kata Relasi: "Masuk Ke Kas"
- Many-to-One ke entitas `students` | Kata Relasi: "Atas Nama"
- Many-to-One ke entitas `users` | Kata Relasi: "Dicatat Oleh"

**Entitas: students**
Atribut: name, nisn, status
Relasi:
- One-to-Many ke entitas `finances` | Kata Relasi: "Memiliki Transaksi"

**Entitas: schools**
Atribut: name, npsn
Relasi:
- One-to-Many ke entitas `finances` | Kata Relasi: "Merekam Arus Kas"

**Entitas: users**
Atribut: name, email
Relasi:
- One-to-Many ke entitas `finances` | Kata Relasi: "Menjadi Bendahara"

*Penjelasan Keseluruhan:*
Diagram ERD pada modul keuangan SPP dan Tabungan dirancang sangat pragmatis dengan memusatkan semua operasi pada satu tabel `finances`. Entitas transaksional tunggal ini menyimpan log logis kapan pun uang masuk atau keluar dari ekosistem sekolah. Untuk menjamin akuntabilitas rekam jejak, setiap baris transaksi yang diinput pada `finances` harus terikat erat dengan entitas `students` guna mengidentifikasi secara gamblang atas nama siswa siapa uang tersebut disetorkan atau ditarik. Di saat yang sama, transaksi ini juga disematkan kepada entitas otentikasi `users` (sebagai pembuat) yang berfungsi sebagai jejak audit (*audit trail*) tentang siapakah staf guru atau kepala sekolah yang memvalidasi arus kas tersebut ke dalam sistem. Seluruh data keuangan ini diisolasi mutlak ke entitas `schools` agar dompet finansial maupun agregasi pelaporan antar sekolah tidak saling tumpang tindih.

3.4.3	Database Schema
- finances
- students
- schools
- users

Tabel `finances` menyimpan detail finansial dengan kolom-kolom pokok seperti `type` (*Enum* dari 'spp' atau 'tabungan'), `amount` (nominal transaksi dengan validasi wajib di atas nol), `transaction_date`, dan `payment_method` (metode pembayaran cash atau transfer). Referensi utama dipenuhi dari *Foreign Key* `school_id`, `student_id`, dan `created_by`.

3.4.4	Activity Diagram
```text
[Start]
   |
   v
Masuk ke Modul Keuangan (SPP/Tabungan)
   |
   v
Pilih Nama Siswa & Tentukan Tanggal Transaksi
   |
   v
Input Nominal Transaksi & Metode Pembayaran
   |
   v
Tambahkan Deskripsi/Catatan (Opsional)
   |
   v
Sistem Memvalidasi Nominal Transaksi
   |
   +-- [Tidak Valid] --> Tampilkan Error Validasi
   |
   +-- [Valid]
           |
           v
    Simpan Data ke Database (Tabel finances)
           |
           v
     Perbarui Rekap Keuangan Anak di Dasbor Wali
           |
           v
         [End]
```
Diagram ini menjelaskan langkah pengguna (Headmaster/Teacher) dalam mendata arus kas untuk siswa. Proses berakhir dengan tersimpannya data ke memori (*database*) yang selanjutnya menjadi riwayat finansial *real-time* yang dapat ditinjau oleh Orang Tua melalui portal pemantauan.

3.4.5	Use Case Diagram
1. Use Case: Kelola Pembayaran SPP
   Role: Headmaster, Teacher
   Deskripsi: Mencatat dan merekap uang sekolah per bulan.

2. Use Case: Kelola Tabungan Anak
   Role: Headmaster, Teacher
   Deskripsi: Menginput setoran masuk maupun tarik tunai dari tabungan siswa.

3. Use Case: Pantau Status SPP & Tabungan
   Role: Parent
   Deskripsi: Melihat riwayat pembayaran biaya edukasi maupun saldo tabungan anaknya (hanya baca).

3.5	Rancangan Marketplace dan Manajemen Transaksi (Modul 11, 12, 13, 14)
Rancangan Marketplace berfungsi sebagai portal *Business to Consumer* (B2C) utama dari platform, memungkinkan pengguna mendaftarkan akun dan bertransaksi membeli produk edukasi seperti kelas kursus (*course*), tiket *webinar*, maupun produk digital (buku/E-book). Arsitektur ini tidak mencakup transaksi fisik melainkan dioptimalkan pada manajemen pesanan otomatis (*order management*) dengan integrasi dompet keranjang (*cart*), kupon promosi, serta penyelesaian pembayaran. *Payment Gateway* (Midtrans) digunakan untuk memverifikasi aliran transaksi dengan sistem respons *callback*, sehingga pesanan akan otomatis aktif tanpa harus ada campur tangan admin saat status berubah menjadi sukses.

3.5.1	Wireframe
Tampilan yang berkaitan dengan fitur ini meliputi:
- Katalog Produk, Webinar, dan Kursus (/products, /webinars, /courses)
- Detail Item Katalog (/[category]/[slug])
- Halaman Keranjang Belanja (/cart)
- Halaman Checkout & Pembayaran (/checkout)
- Halaman Kelola Pesanan dan Produk yang Dibeli (/account/orders, /account/products)

3.5.2	Entity Relationship Diagram (ERD)

**Entitas: products, webinars, courses (Katalog Barang)**
Atribut: title, price, is_active
Relasi:
- One-to-Many ke entitas `order_items` | Kata Relasi: "Dibeli Dalam"

**Entitas: carts & cart_items**
Atribut carts: user_id
Atribut cart_items: item_type, quantity
Relasi:
- `carts` One-to-One ke entitas `users` | Kata Relasi: "Milik Pengguna"
- `carts` One-to-Many ke entitas `cart_items` | Kata Relasi: "Berisi Barang"

**Entitas: orders & order_items**
Atribut orders: order_number, total_amount, status, payment_method
Atribut order_items: item_type, quantity, subtotal
Relasi:
- `orders` Many-to-One ke entitas `users` | Kata Relasi: "Dipesan Oleh"
- `orders` One-to-Many ke entitas `order_items` | Kata Relasi: "Merekam Detail"

**Entitas: promo_codes**
Atribut: code, discount_type, discount_value
Relasi:
- Many-to-One (opsional) dari entitas `orders` | Kata Relasi: "Diterapkan Pada"

*Penjelasan Keseluruhan:*
Konfigurasi tabel pada blok Marketplace ini menyerupai rancangan aplikasi e-commerce modern yang dinamis. Katalog dagang digital (`products`, `webinars`, dan `courses`) dikelompokkan sejajar menyediakan aset yang siap dibeli tanpa harus saling terikat secara fungsional. Proses ritel komersial dimulai pada entitas penampungan keranjang sementara `carts` yang disediakan eksklusif secara mandiri oleh tiap entitas akun pembeli (`users`). Saat pengguna memutuskan melakukan pelunasan tagihan (*checkout*), tumpukan data item dari keranjang akan dikonversi menjadi entitas transaksi permanen pada tabel `orders` yang kemudian rincian harga belinya dipecah satu per satu pada tabel detail `order_items`. Untuk mengakomodasi keragaman jenis produk, tabel `order_items` sengaja dirancang mengimplementasikan pola relasi polimorfik (*polymorphic*). Pola ini memungkinkan sistem merujuk barang tanpa harus membuat banyak Foreign Key spesifik (cukup menaruh string dinamis seperti 'webinar' atau 'course' pada item_type). Sebagai tambahan diskon, entitas `promo_codes` ditambahkan agar bisa ditebus potongannya sewaktu transaksi dibuat di tahap *checkout* akhir.

3.5.3	Database Schema
- products, webinars, courses
- carts, cart_items
- orders, order_items
- promo_codes

Tabel `orders` berisi data krusial transaksi seperti `total_amount`, `status` (*Enum* 'pending', 'paid', 'failed'), tipe pembayaran, serta kode *Midtrans snap token*. Detail barang tersimpan terpisah pada `order_items` dengan menyimpan relasi tipe *polymorphic* atau referensi terhadap produk spesifik yang dibeli (referensi ke tabel kursus, produk, atau webinar). Tabel `promo_codes` menyimpan persentase atau potongan harga untuk diverifikasi saat kalkulasi tagihan.

3.5.4	Activity Diagram
```text
[Start]
   |
   v
Jelajahi Katalog & Pilih Item
   |
   v
Tambahkan ke Keranjang (Add to Cart)
   |
   v
Buka Halaman Checkout
   |
   v
Input Kode Promo (Opsional)
   |
   v
Sistem Mengkalkulasi Total Tagihan
   |
   v
Konfirmasi & Buat Pesanan (Orders)
   |
   v
Tampilkan Antarmuka Pembayaran Midtrans (Snap)
   |
   v
Selesaikan Pembayaran (Customer Action)
   |
   +-- [Gagal/Batal] --> Update Status Pesanan "Failed" --> [End]
   |
   +-- [Sukses]
           |
           v
    Terima Callback/Webhook Midtrans
           |
           v
    Ubah Status Pesanan ke "Paid"
           |
           v
    Buka Akses Konten (Produk/Kursus/Webinar)
           |
           v
         [End]
```
Diagram transaksi memaparkan rentetan proses dari pelanggan menyeleksi barang, menyelesaikan pembayaran menggunakan layanan Midtrans, hingga sistem otomatis membebaskan hak akses konsumen pada material digital berbayar (kursus/webinar/produk) tanpa butuh validasi *back-office* manual.

3.5.5	Use Case Diagram
1. Use Case: Manage Cart & Checkout
   Role: User
   Deskripsi: Menambah barang ke keranjang belanja, memakai kupon, dan membayar tagihan via Midtrans.

2. Use Case: Manage Catalog Content
   Role: Moderator, Admin
   Deskripsi: Membuat, mengubah, serta menghapus draf katalog penjualan produk, kelas, dan webinar.

3. Use Case: View Orders Analytics
   Role: Admin
   Deskripsi: Meninjau riwayat transaksi serta statistik omset pesanan secara global pada platform.

3.6	Rancangan Learning Management System dan Sertifikasi (Modul 15)
LMS (Learning Management System) menjadi pelengkap ruang edukasi asinkron yang memberikan wadah bagi pengguna guna mengonsumsi materi kursus (modul, video, teks PDF) pasca penyelesaian pembelian. Struktur pembelajarannya disusun secara hierarkis (Mata Kursus > Modul > Sub-Materi) untuk menjamin kurikulum yang progresif. Sebagai pelengkap kelulusan kompetensi, sistem ini diinjeksi dengan modul evaluasi kuis (*Quiz*) yang berujung pada perhitungan nilai, pemberian kelulusan, dan pengunduhan sertifikat elektronik (*e-certificate*) secara swadaya.

3.6.1	Wireframe
Tampilan yang berkaitan dengan fitur ini meliputi:
- Halaman Induk Pembelajaran Kursus (/account/courses)
- Antarmuka LMS (/learn/[courseSlug])
- Formulir Kuisioner & Kuis (Di dalam komponen /learn/[courseSlug])
- Halaman Direktori Sertifikat (/account/certificates)
- Panel Manajemen Konten Kursus (Berada pada Panel Admin/Filament)

3.6.2	Entity Relationship Diagram (ERD)

**Entitas: courses**
Atribut: title, price, is_published
Relasi:
- One-to-Many ke entitas `modules` | Kata Relasi: "Memiliki Modul"

**Entitas: modules**
Atribut: title, order
Relasi:
- One-to-Many ke entitas `lessons` | Kata Relasi: "Berisi Materi"
- One-to-Many ke entitas `quizzes` | Kata Relasi: "Memiliki Ujian"

**Entitas: quizzes, quiz_questions, quiz_answers**
Atribut: title, question, answer, is_correct
Relasi:
- `quizzes` One-to-Many ke entitas `quiz_questions` | Kata Relasi: "Mempunyai Soal"

**Entitas: course_enrollments**
Atribut: progress_percentage, certificate_url
Relasi:
- Many-to-One ke entitas `courses` | Kata Relasi: "Mendaftar Pada"
- Many-to-One ke entitas `users` | Kata Relasi: "Diikuti Oleh"

**Entitas: lesson_progress**
Atribut: is_completed
Relasi:
- Many-to-One ke entitas `course_enrollments` | Kata Relasi: "Merupakan Progres"
- Many-to-One ke entitas `lessons` | Kata Relasi: "Status Dari Materi"

*Penjelasan Keseluruhan:*
Konstruksi ERD untuk integrasi *Learning Management System* (LMS) memiliki karakteristik skema hierarkis yang bercabang selayaknya pohon (*tree hierarchy*) yang linier. Entitas draf `courses` menjalar ke bawah bertindak sebagai akar pusat yang memiliki daftar isi bab silabus di dalam entitas `modules`. Setiap tabel modul ini lalu kembali dipecah ke struktur unit spesifik terkecilnya: apakah itu berupa unit pasif/video edukasi (`lessons`), atau komponen evaluasi interaktif (`quizzes`). Khusus untuk perancangan fungsional instrumen kuis, struktur tabel terus dikembangkan dan diturunkan (*one-to-many*) secara mendalam mulai dari wadah draf kuis itu sendiri, rantai pendaftaran bank soal di `quiz_questions`, hingga menjangkau opsi titik poin pilihan ganda peserta dan validasi kebenarannya pada `quiz_answers`. Sementara itu, hak akses untuk bisa memutar konten silabus sepenuhnya dijembatani oleh `course_enrollments` yang berfungsi sebagai gerbang lisensi pembelian kelas oleh entitas `users`. Guna memberikan UX (pengalaman pengguna) yang adaptif layaknya fitur *resume playback*, aplikasi merekam status persentase persis dari penelusuran kursus baris demi baris menggunakan tabel riwayat sentral bernama `lesson_progress`.

3.6.3	Database Schema
- courses, modules, lessons   
- quizzes, quiz_questions, quiz_answers
- course_enrollments, lesson_progress

Tabel susunan belajar `lessons` memuat URL tautan materi (teks/video), urutan tampil (*order*), beserta estimasi durasi. Sementara itu, tabel progres `course_enrollments` dirancang menyimpan variabel penting seperti `progress_percentage` dan tautan fisik `certificate_url` setelah usai 100%. Komponen soal kuis diletakkan pada tabel rekaman `quiz_attempts` dan `quiz_attempt_answers` sebagai log jawaban riil peserta yang skornya digunakan untuk verifikasi tamat kursus.

3.6.4	Activity Diagram
```text
[Start]
   |
   v
Masuk Halaman LMS & Pilih Kursus Aktif
   |
   v
Tonton Video / Baca Teks Modul Pembelajaran
   |
   v
Sistem Mencatat Progres (lesson_progress)
   |
   v
Update Persentase Penyelesaian Kursus
   |
   v
Terdapat Kuisioner/Kuis ?
   |
   +-- [Ya] --> Jawab Soal Kuis --> [Kalkulasi Nilai]
   |                                      |
   +-- [Tidak]                            |
           |                              |
           v                              |
    Materi Terakhir Selesai ? <-----------+
           |
           +-- [Belum] --> Pindah Modul Selanjutnya
           |
           +-- [Ya] (Progress 100%)
                   |
                   v
        Sistem Buat & Generate Sertifikat PDF
                   |
                   v
         Tampilkan Tombol Unduh Sertifikat
                   |
                   v
                 [End]
```
Diagram LMS ini mewakilkan interaksi langkah per langkah peserta memutar materi video, memvalidasi progres otomatis ke dalam database, menghadapi tes kuis, hingga ditutup pada proses generasi (*generate*) dokumen PDF sertifikat kelulusan yang dilepas bebas kepada peserta saat persentase kursus utuh 100%.

3.6.5	Use Case Diagram
1. Use Case: Take Courses & Quizzes
   Role: User
   Deskripsi: Mengakses video belajar, membaca PDF, dan menjawab soal kuis interaktif dari kelas kursus yang telah dibeli.

2. Use Case: Download Certificate
   Role: User
   Deskripsi: Mengunduh arsip dokumen digital sebagai tanda bukti kelulusan pelatihan secara mandiri.

3. Use Case: Compose Course Modules
   Role: Moderator, Admin
   Deskripsi: Mengatur skema kurikulum (menyusun modul silabus, unggah video pembelajaran, dan menyeting kuis) ke dalam draf kursus aktif.

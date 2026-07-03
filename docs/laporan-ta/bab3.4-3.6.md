> **Catatan Pembaruan Dokumen (Sinkronisasi Codebase & Arsitektur):**
> Seluruh isi pada dokumen ini telah mengalami perubahan arsitektur dokumen dan pembaruan substansi sesuai dengan kondisi sistem terkini. Bagian yang diubah secara masif meliputi:
> - **3.4.1 - 3.4.4** (Restrukturisasi urutan, penambahan peran **Operator Sekolah** pada pengelolaan administrasi SPP & Tabungan, dan Class Diagram untuk Manajemen Keuangan).
> - **3.5.1 - 3.5.4** (Restrukturisasi urutan, penyempurnaan alur transaksi otomatis Midtrans, dan Class Diagram untuk Marketplace).
> - **3.6.1 - 3.6.4** (Restrukturisasi urutan, penyempurnaan alur belajar asinkron dan otomatisasi sertifikat digital, dan Class Diagram untuk LMS).
> 
> *Catatan ini dapat Anda hapus saat dokumen akan dicetak.*

---

3.4	Rancangan Manajemen Keuangan: SPP dan Tabungan (Modul 8)
Modul ini dikhususkan untuk memfasilitasi administrasi finansial sekolah seperti pembayaran SPP bulanan dan setoran tabungan siswa, yang menjadi nilai tambah bagi sekolah dengan status berlangganan paket Pro. Sistem ini memungkinkan Kepala Sekolah (*Headmaster*), Operator Sekolah (*Operator*), maupun Guru Kelas (*Teacher*) untuk mencatat setiap aliran dana masuk dan keluar bagi siswa secara digital. Transparansi keuangan terjamin karena setiap data transaksi yang diinput oleh pihak sekolah akan segera terpantul secara *real-time* pada dasbor milik Orang Tua (*Parent*), memungkinkan mereka memantau riwayat pembayaran, sisa kewajiban tagihan, maupun saldo tabungan terkini sang anak tanpa perlu proses rekonsiliasi manual.

3.4.1	Activity Diagram
```text
[Start]
   |
   v
Masuk ke Modul Keuangan (Headmaster / Operator / Teacher)
   |
   v
Pilih Jenis Layanan (Tagihan SPP / Tabungan Siswa)
   |
   v
Pilih Rombongan Belajar (Kelas) & Nama Siswa
   |
   v
Tentukan Tanggal Transaksi & Bulan Tagihan
   |
   v
Input Nominal Transaksi & Metode Pembayaran (Tunai/Transfer)
   |
   v
Tambahkan Deskripsi atau Catatan Tambahan (Opsional)
   |
   v
Sistem Memvalidasi Nominal & Saldo (Khusus Penarikan Tabungan)
   |
   +-- [Tidak Valid / Saldo Tidak Cukup] --> Tampilkan Error Validasi --> [End]
   |
   +-- [Valid]
           |
           v
    Simpan Data ke Database (Tabel finances)
           |
           v
    Perbarui Saldo Akumulatif & Status Pembayaran
           |
           v
    Sinkronisasi Real-Time ke Dasbor Pemantauan Wali
           |
           v
         [End]
```
Diagram ini menjelaskan langkah pengguna administratif (Headmaster, Operator, atau Teacher) dalam mendata arus kas untuk siswa. Proses berakhir dengan tersimpannya data ke memori (*database*) yang selanjutnya menjadi riwayat finansial *real-time* yang dapat ditinjau oleh Orang Tua melalui portal pemantauan.

3.4.2	Use Case Diagram
1. Use Case: Manage SPP Payment
   Role: Operator, Teacher
   Deskripsi: Operator menginput, mencatat pembayaran, dan membuat tagihan SPP bulanan untuk semua siswa, sedangkan teacher hanya bisa mengelola data pembayaran SPP siswa di kelas bimbingannya.

2. Use Case: Manage Student Savings
   Role: Operator, Teacher
   Deskripsi: Operator menginput setoran masuk maupun tarik tunai dari tabungan siswa dengan validasi saldo, sedangkan teacher hanya bisa mengelola data tabungan siswa di kelas bimbingannya.

3. Use Case: Monitor Student SPP & Savings
   Role: Parent
   Deskripsi: Melihat riwayat pembayaran biaya edukasi maupun saldo tabungan anaknya.

3.4.3	Class Diagram
Pemodelan objek pada modul pengelolaan keuangan SPP dan Tabungan dirancang pragmatis namun saling terikat pada fondasi *multi-tenant*. Terdapat empat entitas utama yang saling berkolaborasi:
- **Class `Finance`**: Entitas transaksional tunggal pencatat log uang masuk atau keluar dari ekosistem sekolah. Properti kritis di dalamnya meliputi `type` (*Enum* dari 'spp' atau 'tabungan'), `amount`, `month`, `is_paid`, `paid_at`, `payment_method` (tunai/transfer/qris), dan `transaction_type` (setoran/penarikan). 
- **Class `Student`**: Merupakan subjek utama (pihak tertagih atau pemilik rekening tabungan) dari setiap riwayat finansial. Kelas `Finance` menyematkan *Foreign Key* ke entitas murid ini lewat metode relasional `student()`, sehingga rekap tagihan SPP atau sisa saldo dapat dipilah per individu.
- **Class `User`**: Berperan sebagai staf pencatat atau bendahara (*Headmaster*, *Operator*, atau *Teacher*). Tersambung pada properti `recorded_by` di pencatatan transaksi untuk membentuk jejak audit keamanan (*audit trail*), memastikan siapa penginput setoran ke dalam basis data.
- **Class `School`**: Lingkup kerangka *tenant* finansial. Meskipun tabel `Finance` menempel ke siswa, relasinya mengakar ke atas pada tabel `School` agar penumpukan dana sekolah A tidak akan bocor ke pembukuan kas sekolah B.

3.4.4	Wireframe
Tampilan yang berkaitan dengan fitur ini meliputi:
- Halaman Manajemen Tagihan & Pembayaran SPP (`/finances/spp`)
- Halaman Manajemen Tabungan Siswa (`/finances/savings`)
- Dasbor Pantauan Keuangan Orang Tua (di dalam `/children/:id`)

3.5	Rancangan Marketplace dan Manajemen Transaksi (Modul 11, 12, 13, 14)
Rancangan Marketplace berfungsi sebagai portal *Business to Consumer* (B2C) utama dari platform, memungkinkan pengguna umum mendaftarkan akun dan bertransaksi membeli produk edukasi seperti kelas kursus (*course*), tiket *webinar*, maupun produk digital (buku/E-book). Arsitektur ini tidak mencakup transaksi pengiriman barang fisik melainkan dioptimalkan pada manajemen pesanan otomatis (*order management*) dengan integrasi dompet keranjang (*cart*), kupon promosi, serta penyelesaian pembayaran instan. *Payment Gateway* (Midtrans) digunakan untuk memverifikasi aliran transaksi dengan sistem respons *callback/webhook*, sehingga pesanan akan otomatis aktif tanpa harus ada campur tangan admin saat status berubah menjadi sukses (*Paid*).

3.5.1	Activity Diagram
```text
[Start]
   |
   v
Jelajahi Katalog B2C & Pilih Item (Kursus/Webinar/Produk)
   |
   v
Tambahkan ke Keranjang (Add to Cart)
   |
   v
Buka Halaman Keranjang & Lanjut ke Checkout
   |
   v
Input Kode Promo Diskon (Opsional)
   |
   v
Sistem Mengkalkulasi Total Tagihan & Potongan Harga
   |
   v
Konfirmasi & Buat Pesanan (Orders)
   |
   v
Tampilkan Antarmuka Pembayaran Midtrans (Snap Pop-up)
   |
   v
Selesaikan Pembayaran Sesuai Metode Pilihan (Customer Action)
   |
   +-- [Gagal / Kedaluwarsa / Batal] --> Update Status Pesanan "Failed" --> [End]
   |
   +-- [Sukses / Pembayaran Diterima]
           |
           v
    Terima Callback / Webhook Resmi dari Midtrans
           |
           v
    Verifikasi Signature Key & Ubah Status Pesanan ke "Paid"
           |
           v
    Sistem Membuka Akses Konten (Enrollment Kursus / Webinar)
           |
           v
         [End]
```
Diagram transaksi memaparkan rentetan proses dari pelanggan menyeleksi barang, menyelesaikan pembayaran menggunakan layanan Midtrans, hingga sistem otomatis membebaskan hak akses konsumen pada material digital berbayar tanpa butuh validasi manual dari *back-office*.

3.5.2	Use Case Diagram
1. Use Case: Manage Cart & Checkout
   Role: User
   Deskripsi: Menambah barang ke keranjang belanja, memakai kupon diskon, dan membayar tagihan via Midtrans.

2. Use Case: Manage Catalog Content
   Role: Moderator, Admin
   Deskripsi: Membuat, mengubah, memproses harga, serta menghapus draf katalog penjualan produk digital, kelas kursus, dan webinar.

3. Use Case: View Orders Analytics
   Role: Admin
   Deskripsi: Meninjau riwayat transaksi, grafik omset penjualan, serta statistik keberhasilan pesanan secara global pada platform.

3.5.3	Class Diagram
Pemrosesan transaksi *e-commerce* direpresentasikan lewat beberapa *Class* dinamis di samping entitas pembeli:
- **Class `User`**: Entitas pelanggan atau *customer* yang memegang otorisasi transaksi. Memiliki relasi *one-to-one* eksklusif terhadap dompet `Cart` miliknya sendiri, dan relasi berjejaring *one-to-many* ke daftar pesanannya (`orders()`).
- **Class Katalog (`Product`, `Webinar`, `Course`)**: Menyediakan aset yang siap dibeli. Semua model ini memiliki metode seragam seperti atribut `is_published`, harga dasar, dan kalkulasi harga akhir.
- **Class `Cart` & `CartItem`**: Menampung *instance* barang sementara. `CartItem` menyimpan rincian barang sebelum dieksekusi bayar.
- **Class `Order`**: Titik muara sebuah transaksi *checkout*. Memegang atribut-atribut esensial pembayaran seperti `order_number`, `total_amount`, `discount_amount`, `final_amount`, `promo_code` (dalam bentuk nilai string *snapshot*), serta rekaman gerbang pembayaran `midtrans_order_id` dan `midtrans_transaction_id`. Class ini menyediakan fungsi validasi status seperti `isPaid()`, `isExpired()`, dan penanganan pembaruan nilai *callback* melalui `markAsPaid()`.
- **Class `OrderItem`**: Menggunakan pola relasi *Polymorphic* (mengandalkan tipe objek dinamis seperti 'webinar', 'course', atau 'product') pada properti `item_type` sehingga tidak membutuhkan banyak referensi ID tabel spesifik.
- **Class `PromoCode`**: Dikelola secara mandiri sebagai generator diskon yang perhitungannya diterapkan langsung dan dikalkulasikan di *method* kelas `Order`.

3.5.4	Wireframe
Tampilan yang berkaitan dengan fitur ini meliputi:
- Katalog Produk, Webinar, dan Kursus (`/products`, `/webinars`, `/courses`)
- Detail Item Katalog (`/[category]/[slug]`)
- Halaman Keranjang Belanja (`/cart`)
- Halaman Checkout & Pembayaran (`/checkout`)
- Halaman Kelola Pesanan & Aset Digital yang Dibeli (`/account/orders`, `/account/products`)

3.6	Rancangan Learning Management System dan Sertifikasi (Modul 15)
LMS (*Learning Management System*) menjadi pelengkap ruang edukasi asinkron yang memberikan wadah bagi pengguna guna mengonsumsi materi kursus (modul, video, teks PDF) pasca penyelesaian pembelian. Struktur pembelajarannya disusun secara hierarkis (*Course > Module > Lesson*) untuk menjamin kurikulum yang progresif dan mudah diikuti. Sebagai pelengkap kelulusan kompetensi, sistem ini diinjeksi dengan modul evaluasi kuis (*Quiz*) interaktif yang berujung pada kalkulasi nilai otomatis, penetapan status kelulusan, dan pengunduhan sertifikat elektronik (*e-certificate*) berformat PDF secara mandiri oleh peserta yang telah menuntaskan seluruh progres belajar 100%.

3.6.1	Activity Diagram
```text
[Start]
   |
   v
Masuk Halaman LMS & Pilih Kursus Aktif (My Courses)
   |
   v
Tonton Video / Baca Teks Modul Pembelajaran
   |
   v
Sistem Mencatat Progres Materi (lesson_progress)
   |
   v
Update Kalkulasi Persentase Penyelesaian Kursus
   |
   v
Terdapat Kuisioner/Kuis pada Modul ?
   |
   +-- [Ya] --> Jawab Soal Kuis --> [Kalkulasi Nilai & Simpan QuizAttempt]
   |                                      |
   +-- [Tidak]                            |
           |                              |
           v                              |
    Materi Terakhir Selesai ? <-----------+
           |
           +-- [Belum] --> Pindah ke Modul/Lesson Selanjutnya
           |
           +-- [Ya] (Progress 100%)
                   |
                   v
        Sistem Memvalidasi Kelayakan Kelulusan
                   |
                   v
        Generate Sertifikat PDF (e-Certificate)
                   |
                   v
         Tampilkan Tombol Unduh Sertifikat
                   |
                   v
                 [End]
```
Diagram LMS ini mewakili interaksi langkah per langkah peserta memutar materi video, memvalidasi progres otomatis ke dalam database, menghadapi tes kuis, hingga ditutup pada proses generasi (*generate*) dokumen PDF sertifikat kelulusan yang dilepas bebas kepada peserta saat persentase kursus utuh 100%.

3.6.2	Use Case Diagram
1. Use Case: Take Courses & Quizzes
   Role: User
   Deskripsi: Mengakses video belajar, membaca dokumen materi PDF, dan menjawab soal kuis interaktif dari kelas kursus yang telah dibeli.

2. Use Case: Download Certificate
   Role: User
   Deskripsi: Mengunduh arsip dokumen digital berformat PDF sebagai tanda bukti kelulusan pelatihan secara mandiri setelah progres 100%.

3. Use Case: Manage Course Modules & Quiz Syllabus
   Role: Moderator, Admin
   Deskripsi: Mengatur skema kurikulum (menyusun modul silabus, mengunggah video pembelajaran, dan menyusun kuis pilihan ganda) ke dalam draf kursus aktif.

3.6.3	Class Diagram
Konstruksi *Class Diagram* untuk integrasi *Learning Management System* (LMS) memiliki karakteristik skema hierarkis yang bercabang selayaknya pohon (*tree hierarchy*) linier:
- **Class `User`**: Bertindak sebagai pembelajar mandiri. Memiliki akses langsung ke properti materi modul usai penyelesaian *checkout*. Jejak keterlibatannya secara berkesinambungan tercatat relasional menuju tabel progres dan perolehan skor (`CourseEnrollment` & `QuizAttempt`).
- **Class `Course`**: Bertindak sebagai akar pusat yang menampung atribut `title`, `price`, `slug`, dan status `is_published`. Di dalamnya termuat metode agregasi progres serta fungsi rujukan mentor pemateri.
- **Class `Module`**: Bagian struktural dari `Course`, menyusun daftar bab dari sebuah silabus. Berelasi *one-to-many* untuk menampung unit belajar pasif maupun interaktif.
- **Class `Lesson`**: Merupakan unit materi baca/tonton pasif, menyimpan nilai tautan media (video atau PDF), durasi belajar, dan atribut konfigurasi urutan tayang (`order`).
- **Class `Quiz`, `QuizQuestion`, `QuizAnswer`**: Hierarki khusus komponen tes interaktif. Terhubung berlapis mulai dari wadah draf ujian ke struktur pertanyaannya dan bermuara pada opsi jawaban pilihan ganda (*multiple choice*) berbekal penanda boolean `is_correct`.
- **Class `CourseEnrollment`**: Berfungsi sebagai lisensi kepemilikan kelas bagi `User` (*many-to-one* ke entitas pengikut kursus) yang turut menyimpan `progress_percentage`, tanggal lulus, dan jejak rekam *file* fisik `certificate_url`. 
- **Class `LessonProgress` & `QuizAttempt`**: Model pencatatan interaksi yang diinisiasi setiap kali pengguna mengeklik *"Selesai"* pada sebuah materi atau selesai mengerjakan satu set ujian yang disyaratkan. 

3.6.4	Wireframe
Tampilan yang berkaitan dengan fitur ini meliputi:
- Halaman Induk Pembelajaran Kursus (`/account/courses`)
- Antarmuka Ruang Belajar LMS (`/learn/[courseSlug]`)
- Formulir Kuisioner & Kuis Interaktif (Di dalam komponen `/learn/[courseSlug]`)
- Halaman Direktori Sertifikat Digital (`/account/certificates`)
- Panel Manajemen Konten Kursus & Silabus (Berada pada Panel Admin/Filament)

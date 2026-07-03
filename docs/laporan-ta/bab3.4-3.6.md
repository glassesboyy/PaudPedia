> **Catatan Pembaruan Dokumen (Sinkronisasi Codebase):**
> Seluruh isi pada dokumen ini telah mengalami perubahan arsitektur dokumen dan pembaruan substansi. Bagian yang diubah secara masif meliputi:
> - **3.4.1 - 3.4.4** (Restrukturisasi urutan, penghapusan ERD, penambahan Class Diagram untuk Manajemen Keuangan & SPP).
> - **3.5.1 - 3.5.4** (Restrukturisasi urutan, penghapusan ERD, penambahan Class Diagram untuk Marketplace).
> - **3.6.1 - 3.6.4** (Restrukturisasi urutan, penghapusan ERD, penambahan Class Diagram untuk LMS dan Sertifikasi).
> 
> *Catatan ini dapat Anda hapus saat dokumen akan dicetak.*

---

3.4	Rancangan Manajemen Keuangan: SPP dan Tabungan (Modul 8)
Modul ini dikhususkan untuk memfasilitasi administrasi finansial sekolah seperti pembayaran SPP bulanan dan setoran tabungan siswa, yang menjadi nilai tambah bagi sekolah dengan status berlangganan paket Pro. Sistem ini memungkinkan kepala sekolah maupun guru mencatat setiap aliran dana masuk dan keluar untuk siswa secara digital. Transparansi keuangan terjamin karena setiap data yang diinput oleh sekolah akan segera terpantul pada dasbor milik Orang Tua, memungkinkan mereka memantau riwayat pembayaran, sisa kewajiban, maupun saldo tabungan terkini sang anak tanpa perlu proses rekonsiliasi manual.

3.4.1	Activity Diagram
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

3.4.2	Use Case Diagram
1. Use Case: Kelola Pembayaran SPP
   Role: Headmaster, Teacher
   Deskripsi: Menginput dan membuat tagihan SPP per bulan.

2. Use Case: Kelola Tabungan Anak
   Role: Headmaster, Teacher
   Deskripsi: Menginput setoran masuk maupun tarik tunai dari tabungan siswa.

3. Use Case: Pantau Status SPP & Tabungan
   Role: Parent
   Deskripsi: Melihat riwayat pembayaran biaya edukasi maupun saldo tabungan anaknya (hanya baca).

3.4.3	Class Diagram
Pemodelan objek pada modul pengelolaan keuangan SPP dan Tabungan dirancang pragmatis namun saling terikat pada fondasi *multi-tenant*. Terdapat empat entitas utama yang saling berkolaborasi:
- **Class `Finance`**: Entitas transaksional tunggal pencatat log uang masuk atau keluar dari ekosistem sekolah. Properti kritis di dalamnya meliputi `type` (*Enum* dari 'spp' atau 'tabungan'), `amount`, `month`, `is_paid`, `paid_at`, `payment_method`, dan `transaction_type` (setoran/penarikan). 
- **Class `Student`**: Merupakan subjek utama (pihak tertagih) dari setiap riwayat finansial. Kelas `Finance` menyematkan *Foreign Key* ke entitas murid ini lewat metode relasional `student()`, sehingga rekap utang SPP atau sisa saldo dapat dipilah per individu.
- **Class `User`**: Berperan sebagai staf bendahara (Headmaster/Teacher). Tersambung pada properti `recorded_by` di pencatatan transaksi untuk membentuk jejak audit keamanan (*audit trail*), memastikan siapa penginput setoran ke dalam basis data.
- **Class `School`**: Lingkup kerangka *tenant* finansial. Meskipun tabel `Finance` menempel ke siswa, relasinya mengakar ke atas pada tabel `School` agar penumpukan dana sekolah A tidak akan bocor ke brankas pembukuan kas sekolah B.

3.4.4	Wireframe
Tampilan yang berkaitan dengan fitur ini meliputi:
- Halaman Manajemen Tagihan SPP (/finances/spp)
- Halaman Manajemen Tabungan (/finances/savings)
- Dasbor Pantauan Keuangan Orang Tua (di dalam /children/:id)

3.5	Rancangan Marketplace dan Manajemen Transaksi (Modul 11, 12, 13, 14)
Rancangan Marketplace berfungsi sebagai portal *Business to Consumer* (B2C) utama dari platform, memungkinkan pengguna mendaftarkan akun dan bertransaksi membeli produk edukasi seperti kelas kursus (*course*), tiket *webinar*, maupun produk digital (buku/E-book). Arsitektur ini tidak mencakup transaksi fisik melainkan dioptimalkan pada manajemen pesanan otomatis (*order management*) dengan integrasi dompet keranjang (*cart*), kupon promosi, serta penyelesaian pembayaran. *Payment Gateway* (Midtrans) digunakan untuk memverifikasi aliran transaksi dengan sistem respons *callback*, sehingga pesanan akan otomatis aktif tanpa harus ada campur tangan admin saat status berubah menjadi sukses.

3.5.1	Activity Diagram
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

3.5.2	Use Case Diagram
1. Use Case: Manage Cart & Checkout
   Role: User
   Deskripsi: Menambah barang ke keranjang belanja, memakai kupon, dan membayar tagihan via Midtrans.

2. Use Case: Manage Catalog Content
   Role: Moderator, Admin
   Deskripsi: Membuat, mengubah, serta menghapus draf katalog penjualan produk, kelas, dan webinar.

3. Use Case: View Orders Analytics
   Role: Admin
   Deskripsi: Meninjau riwayat transaksi serta statistik omset pesanan secara global pada platform.

3.5.3	Class Diagram
Pemrosesan transaksi *e-commerce* direpresentasikan lewat beberapa *Class* dinamis di samping entitas pembeli:
- **Class `User`**: Entitas pelanggan atau *customer* yang memegang otorisasi transaksi. Memiliki relasi *one-to-one* eksklusif terhadap dompet `Cart` miliknya sendiri, dan relasi berjejaring *one-to-many* ke daftar pesanannya (`orders()`).
- **Class Katalog (`Product`, `Webinar`, `Course`)**: Menyediakan aset yang siap dibeli. Semua model ini memiliki metode seragam seperti `is_active` dan fungsi *pricing*.
- **Class `Cart` & `CartItem`**: Menampung *instance* barang sementara. `CartItem` menyimpan rincian barang sebelum dieksekusi bayar.
- **Class `Order`**: Titik muara sebuah transaksi *checkout*. Memegang atribut-atribut esensial pembayaran seperti `order_number`, `total_amount`, `discount_amount`, `final_amount`, `promo_code` (dalam bentuk nilai string *snapshot* harga), serta rekaman gerbang pembayaran `midtrans_order_id` dan `midtrans_transaction_id`. Class ini juga menyediakan fungsi validasi status seperti `isPaid()`, `isExpired()`, dan penanganan pembaruan nilai *callback* melalui `markAsPaid()`.
- **Class `OrderItem`**: Menggunakan pola relasi *Polymorphic* (mengandalkan tipe objek dinamis seperti 'webinar' atau 'course') pada properti `item_type` sehingga tidak membutuhkan banyak referensi ID tabel spesifik.
- **Class `PromoCode`**: Dikelola secara mandiri sebagai generator diskon yang perhitungannya diterapkan langsung dan dikalkulasikan (dengan fungsi khusus) di *method* kelas `Order`.

3.5.4	Wireframe
Tampilan yang berkaitan dengan fitur ini meliputi:
- Katalog Produk, Webinar, dan Kursus (/products, /webinars, /courses)
- Detail Item Katalog (/[category]/[slug])
- Halaman Keranjang Belanja (/cart)
- Halaman Checkout & Pembayaran (/checkout)
- Halaman Kelola Pesanan dan Produk yang Dibeli (/account/orders, /account/products)

3.6	Rancangan Learning Management System dan Sertifikasi (Modul 15)
LMS (Learning Management System) menjadi pelengkap ruang edukasi asinkron yang memberikan wadah bagi pengguna guna mengonsumsi materi kursus (modul, video, teks PDF) pasca penyelesaian pembelian. Struktur pembelajarannya disusun secara hierarkis (Mata Kursus > Modul > Sub-Materi) untuk menjamin kurikulum yang progresif. Sebagai pelengkap kelulusan kompetensi, sistem ini diinjeksi dengan modul evaluasi kuis (*Quiz*) yang berujung pada perhitungan nilai, pemberian kelulusan, dan pengunduhan sertifikat elektronik (*e-certificate*) secara swadaya.

3.6.1	Activity Diagram
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

3.6.2	Use Case Diagram
1. Use Case: Take Courses & Quizzes
   Role: User
   Deskripsi: Mengakses video belajar, membaca PDF, dan menjawab soal kuis interaktif dari kelas kursus yang telah dibeli.

2. Use Case: Download Certificate
   Role: User
   Deskripsi: Mengunduh arsip dokumen digital sebagai tanda bukti kelulusan pelatihan secara mandiri.

3. Use Case: Manage Course Modules
   Role: Moderator, Admin
   Deskripsi: Mengatur skema kurikulum (menyusun modul silabus, unggah video pembelajaran, dan menyeting kuis) ke dalam draf kursus aktif.

3.6.3	Class Diagram
Konstruksi *Class Diagram* untuk integrasi *Learning Management System* (LMS) memiliki karakteristik skema hierarkis yang bercabang selayaknya pohon (*tree hierarchy*) linier:
- **Class `User`**: Bertindak sebagai pembelajar mandiri. Memiliki akses langsung ke properti hak cipta modul usai penyelesaian *checkout*. Jejak keterlibatannya secara berkesinambungan tercatat relasional menuju tabel progres dan perolehan skor (*CourseEnrollment* & *QuizAttempt*).
- **Class `Course`**: Bertindak sebagai akar pusat yang menampung atribut `title`, `price`, dan status `is_published`. Di dalamnya termuat metode agregasi seperti fungsi pencarian mentor.
- **Class `Module`**: Bagian struktural dari *Course*, menyusun daftar bab dari sebuah silabus. Berelasi *one-to-many* untuk menampung unit belajar pasif maupun interaktif.
- **Class `Lesson`**: Merupakan unit materi baca/tonton pasif, menyimpan nilai tautan media (video atau PDF) dan atribut konfigurasi urutan tayang (*order*).
- **Class `Quiz`, `QuizQuestion`, `QuizAnswer`**: Hierarki khusus komponen tes interaktif. Terhubung berlapis mulai dari wadah draf ujian ke struktur pertanyaannya dan bermuara pada opsi jawaban pilihan ganda (*multiple choice*).
- **Class `CourseEnrollment`**: Berfungsi sebagai lisensi kelas bagi `User` (*many-to-one* ke entitas pengikut kursus) yang turut menyimpan `progress_percentage` dan jejak rekam *file* fisik `certificate_url`. 
- **Class `LessonProgress` & `QuizAttempt`**: Model-model pencatatan interaksi yang diinisiasi setiap kali pengguna mengeklik *"Selesai"* pada sebuah materi atau selesai mengerjakan satu set ujian yang disyaratkan. 

3.6.4	Wireframe
Tampilan yang berkaitan dengan fitur ini meliputi:
- Halaman Induk Pembelajaran Kursus (/account/courses)
- Antarmuka LMS (/learn/[courseSlug])
- Formulir Kuisioner & Kuis (Di dalam komponen /learn/[courseSlug])
- Halaman Direktori Sertifikat (/account/certificates)
- Panel Manajemen Konten Kursus (Berada pada Panel Admin/Filament)

## 5.3 Prosedur Pengujian
Prosedur pengujian dilakukan untuk memastikan bahwa seluruh antarmuka fitur, integrasi antar halaman, serta aturan logika bisnis pada platform *SaaS* PAUD ini dapat berjalan sesuai dengan kebutuhan spesifikasi dan proses operasional institusi pendidikan. Pengujian dilakukan secara manual menggunakan pendekatan *Black Box Testing*, *End-to-End Testing*, dan *Business Rule Validation Testing*.

Pengujian difokuskan pada fitur-fitur esensial sistem yang berkaitan dengan manajemen akademik, tata kelola keuangan, *marketplace*, proses transaksi, kegiatan belajar asinkron di LMS, hingga validasi hierarki administrator. Setiap pengujian dieksekusi berdasarkan skenario terstruktur guna memverifikasi apakah hasil keluaran dari antarmuka sistem sudah tepat dalam menangani masukan pengguna yang wajar maupun tidak wajar.

Tabel 5.1 menunjukkan daftar prosedur pengujian yang dilakukan pada sistem.

| ID | Object | Butir Uji | Teknik Pengujian |
| :--- | :--- | :--- | :--- |
| BB-001 | Multi-School | Pendaftaran akun dan pembuatan ruang kerja (*tenant*) sekolah baru | Black Box Testing |
| BB-002 | Manajemen Siswa | Penambahan data biodata anak dan penautan akun profil wali murid | Black Box Testing |
| BB-003 | Asesmen PAUD | Pengisian form skala nilai perkembangan anak oleh tenaga pendidik | Black Box Testing |
| BB-004 | Marketplace | Memasukkan produk kursus ke dalam keranjang dan proses *checkout* | Black Box Testing |
| BB-005 | LMS | Memutar pemutar video materi dan mengisi soal pilihan ganda pada kuis | Black Box Testing |
| E2E-001 | Pendaftaran & Akses | Integrasi pendaftaran *tenant* dengan ketepatan isolasi data ruang kerja | End-to-End Testing |
| E2E-002 | Pembayaran SPP | Integrasi pelunasan tagihan SPP dengan pembaruan status riwayat tagihan wali murid | End-to-End Testing |
| E2E-003 | Checkout & LMS | Integrasi keberhasilan pembayaran *marketplace* dengan pembukaan gembok akses kelas LMS | End-to-End Testing |
| E2E-004 | Kuis & Sertifikat | Integrasi kelulusan nilai kuis akhir dengan *generate* sertifikat PDF secara otomatis | End-to-End Testing |
| BR-001 | Limit Registrasi | Validasi penolakan siswa baru jika kuota paket langganan *Free* sekolah telah penuh | Business Rule Validation |
| BR-002 | Validasi Diskon | Validasi nilai *input* harga diskon agar nilai perhitungan akhir tidak menjadi di bawah nol | Business Rule Validation |
| BR-003 | Kelulusan LMS | Validasi sistem menolak pencetakan sertifikat jika progres materi siswa belum mencapai 100% | Business Rule Validation |

## 5.4 Hasil Uji dan Kesimpulan
Pengujian sistem dilakukan untuk memastikan bahwa fungsionalitas UI, alur integrasi antar-halaman, dan keakuratan validasi aturan bisnis pada platform PAUD dapat berjalan sesuai dengan kebutuhan operasional pengguna. Pengujian dieksekusi mengacu pada rancangan skenario uji di tahap prosedur pengujian dengan menggunakan instrumen *Black Box Testing*, *End-to-End Testing*, dan *Business Rule Validation Testing*. Hasil pengujian memvalidasi bahwa sistem mampu memfasilitasi administrasi sekolah, perputaran keuangan, e-commerce, serta manajemen pembelajaran digital secara terintegrasi penuh.

### 5.4.1 Detail Hasil Pengujian
Bagian ini berisi detail hasil pengujian secara menyeluruh dari seluruh kasus uji (*test case*) yang telah dirancang untuk mewakili proses utama pada platform PAUD. Pengujian berikut membuktikan kapabilitas fungsional antarmuka, sinkronisasi status antar modul, serta kekuatan validasi perlindungan logika bisnis.

| Item | Keterangan |
| :--- | :--- |
| **ID Pengujian** | BB-001 |
| **Nama Butir Uji** | Pembuatan Ruang Kerja (*Tenant*) Baru |
| **Deskripsi** | Memeriksa fungsi registrasi akun institusi agar secara otomatis memisahkan *environment* data sekolah baru. |
| **Kondisi Awal** | Pengguna berada di halaman registrasi awam. |
| **Tanggal Pengujian** | 10 Juni 2026 |
| **Penguji** | [Nama Penulis / Anda Sendiri] |
| **Skenario Pengujian** | 1. Mengisi form pendaftaran dengan profil sekolah.<br>2. Menekan tombol submit registrasi.<br>3. *Login* menggunakan akun yang baru saja dibuat. |

**Hasil Pengujian:**
| Yang Diharapkan | Hasil Pengamatan |
| :--- | :--- |
| Dasbor terbuka dengan tabel data murid kosong (bersih) | Ruang kerja baru sukses terbentuk dan terisolasi |
**Kesimpulan:** Pengujian sukses. Sistem mampu membentuk ekosistem *tenant* yang siap pakai.

<br>

| Item | Keterangan |
| :--- | :--- |
| **ID Pengujian** | BB-002 |
| **Nama Butir Uji** | Penambahan Profil Murid & Relasi Wali |
| **Deskripsi** | Memeriksa apakah form penambahan siswa berhasil mengaitkan identitas anak dengan akun (*user*) wali muridnya. |
| **Kondisi Awal** | Berada di halaman tambah siswa (`/students/create`). |
| **Tanggal Pengujian** | 10 Juni 2026 |
| **Penguji** | [Nama Penulis / Anda Sendiri] |
| **Skenario Pengujian** | 1. Memasukkan data identitas siswa.<br>2. Memilih email orang tua dari kotak *dropdown* relasi.<br>3. Menyimpan data ke *database*. |

**Hasil Pengujian:**
| Yang Diharapkan | Hasil Pengamatan |
| :--- | :--- |
| Nama siswa tertaut ke panel keluarga orang tua tersebut | Profil anak otomatis muncul di dasbor *Parent* |
**Kesimpulan:** Pengujian sukses. Relasi data antara entitas siswa dan pengguna terjalin sempurna.

<br>

| Item | Keterangan |
| :--- | :--- |
| **ID Pengujian** | BB-003 |
| **Nama Butir Uji** | Input Nilai Perkembangan (Asesmen) |
| **Deskripsi** | Memeriksa fungsionalitas guru saat memberikan nilai kualitatif berskala huruf (MB, BSH, dll) kepada siswa. |
| **Kondisi Awal** | Guru membuka halaman form asesmen harian siswa terkait. |
| **Tanggal Pengujian** | 10 Juni 2026 |
| **Penguji** | [Nama Penulis / Anda Sendiri] |
| **Skenario Pengujian** | 1. Memilih indikator pembelajaran.<br>2. Memilih skala huruf (*dropdown*).<br>3. Menyimpan nilai. |

**Hasil Pengujian:**
| Yang Diharapkan | Hasil Pengamatan |
| :--- | :--- |
| Nilai berhasil tersimpan dan muncul di rekapitulasi rapor | Nilai kualitatif terekam di pangkalan data |
**Kesimpulan:** Pengujian fungsional lulus. Guru dapat menginput nilai kualitatif tanpa kendala *error*.

<br>

| Item | Keterangan |
| :--- | :--- |
| **ID Pengujian** | BB-004 |
| **Nama Butir Uji** | Pengecekan *Checkout Marketplace* |
| **Deskripsi** | Memeriksa apakah pengunjung awam dapat memesan kursus dari katalog publik dan melihat rincian keranjangnya. |
| **Kondisi Awal** | Pengguna *login* sebagai pengguna awam (non-sekolah) dan melihat *landing page*. |
| **Tanggal Pengujian** | 10 Juni 2026 |
| **Penguji** | [Nama Penulis / Anda Sendiri] |
| **Skenario Pengujian** | 1. Menekan beli pada sebuah kursus.<br>2. Masuk ke laman *checkout*.<br>3. Sistem mengkalkulasi harga dan pajak. |

**Hasil Pengujian:**
| Yang Diharapkan | Hasil Pengamatan |
| :--- | :--- |
| Total *invoice* sesuai dengan harga pokok ditambah potongan | Nominal *checkout* terhitung presisi |
**Kesimpulan:** Black Box fungsional pemesanan berhasil, proses *add-to-cart* berjalan logis.

<br>

| Item | Keterangan |
| :--- | :--- |
| **ID Pengujian** | BB-005 |
| **Nama Butir Uji** | Eksekusi Pemutar LMS dan Kuis |
| **Deskripsi** | Memeriksa apakah video bisa diputar dan soal ujian bisa dipilih jawabannya oleh pelajar. |
| **Kondisi Awal** | Siswa memiliki akses ke kursus dan membuka modul video pembelajaran pertama. |
| **Tanggal Pengujian** | 10 Juni 2026 |
| **Penguji** | [Nama Penulis / Anda Sendiri] |
| **Skenario Pengujian** | 1. Memutar media video sampai selesai.<br>2. Membuka halaman kuis.<br>3. Menjawab pertanyaan pilihan ganda (*radio button*). |

**Hasil Pengujian:**
| Yang Diharapkan | Hasil Pengamatan |
| :--- | :--- |
| *Player* video berjalan dan *input* soal kuis dapat ditekan | Fungsionalitas multimedia beroperasi normal |
**Kesimpulan:** Fungsionalitas antarmuka LMS responsif dan elemen ujian interaktif merespons kursor pengguna.

<br>

| Item | Keterangan |
| :--- | :--- |
| **ID Pengujian** | E2E-001 |
| **Nama Butir Uji** | Integrasi Isolasi Data Lintas Tenant |
| **Deskripsi** | Membuktikan bahwa guru dari Sekolah A tidak akan bisa melihat data profil murid dari Sekolah B. |
| **Kondisi Awal** | Tersedia 2 sekolah berbeda (A dan B) dengan murid yang berbeda. Penguji *login* sebagai guru Sekolah A. |
| **Tanggal Pengujian** | 10 Juni 2026 |
| **Penguji** | [Nama Penulis / Anda Sendiri] |
| **Skenario Pengujian** | 1. Guru Sekolah A mengakses menu direktori siswa.<br>2. Sistem memicu *query database* dengan filter *ID Tenant*.<br>3. Mencoba memanipulasi *URL parameter* untuk melihat murid sekolah B. |

**Hasil Pengujian:**
| Yang Diharapkan | Hasil Pengamatan |
| :--- | :--- |
| Permintaan data murid B dicegat oleh *Middleware* (*Error 403 / 404*) | Sistem menolak upaya akses ilegal beda *tenant* |
**Kesimpulan:** *End-to-End* isolasi data lulus. Batas partisi keamanan ruang kerja institusi berfungsi mutlak.

<br>

| Item | Keterangan |
| :--- | :--- |
| **ID Pengujian** | E2E-002 |
| **Nama Butir Uji** | Integrasi Pelunasan Tagihan SPP |
| **Deskripsi** | Menilai apakah persetujuan pembayaran SPP oleh Admin secara otomatis menghilangkan notifikasi tunggakan di aplikasi Wali Murid. |
| **Kondisi Awal** | Siswa memiliki 1 tagihan SPP berstatus "Unpaid". Wali murid memiliki peringatan merah di dasbor. |
| **Tanggal Pengujian** | 10 Juni 2026 |
| **Penguji** | [Nama Penulis / Anda Sendiri] |
| **Skenario Pengujian** | 1. Admin sekolah menekan konfirmasi "Lunas" pada faktur SPP tersebut.<br>2. Wali murid membuka ulang ( *refresh*) halaman dasbor keluarganya. |

**Hasil Pengujian:**
| Yang Diharapkan | Hasil Pengamatan |
| :--- | :--- |
| Status faktur di panel *parent* berubah menjadi "Paid" dan peringatan hilang | *Badge* status seketika berubah warna menjadi hijau |
**Kesimpulan:** Alur sinkronisasi keuangan berhasil. Antarmuka admin dan wali murid saling terintegrasi secara *real-time*.

<br>

| Item | Keterangan |
| :--- | :--- |
| **ID Pengujian** | E2E-003 |
| **Nama Butir Uji** | Integrasi Pembukaan Gembok LMS via *Marketplace* |
| **Deskripsi** | Menguji proses perizinan di mana kursus yang asalnya digembok akan otomatis terbuka isinya setelah simulasi pelunasan terjadi. |
| **Kondisi Awal** | Pelanggan memesan kursus dan status transaksinya masih "Pending". Tombol *Belajar* di LMS masih buram/dikunci. |
| **Tanggal Pengujian** | 10 Juni 2026 |
| **Penguji** | [Nama Penulis / Anda Sendiri] |
| **Skenario Pengujian** | 1. Sistem mensimulasikan respons *callback* lunas dari *Payment Gateway*.<br>2. Pelanggan membuka halaman daftar kursus miliknya. |

**Hasil Pengujian:**
| Yang Diharapkan | Hasil Pengamatan |
| :--- | :--- |
| Modul materi dan tombol putar video di LMS dapat diakses 100% | Gembok kelas terbuka lebar sesuai skenario |
**Kesimpulan:** *End-to-End* *marketplace* ke LMS terbukti lancar tanpa butuh otorisasi manual admin.

<br>

| Item | Keterangan |
| :--- | :--- |
| **ID Pengujian** | E2E-004 |
| **Nama Butir Uji** | Generate Sertifikat Kelulusan Otomatis |
| **Deskripsi** | Memeriksa apakah sistem mampu memicu pembuatan dokumen PDF sertifikat secara otomatis seketika setelah pengguna berhasil menamatkan kursus LMS. |
| **Kondisi Awal** | Data peserta terdaftar di sebuah kursus, seluruh riwayat video telah selesai diputar, dan kuis akhir siap dikumpulkan. |
| **Tanggal Pengujian** | 10 Juni 2026 |
| **Penguji** | [Nama Penulis / Anda Sendiri] |
| **Skenario Pengujian** | 1. Pengguna menekan tombol "Kumpulkan Kuis" pada halaman akhir kursus.<br>2. Sistem memvalidasi kalkulasi jawaban benar (minimal persentase *passing grade*).<br>3. Sistem mengubah status progres *Enrollment* menjadi Lulus (100%).<br>4. Sistem secara otomatis memproduksi *file* PDF Sertifikat dan melampirkannya ke direktori sertifikat akun peserta. |

**Hasil Pengujian:**
| Yang Diharapkan | Hasil Pengamatan |
| :--- | :--- |
| Skor kuis dihitung dan *progress bar* menyentuh angka 100% | Progres berubah 100% dan skor kuis tersimpan |
| Tautan unduhan sertifikat PDF muncul di dasbor | Sertifikat beratasnamakan *user* tercetak otomatis |
**Kesimpulan:** Pengujian E2E pembuatan sertifikat instan lulus dengan mulus.

<br>

| Item | Keterangan |
| :--- | :--- |
| **ID Pengujian** | BR-001 |
| **Nama Butir Uji** | Validasi Kuota Langganan *Student Limit* |
| **Deskripsi** | Memeriksa apakah sistem tangguh dalam menolak formulir pendaftaran siswa tambahan apabila sekolah tersebut telah menyentuh batas izin kuota maksimal untuk paket berlangganan gratis (*Free*). |
| **Kondisi Awal** | Ruang kerja (*tenant*) sekolah berstatus paket *Free*, dan akumulasi data siswa aktif pada pangkalan data sudah tepat berada di angka batas atas kuota (misalnya 50 siswa). |
| **Tanggal Pengujian** | 10 Juni 2026 |
| **Penguji** | [Nama Penulis / Anda Sendiri] |
| **Skenario Pengujian** | 1. *Admin* sekolah masuk ke halaman tambah siswa baru (`/students/create`).<br>2. Mengisi biodata formulir siswa fiktif secara lengkap.<br>3. *Admin* menekan tombol simpan (*Submit*).<br>4. Sistem mengevaluasi kalkulasi relasi `count()` siswa aktif dengan variabel limit berlangganan. |

**Hasil Pengujian:**
| Yang Diharapkan | Hasil Pengamatan |
| :--- | :--- |
| Sistem memblokir upaya penyimpanan data ke dalam *database* | Tolakan *query database* berjalan aktif |
| Antarmuka menampilkan notifikasi visual perihal limit kuota penuh | Muncul peringatan merah pengajuan layanan *Pro* |
**Kesimpulan:** Validasi aturan pembatasan bisnis lolos sempurna dalam mencegah kecurangan batas penggunaan layanan.

<br>

| Item | Keterangan |
| :--- | :--- |
| **ID Pengujian** | BR-002 |
| **Nama Butir Uji** | Validasi Harga Diskon Negatif |
| **Deskripsi** | Mengevaluasi kekokohan sistem agar tidak mengizinkan formasi potongan diskon yang merugikan (contoh: harga 50rb diberi diskon 100rb). |
| **Kondisi Awal** | Administrator *Marketplace* sedang membuat/mengubah harga sebuah kelas *online*. |
| **Tanggal Pengujian** | 10 Juni 2026 |
| **Penguji** | [Nama Penulis / Anda Sendiri] |
| **Skenario Pengujian** | 1. Memasukkan nilai Harga Asli (*Original Price*) Rp. 50.000.<br>2. Memasukkan nilai Harga Jual (*Discount Price*) Rp. 150.000 (Lebih mahal dari aslinya).<br>3. Menekan tombol Simpan. |

**Hasil Pengujian:**
| Yang Diharapkan | Hasil Pengamatan |
| :--- | :--- |
| Validasi form memblokir aksi karena harga jual tidak logis | Muncul pesan peringatan error warna merah di form |
**Kesimpulan:** Aturan bisnis perlindungan distorsi harga produk bekerja dengan baik.

<br>

| Item | Keterangan |
| :--- | :--- |
| **ID Pengujian** | BR-003 |
| **Nama Butir Uji** | Validasi Prematur Klaim Sertifikat |
| **Deskripsi** | Memastikan peserta yang berusaha melompati proses belajar tidak akan dapat meretas logika tombol klaim penganugerahan sertifikat. |
| **Kondisi Awal** | Siswa berada di dalam LMS dengan progres menonton video baru 20% tanpa menyelesaikan bab akhir. |
| **Tanggal Pengujian** | 10 Juni 2026 |
| **Penguji** | [Nama Penulis / Anda Sendiri] |
| **Skenario Pengujian** | 1. Siswa mencoba menekan tombol "Akses Sertifikat" secara manual atau menembus parameter URL (*Bypass*). |

**Hasil Pengujian:**
| Yang Diharapkan | Hasil Pengamatan |
| :--- | :--- |
| Sistem mengembalikan pesan tolak karena syarat 100% absen | Layar peringatan *Unauthorized / Not Completed* muncul |
**Kesimpulan:** Logika pencegahan validasi penerbitan penghargaan edukasi sangat kuat.

### 5.4.2 Rekapitulasi Hasil Pengujian
Setelah menempuh penyelesaian eksekusi pengujian pada pilar fungsional antarmuka, pengetesan integrasi alur (*End-to-End*), serta injeksi pengujian pada ranah aturan bisnis sistem, seluruh capaian ini direkapitulasi untuk mengukur tingkat keberhasilan (*Success Rate*) aplikasi secara gamblang. Perekapan matriks di bawah ini dikompilasi berdasarkan peta skenario di tahap prosedur pengujian awal.

Berdasarkan hasil pengetesan simulasi komprehensif, seluruh fitur vital platform PAUD berhasil beroperasi mulus merespons masukan (*input*) sesuai dengan proses bisnis instansi. Integrasi antar *endpoint* layar serta tameng perlindungan aturan peladen juga terbukti tidak tertembus kegagalan (*error-free*).

| ID | Object | Butir Uji | Teknik Pengujian | Status |
| :--- | :--- | :--- | :--- | :--- |
| BB-001 | Multi-School | Pendaftaran akun dan pembuatan ruang kerja (*tenant*) sekolah baru | Black Box Testing | Passed |
| BB-002 | Manajemen Siswa | Penambahan data biodata anak dan penautan akun profil wali murid | Black Box Testing | Passed |
| BB-003 | Asesmen PAUD | Pengisian form skala nilai perkembangan anak oleh tenaga pendidik | Black Box Testing | Passed |
| BB-004 | Marketplace | Memasukkan produk kursus ke dalam keranjang dan proses *checkout* | Black Box Testing | Passed |
| BB-005 | LMS | Memutar pemutar video materi dan mengisi soal pilihan ganda pada kuis | Black Box Testing | Passed |
| E2E-001 | Pendaftaran & Akses | Integrasi pendaftaran *tenant* dengan ketepatan isolasi data ruang kerja | End-to-End Testing | Passed |
| E2E-002 | Pembayaran SPP | Integrasi pelunasan tagihan SPP dengan pembaruan status riwayat tagihan wali murid | End-to-End Testing | Passed |
| E2E-003 | Checkout & LMS | Integrasi keberhasilan pembayaran *marketplace* dengan pembukaan gembok akses kelas LMS | End-to-End Testing | Passed |
| E2E-004 | Kuis & Sertifikat | Integrasi kelulusan nilai kuis akhir dengan *generate* sertifikat PDF secara otomatis | End-to-End Testing | Passed |
| BR-001 | Limit Registrasi | Validasi penolakan siswa baru jika kuota paket langganan *Free* sekolah telah penuh | Business Rule Validation | Passed |
| BR-002 | Validasi Diskon | Validasi nilai *input* harga diskon agar nilai perhitungan akhir tidak menjadi di bawah nol | Business Rule Validation | Passed |
| BR-003 | Kelulusan LMS | Validasi sistem menolak pencetakan sertifikat jika progres materi siswa belum mencapai 100% | Business Rule Validation | Passed |

Berdasarkan himpunan rekapitulasi data empiris di atas, keseluruhan sampel prosedur uji coba berhasil menyandang status **Passed**. Fakta kuantitatif ini mensahkan bahwa fungsional antarmuka publik, persilangan data alur pengguna, dan pilar logika algoritma bisnis aplikasi telah beroperasi dengan semestinya.

### 5.4.3 Kesimpulan Pengujian
Mempertimbangkan seluruh pencapaian pada fase pengujian berbasis *Black Box Testing*, *End-to-End Testing*, maupun *Business Rule Validation Testing*, dapat disimpulkan bahwa platform manajemen SaaS PAUD ini telah berdiri kokoh dan sanggup memfasilitasi kebutuhan alur bisnis ekosistem persekolahan modern. Skenario evaluasi krusial yang membentang dari operasi registrasi instansi, manajemen ruang kelas siswa, pengolahan transaksi *e-commerce*, hingga area belajar LMS mandiri tercatat sukses menyelesaikan tugasnya masing-masing.

Lebih dari sekadar fungsionalitas UI murni, antarmuka sistem juga membuktikan ketahanannya dalam menjaga sinkronisasi (*integration*) di layar maupun memblokir upaya *input* yang menyalahi logika transaksi melalui pembatasan keamanan bisnis (*business rule*). Dengan perolehan angka ketuntasan uji fungsional sempurna ini, sistem peranti lunak berbasis layanan multitenan ini dapat dinyatakan laik (*feasible*) untuk melenggang ke tahap operasional pada lingkungan sekolah sesungguhnya.

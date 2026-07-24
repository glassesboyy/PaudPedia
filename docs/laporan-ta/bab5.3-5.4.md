## 5.3 Prosedur Pengujian
Prosedur pengujian dilakukan untuk memastikan bahwa seluruh antarmuka fitur, integrasi antar halaman, serta aturan logika bisnis pada platform SaaS PAUD ini dapat berjalan sesuai dengan kebutuhan spesifikasi dan proses operasional institusi pendidikan. Pengujian dilakukan secara manual menggunakan pendekatan *Functional Testing* (Howden, 1980).

Pengujian difokuskan pada fitur-fitur esensial sistem yang berkaitan dengan manajemen akademik, tata kelola keuangan, marketplace, proses transaksi, kegiatan belajar asinkron di LMS, hingga validasi hierarki administrator. Setiap pengujian dieksekusi berdasarkan skenario terstruktur guna memverifikasi apakah hasil keluaran dari antarmuka sistem sudah tepat dalam menangani masukan pengguna yang wajar maupun tidak wajar. Rincian skenario pengujian yang digunakan pada penelitian ini disajikan pada Tabel 12.

**Tabel 12 Prosedur Pengujian**

| ID | Object | Butir Uji | Teknik |
| :--- | :--- | :--- | :--- |
| FT-001 | Multi-School | Pendaftaran Tenant (Sekolah) | Functional Testing |
| FT-002 | Manajemen Siswa | Penambahan profil murid & relasi wali | Functional Testing |
| FT-003 | Asesmen PAUD | Input nilai perkembangan (Asesmen) | Functional Testing |
| FT-004 | Marketplace | Pengecekan Checkout Marketplace | Functional Testing |
| FT-005 | LMS | Eksekusi LMS dan Kuis | Functional Testing |
| FT-006 | Pendaftaran & Akses | Integrasi Isolasi Data Lintas Tenant | Functional Testing |
| FT-007 | Pembayaran SPP | Integrasi Pelunasan Tagihan SPP | Functional Testing |
| FT-008 | Limit Registrasi | Validasi Kuota Langganan Student Limit | Functional Testing |
| FT-009 | Validasi Diskon | Validasi Harga Diskon Negatif | Functional Testing |
| FT-010 | Kelulusan LMS | Validasi Prematur Klaim Sertifikat | Functional Testing |

## 5.4 Hasil Uji dan Kesimpulan
Pengujian sistem dilakukan untuk memastikan bahwa fungsionalitas UI, alur integrasi antar-halaman, dan keakuratan validasi aturan bisnis pada platform PAUD dapat berjalan sesuai dengan kebutuhan operasional pengguna. Pengujian dieksekusi mengacu pada rancangan skenario uji di tahap prosedur pengujian dengan menggunakan instrumen *Functional Testing*. Hasil pengujian memvalidasi bahwa sistem mampu memfasilitasi administrasi sekolah, perputaran keuangan, e-commerce, serta manajemen pembelajaran digital secara terintegrasi penuh.

### 5.4.1 Detail Hasil Pengujian
Rincian hasil pengujian untuk setiap skenario yang telah dirancang disajikan pada Tabel 13 hingga Tabel 22. Setiap tabel memuat informasi mengenai kondisi awal, skenario pengujian, hasil yang diharapkan, hasil pengamatan, serta kesimpulan untuk memverifikasi bahwa setiap fungsi sistem telah berjalan sesuai dengan kebutuhan yang ditetapkan.

**Tabel 13 Hasil Uji - Pembuatan Tenant**

| Item | Keterangan |
| :--- | :--- |
| **ID Pengujian** | FT-001 |
| **Nama Butir Uji** | Pendaftaran Tenant (Sekolah) |
| **Deskripsi** | Memeriksa fungsi registrasi akun institusi agar secara otomatis memisahkan environment data sekolah baru. |
| **Kondisi Awal** | Pengguna berada di halaman registrasi sekolah. |
| **Tanggal Pengujian** | 19 Mei 2026 |
| **Penguji** | Teguh Surya Zulfikar |
| **Skenario Pengujian** | 1. Mengisi form pendaftaran dengan profil sekolah.<br>2. Menekan tombol submit registrasi.<br>3. Login menggunakan akun yang baru saja dibuat. |

**Hasil Pengujian:**
| Yang Diharapkan | Hasil Pengamatan |
| :--- | :--- |
| Dasbor terbuka dengan tabel data murid kosong (bersih) | Ruang kerja baru sukses terbentuk dan terisolasi |
**Kesimpulan:** Pengujian berhasil. Sistem mampu membentuk ekosistem tenant yang siap pakai.

<br>

**Tabel 14 Hasil Uji - Penambahan Profil Murid & Relasi Wali**

| Item | Keterangan |
| :--- | :--- |
| **ID Pengujian** | FT-002 |
| **Nama Butir Uji** | Penambahan Profil Murid & Relasi Wali |
| **Deskripsi** | Memeriksa apakah form penambahan siswa berhasil mengaitkan identitas anak dengan akun (user) wali muridnya. |
| **Kondisi Awal** | Berada di halaman tambah siswa. |
| **Tanggal Pengujian** | 19 Mei 2026 |
| **Penguji** | Teguh Surya Zulfikar |
| **Skenario Pengujian** | 1. Memasukkan data identitas siswa.<br>2. Memilih email orang tua dari kotak dropdown relasi.<br>3. Menyimpan data ke database. |

**Hasil Pengujian:**
| Yang Diharapkan | Hasil Pengamatan |
| :--- | :--- |
| Nama siswa tertaut ke panel keluarga orang tua tersebut | Profil anak otomatis muncul di dasbor Parent |
**Kesimpulan:** Pengujian berhasil. Relasi data antara entitas siswa dan pengguna terjalin sempurna.

<br>

**Tabel 15 Hasil Uji - Input Nilai Perkembangan (Assesmen)**

| Item | Keterangan |
| :--- | :--- |
| **ID Pengujian** | FT-003 |
| **Nama Butir Uji** | Input Nilai Perkembangan (Asesmen) |
| **Deskripsi** | Memeriksa fungsionalitas guru saat memberikan nilai kualitatif berskala huruf (MB, BSH, dan sejenisnya) kepada siswa. |
| **Kondisi Awal** | Guru membuka halaman form asesmen harian siswa. |
| **Tanggal Pengujian** | 19 Mei 2026 |
| **Penguji** | Teguh Surya Zulfikar |
| **Skenario Pengujian** | 1. Memilih kelas, program pembelajaran, semester, dan bulan.<br>2. Memilih skala huruf (BB, MB, BSH, BSB) pada setiap indikator di program pembelajaran.<br>3. Menyimpan nilai atau assesmen. |

**Hasil Pengujian:**
| Yang Diharapkan | Hasil Pengamatan |
| :--- | :--- |
| Nilai berhasil tersimpan dan muncul di rekapitulasi rapor | Nilai kualitatif terekam di pangkalan data |
**Kesimpulan:** Pengujian fungsional lulus. Guru dapat menginput nilai kualitatif tanpa kendala error.

<br>

**Tabel 16 Hasil Uji - Pengecekan Checkout Marketplace**

| Item | Keterangan |
| :--- | :--- |
| **ID Pengujian** | FT-004 |
| **Nama Butir Uji** | Pengecekan Checkout Marketplace |
| **Deskripsi** | Memeriksa apakah pengguna dapat memesan kursus dari katalog publik dan melihat rincian keranjang belanjanya. |
| **Kondisi Awal** | Pengguna sudah terdaftar pada sistem, sudah melakukan login sistem dan berada di halaman utama aplikasi. |
| **Tanggal Pengujian** | 19 Mei 2026 |
| **Penguji** | Teguh Surya Zulfikar |
| **Skenario Pengujian** | 1. Menekan tombol keranjang lalu beli pada salah satu kursus / webinar / produk.<br>2. Masuk ke halaman checkout.<br>3. Sistem menghitung harga, diskon, dan total pembayaran. |

**Hasil Pengujian:**
| Yang Diharapkan | Hasil Pengamatan |
| :--- | :--- |
| Total invoice sesuai dengan harga item setelah perhitungan diskon dan biaya terkait | Nominal checkout terhitung dengan tepat dan sesuai perhitungan |
**Kesimpulan:** Pengujian berhasil. Proses pemesanan, penambahan ke keranjang, dan perhitungan total pembayaran berjalan sesuai fungsinya.

<br>

**Tabel 17 Hasil Uji - Eksekusi LMS dan Kuis**

| Item | Keterangan |
| :--- | :--- |
| **ID Pengujian** | FT-005 |
| **Nama Butir Uji** | Eksekusi LMS dan Kuis |
| **Deskripsi** | Memeriksa apakah video/PDF/teks pembelajaran dapat ditampilkan dan soal kuis dapat dijawab oleh peserta didik. |
| **Kondisi Awal** | Siswa memiliki akses ke kursus dan membuka modul video/PDF/teks pembelajaran pertama. |
| **Tanggal Pengujian** | 19 Mei 2026 |
| **Penguji** | Teguh Surya Zulfikar |
| **Skenario Pengujian** | 1. Membuka ataupun memutar PDF/video/teks pembelajaran.<br>2. Membuka halaman kuis.<br>3. Menjawab pertanyaan pilihan ganda melalui radio button. |

**Hasil Pengujian:**
| Yang Diharapkan | Hasil Pengamatan |
| :--- | :--- |
| Pemutar video dan penampil PDF dan teks berfungsi dengan baik dan pilihan jawaban kuis dapat dipilih oleh pengguna | Fungsionalitas multimedia dan komponen kuis berjalan normal |
**Kesimpulan:** Pengujian berhasil. Antarmuka LMS responsif, video dapat diputar dengan baik, PDF dan teks pembelajaran dapat dibuka dengan baik, dan elemen kuis interaktif dapat digunakan sesuai fungsinya.

<br>

**Tabel 18 Hasil Uji - Integrasi Isolasi Data Lintas Tenant**

| Item | Keterangan |
| :--- | :--- |
| **ID Pengujian** | FT-006 |
| **Nama Butir Uji** | Integrasi Isolasi Data Lintas Tenant |
| **Deskripsi** | Memastikan bahwa pengguna dari satu sekolah tidak dapat mengakses atau melihat data yang dimiliki oleh sekolah lain. |
| **Kondisi Awal** | Tersedia dua sekolah berbeda (Sekolah A dan Sekolah B) dengan data siswa yang berbeda. Penguji login sebagai guru Sekolah A. |
| **Tanggal Pengujian** | 19 Mei 2026 |
| **Penguji** | Teguh Surya Zulfikar |
| **Skenario Pengujian** | 1. Guru Sekolah A membuka menu direktori siswa.<br>2. Sistem mengambil data berdasarkan identitas tenant yang aktif.<br>3. Penguji mencoba memanipulasi parameter URL untuk mengakses data siswa milik Sekolah B. |

**Hasil Pengujian:**
| Yang Diharapkan | Hasil Pengamatan |
| :--- | :--- |
| Permintaan akses terhadap data siswa Sekolah B ditolak oleh sistem dengan respons Error 403 atau Error 404 | Sistem berhasil menolak upaya akses data lintas tenant yang tidak sah |
**Kesimpulan:** Pengujian berhasil. Mekanisme isolasi data antar tenant berjalan dengan baik sehingga setiap sekolah hanya dapat mengakses data yang menjadi hak aksesnya.

<br>

**Tabel 19 Hasil Uji - Integrasi Pelunasan Tagihan SPP**

| Item | Keterangan |
| :--- | :--- |
| **ID Pengujian** | FT-007 |
| **Nama Butir Uji** | Integrasi Pelunasan Tagihan SPP |
| **Deskripsi** | Memastikan bahwa perubahan status pembayaran SPP yang dilakukan oleh operator sekolah atau guru secara otomatis tercermin pada aplikasi wali murid. |
| **Kondisi Awal** | Siswa memiliki satu tagihan SPP berstatus belum dibayar. Pada dasbor wali murid terdapat informasi tunggakan yang masih aktif. |
| **Tanggal Pengujian** | 19 Mei 2026 |
| **Penguji** | Teguh Surya Zulfikar |
| **Skenario Pengujian** | 1. Operator sekolah atau guru membuka detail tagihan SPP.<br>2. Operator sekolah atau guru mengubah status tagihan menjadi "Lunas" atau "Paid".<br>3. Wali murid melakukan refresh pada halaman dasbor miliknya. |

**Hasil Pengujian:**
| Yang Diharapkan | Hasil Pengamatan |
| :--- | :--- |
| Status tagihan pada panel wali murid berubah menjadi "Paid" dan notifikasi tunggakan tidak lagi ditampilkan | Status tagihan berhasil diperbarui dan indikator pembayaran berubah sesuai status pelunasan |
**Kesimpulan:** Pengujian berhasil. Perubahan status pembayaran pada modul administrasi sekolah tersinkronisasi dengan baik ke aplikasi wali murid sehingga informasi tagihan selalu konsisten pada kedua sisi sistem.

<br>

**Tabel 20 Hasil Uji - Validasi Kuota Langganan Student Limit**

| Item | Keterangan |
| :--- | :--- |
| **ID Pengujian** | FT-008 |
| **Nama Butir Uji** | Validasi Kuota Langganan Student Limit |
| **Deskripsi** | Memastikan sistem menolak penambahan siswa baru ketika jumlah siswa aktif telah mencapai batas maksimum yang ditentukan oleh paket langganan sekolah. |
| **Kondisi Awal** | Sekolah menggunakan paket Free dan jumlah siswa aktif telah mencapai batas maksimum kuota yang diperbolehkan (misalnya 20 siswa). |
| **Tanggal Pengujian** | 19 Mei 2026 |
| **Penguji** | Teguh Surya Zulfikar |
| **Skenario Pengujian** | 1. Admin sekolah membuka halaman tambah siswa baru.<br>2. Mengisi seluruh data siswa baru pada formulir.<br>3. Menekan tombol simpan (Submit).<br>4. Sistem melakukan validasi jumlah siswa aktif terhadap batas kuota paket langganan yang digunakan. |

**Hasil Pengujian:**
| Yang Diharapkan | Hasil Pengamatan |
| :--- | :--- |
| Sistem menolak penyimpanan data siswa baru ke dalam database. Sistem menampilkan notifikasi bahwa kuota siswa telah penuh | Proses penyimpanan dibatalkan karena kuota siswa telah mencapai batas maksimum. Pesan peringatan ditampilkan dan pengguna diarahkan untuk meningkatkan paket langganan |
**Kesimpulan:** Pengujian berhasil. Sistem mampu menerapkan pembatasan jumlah siswa sesuai ketentuan paket langganan sehingga penggunaan layanan tetap sesuai dengan aturan yang berlaku.

<br>

**Tabel 21 Hasil Uji - Validasi Harga Diskon Negatif**

| Item | Keterangan |
| :--- | :--- |
| **ID Pengujian** | FT-009 |
| **Nama Butir Uji** | Validasi Harga Diskon Negatif |
| **Deskripsi** | Memastikan sistem menerapkan validasi harga sehingga nilai diskon atau harga jual tidak menghasilkan kondisi harga yang tidak logis. |
| **Kondisi Awal** | Administrator Marketplace sedang membuat atau mengubah data kursus pada halaman manajemen produk. |
| **Tanggal Pengujian** | 19 Mei 2026 |
| **Penguji** | Teguh Surya Zulfikar |
| **Skenario Pengujian** | 1. Memasukkan Harga Asli (Original Price) sebesar Rp50.000.<br>2. Memasukkan Harga Jual (Discount Price) sebesar Rp150.000 sehingga nilainya tidak sesuai dengan aturan diskon yang berlaku.<br>3. Menekan tombol Simpan.<br>4. Sistem melakukan validasi terhadap nilai harga yang dimasukkan. |

**Hasil Pengujian:**
| Yang Diharapkan | Hasil Pengamatan |
| :--- | :--- |
| Sistem menolak penyimpanan data karena nilai harga atau diskon tidak memenuhi aturan yang ditetapkan | Proses penyimpanan dibatalkan dan sistem menampilkan pesan validasi pada formulir |
**Kesimpulan:** Pengujian berhasil. Sistem mampu mendeteksi dan menolak konfigurasi harga yang tidak valid sehingga integritas data harga produk tetap terjaga.

<br>

**Tabel 22 Hasil Uji - Validasi Prematur Klaim Sertifikat**

| Item | Keterangan |
| :--- | :--- |
| **ID Pengujian** | FT-010 |
| **Nama Butir Uji** | Validasi Prematur Klaim Sertifikat |
| **Deskripsi** | Memastikan sistem hanya mengizinkan penerbitan sertifikat kepada peserta yang telah memenuhi seluruh persyaratan penyelesaian kursus. |
| **Kondisi Awal** | Peserta berada di dalam LMS dengan progres pembelajaran baru mencapai 20% dan belum menyelesaikan seluruh materi maupun evaluasi akhir. |
| **Tanggal Pengujian** | 19 Mei 2026 |
| **Penguji** | Teguh Surya Zulfikar |
| **Skenario Pengujian** | 1. Peserta mencoba mengakses halaman sertifikat melalui tombol yang tersedia.<br>2. Peserta mencoba mengakses URL sertifikat secara langsung tanpa memenuhi syarat kelulusan.<br>3. Sistem melakukan validasi terhadap progres pembelajaran peserta terhadap nilai harga yang dimasukkan. |

**Hasil Pengujian:**
| Yang Diharapkan | Hasil Pengamatan |
| :--- | :--- |
| Sistem menolak permintaan penerbitan atau akses sertifikat karena progres pembelajaran belum mencapai 100% | Sistem menampilkan pesan bahwa kursus belum selesai dan akses sertifikat tidak diberikan |
**Kesimpulan:** Pengujian berhasil. Sistem mampu mencegah akses maupun penerbitan sertifikat bagi peserta yang belum menyelesaikan seluruh persyaratan kursus sehingga proses sertifikasi tetap sesuai dengan ketentuan yang berlaku.

### 5.4.2 Rekapitulasi Hasil Pengujian
Setelah seluruh skenario pengujian dilaksanakan, hasil pengujian direkapitulasi untuk mengukur tingkat keberhasilan implementasi fungsional sistem secara keseluruhan. Rekapitulasi tersebut disusun berdasarkan seluruh skenario pengujian yang telah dirancang pada tahap prosedur pengujian. Rincian rekapitulasi hasil pengujian disajikan pada Tabel 23.

Berdasarkan hasil pengujian, seluruh fitur utama pada platform PAUD berhasil beroperasi sesuai dengan kebutuhan fungsional yang telah ditetapkan. Integrasi antar modul, validasi aturan bisnis, serta respons sistem terhadap masukan pengguna juga berjalan dengan baik tanpa ditemukan kegagalan pada seluruh skenario pengujian.

**Tabel 23 Rekapitulasi Hasil Pengujian**

| ID | Object | Butir Uji | Teknik | Status |
| :--- | :--- | :--- | :--- | :--- |
| FT-001 | Multi-School | Pendaftaran Tenant (Sekolah) | FunctionalTesting | Passed |
| FT-002 | Manajemen Siswa | Penambahan profil murid & relasi wali | FunctionalTesting | Passed |
| FT-003 | Asesmen PAUD | Input nilai perkembangan (Asesmen) | FunctionalTesting | Passed |
| FT-004 | Marketplace | Pengecekan Checkout Marketplace | FunctionalTesting | Passed |
| FT-005 | LMS | Eksekusi LMS dan KuiS | FunctionalTesting | Passed |
| FT-006 | Pendaftaran & Akses | Integrasi Isolasi Data Lintas Tenant | FunctionalTesting | Passed |
| FT-007 | Pembayaran SPP | Integrasi Pelunasan Tagihan SPP | FunctionalTesting | Passed |
| FT-008 | Limit Registrasi | Validasi Kuota Langganan Student Limit | FunctionalTesting | Passed |
| FT-009 | Validasi Diskon | Validasi Harga Diskon Negatif | FunctionalTesting | Passed |
| FT-010 | Kelulusan LMS | Validasi Prematur Klaim Sertifikat | FunctionalTesting | Passed |

Berdasarkan rekapitulasi hasil pengujian pada Tabel 23, seluruh skenario pengujian memperoleh status Passed. Hasil tersebut menunjukkan bahwa seluruh fungsi yang diuji telah berjalan sesuai dengan kebutuhan sistem, baik pada aspek fungsional antarmuka, integrasi antar modul, maupun penerapan aturan logika bisnis.

### 5.4.3 Kesimpulan Pengujian
Mempertimbangkan seluruh pencapaian pada fase pengujian berbasis Functional Testing, dapat disimpulkan bahwa platform manajemen SaaS PAUD ini telah berdiri kokoh dan sanggup memfasilitasi kebutuhan alur bisnis ekosistem persekolahan PAUD modern. Skenario evaluasi krusial yang membentang dari operasi registrasi instansi, manajemen ruang kelas siswa, pengolahan transaksi e-commerce, hingga area belajar LMS mandiri tercatat sukses menyelesaikan tugasnya masing-masing.

Lebih dari sekadar fungsionalitas UI murni, antarmuka sistem juga membuktikan ketahanannya dalam menjaga sinkronisasi (integration) di layar maupun memblokir upaya input yang menyalahi logika transaksi melalui pembatasan keamanan bisnis (business rule). Dengan perolehan angka ketuntasan uji fungsional sempurna ini, sistem perangkat lunak berbasis layanan multi-tenant ini dapat dinyatakan layak untuk masuk ke tahap operasional pada lingkungan sekolah sesungguhnya.

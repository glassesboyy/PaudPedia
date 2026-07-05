## 5.3 Prosedur Pengujian
Prosedur pengujian dilakukan untuk memastikan bahwa seluruh antarmuka fitur, integrasi antar-halaman, serta aturan logika bisnis pada platform *SaaS* PAUD ini dapat berjalan sesuai dengan kebutuhan spesifikasi dan proses operasional institusi pendidikan. Seluruh tahapan pengujian ini dieksekusi secara manual mengacu pada metode terpadu **Pengujian Fungsional (*Functional Testing*)**.

Pengujian difokuskan pada fitur-fitur esensial sistem yang berkaitan dengan manajemen akademik oleh Kepala Sekolah dan Operator Sekolah, tata kelola keuangan internal, perniagaan digital di *marketplace*, proses integrasi transaksi, kegiatan belajar asinkron di LMS, hingga validasi hierarki administrator dan kuota langganan. Setiap pengujian dieksekusi berdasarkan skenario terstruktur guna memverifikasi apakah keluaran dari antarmuka sistem sudah tepat dalam menangani masukan pengguna yang wajar maupun tidak wajar.

Tabel 5.1 menunjukkan daftar prosedur pengujian fungsional yang dilakukan pada sistem.

| ID | Object | Butir Uji | Teknik Pengujian |
| :--- | :--- | :--- | :--- |
| FT-001 | Multi-School | Pendaftaran akun Kepala Sekolah dan pembuatan ruang kerja (*tenant*) sekolah baru | Pengujian Fungsional |
| FT-002 | Manajemen Siswa | Penambahan data biodata anak dan penautan akun profil wali murid oleh **Operator Sekolah** | Pengujian Fungsional |
| FT-003 | Asesmen PAUD | Pengisian form skala nilai perkembangan anak oleh guru dengan validasi *Temporal Assessment Integrity* | Pengujian Fungsional |
| FT-004 | Rapor Naratif | Penyusunan narasi rapor akhir semester dan pencetakan PDF dengan penyaringan cerdas *Smart Omission* | Pengujian Fungsional |
| FT-005 | Keuangan SIAKAD | Pencatatan iuran SPP dan tabungan siswa sebagai buku besar administratif (*ledger*) oleh Operator & Guru | Pengujian Fungsional |
| FT-006 | Marketplace | Memasukkan produk kursus ke dalam keranjang, perhitungan diskon, dan proses *checkout* B2C | Pengujian Fungsional |
| FT-007 | Integrasi LMS | Pembukaan gembok akses kelas LMS secara otomatis setelah konfirmasi pembayaran *marketplace* lunas | Pengujian Fungsional |
| FT-008 | LMS Interaktif | Pemutaran video materi pembelajaran dan pengisian soal pilihan ganda pada kuis | Pengujian Fungsional |
| FT-009 | Sertifikasi | Penerbitan sertifikat PDF secara otomatis setelah progres materi mencapai 100% dan lulus kuis | Pengujian Fungsional |
| FT-010 | Limit Registrasi | Validasi penolakan siswa baru oleh sistem jika kuota paket langganan *Free* sekolah telah penuh | Pengujian Fungsional |
| FT-011 | Validasi Diskon | Validasi penolakan input harga diskon produk agar harga akhir tidak bernilai negatif atau melampaui harga asli | Pengujian Fungsional |
| FT-012 | Validasi Sertifikat | Validasi penolakan pencetakan sertifikat jika progres materi belajar siswa di LMS belum mencapai 100% | Pengujian Fungsional |

## 5.4 Hasil Uji dan Kesimpulan
Pengujian sistem dilakukan untuk memastikan bahwa fungsionalitas UI, alur integrasi antar-halaman, dan keakuratan validasi aturan bisnis pada platform PAUD dapat berjalan sesuai dengan kebutuhan operasional pengguna. Pengujian dieksekusi mengacu pada rancangan skenario uji di tahap prosedur pengujian dengan menggunakan instrumen **Pengujian Fungsional (*Functional Testing*)**. Hasil pengujian memvalidasi bahwa sistem mampu memfasilitasi administrasi sekolah multi-tenant, perputaran catatan keuangan, e-commerce, serta manajemen pembelajaran digital secara terintegrasi penuh.

### 5.4.1 Detail Hasil Pengujian
Bagian ini berisi detail hasil pengujian secara menyeluruh dari seluruh kasus uji (*test case*) yang telah dirancang untuk mewakili proses utama pada platform PAUD. Pengujian berikut membuktikan kapabilitas fungsional antarmuka, sinkronisasi status antar modul, serta kekuatan validasi perlindungan logika bisnis.

| Item | Keterangan |
| :--- | :--- |
| **ID Pengujian** | FT-001 |
| **Nama Butir Uji** | Pembuatan Ruang Kerja (*Tenant*) Sekolah Baru |
| **Deskripsi** | Memeriksa fungsi registrasi akun Kepala Sekolah agar secara otomatis memisahkan *environment* data sekolah baru. |
| **Kondisi Awal** | Pengguna berada di halaman registrasi awal (`/auth/register`). |
| **Tanggal Pengujian** | 10 Juni 2026 |
| **Penguji** | [Nama Penulis / Anda Sendiri] |
| **Skenario Pengujian** | 1. Mengisi form pendaftaran dengan identitas Kepala Sekolah dan profil sekolah.<br>2. Menekan tombol submit registrasi.<br>3. *Login* menggunakan akun yang baru saja dibuat. |

**Hasil Pengujian:**
| Yang Diharapkan | Hasil Pengamatan |
| :--- | :--- |
| Dasbor terbuka dengan tabel data murid kosong (bersih) dan nama sekolah tercantum di *header* | Ruang kerja baru sukses terbentuk dan terisolasi sempurna |
**Kesimpulan:** Pengujian sukses. Sistem mampu membentuk ekosistem *tenant* yang siap pakai.

<br>

| Item | Keterangan |
| :--- | :--- |
| **ID Pengujian** | FT-002 |
| **Nama Butir Uji** | Penambahan Profil Murid & Relasi Wali oleh Operator |
| **Deskripsi** | Memeriksa apakah form penambahan siswa oleh Operator Sekolah berhasil mengaitkan identitas anak dengan akun profil wali muridnya. |
| **Kondisi Awal** | Operator Sekolah berada di halaman tambah siswa (`/students/create`). |
| **Tanggal Pengujian** | 10 Juni 2026 |
| **Penguji** | [Nama Penulis / Anda Sendiri] |
| **Skenario Pengujian** | 1. Operator memasukkan data identitas lengkap siswa.<br>2. Operator memilih kelas rombel dan nama orang tua dari kotak *dropdown* relasi.<br>3. Menekan tombol simpan data ke *database*. |

**Hasil Pengujian:**
| Yang Diharapkan | Hasil Pengamatan |
| :--- | :--- |
| Siswa tersimpan ke dalam rombel kelas dan namanya otomatis tertaut ke panel dasbor keluarga orang tua tersebut | Profil anak seketika muncul di dasbor *Parent* secara *read-only* |
**Kesimpulan:** Pengujian fungsional sukses. Relasi data antara entitas siswa, operator, dan wali murid terjalin sempurna.

<br>

| Item | Keterangan |
| :--- | :--- |
| **ID Pengujian** | FT-003 |
| **Nama Butir Uji** | Input Nilai Perkembangan dengan *Temporal Assessment Integrity* |
| **Deskripsi** | Memeriksa fungsionalitas guru saat memberikan nilai kualitatif berskala huruf (BB, MB, BSH, BSB) dan memastikan indikator baru tidak muncul pada masa penilaian lampau. |
| **Kondisi Awal** | Guru membuka halaman form asesmen observasi bulanan siswa (`/assessments`). |
| **Tanggal Pengujian** | 10 Juni 2026 |
| **Penguji** | [Nama Penulis / Anda Sendiri] |
| **Skenario Pengujian** | 1. Guru memilih bulan penilaian lampau (misal: Juli).<br>2. Memeriksa daftar indikator yang tampil (indikator yang baru dibuat Operator di bulan November tidak boleh muncul).<br>3. Memilih skala huruf (*dropdown*) dan menyimpannya. |

**Hasil Pengujian:**
| Yang Diharapkan | Hasil Pengamatan |
| :--- | :--- |
| Daftar indikator terfilter sesuai waktu pembuatannya (*timestamp*), dan nilai observasi berhasil tersimpan ke pangkalan data | Filter temporal aktif dan nilai kualitatif terekam akurat tanpa *error* |
**Kesimpulan:** Pengujian lulus. *Temporal Assessment Integrity* bekerja baik melindungi konsistensi penilaian guru.

<br>

| Item | Keterangan |
| :--- | :--- |
| **ID Pengujian** | FT-004 |
| **Nama Butir Uji** | Cetak Rapor Naratif dengan Penyaringan *Smart Omission* |
| **Deskripsi** | Memeriksa apakah pencetakan dokumen resmi PDF rapor akhir semester oleh Kepala Sekolah/Operator otomatis menyaring indikator yang bernilai kosong (0). |
| **Kondisi Awal** | Wali kelas telah melengkapi catatan pengantar dan rekomendasi di halaman rapor siswa (`/reports/:studentId`). |
| **Tanggal Pengujian** | 10 Juni 2026 |
| **Penguji** | [Nama Penulis / Anda Sendiri] |
| **Skenario Pengujian** | 1. Kepala Sekolah/Operator membuka halaman detail rapor siswa.<br>2. Menekan tombol "Cetak Rapor PDF".<br>3. Memeriksa isi dokumen PDF yang dihasilkan sistem. |

**Hasil Pengujian:**
| Yang Diharapkan | Hasil Pengamatan |
| :--- | :--- |
| Dokumen PDF tercetak rapi, di mana aspek/indikator yang tidak memiliki catatan observasi (kosong) tidak ditampilkan pada tabel cetakan | Fitur *Smart Omission* aktif mengabaikan indikator kosong pada cetakan PDF |
**Kesimpulan:** Pengujian sukses. Dokumen rapor naratif tetap ringkas, estetik, dan informatif bagi orang tua.

<br>

| Item | Keterangan |
| :--- | :--- |
| **ID Pengujian** | FT-005 |
| **Nama Butir Uji** | Pencatatan Keuangan SPP & Tabungan sebagai *Ledger* Administratif |
| **Deskripsi** | Membuktikan fungsi Modul Keuangan sebagai buku besar pembukuan sekolah oleh Operator & Guru, tanpa transaksi langsung di aplikasi bagi Orang Tua. |
| **Kondisi Awal** | Operator atau Guru membuka menu Manajemen Pembayaran SPP (`/finances/spp`) atau Tabungan (`/finances/savings`). |
| **Tanggal Pengujian** | 10 Juni 2026 |
| **Penguji** | [Nama Penulis / Anda Sendiri] |
| **Skenario Pengujian** | 1. Operator/Guru memilih siswa yang telah membayar SPP secara tunai/transfer bank konvensional.<br>2. Menekan konfirmasi status "Lunas / Paid".<br>3. Orang tua membuka ulang (*refresh*) dasbor pemantauan keuangannya. |

**Hasil Pengujian:**
| Yang Diharapkan | Hasil Pengamatan |
| :--- | :--- |
| Status tagihan di dasbor Operator berubah lunas, dan notifikasi merah tunggakan di dasbor Orang Tua seketika berubah menjadi hijau (*Paid*) | Sinkronisasi buku besar keuangan aktif secara *real-time* |
**Kesimpulan:** Pengujian lulus. Alur pencatatan kasir oleh Operator/Guru beroperasi logis sesuai batasan sistem.

<br>

| Item | Keterangan |
| :--- | :--- |
| **ID Pengujian** | FT-006 |
| **Nama Butir Uji** | Pengecekan Keranjang & *Checkout Marketplace* B2C |
| **Deskripsi** | Memeriksa apakah pengguna publik dapat memasukkan kursus ke keranjang, menyuntikkan kupon diskon, dan melihat kalkulasi tagihan. |
| **Kondisi Awal** | Pengguna publik *login* ke portal B2C dan melihat etalase katalog (`/courses`). |
| **Tanggal Pengujian** | 10 Juni 2026 |
| **Penguji** | [Nama Penulis / Anda Sendiri] |
| **Skenario Pengujian** | 1. Menekan tombol beli ("Add to Cart") pada suatu kelas kursus.<br>2. Masuk ke halaman keranjang (`/cart`) dan menuju layar *checkout*.<br>3. Memasukkan kode promo diskon dan menekan terapkan. |

**Hasil Pengujian:**
| Yang Diharapkan | Hasil Pengamatan |
| :--- | :--- |
| Sistem menghitung ulang subtotal, mengurangi nominal potongan promo, dan memunculkan *popup iframe Payment Gateway Midtrans Snap* | Kalkulasi *invoice* presisi dan gerbang pembayaran muncul |
**Kesimpulan:** Pengujian fungsional perniagaan digital berhasil, alur pesanan berjalan akurat.

<br>

| Item | Keterangan |
| :--- | :--- |
| **ID Pengujian** | FT-007 |
| **Nama Butir Uji** | Integrasi Pembukaan Gembok LMS setelah *Checkout* Lunas |
| **Deskripsi** | Menguji pembukaan gembok akses materi LMS secara otomatis sesaat setelah transaksi pembayaran *marketplace* dikonfirmasi lunas. |
| **Kondisi Awal** | Pelanggan memesan kursus dengan status "Pending" (tombol belajar di LMS dikunci). |
| **Tanggal Pengujian** | 10 Juni 2026 |
| **Penguji** | [Nama Penulis / Anda Sendiri] |
| **Skenario Pengujian** | 1. Sistem mensimulasikan respons *callback/webhook* lunas dari Midtrans.<br>2. Pelanggan membuka halaman daftar kelas miliknya (`/account/courses`).<br>3. Menekan tombol "Mulai Belajar". |

**Hasil Pengujian:**
| Yang Diharapkan | Hasil Pengamatan |
| :--- | :--- |
| Gembok kelas terbuka lebar, memungkinkan pengguna mengakses seluruh video dan silabus materi | Status kepemilikan lisensi aktif seketika tanpa verifikasi manual admin |
**Kesimpulan:** Pengujian integrasi *marketplace* ke LMS terbukti mulus dan otomatis.

<br>

| Item | Keterangan |
| :--- | :--- |
| **ID Pengujian** | FT-008 |
| **Nama Butir Uji** | Eksekusi Pemutar Video LMS dan Evaluasi Kuis Interaktif |
| **Deskripsi** | Memeriksa apakah media video bisa diputar dan soal ujian kuis pilihan ganda dapat dipilih serta dihitung skornya. |
| **Kondisi Awal** | Siswa membuka ruang kelas LMS (`/learn/[courseSlug]`). |
| **Tanggal Pengujian** | 10 Juni 2026 |
| **Penguji** | [Nama Penulis / Anda Sendiri] |
| **Skenario Pengujian** | 1. Memutar media video hingga selesai lalu menekan tombol "Tandai Selesai".<br>2. Membuka modul kuis pilihan ganda di akhir bab.<br>3. Memilih opsi jawaban (*radio button*) dan menekan tombol kumpulkan kuis. |

**Hasil Pengujian:**
| Yang Diharapkan | Hasil Pengamatan |
| :--- | :--- |
| *Progress bar* materi meningkat dan sistem langsung menampilkan persentase skor kelulusan kuis | Multimedia beroperasi normal dan evaluasi ujian merespons akurat |
**Kesimpulan:** Fungsionalitas antarmuka LMS responsif dan interaktif.

<br>

| Item | Keterangan |
| :--- | :--- |
| **ID Pengujian** | FT-009 |
| **Nama Butir Uji** | Penerbitan Sertifikat Kelulusan PDF Otomatis |
| **Deskripsi** | Memeriksa apakah sistem mampu memproduksi dokumen PDF sertifikat secara otomatis seketika setelah progres materi menyentuh 100%. |
| **Kondisi Awal** | Siswa telah menonton seluruh video materi dan menyisakan satu kuis akhir yang siap dikumpulkan. |
| **Tanggal Pengujian** | 10 Juni 2026 |
| **Penguji** | [Nama Penulis / Anda Sendiri] |
| **Skenario Pengujian** | 1. Siswa mengumpulkan jawaban kuis akhir dengan skor memenuhi *passing grade*.<br>2. Sistem memperbarui persentase progres menjadi 100% (*Completed*).<br>3. Siswa memeriksa halaman direktori sertifikat (`/account/certificates`). |

**Hasil Pengujian:**
| Yang Diharapkan | Hasil Pengamatan |
| :--- | :--- |
| Tautan unduhan dokumen PDF sertifikat kelulusan muncul di dasbor dan dapat diunduh | Sertifikat resmi beratasnamakan pengguna tercetak otomatis oleh sistem |
**Kesimpulan:** Pengujian pembuatan sertifikat instan lulus dengan mulus.

<br>

| Item | Keterangan |
| :--- | :--- |
| **ID Pengujian** | FT-010 |
| **Nama Butir Uji** | Validasi Batas Kuota Registrasi Siswa (*Student Limit*) |
| **Deskripsi** | Memeriksa ketahanan sistem dalam menolak pendaftaran siswa baru jika sekolah tersebut telah menyentuh batas kuota maksimal paket *Free*. |
| **Kondisi Awal** | Tenant sekolah berstatus *Free Plan*, dan akumulasi siswa aktif di *database* sudah mencapai angka batas kuota (misal: 50 siswa). |
| **Tanggal Pengujian** | 10 Juni 2026 |
| **Penguji** | [Nama Penulis / Anda Sendiri] |
| **Skenario Pengujian** | 1. Operator Sekolah masuk ke halaman tambah siswa baru (`/students/create`).<br>2. Mengisi biodata siswa fiktif secara lengkap.<br>3. Menekan tombol simpan (*Submit*). |

**Hasil Pengujian:**
| Yang Diharapkan | Hasil Pengamatan |
| :--- | :--- |
| Sistem memblokir penyimpanan data ke *database* dan menampilkan peringatan warna merah bahwa kuota penuh (saran *upgrade* ke paket *Pro*) | Tolakan validasi bekerja aktif melindungi batasan model bisnis langganan |
**Kesimpulan:** Validasi aturan bisnis lolos sempurna mencegah kelebihan kuota layanan.

<br>

| Item | Keterangan |
| :--- | :--- |
| **ID Pengujian** | FT-011 |
| **Nama Butir Uji** | Validasi Harga Diskon Katalog yang Tidak Wajar |
| **Deskripsi** | Mengevaluasi sistem agar tidak mengizinkan formasi potongan harga diskon yang merugikan (contoh: harga diskon lebih mahal dari harga asli atau minus). |
| **Kondisi Awal** | Admin/Moderator sedang membuat atau menyunting harga sebuah kelas di Admin Panel (`/admin/courses`). |
| **Tanggal Pengujian** | 10 Juni 2026 |
| **Penguji** | [Nama Penulis / Anda Sendiri] |
| **Skenario Pengujian** | 1. Memasukkan Harga Asli (*Original Price*) Rp 50.000.<br>2. Memasukkan Harga Jual (*Discount Price*) Rp 150.000 (lebih tinggi dari asli) atau minus.<br>3. Menekan tombol Simpan. |

**Hasil Pengujian:**
| Yang Diharapkan | Hasil Pengamatan |
| :--- | :--- |
| Formulir menolak penyimpanan dan memunculkan pesan galat (*error*) warna merah pada kolom harga | Validasi proteksi distorsi harga beroperasi normal |
**Kesimpulan:** Aturan bisnis perlindungan harga produk bekerja dengan baik.

<br>

| Item | Keterangan |
| :--- | :--- |
| **ID Pengujian** | FT-012 |
| **Nama Butir Uji** | Validasi Prematur Klaim Sertifikat LMS |
| **Deskripsi** | Memastikan peserta yang belum menuntaskan materi belajar tidak dapat meretas atau memicu pencetakan sertifikat kelulusan. |
| **Kondisi Awal** | Siswa berada di dalam LMS dengan progres belajar baru mencapai 30% tanpa menyelesaikan kuis akhir. |
| **Tanggal Pengujian** | 10 Juni 2026 |
| **Penguji** | [Nama Penulis / Anda Sendiri] |
| **Skenario Pengujian** | 1. Siswa mencoba menekan tombol "Klaim Sertifikat" atau menembus parameter rute URL pencetakan berkas secara paksa (*bypass*). |

**Hasil Pengujian:**
| Yang Diharapkan | Hasil Pengamatan |
| :--- | :--- |
| Sistem menolak permintaan dan mengembalikan peringatan bahwa syarat kelulusan 100% belum terpenuhi (*Unauthorized / Incomplete*) | Tameng proteksi kelulusan aktif mencegat akses prematur |
**Kesimpulan:** Logika pencegahan penerbitan sertifikat ilegal sangat kuat dan dapat diandalkan.

### 5.4.2 Rekapitulasi Hasil Pengujian
Setelah menempuh penyelesaian eksekusi pengujian pada seluruh butir uji fungsional antarmuka, pengetesan integrasi alur lintas modul, serta injeksi validasi aturan bisnis sistem, seluruh capaian ini direkapitulasi untuk mengukur tingkat keberhasilan (*Success Rate*) aplikasi secara gamblang. Perekapan matriks di bawah ini dikompilasi berdasarkan peta skenario di tahap prosedur pengujian awal.

Berdasarkan hasil pengetesan simulasi komprehensif, seluruh fitur vital platform PAUD berhasil beroperasi mulus merespons masukan (*input*) sesuai dengan proses bisnis instansi. Integrasi antar-*endpoint* layar serta tameng perlindungan aturan peladen juga terbukti tidak tertembus kegagalan (*error-free*).

| ID | Object | Butir Uji | Teknik Pengujian | Status |
| :--- | :--- | :--- | :--- | :--- |
| FT-001 | Multi-School | Pendaftaran akun Kepala Sekolah dan pembuatan ruang kerja (*tenant*) sekolah baru | Pengujian Fungsional | Passed |
| FT-002 | Manajemen Siswa | Penambahan data biodata anak dan penautan akun profil wali murid oleh **Operator Sekolah** | Pengujian Fungsional | Passed |
| FT-003 | Asesmen PAUD | Pengisian form skala nilai perkembangan anak oleh guru dengan validasi *Temporal Assessment Integrity* | Pengujian Fungsional | Passed |
| FT-004 | Rapor Naratif | Penyusunan narasi rapor akhir semester dan pencetakan PDF dengan penyaringan cerdas *Smart Omission* | Pengujian Fungsional | Passed |
| FT-005 | Keuangan SIAKAD | Pencatatan iuran SPP dan tabungan siswa sebagai buku besar administratif (*ledger*) oleh Operator & Guru | Pengujian Fungsional | Passed |
| FT-006 | Marketplace | Memasukkan produk kursus ke dalam keranjang, perhitungan diskon, dan proses *checkout* B2C | Pengujian Fungsional | Passed |
| FT-007 | Integrasi LMS | Pembukaan gembok akses kelas LMS secara otomatis setelah konfirmasi pembayaran *marketplace* lunas | Pengujian Fungsional | Passed |
| FT-008 | LMS Interaktif | Pemutaran video materi pembelajaran dan pengisian soal pilihan ganda pada kuis | Pengujian Fungsional | Passed |
| FT-009 | Sertifikasi | Penerbitan sertifikat PDF secara otomatis setelah progres materi mencapai 100% dan lulus kuis | Pengujian Fungsional | Passed |
| FT-010 | Limit Registrasi | Validasi penolakan siswa baru oleh sistem jika kuota paket langganan *Free* sekolah telah penuh | Pengujian Fungsional | Passed |
| FT-011 | Validasi Diskon | Validasi penolakan input harga diskon produk agar harga akhir tidak bernilai negatif atau melampaui harga asli | Pengujian Fungsional | Passed |
| FT-012 | Validasi Sertifikat | Validasi penolakan pencetakan sertifikat jika progres materi belajar siswa di LMS belum mencapai 100% | Pengujian Fungsional | Passed |

Berdasarkan himpunan rekapitulasi data empiris di atas, keseluruhan sampel prosedur uji coba berhasil menyandang status **Passed** (100% Tingkat Keberhasilan). Fakta kuantitatif ini mensahkan bahwa fungsionalitas antarmuka publik, persilangan data alur pengguna, dan pilar logika algoritma bisnis aplikasi telah beroperasi dengan semestinya.

### 5.4.3 Kesimpulan Pengujian
Mempertimbangkan seluruh pencapaian pada fase **Pengujian Fungsional (*Functional Testing*)**, dapat disimpulkan bahwa platform manajemen SaaS PAUD ini telah berdiri kokoh dan sanggup memfasilitasi kebutuhan alur bisnis ekosistem persekolahan maupun perniagaan digital modern. Skenario evaluasi krusial yang membentang dari operasi registrasi *tenant*, manajemen operasional siswa oleh Operator Sekolah, pencatatan keuangan *ledger*, pengolahan asesmen berfilter temporal, hingga area belajar LMS mandiri tercatat sukses menyelesaikan tugasnya masing-masing tanpa cacat.

Lebih dari sekadar fungsionalitas UI murni, antarmuka sistem juga membuktikan ketahanannya dalam menjaga sinkronisasi (*integration*) di layar maupun memblokir upaya *input* yang menyalahi logika transaksi melalui pembatasan keamanan bisnis (*business rule validation*). Dengan perolehan angka ketuntasan uji fungsional sempurna (100% *Passed*) ini, sistem perangkat lunak berbasis layanan multitenan ini dapat dinyatakan layak (*feasible*) untuk melenggang ke tahap operasional pada lingkungan instansi pendidikan sesungguhnya.

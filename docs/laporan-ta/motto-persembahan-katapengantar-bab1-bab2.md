# HALAMAN MOTTO

> "Pendidikan adalah senjata paling mematikan di dunia, karena dengan pendidikan, Anda dapat mengubah dunia."
> — **Nelson Mandela**

> "Teknologi hanyalah sebuah alat. Namun dalam hal membuat anak-anak bekerja sama dan memotivasi mereka, guru adalah yang paling utama."
> — **Bill Gates**

---

# HALAMAN PERSEMBAHAN

Laporan Tugas Akhir ini penulis persembahkan dengan tulus kepada:
1. **Kedua Orang Tua dan Keluarga Besar**, yang senantiasa memberikan dukungan moral, materi, serta doa yang tidak pernah putus demi keberhasilan penulis dalam menempuh pendidikan.
2. **Dosen Pembimbing**, yang telah meluangkan waktu dan pikiran untuk memberikan arahan, bimbingan, serta dorongan yang sangat berharga dalam penyelesaian karya ini.
3. **Pihak CV Webina Naramedia**, sebagai mitra kerja yang telah memberikan kesempatan dan pengalaman berharga dalam lingkungan kerja profesional selama pembuatan program PaudPedia.
4. **Teman-teman Seperjuangan**, yang selalu setia memberikan semangat, bantuan, dan dukungan di masa-masa sulit penyusunan laporan ini.
5. **Almamater Tercinta**, tempat penulis menimba ilmu dan membentuk karakter sebagai bekal di masa depan.

---

# KATA PENGANTAR

Puji syukur kehadirat Tuhan Yang Maha Esa atas segala rahmat, hidayah, dan karunia-Nya, sehingga penulis dapat menyelesaikan laporan Tugas Akhir yang berjudul **"Pengembangan Platform PaudPedia: Integrasi Sistem Informasi Akademik Multi-Tenant dan Marketplace Edukasi Berbasis SaaS"** dengan baik dan tepat pada waktunya. Laporan ini disusun sebagai salah satu syarat kelulusan dan pemenuhan tugas akhir dalam jenjang pendidikan pada program studi terkait.

Dalam proses penyusunan laporan dan pengerjaan proyek Tugas Akhir ini, penulis menyadari bahwa hasil yang dicapai tidak terlepas dari bantuan, bimbingan, serta dukungan dari berbagai pihak. Oleh karena itu, melalui kesempatan ini penulis ingin menyampaikan ucapan terima kasih yang sebesar-besarnya kepada pimpinan instansi pendidikan beserta seluruh jajaran staf pengajar yang telah membekali penulis dengan dasar ilmu pengetahuan yang kuat dan bermanfaat. Penulis juga mengucapkan terima kasih kepada dosen pembimbing atas kesabaran, ketelitian, dan masukan yang secara terus-menerus diberikan selama masa perancangan hingga penyelesaian laporan ini.

Ungkapan terima kasih yang tulus juga penulis sampaikan kepada pimpinan dan seluruh rekan tim di CV Webina Naramedia. Kepercayaan, bimbingan teknis, serta pengalaman terjun langsung ke dunia kerja yang diberikan selama masa pembuatan sistem ini telah memberikan wawasan yang sangat berharga. Selain itu, penulis juga berterima kasih kepada rekan-rekan mahasiswa dan semua pihak yang tidak dapat disebutkan satu per satu, yang selalu hadir untuk saling berbagi ide dan semangat.

Penulis menyadari bahwa laporan ini masih memiliki kekurangan dan belum sempurna. Oleh karena itu, segala bentuk masukan dan saran yang membangun sangat penulis harapkan untuk perbaikan di masa yang akan datang. Akhir kata, penulis berharap semoga dokumen Tugas Akhir ini dapat memberikan manfaat yang nyata bagi para pembaca, khususnya sebagai acuan pembuatan teknologi di bidang pendidikan.

Surakarta, Mei 2026

Penulis

---

# BAB 1
# PENDAHULUAN

## 1.1 Latar Belakang
    Pendidikan Anak Usia Dini (PAUD) merupakan tahapan paling penting dalam proses pembentukan karakter, kecerdasan pikiran, dan emosi anak. Pada masa ini, peran sekolah bukan hanya memberikan pelajaran, tetapi juga mencatat setiap perkembangan siswa secara lengkap. Namun, pada kenyataannya di lapangan, sebagian besar sekolah PAUD di Indonesia masih menggunakan cara pengelolaan pendataan yang lama. Proses penting seperti pencatatan kehadiran harian, penilaian perkembangan anak (asesmen), hingga pendataan keuangan dan pembayaran SPP seringkali masih menggunakan buku cetak atau aplikasi ketik biasa yang datanya tidak saling terhubung. Hal ini memunculkan beban kerja tambahan yang sangat berat bagi para guru, sehingga menyita waktu yang seharusnya dapat digunakan untuk fokus mendampingi anak-anak belajar di kelas. Selain itu, penyimpanan data yang terpisah-pisah dan dilakukan secara manual sangat rawan akan kehilangan data, kesalahan penulisan, dan menyulitkan pihak pengelola sekolah dalam mengambil keputusan yang tepat.

    Dampak dari pengurusan sekolah yang kurang cepat ini tidak hanya dirasakan oleh pihak sekolah, tetapi juga oleh orang tua siswa. Dalam pendidikan masa kini, keikutsertaan orang tua sangat berpengaruh pada keberhasilan belajar anak. Sayangnya, sistem yang masih menggunakan kertas menyebabkan orang tua kesulitan mendapatkan informasi yang jelas dan terbaru mengenai nilai perkembangan anak mereka di sekolah. Di sisi lain, seiring dengan meningkatnya keinginan masyarakat terhadap pendidikan usia dini, kebutuhan akan akses terhadap bacaan cara mendidik anak (parenting) dan bahan belajar anak semakin tinggi. Orang tua sering kali harus mencari ke berbagai tempat yang berbeda-beda untuk mendapatkan kelas parenting, mengikuti siaran pendidikan, atau membeli produk digital belajar yang dapat dipercaya. Terpisahnya sistem pendataan sekolah dengan penyediaan bahan belajar untuk masyarakat ini menciptakan jarak yang menghambat terciptanya pendidikan anak yang menyeluruh.

    Untuk mengatasi masalah tersebut, diperlukan sebuah pembaruan teknologi yang mampu menghubungkan kebutuhan pendataan sekolah sekaligus menyediakan bahan belajar untuk masyarakat. Untuk itulah program PaudPedia dirancang dan dibuat. PaudPedia hadir menawarkan sebuah sistem perangkat lunak berbasis internet (SaaS) yang menyatukan dua bagian utama, yakni Sistem Informasi Akademik (SIAKAD) yang bisa dipakai oleh banyak sekolah sekaligus, serta portal tempat jual-beli (*Marketplace*) bahan belajar yang terhubung dengan sistem ruang belajar (*Learning Management System* / LMS) untuk masyarakat umum. Dengan penggabungan ini, sekolah dapat mengubah sistem mereka menjadi digital dengan biaya terjangkau, sementara orang tua dan masyarakat luas mendapatkan sebuah tempat mencari ilmu yang terpercaya dan mudah dijangkau.

    Berdasarkan penjelasan di atas mengenai pentingnya perubahan teknologi pada sekolah PAUD serta besarnya manfaat dari penggabungan layanan ini, maka rumusan permasalahan yang menjadi dasar dalam penyusunan Tugas Akhir ini adalah: **"Bagaimana merancang dan membuat sebuah program berbasis internet yang digabungkan untuk mempermudah pendataan administrasi sekolah PAUD, sekaligus menyediakan tempat pembelajaran untuk masyarakat dan ruang jual-beli bahan belajar yang aman, stabil, dan terukur?"**

## 1.2 Tujuan
Tujuan utama yang ingin dicapai melalui pelaksanaan Tugas Akhir ini adalah:
1. Menghasilkan sistem informasi akademik (SIAKAD) yang dapat digunakan oleh banyak sekolah sekaligus (*multi-tenant*) untuk mengelola data operasional seperti data guru, pendaftaran siswa, kehadiran, penilaian perkembangan, dan keuangan secara terpusat namun tetap aman untuk masing-masing sekolah.
2. Membangun portal untuk masyarakat umum berupa ruang jual-beli (*marketplace*) dan sistem ruang kelas berjalan (*Learning Management System* / LMS) agar pengguna dapat dengan mudah mencari, membeli, dan mengakses kelas, siaran pembelajaran (webinar), dan produk digital lainnya.
3. Menyatukan kedua bagian sistem tersebut (untuk sekolah dan untuk masyarakat umum) dalam satu program yang berjalan lancar dengan menggunakan teknologi pembuatan web seperti Laravel dan Vue.js.

## 1.3 Manfaat Produk
Implementasi program PaudPedia diharapkan mampu memberikan manfaat sebagai berikut:
1. **Bagi Sekolah PAUD:** Membantu mempercepat proses pendataan sekolah dengan mengurangi penggunaan kertas dan meminimalkan kesalahan penulisan, serta mempermudah pembuatan draf rapor siswa dan rekapitulasi pembayaran secara otomatis.
2. **Bagi Guru dan Tenaga Pendidik:** Memudahkan proses memasukkan nilai perkembangan anak dan kehadiran siswa secara cepat melalui perangkat elektronik (HP atau laptop), sehingga guru dapat lebih memusatkan perhatian pada kegiatan mendidik di kelas.
3. **Bagi Orang Tua:** Memberikan kemudahan untuk memantau nilai perkembangan dan kehadiran anak di sekolah, melihat status tagihan biaya, serta mendapatkan materi cara mendidik anak (*parenting*) langsung melalui perangkat mereka kapan saja.
4. **Bagi Masyarakat Umum:** Memudahkan akses ke berbagai bahan pelatihan dan tempat belajar yang terpercaya untuk membantu proses pendidikan anak.

## 1.4 Metode Pengembangan
Dalam upaya memastikan program PaudPedia dapat dibuat dengan kualitas yang baik dan mampu menyesuaikan perubahan keinginan pengguna, metode pembuatan perangkat lunak yang digunakan dalam Tugas Akhir ini adalah metode Agile. Pemilihan cara Agile dilakukan karena proyek ini memiliki jumlah fitur yang banyak dan membutuhkan kerja sama yang erat dengan berbagai pihak. Berbeda dengan cara lama yang kaku, metode Agile menawarkan tahapan pengerjaan yang dilakukan secara berulang-ulang dan bertahap, memberikan ruang yang leluasa untuk menambah atau mengubah fitur di tengah berjalannya proyek tanpa harus merusak susunan sistem yang sudah jadi.

Proses pembuatan dengan metode Agile dalam proyek ini dilakukan melalui beberapa tahapan yang terus menyambung. Proses dimulai dengan tahapan pencarian kebutuhan (*Requirement*), di mana dilakukan tanya jawab dengan calon pengguna seperti pihak sekolah untuk mengetahui fitur-fitur penting apa saja yang harus ada pada sistem SIAKAD maupun bagian Marketplace. Hasil diskusi tersebut kemudian diubah ke dalam tahap perancangan (*Design*), yang meliputi pembuatan rancangan tempat penyimpanan data (ERD), alur cara pemakaian pengguna, hingga pembuatan desain gambaran awal tampilan layar (UI/UX) yang menyesuaikan ukuran HP maupun laptop. Tahap selanjutnya adalah proses penulisan kode (*Development*) yang dikerjakan dalam jangka waktu pendek-pendek yang disebut sprint. Setiap sprint berfokus menyelesaikan satu bagian fitur tertentu, yang kemudian langsung dilanjutkan ke tahap pengujian (*Testing*) untuk memastikan fiturnya berjalan dengan benar dan mencari tahu apakah ada kerusakan sistem (*bug*). Di akhir setiap putaran, dilakukan tahapan penilaian ulang (*Review*) guna mengecek hasil kerja yang telah dicapai, mengumpulkan masukan, dan merencanakan perbaikan untuk tahap pengerjaan berikutnya.

## 1.5 Definisi dan Istilah
Guna menghindari kesalahpahaman dan menyamakan pengertian terhadap berbagai istilah teknis yang dibahas di dalam laporan Tugas Akhir ini, berikut disajikan tabel daftar istilah:

| No | Istilah / Singkatan | Keterangan |
|----|----------------------|------------|
| 1  | **SaaS**             | *Software as a Service*, yaitu model layanan di mana program komputer disediakan oleh pembuatnya untuk digunakan oleh pelanggan melalui sambungan internet tanpa perlu dipasang (install) di komputer pengguna. |
| 2  | **Multi-Tenant**     | Sebuah cara pembuatan program di mana satu aplikasi yang berjalan melayani banyak kelompok pengguna (sekolah) yang berbeda pada saat bersamaan, dengan keamanan data yang terpisah untuk setiap sekolah. |
| 3  | **SIAKAD**           | Sistem Informasi Akademik, yaitu program yang dibuat untuk mengubah pendataan manual menjadi digital dalam mengurus kegiatan sekolah. |
| 4  | **LMS**              | *Learning Management System*, yaitu sebuah sistem untuk mengadakan, melacak kemajuan, dan membagikan bahan pelajaran dan kelas secara online. |
| 5  | **B2B / B2C**        | *Business-to-Business* merujuk pada penyediaan layanan untuk keperluan operasional sekolah, sedangkan *Business-to-Consumer* berfokus pada penyediaan produk langsung kepada masyarakat umum. |
| 6  | **PAUD**             | Pendidikan Anak Usia Dini, suatu upaya pembinaan yang ditujukan kepada anak sejak lahir sampai dengan usia enam tahun yang dilakukan melalui pemberian rangsangan pendidikan. |
| 7  | **RLS**              | *Row Level Security*, yaitu aturan keamanan pada tempat penyimpanan data yang secara otomatis menyembunyikan data sekolah lain sehingga pengguna hanya bisa melihat data sekolahnya sendiri. |
| 8  | **Tenant**           | Pelanggan (dalam hal ini merujuk pada sekolah PAUD) yang menggunakan dan berjalan di dalam sistem multi-tenant. |

## 1.6 Referensi
*(Bagian ini akan diisi kemudian oleh penulis).*

---

# BAB 2
# DESKRIPSI PRODUK

## 2.1 Deskripsi Produk
PaudPedia adalah sebuah program terpadu yang diwujudkan dalam bentuk aplikasi berbasis web (*web-based application*). Produk ini dibuat sebagai sebuah jalan keluar satu pintu (*one-stop solution*) yang menghubungkan berbagai kebutuhan pendataan sekolah dan layanan belajar pada tingkat Pendidikan Anak Usia Dini. Keunggulan utama dari PaudPedia terletak pada pembagian pelayanannya yang menggabungkan dua kegunaan besar yang saling mendukung namun melayani sasaran pengguna yang berbeda secara bersamaan.

Bagian pertama dari program ini adalah halaman Manajemen Sekolah atau yang lebih dikenal dengan SIAKAD. Bagian ini ditujukan sepenuhnya sebagai alat bantu pengurusan dalam sekolah (*B2B*). Dengan memanfaatkan kemampuan penyimpanan data untuk banyak penyewa (*multi-tenant*), PaudPedia memungkinkan banyak sekolah yang berbeda untuk menyewa dan menggunakan sistem ini. Setiap sekolah memiliki ruang penyimpanan data yang dipisah secara ketat, sehingga mereka dapat mendaftarkan guru, memasukkan kehadiran harian, menyusun penilaian perkembangan anak, hingga mencatat iuran secara mandiri dan aman dari potensi tercampurnya data antar sekolah.

Bagian kedua adalah Halaman Publik yang menggabungkan tempat jual-beli (*Marketplace*) dan Ruang Kelas Online (*LMS*). Berbeda dengan bagian pertama yang tertutup untuk keperluan dalam sekolah, halaman publik ini dibuat terbuka untuk memenuhi kebutuhan masyarakat luas (*B2C*). Melalui halaman yang mudah digunakan, masyarakat luas, orang tua, maupun guru dapat mencari berbagai produk ilmu pengetahuan. Pengguna memiliki kebebasan untuk membeli kelas, mengikuti pendaftaran siaran langsung (webinar), atau mengunduh berbagai bahan belajar anak. Seluruh produk pembelajaran digital yang telah berhasil dibeli dapat langsung diakses melalui fitur ruang kelas (LMS) yang dilengkapi dengan pemutar video, pelacakan waktu belajar, ujian penilaian, dan sistem pembuatan sertifikat otomatis.

Secara keseluruhan, gabungan yang selaras antara kemudahan pendataan pelaporan dan penyediaan akses terhadap banyaknya bahan pelajaran ini menjadikan PaudPedia bukan sekadar aplikasi pencatatan biasa, melainkan sebuah alat penting yang mempercepat peningkatan mutu pendidikan anak usia dini secara menyeluruh.

## 2.2 Fungsional Produk
*(Bagian tabel Functional Requirements (FR) akan diisi kemudian oleh penulis).*

## 2.3 Penggolongan dan Karakteristik Pengguna
Banyaknya fitur yang ditawarkan oleh PaudPedia menuntut adanya perbedaan peran pengguna yang sangat rinci dan bertingkat. Sistem pembagian hak akses pada program ini dikelompokkan ke dalam beberapa peran utama berdasarkan batasan dan kewenangannya, seperti yang dijelaskan pada tabel berikut:

| No | Peran Pengguna | Deskripsi Karakteristik |
|---|---|---|
| 1 | **Admin Global** | Pihak pembuat atau pemilik sistem yang memiliki kendali penuh untuk mengatur sekolah, mengatur sistem, dan menyaring daftar jualan di marketplace agar sesuai kelayakan. |
| 2 | **Moderator** | Pengguna yang bertugas membantu admin dalam mengelola tulisan (artikel blog), menyetujui jadwal webinar, dan membantu memelihara halaman masyarakat umum. |
| 3 | **Headmaster (Kepala Sekolah)** | Pemimpin di sekolah yang memiliki hak untuk mengatur profil sekolahnya, mendaftarkan guru dan siswa, serta memantau laporan uang dan kelas. |
| 4 | **Teacher (Guru)** | Pengguna yang bertugas memasukkan data kehadiran siswa, menyusun nilai tulisan tentang perkembangan anak (asesmen), dan mengurus anak-anak di kelas sehari-hari. |
| 5 | **Parent (Orang Tua)** | Pengguna yang memiliki akses khusus untuk memantau kehadiran, melihat nilai rapor, dan mengecek tagihan biaya sekolah secara khusus untuk anak mereka sendiri saja. |
| 6 | **User (Pengguna Publik)** | Masyarakat umum yang sudah membuat akun untuk bisa membeli produk bahan belajar dan mengikuti kelas di dalam sistem kelas berjalan. |
| 7 | **Guest (Pengunjung)** | Pengguna umum yang belum membuat akun, yang hanya dapat melihat-lihat artikel atau daftar jualan tanpa bisa melakukan pembelian. |

## 2.4 Lingkungan Operasi
*(Bagian ini akan diisi kemudian oleh penulis).*

## 2.5 Batasan Desain dan Implementasi
Dalam upaya membuat program yang bisa diandalkan namun tetap memperhatikan batas waktu pembuatan Tugas Akhir, pengembangan aplikasi PaudPedia dibatasi oleh sejumlah aturan pembuatan (batasan sistem). Batasan ini dibagi menjadi dua, yaitu batasan lingkungan pengerjaan dan batasan kebutuhan non-fungsional (NFR).

**Tabel 2.1 - Batasan Sistem dan Teknologi**
| No | Kategori | Deskripsi Batasan |
|---|---|---|
| 1 | Lingkungan Sistem |    |
| 2 | Teknologi Pendukung Belakang (*Backend*) | Pembuatan sistem utama menggunakan bahasa pemrograman PHP dengan menggunakan kerangka kerja Laravel versi 12. |
| 3 | Teknologi Tampilan Depan (*Frontend*) | Tampilan program untuk pengaturan sekolah menggunakan Vue.js versi 3, sedangkan halaman untuk masyarakat menggunakan Nuxt.js versi 4 agar mudah ditemukan di mesin pencari (SEO). |
| 4 | Layanan Pembayaran | Proses periksa pembayaran otomatis dibatasi hanya menggunakan layanan pembayaran dari pihak ketiga yaitu Midtrans. Sistem tidak menyimpan nomor kartu kredit atau ATM milik pengguna. |
| 5 | Keamanan Data | Penerapan sistem pemisahan data sekolah (*multi-tenant*) wajib menggunakan aturan keamanan baris (*Row Level Security*), sehingga data satu sekolah dipastikan tidak akan bisa dibaca oleh sekolah lainnya. |

**Tabel 2.2 - Kebutuhan Non-Fungsional (NFR)**
| Tipe | Kode Req | Grup / Kategori | Deskripsi Requirement |
|---|---|---|---|
| NFR | NFR-PF-01 | Performance | Waktu respons API harus kurang lebih 700 ms |
| NFR | NFR-SC-01 | Security | Sistem harus menggunakan enkripsi HTTPS untuk semua komunikasi |
| NFR | NFR-SC-02 | Security | Sistem harus menerapkan Row Level Security (RLS) untuk isolasi data multi-tenant |
| NFR | NFR-SC-03 | Security | Sistem harus mengenkripsi kata sandi menggunakan password hashing |
| NFR | NFR-SC-04 | Security | Sistem harus memverifikasi email pengguna setelah registrasi |
| NFR | NFR-US-01 | Usability | Sistem harus memiliki desain responsif untuk perangkat mobile |
| NFR | NFR-US-02 | Usability | Antarmuka sistem harus menggunakan Bahasa Indonesia |
| NFR | NFR-US-03 | Usability | Sistem harus menyediakan validasi form dengan pesan kesalahan yang jelas dan membantu |

## 2.6 Dokumentasi Pengguna
Mengingat aplikasi PaudPedia digunakan oleh berbagai macam orang dengan tingkat kemampuan teknologi yang berbeda-beda, penyediaan buku panduan menjadi bagian yang sangat penting. Bentuk panduan bagi pengguna dibuat beragam agar sesuai dengan cara pengguna mencari bantuan jika terjadi kebingungan. Untuk panduan lengkap, disediakan Buku Petunjuk (Manual Produk) dalam bentuk tulisan (PDF) yang menjelaskan urutan cara memakai setiap fitur sesuai peran masing-masing, mulai dari cara Kepala Sekolah mengatur pengaturan awal hingga cara Guru memasukkan nilai.

Selain panduan tertulis, sistem juga dilengkapi dengan halaman Pusat Bantuan (*Help Center*) yang berisi kumpulan jawaban atas pertanyaan yang sering ditanyakan, sehingga pengguna bisa langsung menemukan jalan keluar dari masalah umum seperti gagal masuk akun atau gagal bayar. Agar lebih mudah dipahami, disediakan juga kumpulan Video Panduan berupa rekaman layar singkat yang mencontohkan langkah-langkah tertentu secara langsung. Detail lebih lanjut mengenai Buku Petunjuk bagi pengguna tidak akan dituliskan di bagian ini, melainkan akan disertakan secara lengkap pada bagian lampiran laporan.

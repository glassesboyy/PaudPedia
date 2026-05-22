# BAB 4
# IMPLEMENTASI SISTEM

Bab ini menguraikan tahap implementasi dan penerapan fungsionalitas aplikasi dari rancangan sistem yang telah dijabarkan pada bab-bab sebelumnya. Pembahasan implementasi pada sistem PaudPedia disusun secara komprehensif berdasarkan pemisahan alur proses (*flow*) sistem utama, yang mencakup arsitektur interaksi pengguna (*frontend*) hingga mekanisme manipulasi data pada sisi peladen (*backend*). Pendekatan ini ditujukan untuk memberikan visualisasi operasional mengenai bagaimana sistem dieksekusi dalam menangani kebutuhan fungsional *Software as a Service* (SaaS) berbasis *multi-tenant* yang melingkupi modul Sistem Informasi Akademik (SIAKAD) dan Portal Ekosistem Edukasi.

Implementasi sistem secara keseluruhan dibagi ke dalam tujuh fokus alur utama yang diuraikan sebagai berikut:

## 4.1 Implementasi Pemisahan Data Antar Sekolah (Multi-Tenant Row Level Security)

### 4.1.1 Tujuan Implementasi
Sistem perangkat lunak PaudPedia dirancang menggunakan arsitektur *Software as a Service* (SaaS) berbasis *multi-tenant*, yang menuntut tingkat isolasi data yang tinggi antar institusi PAUD yang terdaftar. Implementasi ini diterapkan untuk memastikan bahwa rekam jejak operasional, data siswa, serta informasi finansial dari setiap sekolah tersimpan di dalam satu instansi basis data secara terpusat, namun terisolasi secara ketat sehingga pengguna dari satu sekolah tidak memiliki akses untuk melihat maupun memanipulasi data yang dikelola oleh sekolah lain.

### 4.1.2 Penjelasan Alur Sistem
Pemisahan data ini diwujudkan melalui penerapan *Row Level Security* pada level aplikasi. Secara teknis, ketika pengguna melakukan autentikasi masuk (login), sistem secara dinamis mendeteksi dan mengikat identitas institusi pengguna tersebut ke dalam sesi yang sedang berjalan. Identitas ini direpresentasikan melalui parameter `school_id`. Selanjutnya, pada setiap permintaan baca dan tulis ke basis data, sistem secara otomatis menyisipkan klausa penyaring (`where('school_id', $user->school_id)`) sebelum kueri dieksekusi.

### 4.1.3 Tampilan Antarmuka
> **[Gambar 4.1 Tampilan Halaman Daftar Siswa]**

Antarmuka pada Gambar 4.1 membuktikan keberhasilan isolasi data pada sisi pengguna. Pada halaman manajemen daftar siswa yang diakses melalui akun pengajar (*Teacher*), sistem memuat tabel yang secara eksklusif hanya menampilkan anak didik yang terdaftar pada sekolah tempat guru tersebut bernaung. Intervensi antar-institusi dicegah secara visual, memastikan bahwa informasi sensitif siswa tidak dapat tumpang tindih.

### 4.1.4 Implementasi Kode Utama
> **[Kode Program 4.1 Cuplikan filter Multi-Tenant pada StudentController]**

Penerapan logika keamanan data ini dapat diamati pada fungsi `index()` di dalam `StudentController`. Variabel `$user->school_id` ditangkap secara *real-time* dari sesi pengguna yang aktif, kemudian digunakan sebagai penyeleksi mutlak pada koleksi data `Student`. Pendekatan ini memastikan bahwa lapisan basis data selalu menolak segala bentuk anomali maupun percobaan eksploitasi akses lintas sekolah, sehingga integritas dan keamanan data pelanggan SaaS tetap terjamin.

### 4.1.5 Hasil Implementasi
Hasil dari penerapan kode ini adalah keamanan data terjamin secara menyeluruh. Sistem berhasil menolak akses secara mutlak ketika terdapat permintaan eksploitasi data lintas batas, memvalidasi bahwa setiap sekolah pada ekosistem PaudPedia beroperasi di dalam ruang lingkup data yang sepenuhnya terisolasi dan independen.

---

## 4.2 Implementasi Alur Autentikasi dan Manajemen Pengguna

### 4.2.1 Tujuan Implementasi
Keamanan akses menuju layanan PaudPedia dijaga melalui sistem autentikasi gerbang utama yang terintegrasi dengan struktur pembagian peran (*Role-Based Access Control*). Sistem ini mengkategorikan hak akses pengguna ke dalam beberapa tingkatan hierarki, seperti *Admin*, *Headmaster* (Kepala Sekolah), *Teacher* (Guru), *Parent* (Orang Tua), dan *User* (Pengguna Umum/Publik). Diferensiasi peran ini diimplementasikan untuk memastikan bahwa setiap entitas pengguna hanya mendapatkan otorisasi terhadap modul dan antarmuka yang relevan dengan tanggung jawabnya.

### 4.2.2 Penjelasan Alur Sistem
Proses autentikasi dimulai ketika pengguna mengakses gerbang masuk aplikasi dan mengirimkan kredensial berupa alamat surel dan kata sandi. Kredensial tersebut diproses melalui lapisan enkripsi sebelum dicocokkan dengan rekaman pada tabel pengguna. Apabila verifikasi gagal, sistem merespons seketika dengan memberikan umpan balik visual bahwa kredensial tidak valid. Namun, jika verifikasi berhasil, sistem segera memvalidasi peran spesifik pengguna, menerbitkan *Token API*, dan mengarahkan pengguna ke dasbor yang relevan.

### 4.2.3 Tampilan Antarmuka
> **[Gambar 4.2 Tampilan Halaman Login SIAKAD]**

Antarmuka pada Gambar 4.2 memperlihatkan formulir *login* yang dirancang dengan tata letak minimalis untuk memaksimalkan fokus pengguna. Formulir ini divalidasi secara asinkron di sisi peramban sebelum dikirim ke peladen, meminimalisasi beban komputasi dan memberikan respons yang cepat. Penanganan *error* diterapkan secara halus di mana peringatan penolakan akses ditampilkan tanpa memerlukan pemuatan ulang halaman secara penuh.

### 4.2.4 Implementasi Kode Utama
> **[Kode Program 4.2 Cuplikan pembuatan sesi pada AuthController]**

Implementasi lapisan *backend* ditangani oleh fungsi `login()` di dalam `AuthController`. Fungsi ini memanfaatkan metode bawaan kerangka kerja untuk melakukan pencocokan (*hashing*) kata sandi. Setelah kredensial terverifikasi sah, token akses personal (*Personal Access Token*) dibangkitkan dan dikembalikan ke klien. Token tersebut kemudian bertindak sebagai instrumen validasi elektronik untuk seluruh rute API yang dilindungi, menjaga persistensi sesi agar pengguna dapat berselancar di dalam sistem tanpa terputus.

### 4.2.5 Hasil Implementasi
Pengguna tervalidasi berhasil masuk ke dalam sistem dengan koneksi yang aman. Manajemen sesi terjaga dengan baik selama masa aktif token, dan tata kelola navigasi halaman beroperasi secara otomatis untuk menyesuaikan akses menu berdasarkan tingkatan privilese (*privilege*) masing-masing peran.

---

## 4.3 Implementasi Alur Operasional SIAKAD

### 4.3.1 Tujuan Implementasi
Proses administrasi harian sekolah PAUD, yang meliputi pemantauan kehadiran anak dan rekam medis perkembangan (asesmen), ditransformasi ke dalam alur digital terpadu melalui modul Sistem Informasi Akademik (SIAKAD). Transformasi ini diimplementasikan untuk mengurangi ketergantungan pada pencatatan fisik dan mempercepat rantai distribusi informasi akademik dari pihak sekolah langsung kepada orang tua wali murid.

### 4.3.2 Penjelasan Alur Sistem
Dalam operasional pelaporannya, seorang guru dapat memilih ruang kelas yang berada di bawah wewenangnya untuk melakukan pencatatan kehadiran. Proses absensi tidak lagi dilakukan secara individual, melainkan menggunakan skema pengisian masal (*bulk input*). Setelah data diserahkan ke peladen, status kehadiran masing-masing anak akan langsung termutakhirkan dan dikomunikasikan secara seketika (*real-time*) ke dasbor portal pemantauan orang tua.

### 4.3.3 Tampilan Antarmuka
> **[Gambar 4.3 Tampilan Tabel Input Kehadiran Harian]**

Tampilan pada Gambar 4.3 mengilustrasikan formulir absensi harian berbasis tabel interaktif. Antarmuka ini mengadopsi model *bulk input* yang mengurutkan seluruh siswa dalam satu kelas secara vertikal. Guru cukup memilih status kehadiran pada setiap baris siswa yang relevan dan menyimpannya secara serentak melalui satu aksi pengiriman, sehingga mereduksi hambatan dan mengoptimalkan efisiensi waktu tugas administratif tenaga pendidik di sekolah.

### 4.3.4 Implementasi Kode Utama
> **[Kode Program 4.3 Cuplikan penyimpanan data absensi masal pada AttendanceController]**

Pemrosesan logika masukan ganda tersebut direalisasikan pada kontroler `AttendanceController`. Fungsi `store()` bertugas melakukan iterasi (*looping*) terhadap himpunan data presensi yang masuk. Terdapat mekanisme validasi yang krusial pada fungsi ini, di mana sistem secara otomatis mencegah terjadinya duplikasi data. Apabila sistem mendeteksi bahwa kelas dan tanggal absen telah tercatat sebelumnya, proses perekaman akan digugurkan untuk mempertahankan akurasi riwayat rekapitulasi pada akhir semester.

### 4.3.5 Hasil Implementasi
Sistem merekam baris data kehadiran harian dalam jumlah besar dengan cepat, tepat, dan tanpa redundansi data. Informasi yang diunggah seketika tersedia sebagai basis data untuk pencetakan dokumen rekapitulasi penilaian.

---

## 4.4 Implementasi Alur Transaksi Marketplace Publik

### 4.4.1 Tujuan Implementasi
Guna memperluas jangkauan edukasi ke masyarakat umum, PaudPedia menyertakan ekosistem *marketplace* yang memfasilitasi transaksi komersial atas berbagai produk digital, seperti kursus daring maupun pendaftaran seminar web (*webinar*). Sistem perdagangan elektronik ini diimplementasikan agar dapat beroperasi secara otonom melayani publik tanpa membutuhkan intervensi persetujuan pembayaran manual dari pengelola platform.

### 4.4.2 Penjelasan Alur Sistem
Rantai interaksi bisnis dimulai saat pelanggan mengeksplorasi katalog yang tersedia, lalu menempatkan layanan yang diminati ke dalam keranjang belanja. Tahap penyelesaian pembelian (*checkout*) dilakukan dengan menggandeng gerbang pembayaran elektronik pihak ketiga (Midtrans). Melalui interkoneksi jaringan ini, kalkulasi biaya akhir direkapitulasi secara otomatis, dan pilihan saluran transfer bank atau dompet digital langsung disajikan ke layar pengguna.

### 4.4.3 Tampilan Antarmuka
> **[Gambar 4.4 Tampilan Halaman Checkout dan Popup Midtrans]**

Gambar 4.4 menampilkan antarmuka penyelesaian pemesanan yang responsif pada portal publik. Ketika pesanan dikonfirmasi, sistem merender jendela dialog pembayaran (*popup*) dari Midtrans langsung di atas halaman aplikasi utama. Arsitektur ini memastikan pelanggan tidak perlu meninggalkan situs untuk menyelesaikan tagihannya, menciptakan pengalaman berbelanja yang mulus (*seamless*) dan meningkatkan rasio keberhasilan konversi penjualan.

### 4.4.4 Implementasi Kode Utama
> **[Kode Program 4.4 Cuplikan penanganan notifikasi otomatis pada MidtransController]**

Konfirmasi pelunasan tagihan ditangani melalui arsitektur komunikasi antar-peladen menggunakan *Webhook*, yang direpresentasikan oleh `MidtransController`. Begitu dana diterima oleh pihak bank, layanan Midtrans menembakkan notifikasi asinkron ke *endpoint* peladen. Fungsi pengendali ini selanjutnya memverifikasi parameter *signature key* untuk memitigasi serangan manipulasi data palsu, lalu mengeksekusi logika modifikasi status pesanan menjadi Lunas secara mandiri di dalam basis data.

### 4.4.5 Hasil Implementasi
Transaksi diselesaikan melalui variasi metode pembayaran bank secara seketika. Alur ini secara drastis mengeleminasi beban rekonsiliasi keuangan manual, dan pengguna langsung mendapatkan akses terhadap materi edukasi sepersekian detik setelah mutasi bank dinyatakan berhasil.

---

## 4.5 Implementasi Alur Sistem Pembelajaran Jarak Jauh (E-Learning)

### 4.5.1 Tujuan Implementasi
Setelah pengguna publik menyelesaikan transaksi pembelian materi belajar, sistem akan mengarahkan mereka ke dalam ruang belajar mandiri atau *Learning Management System* (LMS). Implementasi fitur ini bertujuan untuk menyajikan beragam materi pembelajaran secara rapi dan terstruktur agar mudah dipelajari. Materi kursus yang disajikan sangat bervariasi, meliputi penyediaan dokumen bacaan (PDF), tautan video interaktif dari YouTube, hingga artikel teks yang disusun langsung oleh pihak pengelola menggunakan penyunting teks (*Rich Text Editor*). Ruang belajar ini juga mendukung pengadaan kuis sebagai sarana evaluasi tambahan yang bersifat opsional.

### 4.5.2 Penjelasan Alur Sistem
Sistem pembelajaran jarak jauh ini membagi susunan materi ke dalam tingkatan modul dan sub-materi (*lesson*). Pengguna dapat menelusuri dan mempelajari setiap materi secara berurutan. Setiap kali pengguna selesai membaca atau menonton sebuah materi, sistem akan merekam dan menambahkan persentase kemajuan (*progress*) belajar mereka. Apabila seluruh materi dalam satu kursus telah diselesaikan dan persentase kemajuan mencapai angka 100%, sistem akan secara otomatis menerbitkan dokumen sertifikat kelulusan dalam bentuk digital yang dapat diunduh pada saat itu juga. Meskipun beberapa kursus menyediakan kuis sebagai latihan tambahan, penerbitan sertifikat murni didasarkan pada tingkat penyelesaian materi secara keseluruhan.

### 4.5.3 Tampilan Antarmuka
> **[Gambar 4.5 Tampilan Ruang Belajar LMS dan Daftar Modul Materi]**

Antarmuka ruang belajar pada Gambar 4.5 dirancang agar sangat nyaman digunakan. Layar dibagi menjadi dua bagian utama: daftar isi materi di sebelah samping untuk memudahkan pengguna berpindah antar modul, dan layar utama di bagian tengah yang menampilkan isi materi (baik itu pemutar video YouTube, penampil dokumen PDF, maupun teks bacaan). Sistem ini juga menampilkan bilah kemajuan (*progress bar*) di bagian atas yang memberikan gambaran visual kepada pengguna mengenai seberapa jauh mereka telah menyelesaikan kursus tersebut.

### 4.5.4 Implementasi Kode Utama
> **[Kode Program 4.5 Cuplikan logika pelacakan kemajuan belajar (Progress Tracking) pada LmsController]**

Proses pelacakan kemajuan belajar ditangani secara otomatis di sisi peladen (server), tepatnya menggunakan fungsi pelacakan di dalam file `LmsController`. Cara kerjanya adalah setiap kali pengguna menandai sebuah materi sebagai selesai, sistem akan mencatat riwayat tersebut ke dalam basis data. Selanjutnya, sistem akan membagi jumlah materi yang sudah diselesaikan dengan total materi yang ada pada kursus tersebut untuk mendapatkan nilai persentase akhir. Pendekatan perhitungan ini memastikan bahwa indikator penyelesaian belajar terpantau secara sah dan sertifikat kelulusan hanya diterbitkan apabila sistem memvalidasi bahwa seluruh komponen materi benar-benar telah diakses oleh pengguna.

### 4.5.5 Hasil Implementasi
Sistem berhasil menyajikan materi kursus dalam berbagai ragam format secara interaktif. Pelacakan kemajuan belajar berjalan akurat secara seketika (*real-time*), dan sistem mampu menerbitkan sertifikat kelulusan secara otomatis tepat ketika pengguna telah mencapai penyelesaian kursus secara penuh.

---

## 4.6 Implementasi Alur Manajemen Berlangganan (Upgrade Layanan)

### 4.6.1 Tujuan Implementasi
Untuk mendukung keberlangsungan aplikasi, PaudPedia menerapkan sistem paket berlangganan. Sistem ini membagi jenis fasilitas yang bisa digunakan antara akun sekolah reguler (Gratis) dan akun berbayar (Premium/Pro). Tujuannya adalah untuk menarik pihak sekolah agar mau beralih ke paket berbayar, karena paket Premium menawarkan kelebihan seperti akses untuk mencetak laporan rapor secara otomatis dalam bentuk PDF serta fitur tampilan data statistik yang lebih lengkap.

### 4.6.2 Penjelasan Alur Sistem
Alur berlangganan ini berpusat pada antarmuka akun Kepala Sekolah. Di halaman dasbor mereka, sistem menampilkan perbandingan yang jelas antara apa yang mereka dapatkan saat ini dengan keuntungan dari paket Premium. Jika Kepala Sekolah memutuskan untuk berlangganan dan menyelesaikan pembayarannya, seluruh fitur Premium yang sebelumnya terkunci akan langsung terbuka dan bisa digunakan oleh semua guru maupun staf di sekolah tersebut.

### 4.6.3 Tampilan Antarmuka
> **[Gambar 4.6 Tampilan Halaman Pemilihan Paket Berlangganan (Subscription)]**

Gambar 4.6 memperlihatkan tabel perbandingan fasilitas antar paket langganan. Tampilan daftar harga ini dirancang sesederhana mungkin agar mudah dipahami, sehingga Kepala Sekolah dapat dengan cepat melihat fitur-fitur berharga apa saja yang hilang di paket Gratis dan tersedia di paket Pro. Desain ini bertujuan memotivasi pihak pimpinan sekolah untuk segera membeli paket langganan demi meningkatkan kelancaran operasional tata usaha sekolah mereka.

### 4.6.4 Implementasi Kode Utama
> **[Kode Program 4.6 Cuplikan pemblokiran akses fitur khusus melalui CheckProPlan Middleware]**

Keamanan pembatasan akses fitur berbayar ini dijaga dengan ketat menggunakan kode perantara yang disebut `CheckProPlan Middleware`. Kode ini berfungsi sebagai pintu penjaga. Jika ada institusi dengan paket Gratis mencoba mengakses halaman atau fitur khusus Premium, kode penjaga ini akan langsung memblokir permintaan tersebut dan menampilkan peringatan bahwa akses ditolak (HTTP 403 *Forbidden*). Perlindungan ini memastikan bahwa fitur berbayar benar-benar aman dan tidak bisa diakali hanya dengan mengubah tautan halaman web (URL).

### 4.6.5 Hasil Implementasi
Sistem pemblokiran fitur berfungsi dengan sangat baik di seluruh ekosistem aplikasi SIAKAD. Menu-menu layanan berbayar akan tetap tersembunyi dan sistem menolak memproses data apa pun sampai pihak sekolah secara resmi memperpanjang masa aktif paket langganannya.

---

## 4.7 Implementasi Alur Manajemen Konten Global (CMS Admin)

### 4.7.1 Tujuan Implementasi
Pengelolaan seluruh inti informasi pada sistem PaudPedia dijalankan melalui halaman panel kendali utama (*Content Management System* / CMS) yang khusus dibuat untuk tim internal dan moderator aplikasi. Panel ini berfungsi sebagai pusat kendali (*control center*). Tujuannya adalah agar pihak pengelola dapat dengan bebas mengatur dan mengubah informasi penting situs secara dinamis, mulai dari memperbarui konten edukasi, mengubah nama dan pengaturan tampilan situs, hingga mengatur batas harga dan fasilitas pada paket berlangganan, tanpa harus membongkar kode program secara langsung.

### 4.7.2 Penjelasan Alur Sistem
Alur kerja pada panel ini dirancang agar setiap perubahan pengaturan dapat langsung mempengaruhi tampilan dan fungsi sistem secara keseluruhan. Sebagai contoh, ketika staf admin memperbarui informasi kontak sekolah, menyesuaikan harga langganan pada halaman paket, atau menyetujui materi kursus baru untuk dipublikasikan, sistem akan langsung mengirim pembaruan tersebut ke basis data. Perubahan tersebut kemudian akan disebarkan secara serempak ke seluruh bagian aplikasi, memastikan bahwa pengunjung web maupun pengguna portal SIAKAD selalu berinteraksi dengan informasi dan aturan terbaru.

### 4.7.3 Tampilan Antarmuka
> **[Gambar 4.7 Tampilan Halaman Dasbor Admin Global]**

Gambar 4.7 memperlihatkan tampilan panel kendali utama untuk Admin Global. Halaman kendali ini dibangun terpisah dari halaman sekolah dengan menggunakan alat dari *Filament PHP*. Fitur pada halaman ini mencakup beragam menu pengaturan yang menyeluruh. Mulai dari menu tata kelola situs untuk mengubah identitas web, menu manajemen langganan untuk mengubah harga dan batasan fitur, hingga kotak penyunting teks (*Rich Text Editor*) yang memudahkan admin merangkai artikel informasi dengan cepat dan rapi.

### 4.7.4 Implementasi Kode Utama
> **[Kode Program 4.7 Cuplikan deklarasi formulir pengaturan pada komponen Filament]**

Pembuatan panel antarmuka admin yang kaya akan fitur ini sepenuhnya digerakkan oleh kerangka kerja *Filament PHP*, seperti yang direpresentasikan pada berkas halaman pengaturan (misalnya `ManageSiteSettings.php`). Cara kerjanya didasarkan pada pendeklarasian susunan formulir (*form schema*) langsung di dalam kode peladen. Alih-alih merancang antarmuka web dari nol menggunakan bahasa pemrograman visual (HTML) yang rumit, pengembang cukup mendefinisikan komponen-komponen yang dibutuhkan—seperti kolom isian teks untuk nama situs, deskripsi, alamat, dan lain-lain—melalui fungsi bawaan Filament. Alat ini kemudian secara otomatis menerjemahkan dan menggambar kode tersebut menjadi tampilan layar admin yang canggih dan siap dipakai.

### 4.7.5 Hasil Implementasi
Pihak admin internal PaudPedia kini memiliki kendali penuh atas pengelolaan ekosistem aplikasi secara mandiri dan cepat. Setiap penyesuaian teks, perubahan harga paket, maupun pembaruan produk edukasi yang mereka simpan akan langsung memperbarui seluruh antarmuka web seketika itu juga.

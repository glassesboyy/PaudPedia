> **Catatan Pembaruan Dokumen (Sinkronisasi Codebase):**
> Seluruh isi pada dokumen ini telah mengalami perubahan arsitektur dokumen dan pembaruan substansi. Bagian yang diubah secara masif meliputi:
> - **3.1.1 - 3.1.4** (Restrukturisasi urutan, penghapusan ERD, penambahan Class Diagram untuk Manajemen Multi-School).
> - **3.2.1 - 3.2.4** (Restrukturisasi urutan, penghapusan ERD, penambahan Class Diagram untuk Manajemen Data Siswa).
> - **3.3.1 - 3.3.4** (Restrukturisasi urutan, penghapusan ERD, penambahan Class Diagram untuk Sistem Penilaian & Skala PAUD).
> 
> *Catatan ini dapat Anda hapus saat dokumen akan dicetak.*

---

BAB 3	PERANCANGAN PRODUK
Arsitektur sistem pada platform Paudpedia dirancang menggunakan pendekatan modular yang membagi aplikasi ke dalam tiga komponen utama, yaitu Admin Panel, Public B2C, dan SIAKAD (Sistem Informasi Akademik). Admin Panel digunakan oleh Admin dan Moderator untuk melakukan pengelolaan konten, katalog produk, administrasi platform, serta monitoring keseluruhan sistem. Sementara itu, Public B2C ditujukan bagi pengguna umum (User/Guest) yang menyediakan layanan Marketplace untuk produk digital, sistem transaksi, serta Learning Management System (LMS) yang dilengkapi dengan fitur sertifikasi.

[Gambar 4.x Diagram Arsitektur Sistem Paudpedia]

Komponen SIAKAD berfungsi sebagai sistem manajemen internal sekolah berbasis multi-tenant yang dapat diakses oleh Headmaster (Kepala Sekolah), Teacher (Guru), dan Parent (Orang Tua). Modul ini mencakup berbagai fitur seperti Manajemen Multi-School, Manajemen Hak Akses, Manajemen Data Siswa dan Orang Tua, Sistem Penilaian dan Skala PAUD, serta Manajemen Keuangan. Seluruh komponen aplikasi terintegrasi dengan Central Database (Database Terpusat) sebagai pusat penyimpanan data, serta terhubung dengan Midtrans Payment Gateway untuk mendukung berbagai proses transaksi keuangan yang berlangsung di dalam platform.

3.1	Rancangan Manajemen Multi-School dan Anggota Sekolah (Modul 2)
Fitur ini dirancang menggunakan arsitektur multi-tenant yang memungkinkan satu platform (SIAKAD) digunakan oleh banyak sekolah secara independen. Setiap sekolah memiliki ruang kerja dan datanya sendiri yang tidak saling bercampur, di mana manajemen data, paket langganan (Free/Pro), serta kapasitas siswa dan guru dikelola secara terpusat oleh Headmaster. Selain itu, fitur Hak Akses didasarkan pada Role-Based Access Control (RBAC) yang membedakan otoritas pengguna menjadi Kepala Sekolah (Headmaster), Guru (Teacher), dan Orang Tua (Parent). Implementasi ini memastikan bahwa guru hanya dapat melihat dan mengelola data di sekolah tempat mereka terdaftar, dan satu pengguna (User) dapat tergabung dalam beberapa sekolah dengan role yang berbeda secara fleksibel.

3.1.1	Activity Diagram
```text
[Start]
   |
   v
Login ke Sistem
   |
   v
Validasi Kredensial Pengguna
   |
   +-- [Tidak Valid] --> Tampilkan Pesan Gagal Login --> [End]
   |
   +-- [Valid]
           |
           v
   Cek Data Pengguna pada school_members
           |
           v
   Jumlah Sekolah > 1 ?
           |
           +-- [Ya]
           |       |
           |       v
           |  Tampilkan Halaman Pilih Sekolah
           |       |
           |       v
           |  Pengguna Memilih Sekolah
           |
           +-- [Tidak]
                   |
                   v
          Ambil School yang Terhubung
                   |
                   v
       Muat Session (school_id & role)
                   |
                   v
       Tentukan Hak Akses Berdasarkan Role
                   |
                   +-- [Headmaster]
                   |       |
                   |       v
                   |  Akses Penuh Manajemen
                   |
                   +-- [Teacher]
                   |       |
                   |       v
                   |  Akses Data Pembelajaran
                   |
                   +-- [Parent]
                           |
                           v
                    Akses Pemantauan
                           |
                           v
                 Tampilkan Dashboard
                           |
                           v
                         [End]
```
Diagram di atas menggambarkan alur aktivitas saat pengguna masuk ke dalam sistem dengan kapabilitas multi-school. Sistem akan memvalidasi akun, mengecek afiliasi sekolah, lalu menentukan hak akses (role) secara dinamis baik itu sebagai Kepala Sekolah, Guru, maupun Orang Tua di ruang kerja tenant sekolah yang dipilih.

3.1.2	Use Case Diagram
1. Use Case: Switch School
   Role: Headmaster, Teacher, Parent
   Deskripsi: Pengguna yang memiliki peranan di banyak sekolah beralih dari satu sistem tenant sekolah ke sekolah lainnya melalui dashboard.

3.1.3	Class Diagram
Struktur *Class Diagram* pada modul ini dibangun menggunakan arsitektur Model dari Eloquent ORM di Laravel, yang mewakili representasi OOP (*Object-Oriented Programming*) dari struktur arsitektur *multi-tenant*:
- **Class `School`**: Model sentral dari tenant. Memiliki atribut `name`, `npsn`, `subscription_plan`, dan `subscription_ended_at`. Memuat relasi koleksi *one-to-many* seperti `members()`, `teachers()`, `classes()`. Diperkaya fungsi validasi bisnis seperti `isPro()`, `isFree()`, `getRemainingStudentSlots()`, dan `canAddTeacher()`.
- **Class `User`**: Model pengelola autentikasi global. Menyimpan state kredensial `name`, `email`, `password`, dan properti identitas pengguna lainnya. Dilengkapi mekanisme otorisasi lewat *method* seperti `hasSchoolRole()`, `isTeacher()`, dan `isParent()`.
- **Class `SchoolMember`**: *Junction Class* atau perantara yang menghubungkan `User` dengan `School`. Memegang kunci otorisasi per-*tenant* melalui properti *Enum* `role_type` (seperti *Headmaster*, *Teacher*, *Parent*). Dilengkapi dengan metode validasi fungsional seperti `isHeadmaster()` dan ruang lingkup *query* (*Scope*) spesifik peran.
- **Class `ClassRoom`**: Merepresentasikan entitas fisik ruangan kelas. Terikat kuat pada *tenant* (`school_id`). Menyimpan detail kapasitas (`capacity`) dan merelasikan guru penanggung jawab lewat `homeroomTeacher()`.

3.1.4	Wireframe
Tampilan yang berkaitan dengan fitur ini meliputi:
- Halaman Registrasi & Pembuatan Sekolah (/auth/register)
- Halaman Selector Sekolah (/select-school)
- Halaman Manajemen Anggota Sekolah (/teachers)
- Halaman Pengaturan Profil Sekolah (/school/profile, /school/settings)

3.2	Rancangan Manajemen Data Siswa dan Orang Tua 
Rancangan manajemen data ini secara eksplisit memisahkan identitas anak didik (Siswa) dari akun pengguna otentikasi sistem, dikarenakan rentang usia anak usia dini (PAUD) yang belum mampu mengelola login mandiri. Oleh karena itu, data profil dan perekaman perkembangan siswa akan selalu ditautkan secara langsung pada profil Orang Tua (Parent Profile) yang berfungsi sebagai wali sah pemegang akses masuk ke sistem (Login). Mekanisme ini juga dirancang sedemikian rupa sehingga satu entitas Orang Tua dapat terhubung secara sentral ke profil beberapa anak sekaligus (Multiple Children Support) di berbagai kelas, bahkan ketika belum ada siswa yang dimasukkan (skenario *waiting list* penerimaan).

3.2.1	Activity Diagram
```text
[Start]
   |
   v
Masuk ke Manajemen Siswa
   |
   v
Pilih "Tambah Siswa" & Isi Data Pokok
   |
   v
Buka Opsi Orang Tua/Wali
   |
   +-- [Sudah Terdaftar] --> Pilih dari Daftar Wali
   |                                 |
   +-- [Belum Terdaftar]             |
           |                         |
           v                         |
     Input Email Wali Baru           |
           |                         |
           v                         |
    Sistem Buat Profil Parent <------+
           |
           v
      Pilih Kelas
           |
           v
    Simpan Data Siswa (Relasi parent_profile_id)
           |
           v
Kirim Email Akses ke Wali
           |
           v
         [End]
```
Diagram di atas menjabarkan proses ketika Kepala Sekolah menambahkan siswa baru. Sistem memfasilitasi penautan langsung kepada profil Orang Tua yang ada atau otomatis memandu pembuatan profil wali baru agar akun *login* Orang Tua siap digunakan saat itu juga untuk memantau anak.

3.2.2	Use Case Diagram
1. Use Case: Manage Students
   Role: Headmaster
   Deskripsi: Headmaster menambah, menyunting, atau menonaktifkan akun siswa pada sekolah.

2. Use Case: Manage Parent Profiles
   Role: Headmaster
   Deskripsi: Headmaster mengelola profil akses khusus wali dari para siswa.

3. Use Case: View School Students
   Role: Teacher
   Deskripsi: Teacher melihat rekap daftar siswa di sekolah secara *read-only*.

4. Use Case: View Children Data
   Role: Parent
   Deskripsi: Parent memantau eksklusif rekapan data anak miliknya yang terhubung dan tidak dapat mengintip rekapan anak di luar tanggung jawabnya.

3.2.3	Class Diagram
Model dan pemetaan objek untuk manajemen siswa dan wali melibatkan kolaborasi entitas spesifik beserta entitas pendukung dari struktur *tenant* sekolah:
- **Class `ParentProfile`**: Mewakili wali murid di sebuah institusi. Model ini memfasilitasi informasi komprehensif orang tua (`father_name`, `mother_name`, `phone`, `occupation`, `address`) yang dikaitkan langsung ke `User` (untuk otentikasi login) dan `School` (sebagai konteks data). Class ini memiliki fungsi `children()` untuk menarik kumpulan (koleksi) data anak secara *one-to-many*.
- **Class `Student`**: Model utama entitas siswa anak usia dini. Merupakan objek tanpa kredensial akses, yang hanya menampung properti riwayat pendidikan (`enrollment_date`, `status`, `class_id`) serta biodata lengkap. Entitas ini secara penuh bergantung pada metode `parent()` sebagai penunjuk kepemilikan. Model ini juga memiliki fungsi-fungsi pintar untuk memproduksi nilai utilitas seperti `getAgeInMonths()` maupun rasio persentase kehadiran `getAttendancePercentage()`.
- **Class `School`**: Model pilar pendaftaran yang menyimpan *registry* seluruh murid, memastikan profil `Student` dan `ParentProfile` yang terdaftar hanya dapat diakses dalam ruang lingkup (*scope*) institusi yang bersangkutan.
- **Class `ClassRoom`**: Berfungsi sebagai pengelompokan fisik penempatan murid. Profil `Student` direlasikan (*many-to-one*) kepada `ClassRoom` agar rekap murid dapat difilter per rombongan belajar.

3.2.4	Wireframe
Tampilan yang berkaitan dengan fitur ini meliputi:
- Halaman Data Induk Siswa (/students)
- Formulir Tambah/Edit Siswa (/students/create, /students/:id/edit)
- Halaman Manajemen Profil Orang Tua (/parents)
- Halaman Profil Siswa atau Detail (/students/:id)
- Dashboard Parent (/children)

3.3	Rancangan Sistem Penilaian dan Skala PAUD 
Modul sistem penilaian ini telah sepenuhnya diadaptasi secara *custom* dengan metode Kurikulum PAUD yang bertumpu pada laporan kualitatif serta pemantauan tumbuh kembang keseharian, alih-alih kuantitatif. Guru diberikan kemudahan untuk melakukan evaluasi observatif menggunakan indikator deskriptif terstruktur dari skala PAUD resmi, yaitu: BB (Belum Berkembang), MB (Mulai Berkembang), BSH (Berkembang Sesuai Harapan), dan BSB (Berkembang Sangat Baik). Penginputan bersifat progresif sehingga poin absensi kehadiran rutin dengan pencatatan rapor evaluatif akan terkalkulasi terus menerus lalu digabungkan menjadi rekap Rapor dalam format fisik maupun cetak file (PDF) yang bisa sewaktu-waktu ditilik oleh Orang Tua secara *real-time*.

3.3.1	Activity Diagram
```text
[Start]
   |
   v
Login & Buka Modul Asesmen
   |
   v
Pilih Siswa & Tanggal
   |
   v
Isi Kategori & Indikator
   |
   v
Pilih Skala Skor (BB/MB/BSH/BSB)
   |
   v
Tambah Catatan Naratif (Opsional)
   |
   v
Simpan Penilaian ke Database
   |
   +-- [Otomatis Sinkron]
           |
           v
    Data Masuk ke Dashboard Parent
           |
           v
         [End]
```
Diagram ini memodelkan alur evaluasi harian dimana guru PAUD secara langsung menginputkan laporan perkembangan menggunakan instrumen kualitatif yang kemudian tersinkronisasi *real-time* dengan antarmuka wali di rumah.

3.3.2	Use Case Diagram
1.	Use Case: Input Absensi
Role: Teacher	
Deskripsi: Menginput data historis kehadiran harian murid.
2.	Use Case: Input Nilai Asesmen
Role: Teacher
Deskripsi: Menginput data penilaian rapor kualitatif PAUD ke setiap entitas murid di kelas.
3.	Use Case: View Rapor Siswa
Role: Teacher, Parent, Headmaster
Deskripsi: Memantau historis laporan, disaat bersamaan Parent juga memantau log evaluatif bagi anak kandungnya.
4.	Use Case: Generate Rapor
Role: Headmaster, Teacher
Deskripsi: Membuat kompilasi transkrip resmi rekam nilai untuk dilebur menjadi dokumen file berektensi (*.pdf) yang diunduh pada tingkat paket Pro.


3.3.3	Class Diagram
Pemrosesan akademik direpresentasikan menggunakan kelas-kelas *logger* periodik yang menyimpan hasil pengamatan yang berjalan, dengan ditautkan pada subjek penanggung jawab:
- **Class `DevelopmentIndicator`**: Model statis yang memuat referensi target capaian kurikulum per aspek pertumbuhan. Kelas ini menjadi pustaka wajib yang dikonfigurasi oleh Kepala Sekolah.
- **Class `Assessment`**: Representasi pencatatan rapor periodik per anak. Setiap *instance* ini menyimpan `scale` observasi, relasi ke `development_indicators`, dan ter-cap waktu di `assessment_month`. Class ini menyediakan fungsi validasi bawaan `isPassing()` untuk mengonversi skala PAUD kualitatif menjadi matriks evaluasi akhir.
- **Class `Attendance`**: Model pencatatan aktivitas absensi harian yang menyematkan `status` kehadiran dan `proof_file_path` untuk data pendukung. Secara hierarkis, catatan presensi terhubung ke ruang kelas (`class_id`) yang secara spesifik menunjuk guru pembimbing kelas tersebut sebagai penanggung jawab rekam log.
- **Class `Teacher`**: Profil spesifik untuk peran pendidik. Menyimpan `nip` dan `bio`, serta mewarisi kendali sebagai wali kelas (*homeroom teacher*) melalui relasi `homeroomClasses()`. 
- **Class `Student`**: Subjek utama yang dikenai aktivitas penilaian. Model ini berelasi langsung *one-to-many* kepada himpunan nilai harian `Assessment` maupun daftar kehadiran harian `Attendance` miliknya.
- **Class `ClassRoom`**: Menjadi muara tempat evaluasi dilakukan secara massal. Presensi murid tidak ditautkan ke akun individual guru, melainkan dicatat melekat pada entitas `ClassRoom` (*class_id*) yang mewakilinya.

3.3.4	Wireframe
Tampilan yang berkaitan dengan pemantauan belajar meliputi:
- Halaman Input Absensi Harian (/attendance)
- Halaman Input Nilai Asesmen (/assessments)
- Halaman Rekapitulasi Rapor (/reports, /reports/:studentId)
- Halaman Pantauan Akademik Parent (/children/:id)

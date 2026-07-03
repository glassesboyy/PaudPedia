> **Catatan Pembaruan Dokumen (Sinkronisasi Codebase & Arsitektur):**
> Seluruh isi pada dokumen ini telah mengalami perubahan arsitektur dokumen dan pembaruan substansi sesuai dengan kondisi sistem terkini. Bagian yang diubah secara masif meliputi:
> - **3.1.1 - 3.1.4** (Restrukturisasi urutan, penambahan peran **Operator Sekolah** pada arsitektur RBAC, dan Class Diagram untuk Manajemen Multi-School).
> - **3.2.1 - 3.2.4** (Restrukturisasi urutan, penambahan hak akses **Operator** dalam pengelolaan data siswa dan wali, dan Class Diagram).
> - **3.3.1 - 3.3.4** (Restrukturisasi urutan, penambahan arsitektur **Temporal Assessment Integrity** / Proteksi Validitas Waktu Kurikulum, pembaruan 5 Pilar Algoritma Penanganan Kasus Negatif, dan Class Diagram untuk Sistem Penilaian & Skala PAUD).
> 
> *Catatan ini dapat Anda hapus saat dokumen akan dicetak.*

---

BAB 3	PERANCANGAN PRODUK
Arsitektur sistem pada platform Paudpedia dirancang menggunakan pendekatan modular yang membagi aplikasi ke dalam tiga komponen utama, yaitu Admin Panel, Public B2C, dan SIAKAD (Sistem Informasi Akademik). Admin Panel digunakan oleh Admin dan Moderator untuk melakukan pengelolaan konten, katalog produk, administrasi platform, serta monitoring keseluruhan sistem. Sementara itu, Public B2C ditujukan bagi pengguna umum (User/Guest) yang menyediakan layanan Marketplace untuk produk digital, sistem transaksi, serta Learning Management System (LMS) yang dilengkapi dengan fitur sertifikasi.

[Gambar 4.x Diagram Arsitektur Sistem Paudpedia]

Komponen SIAKAD berfungsi sebagai sistem manajemen internal sekolah berbasis multi-tenant yang dapat diakses oleh empat peran (*Role*) utama: **Headmaster** (Kepala Sekolah), **Operator** (Staf Administrasi Sekolah), **Teacher** (Guru Kelas), dan **Parent** (Orang Tua/Wali). Modul ini mencakup berbagai fitur seperti Manajemen Multi-School, Manajemen Hak Akses, Manajemen Data Siswa dan Orang Tua, Sistem Penilaian dan Skala PAUD dengan Proteksi Integritas Temporal, serta Manajemen Keuangan. Seluruh komponen aplikasi terintegrasi dengan Central Database (Database Terpusat) sebagai pusat penyimpanan data, serta terhubung dengan Midtrans Payment Gateway untuk mendukung berbagai proses transaksi keuangan yang berlangsung di dalam platform.

3.1	Rancangan Manajemen Multi-School dan Anggota Sekolah (Modul 2)
Fitur ini dirancang menggunakan arsitektur multi-tenant yang memungkinkan satu platform (SIAKAD) digunakan oleh banyak sekolah secara independen. Setiap sekolah memiliki ruang kerja dan datanya sendiri yang tidak saling bercampur, di mana manajemen data, paket langganan (Free/Pro), serta kapasitas siswa dan guru dikelola secara terpusat oleh Headmaster. Selain itu, fitur Hak Akses didasarkan pada *Role-Based Access Control* (RBAC) yang membedakan otoritas pengguna menjadi empat peran operasional:
1. **Kepala Sekolah (*Headmaster*)**: Memiliki otoritas tertinggi pada *tenant* sekolah untuk mengatur profil institusi, kapasitas slot, langganan, dan pengangkatan staf.
2. **Operator Sekolah (*Operator*)**: Peran administratif yang ditambahkan untuk membantu Kepala Sekolah mengelola operasional harian seperti data induk siswa, rombongan belajar (kelas), pengaturan kurikulum (master program dan indikator penilaian), serta transaksi keuangan tanpa harus memegang hak akses finansial/langganan tingkat tinggi Kepala Sekolah.
3. **Guru Kelas (*Teacher*)**: Memiliki hak akses khusus pada kelas yang dibimbingnya (*homeroom class*) untuk melakukan penginputan absensi harian, evaluasi observasi bulanan, serta penyusunan narasi rapor perkembangan.
4. **Orang Tua (*Parent*)**: Peran pemantauan eksklusif yang hanya dapat melihat rekapitulasi data akademik, kehadiran, penilaian, dan keuangan anak kandungnya sendiri.

Implementasi ini memastikan bahwa guru dan operator hanya dapat melihat dan mengelola data di sekolah tempat mereka terdaftar, dan satu pengguna (*User*) dapat tergabung dalam beberapa sekolah dengan peran (*role*) yang berbeda secara fleksibel.

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
                   |  Akses Penuh Manajemen & Langganan
                   |
                   +-- [Operator]
                   |       |
                   |       v
                   |  Akses Administrasi Sekolah & Master Kurikulum
                   |
                   +-- [Teacher]
                   |       |
                   |       v
                   |  Akses Pembelajaran & Input Penilaian Kelas
                   |
                   +-- [Parent]
                           |
                           v
                    Akses Pemantauan Eksklusif Anak
                           |
                           v
                 Tampilkan Dashboard Sesuai Role
                           |
                           v
                         [End]
```
Diagram di atas menggambarkan alur aktivitas saat pengguna masuk ke dalam sistem dengan kapabilitas *multi-school*. Sistem akan memvalidasi akun, mengecek afiliasi sekolah, lalu menentukan hak akses (*role*) secara dinamis baik itu sebagai Kepala Sekolah, Operator, Guru, maupun Orang Tua di ruang kerja *tenant* sekolah yang dipilih.

3.1.2	Use Case Diagram
1. Use Case: Switch School
   Role: Headmaster, Operator, Teacher, Parent
   Deskripsi: Pengguna yang memiliki peranan di banyak sekolah beralih dari satu sistem *tenant* sekolah ke sekolah lainnya melalui dasbor tanpa perlu *re-login*.

2. Use Case: Manage School Members
   Role: Headmaster
   Deskripsi: Kepala Sekolah menambahkan, menyunting peran, atau menonaktifkan anggota sekolah (Guru dan Operator).

3.1.3	Class Diagram
Struktur *Class Diagram* pada modul ini dibangun menggunakan arsitektur Model dari Eloquent ORM di Laravel, yang mewakili representasi OOP (*Object-Oriented Programming*) dari struktur arsitektur *multi-tenant*:
- **Class `School`**: Model sentral dari *tenant*. Memiliki atribut `name`, `npsn`, `subscription_plan`, dan `subscription_ended_at`. Memuat relasi koleksi *one-to-many* seperti `members()`, `teachers()`, `classes()`. Diperkaya fungsi validasi bisnis seperti `isPro()`, `isFree()`, `getRemainingStudentSlots()`, dan `canAddTeacher()`.
- **Class `User`**: Model pengelola autentikasi global. Menyimpan *state* kredensial `name`, `email`, `password`, dan properti identitas pengguna lainnya. Dilengkapi mekanisme otorisasi lewat *method* seperti `hasSchoolRole()`, `isTeacher()`, `isOperator()`, dan `isParent()`.
- **Class `SchoolMember`**: *Junction Class* atau perantara yang menghubungkan `User` dengan `School`. Memegang kunci otorisasi per-*tenant* melalui properti *Enum* `role_type` (*Headmaster*, *Operator*, *Teacher*, *Parent*). Dilengkapi dengan metode validasi fungsional seperti `isHeadmaster()`, `isOperator()`, dan ruang lingkup *query* (*Scope*) spesifik peran.
- **Class `ClassRoom`**: Merepresentasikan entitas fisik ruangan kelas. Terikat kuat pada *tenant* (`school_id`). Menyimpan detail kapasitas (`capacity`) dan merelasikan guru penanggung jawab lewat `homeroomTeacher()`.

3.1.4	Wireframe
Tampilan yang berkaitan dengan fitur ini meliputi:
- Halaman Registrasi & Pembuatan Sekolah (`/auth/register`)
- Halaman Selector Sekolah (`/select-school`)
- Halaman Manajemen Anggota Sekolah & Operator (`/teachers`, `/operators`)
- Halaman Pengaturan Profil Sekolah (`/school/profile`, `/school/settings`)

3.2	Rancangan Manajemen Data Siswa dan Orang Tua 
Rancangan manajemen data ini secara eksplisit memisahkan identitas anak didik (Siswa) dari akun pengguna otentikasi sistem, dikarenakan rentang usia anak usia dini (PAUD) yang belum mampu mengelola login mandiri. Oleh karena itu, data profil dan perekaman perkembangan siswa akan selalu ditautkan secara langsung pada profil Orang Tua (*Parent Profile*) yang berfungsi sebagai wali sah pemegang akses masuk ke sistem (*Login*). Mekanisme ini dirancang sedemikian rupa sehingga satu entitas Orang Tua dapat terhubung secara sentral ke profil beberapa anak sekaligus (*Multiple Children Support*) di berbagai kelas, bahkan ketika belum ada siswa yang dimasukkan (skenario *waiting list* penerimaan). Dalam operasionalnya, penambahan dan modifikasi data siswa serta wali dapat dilakukan secara kolaboratif oleh **Headmaster** maupun **Operator Sekolah**.

3.2.1	Activity Diagram
```text
[Start]
   |
   v
Masuk ke Manajemen Siswa (Headmaster / Operator)
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
      Pilih Kelas Rombel
           |
           v
    Simpan Data Siswa (Relasi parent_profile_id)
           |
           v
Kirim Email Akses Kredensial ke Wali
           |
           v
         [End]
```
Diagram di atas menjabarkan proses ketika Kepala Sekolah atau Operator menambahkan siswa baru. Sistem memfasilitasi penautan langsung kepada profil Orang Tua yang ada atau otomatis memandu pembuatan profil wali baru agar akun *login* Orang Tua siap digunakan saat itu juga untuk memantau anak.

3.2.2	Use Case Diagram
1. Use Case: Manage Students
   Role: Operator
   Deskripsi: Operator menambah, menyunting, memutasi kelas, atau menonaktifkan akun siswa pada sekolah.

2. Use Case: Manage Parent
   Role: Operator
   Deskripsi: Operator mengelola profil akses khusus wali dari para siswa serta menautkan relasi keluarga.

3. Use Case: View School Students
   Role: Headmaster, Teacher
   Deskripsi: Headmaster dan Teacher melihat rekap daftar siswa di sekolah secara *read-only*.

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
- Halaman Data Induk Siswa (`/students`)
- Formulir Tambah/Edit Siswa (`/students/create`, `/students/:id/edit`)
- Halaman Manajemen Profil Orang Tua (`/parents`)
- Halaman Profil Siswa atau Detail (`/students/:id`)
- Dasbor Parent (`/children`)

3.3	Rancangan Sistem Penilaian dan Skala PAUD (Arsitektur Integritas Temporal)
Modul sistem penilaian ini diadaptasi secara *custom* dengan metode Kurikulum Merdeka PAUD yang bertumpu pada laporan observasi kualitatif serta pemantauan tumbuh kembang keseharian. Guru diberikan kemudahan untuk melakukan evaluasi observatif menggunakan indikator deskriptif terstruktur dari skala PAUD resmi, yaitu: **BB** (Belum Berkembang), **MB** (Mulai Berkembang), **BSH** (Berkembang Sesuai Harapan), dan **BSB** (Berkembang Sangat Baik).

Untuk menjaga keutuhan data historis dari anomali perubahan kurikulum di pertengahan atau akhir semester (misalnya ketika Operator menambah, menyunting, atau menghapus indikator di master data), sistem menerapkan **Arsitektur Integritas Temporal (*Temporal Assessment Integrity*)** yang didukung oleh 5 Pilar Algoritma Penanganan Kasus Negatif:
1. **Restrict Delete Guard (`HTTP 422`)**: Sistem menolak penghapusan fisik (*hard delete*) pada program atau indikator yang telah memiliki riwayat penilaian siswa. Operator diarahkan menggunakan fitur penonaktifan (*Soft Deletes* / `is_active = false`).
2. **Temporal Filter (`created_at`)**: Saat guru membuka form penilaian bulan tertentu, sistem memuat indikator berdasarkan filter waktu kelahiran (`created_at <= akhir_bulan`). Indikator yang baru dibuat di masa depan tidak akan muncul sebagai sel kosong yang mengganggu pada form observasi bulan lampau.
3. **Draft Protection Guard (`hasExistingReport`)**: Pada modul penyusunan rapor naratif, sistem mendeteksi apakah draf rapor siswa sudah pernah disimpan. Jika sudah ada (`hasExistingReport == true`), validasi kelengkapan matriks otomatis terlewati sehingga form rapor lama tidak akan pernah terkunci (*disabled*) oleh penambahan indikator baru di masa depan.
4. **Smart Matrix Completeness Rule**: Aturan kelengkapan penyusunan rapor baru dilonggarkan dari *"100% sel wajib terisi"* menjadi *"Setiap Aspek Program Perkembangan minimal memiliki 1 observasi penilaian pada indikator apa pun selama semester berjalan"*. Ini mengakomodasi asesmen autentik PAUD serta siswa pindahan.
5. **Smart Omission & Program Omission**: Pada generator cetak PDF Rapor (`ReportController`), indikator yang memiliki 0 nilai observasi di sepanjang 6 bulan semester tersebut otomatis disembunyikan (*omitted*) dari tabel PDF. Jika seluruh indikator di bawah suatu program hilang, judul Program Perkembangan juga tidak dicetak agar laporan tetap bersih dan profesional.

3.3.1	Activity Diagram
```text
[Start]
   |
   v
Login & Buka Modul Asesmen (Guru Kelas)
   |
   v
Pilih Kelas, Siswa, dan Bulan Penilaian
   |
   v
Sistem Memuat Indikator Aktif & Valid Secara Temporal (created_at <= Akhir Bulan)
   |
   v
Guru Input Skala Skor (BB/MB/BSH/BSB) pada Indikator yang Tersedia
   |
   v
Simpan Penilaian ke Database (assessments)
   |
   v
Buka Modul Penyusunan Rapor Naratif Semester
   |
   v
Sistem Memeriksa Status Draft & Kelengkapan Matriks
   |
   +-- [Draft Sudah Ada / Syarat Minimal 1 Observasi Terpenuhi]
   |       |
   |       v
   |  Form Narasi Terbuka (Draft Protection Guard Aktif)
   |       |
   |       v
   |  Guru Menyusun Narasi Pendahuluan, Per Program, & Rekomendasi
   |       |
   |       v
   |  Simpan Rapor Naratif (student_reports)
   |       |
   |       v
   |  Cetak Rapor PDF (Sistem Menjalankan Smart Omission untuk Item Kosong)
   |
   +-- [Matriks Kosong Total / Belum Ada Nilai]
           |
           v
      Form Narasi Dikunci (Empty Matrix Block) --> [End]
```
Diagram ini memodelkan alur evaluasi dari input observasi bulanan berfilter temporal hingga pencetakan rapor naratif yang dilindungi oleh mekanisme *Draft Protection* dan *Smart Omission*.

3.3.2	Use Case Diagram
1. Use Case: Manage Program & Indicator
   Role: Operator
   Deskripsi: Mengatur katalog Program Perkembangan dan Indikator.

2. Use Case: Input Daily Attendance
   Role: Teacher
   Deskripsi: Menginput data kehadiran harian murid di kelas bimbingannya.

3. Use Case: Input Monthly Assessment
   Role: Teacher
   Deskripsi: Menginput data penilaian observasi kualitatif PAUD per bulan berdasarkan indikator yang valid secara temporal.

4. Use Case: Manage Student Reports
   Role: Teacher, Headmaster
   Deskripsi: Guru menyusun narasi rapor perkembangan siswa. Kepala Sekolah dapat mengunduh dokumen cetak resmi PDF dan bisa melihat data narasi rapoe secara read only.

5. Use Case: Monitor Student Reports
   Role: Parent
   Deskripsi: Orang tua memantau riwayat evaluasi observasi bulanan dan mengunduh rapor naratif akhir semester anaknya.

3.3.3	Class Diagram
Pemrosesan akademik direpresentasikan menggunakan kelas-kelas *logger* periodik dan master kurikulum yang diperkaya dengan kontrol integritas temporal:
- **Class `DevelopmentProgram` & `DevelopmentIndicator`**: Model katalog kurikulum. Memiliki atribut `name`, `order`, dan status `is_active` (*boolean*). Dilengkapi relasi bersarang serta proteksi di level *controller* untuk mencegah penghapusan jika telah berelasi dengan data nilai siswa.
- **Class `Assessment`**: Representasi pencatatan rapor periodik per anak. Setiap *instance* menyimpan `scale` observasi (*Enum* BB/MB/BSH/BSB), relasi ke `development_indicators`, dan cap waktu `assessment_month`. Terintegrasi dengan filter *scope* untuk pemetaan matriks 6 bulan.
- **Class `StudentReport` & `StudentReportDetail`**: Menyimpan transkrip rapor naratif semester. `StudentReport` memegang `introduction_notes`, `closing_notes`, `recommendation`, dan status publikasi. `StudentReportDetail` menyimpan uraian narasi spesifik per `development_program_id`.
- **Class `Attendance`**: Model pencatatan aktivitas absensi harian yang menyematkan `status` (*Present, Sick, Permission, Absent*) dan `proof_file_path`. Terhubung ke ruang kelas (`class_id`).
- **Class `Teacher`**: Profil spesifik untuk peran pendidik. Menyimpan `nip` dan `bio`, serta mewarisi kendali sebagai wali kelas (*homeroom teacher*) melalui relasi `homeroomClasses()`. 
- **Class `Student`**: Subjek utama yang dikenai aktivitas penilaian. Model ini berelasi langsung *one-to-many* kepada himpunan nilai harian `Assessment`, daftar kehadiran `Attendance`, dan rapor akhir `StudentReport`.
- **Class `ClassRoom`**: Menjadi muara tempat evaluasi dilakukan secara massal. Presensi murid dan input nilai dicatat melekat pada entitas `ClassRoom` (*class_id*) yang mewakilinya.

3.3.4	Wireframe
Tampilan yang berkaitan dengan pemantauan belajar meliputi:
- Halaman Master Pengaturan Penilaian (`/settings/assessments`)
- Halaman Input Absensi Harian (`/attendance`)
- Halaman Input Nilai Asesmen Bulanan (`/assessments`)
- Halaman Penyusunan Rapor Naratif (`/assessments/reports`)
- Halaman Rekapitulasi & Unduh Rapor (`/reports`, `/reports/:studentId`)
- Halaman Pantauan Akademik Parent (`/children/:id`)

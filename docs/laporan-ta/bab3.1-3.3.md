BAB 3	PERANCANGAN PRODUK
Arsitektur sistem pada platform Paudpedia dirancang menggunakan pendekatan modular yang membagi aplikasi ke dalam tiga komponen utama, yaitu Admin Panel, Public B2C, dan SIAKAD (Sistem Informasi Akademik). Admin Panel digunakan oleh Admin dan Moderator untuk melakukan pengelolaan konten, katalog produk, administrasi platform, serta monitoring keseluruhan sistem. Sementara itu, Public B2C ditujukan bagi pengguna umum (User/Guest) yang menyediakan layanan Marketplace untuk produk digital, sistem transaksi, serta Learning Management System (LMS) yang dilengkapi dengan fitur sertifikasi.

[Gambar 4.x Diagram Arsitektur Sistem Paudpedia]

Komponen SIAKAD berfungsi sebagai sistem manajemen internal sekolah berbasis multi-tenant yang dapat diakses oleh Headmaster (Kepala Sekolah), Teacher (Guru), dan Parent (Orang Tua). Modul ini mencakup berbagai fitur seperti Manajemen Multi-School, Manajemen Hak Akses, Manajemen Data Siswa dan Orang Tua, Sistem Penilaian dan Skala PAUD, serta Manajemen Keuangan. Seluruh komponen aplikasi terintegrasi dengan Central Database (Database Terpusat) sebagai pusat penyimpanan data, serta terhubung dengan Midtrans Payment Gateway untuk mendukung berbagai proses transaksi keuangan yang berlangsung di dalam platform.

3.1	Rancangan Manajemen Multi-School dan Anggota Sekolah (Modul 2)
Fitur ini dirancang menggunakan arsitektur multi-tenant yang memungkinkan satu platform (SIAKAD) digunakan oleh banyak sekolah secara independen. Setiap sekolah memiliki ruang kerja dan datanya sendiri yang tidak saling bercampur, di mana manajemen data, paket langganan (Free/Pro), serta kapasitas siswa dan guru dikelola secara terpusat oleh Headmaster. Selain itu, fitur Hak Akses didasarkan pada Role-Based Access Control (RBAC) yang membedakan otoritas pengguna menjadi Kepala Sekolah (Headmaster), Guru (Teacher), dan Orang Tua (Parent). Implementasi ini memastikan bahwa guru hanya dapat melihat dan mengelola data di sekolah tempat mereka terdaftar, dan satu pengguna (User) dapat tergabung dalam beberapa sekolah dengan role yang berbeda secara fleksibel.

3.1.1	Wireframe
Tampilan yang berkaitan dengan fitur ini meliputi:
- Halaman Registrasi & Pembuatan Sekolah (/auth/register)
- Halaman Selector Sekolah (/select-school)
- Halaman Manajemen Anggota Sekolah (/teachers)
- Halaman Pengaturan Profil Sekolah (/school/profile, /school/settings)

3.1.2	Entity Relationship Diagram (ERD)

**Entitas: schools**
Atribut: name, npsn, subscription_plan, subscription_expires_at
Relasi:
- One-to-Many ke entitas `school_members` | Kata Relasi: "Memiliki Anggota"
- One-to-Many ke entitas `classes` | Kata Relasi: "Memiliki Kelas"

**Entitas: users**
Atribut: name, email, password, phone
Relasi:
- One-to-Many ke entitas `school_members` | Kata Relasi: "Terdaftar Sebagai"

**Entitas: school_members**
Atribut: role, is_active, joined_at
Relasi:
- Many-to-One ke entitas `schools` | Kata Relasi: "Bagian Dari"
- Many-to-One ke entitas `users` | Kata Relasi: "Dimiliki Oleh"

**Entitas: classes**
Atribut: name, level, capacity, academic_year
Relasi:
- Many-to-One ke entitas `schools` | Kata Relasi: "Berada Di"

*Penjelasan Keseluruhan:*
Diagram ERD pada modul ini berfokus pada manajemen identitas dan isolasi arsitektur *multi-tenant* (banyak sekolah dalam satu platform). Entitas `schools` bertindak sebagai sentral/induk yang menyimpan profil institusi dan memegang batas kedaluwarsa paket langganan. Untuk mengatur hak akses, entitas autentikasi global `users` dihubungkan dengan `schools` melalui entitas penghubung (tabel pivot) bernama `school_members`. Penggunaan tabel penghubung ini sangat krusial karena satu entitas *user* (pengguna) dimungkinkan untuk memiliki multi-peran di berbagai instansi yang berbeda, misalnya bertindak sebagai wali murid di Sekolah A, namun sekaligus menjabat sebagai guru di Sekolah B. *Role* atau jabatan tersebut disimpan secara eksplisit dalam entitas `school_members`. Lebih lanjut, entitas `schools` juga mendistribusikan kewenangannya ke bawah dengan memiliki entitas `classes` yang merepresentasikan daftar pembagian rombongan belajar (rombel) fisik sesuai batas kapasitas dan jenjang akademik yang diselenggarakan oleh institusi sekolah yang bersangkutan.

3.1.3	Database Schema
- schools
- users
- school_members
- classes

Tabel `schools` berisi kolom id, nama, npsn, tipe berlangganan, batas siswa/guru, dan status. Tabel `users` berisi kredensial akun pengguna secara umum. Tabel `school_members` bertindak sebagai *junction table* yang memiliki Foreign Key `school_id` ke tabel schools, `user_id` ke tabel users, serta kolom `role`. Tabel `classes` memiliki Foreign Key `school_id` ke schools, serta `homeroom_teacher_id` opsional.

3.1.4	Activity Diagram
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

3.1.5	Use Case Diagram
1. Use Case: Register School
   Role: Headmaster
   Deskripsi: Headmaster mendaftarkan sekolah baru ke dalam sistem untuk tenant pertamanya.

2. Use Case: Manage Teachers
   Role: Headmaster
   Deskripsi: Headmaster menambahkan atau mencopot akses guru di sekolah tersebut.

3. Use Case: Manage Classes
   Role: Headmaster
   Deskripsi: Headmaster membuat ruangan kelas dan menugaskan wali kelas.

4. Use Case: Switch School
   Role: Headmaster, Teacher, Parent
   Deskripsi: Pengguna yang memiliki peranan di banyak sekolah beralih dari satu sistem tenant sekolah ke sekolah lainnya melalui dashboard.

3.2	Rancangan Manajemen Data Siswa dan Orang Tua 
Rancangan manajemen data ini secara eksplisit memisahkan identitas anak didik (Siswa) dari akun pengguna otentikasi sistem, dikarenakan rentang usia anak usia dini (PAUD) yang belum mampu mengelola login mandiri. Oleh karena itu, data profil dan perekaman perkembangan siswa akan selalu ditautkan secara langsung pada profil Orang Tua (Parent Profile) yang berfungsi sebagai wali sah pemegang akses masuk ke sistem (Login). Mekanisme ini juga dirancang sedemikian rupa sehingga satu entitas Orang Tua dapat terhubung secara sentral ke profil beberapa anak sekaligus (Multiple Children Support) di berbagai kelas, bahkan ketika belum ada siswa yang dimasukkan (skenario *waiting list* penerimaan).

3.2.1	Wireframe
Tampilan yang berkaitan dengan fitur ini meliputi:
- Halaman Data Induk Siswa (/students)
- Formulir Tambah/Edit Siswa (/students/create, /students/:id/edit)
- Halaman Manajemen Profil Orang Tua (/parents)
- Halaman Profil Siswa atau Detail (/students/:id)
- Dashboard Parent (/children)

3.2.2	Entity Relationship Diagram (ERD)

**Entitas: parent_profiles**
Atribut: father_name, mother_name, phone, address
Relasi:
- Many-to-One ke entitas `schools` | Kata Relasi: "Terdaftar Di"
- One-to-One ke entitas `users` | Kata Relasi: "Dikaitkan Dengan"
- One-to-Many ke entitas `students` | Kata Relasi: "Mewali"

**Entitas: students**
Atribut: name, nisn, birth_date, gender, enrollment_date, status
Relasi:
- Many-to-One ke entitas `parent_profiles` | Kata Relasi: "Diasuh Oleh"
- Many-to-One ke entitas `schools` | Kata Relasi: "Bersekolah Di"
- Many-to-One ke entitas `classes` | Kata Relasi: "Ditempatkan Di"

**Entitas: schools**
Atribut: name, npsn
Relasi:
- One-to-Many ke entitas `parent_profiles` | Kata Relasi: "Memiliki Wali"
- One-to-Many ke entitas `students` | Kata Relasi: "Mendidik Siswa"

**Entitas: classes**
Atribut: name, level
Relasi:
- One-to-Many ke entitas `students` | Kata Relasi: "Ditempati Oleh"

*Penjelasan Keseluruhan:*
Struktur ERD pada modul pendaftaran ini secara tegas memisahkan data personal anak dari entitas autentikasi sistem. Data profil wali murid disimpan khusus pada entitas `parent_profiles` yang berelasi *one-to-one* dengan akun masuk sistem global `users`, sekaligus berafiliasi kepada `schools` tertentu. Sementara itu, entitas rekam jejak anak (`students`) diatur sedemikian rupa agar bergantung mutlak dengan kardinalitas *many-to-one* kepada profil orang tua (`parent_profiles`). Hal ini merepresentasikan logika di dunia nyata bahwa satu profil keluarga dapat mendaftarkan, membayarkan, dan memantau perkembangan lebih dari satu anak (kakak-beradik) sekaligus dalam satu *dashboard* aplikasi. Entitas `students` sama sekali tidak diberikan hak sistem otentikasi mandiri, melainkan hak interaksinya sepenuhnya diwakili/dipegang oleh wali mereka. Selain itu, entitas anak juga memuat *Foreign Key* ke tabel `classes` untuk menetapkan lokasi pembagian rombongan belajar fiktifnya, serta referensi tegas ke entitas `schools` guna menjamin rekaman anak PAUD A tidak akan bocor atau terekspos secara *cross-tenant* ke institusi PAUD B.

3.2.3	Database Schema
- students
- parent_profiles
- schools
- classes

Tabel `parent_profiles` memiliki referensi *Foreign Key* berupa `school_id` dan `user_id`. Terdapat pengisian spesifik wali (father_name, mother_name) dan email yang dibuat komposit eksklusif per `school_id`. Tabel `students` memuat referensi relasional berupa Foreign Key `school_id`, `class_id`, dan `parent_profile_id`. Atribut yang dicatat berupa biodata nama, nisn, gender. Constraint dirancang Restrict pada wali murid agar data tidak terhapus selama anak didik masih tertaut.

3.2.4	Activity Diagram
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

3.2.5	Use Case Diagram
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

3.3	Rancangan Sistem Penilaian dan Skala PAUD 
Modul sistem penilaian ini telah sepenuhnya diadaptasi secara *custom* dengan metode Kurikulum PAUD yang bertumpu pada laporan kualitatif serta pemantauan tumbuh kembang keseharian, alih-alih kuantitatif. Guru diberikan kemudahan untuk melakukan evaluasi observatif menggunakan indikator deskriptif terstruktur dari skala PAUD resmi, yaitu: BB (Belum Berkembang), MB (Mulai Berkembang), BSH (Berkembang Sesuai Harapan), dan BSB (Berkembang Sangat Baik). Penginputan bersifat progresif sehingga poin absensi kehadiran rutin dengan pencatatan rapor evaluatif akan terkalkulasi terus menerus lalu digabungkan menjadi rekap Rapor dalam format fisik maupun cetak file (PDF) yang bisa sewaktu-waktu ditilik oleh Orang Tua secara *real-time*.

3.3.1	Wireframe
Tampilan yang berkaitan dengan pemantauan belajar meliputi:
- Halaman Input Absensi Harian (/attendance)
- Halaman Input Nilai Asesmen (/assessments)
- Halaman Rekapitulasi Rapor (/reports, /reports/:studentId) ------------ INI BELOM
- Halaman Pantauan Akademik Parent (/children/:id)

3.3.2	Entity Relationship Diagram (ERD)

**Entitas: assessments**
Atribut: category, indicator, score, notes, assessment_date, semester
Relasi:
- Many-to-One ke entitas `students` | Kata Relasi: "Menilai Siswa"
- Many-to-One ke entitas `teachers` | Kata Relasi: "Dinilai Oleh"
- Many-to-One ke entitas `classes` | Kata Relasi: "Diadakan Di Kelas"

**Entitas: attendance**
Atribut: date, status, notes
Relasi:
- Many-to-One ke entitas `students` | Kata Relasi: "Merekam Kehadiran"
- Many-to-One ke entitas `teachers` | Kata Relasi: "Dicatat Oleh"

**Entitas: students**
Atribut: name, nisn
Relasi:
- One-to-Many ke entitas `assessments` | Kata Relasi: "Mendapat Penilaian"
- One-to-Many ke entitas `attendance` | Kata Relasi: "Memiliki Presensi"

**Entitas: teachers**
Atribut: nip, specialization
Relasi:
- One-to-Many ke entitas `assessments` | Kata Relasi: "Memberikan Penilaian"
- One-to-Many ke entitas `attendance` | Kata Relasi: "Mencatat Presensi"

*Penjelasan Keseluruhan:*
Diagram ERD pada blok operasional akademik ini sangat bergantung pada keberadaan entitas bersifat *transaksional* (data yang bertambah seiring waktu). Entitas utama yang di-highlight adalah `assessments` yang mewakili rekaman nilai rapor atau buku penghubung siswa berdasarkan indikator kompetensi PAUD, serta entitas `attendance` yang berguna sebagai lembar absensi harian (status hadir, izin, sakit, atau alfa). Kedua tabel log ini mencatat peristiwa secara periodik berulang, sehingga wujud grafisnya harus berelasi *many-to-one* secara konvergen (memusat) kepada entitas dasar `students` (subjek murid siapa yang dinilai/diabsen) dan kepada entitas `teachers` (aktor tenaga pendidik mana yang bertanggung jawab menyematkan penilaian tersebut). Relasi triangulasi yang saling bersilang ini memastikan bahwa *tracer study* anak—atau riwayat rekam jejak progres belajarnya—bisa ditelusuri dan diaudit secara sangat mendetail berdasarkan urutan semester dan wali kelasnya masing-masing. Terakhir, semua pencatatan transaksi ini dijangkarkan (Foreign Key) terisolasi pada satu payung entitas `schools` yang sama agar keamanan dokumen penilaian privasi di tiap tenant dijamin tidak akan pernah tertukar.

3.3.3	Database Schema
- assessments
- attendance
- students
- teachers
- classes

Tabel `attendance` menyimpan log referensi *Foreign Key* `student_id`, `teacher_id`, `class_id`, dan `school_id`. Pencatat utamanya berisi `date` dengan tipe *Enum* terhadap status rekaman `present`, `sick`, `permission`, atau `absent`. Tabel `assessments` memuat skema relasi *Foreign Key* yang persis serupa dengan absensi. Nilai kolom dikhususkan meliputi `category`, `indicator` penialaian, `score` tipe *Enum* ('BB', 'MB', 'BSH', 'BSB'), field ekstra `notes`, hingga parameter `semester`.

3.3.4	Activity Diagram
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

3.3.5	Use Case Diagram
1. Use Case: Input Absensi
   Role: Teacher, Headmaster
   Deskripsi: Menginput data historis kehadiran harian murid.

2. Use Case: Input Nilai Asesmen
   Role: Teacher
   Deskripsi: Menginjeksi matriks data penilaian rapor kualitatif PAUD ke setiap entitas murid di kelas.

3. Use Case: View Rapor Siswa
   Role: Teacher, Parent
   Deskripsi: Memantau historis laporan, disaat bersamaan Parent juga memantau log evaluatif bagi anak kandungnya.

4. Use Case: Generate PDF Rapor
   Role: Headmaster, Teacher
   Deskripsi: Membuat kompilasi transkrip resmi rekam nilai untuk dilebur menjadi dokumen file berektensi (*.pdf) yang diunduh pada tingkat paket Pro.

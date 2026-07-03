BAB 4	IMPLEMENTASI
Bab ini menguraikan tahapan krusial dalam pengembangan perangkat lunak, yaitu fase implementasi sistem. Pada tahap ini, seluruh rancangan teoretis dan arsitektur logis yang telah didefinisikan secara komprehensif pada bab-bab sebelumnya mulai direalisasikan ke dalam bentuk produk nyata. Implementasi merupakan proses konversi dari desain pemodelan—seperti *Entity Relationship Diagram* (ERD), skema struktur basis data, hingga alur pemrosesan *Activity Diagram*—menjadi baris-baris kode program (*source code*) yang fungsional. Tujuan utama dari bab ini adalah untuk memberikan pembuktian bahwa spesifikasi kebutuhan sistem dapat dieksekusi secara teknis dan beroperasi dengan baik sesuai dengan batasan ekspektasi yang telah ditetapkan.

Lebih lanjut, pembahasan pada bab ini akan merincikan perwujudan sistem dari dua sudut pandang utama, yakni pengembangan tampilan antarmuka (*front-end*) dan logika pemrograman inti (*back-end*). Dari sisi antarmuka, penjabaran akan berfokus pada hasil translasi *wireframe* menjadi tata letak visual yang interaktif guna menjamin pengalaman pengguna (*User Experience*) yang optimal. Sementara itu, dari sisi struktur *back-end*, pembahasan akan menitikberatkan pada penjabaran potongan baris kode (*code snippets*) untuk memperlihatkan bagaimana arsitektur keamanan, pengelolaan data relasional, serta algoritma komputasi diimplementasikan di balik layar. Seluruh integrasi dari elemen-elemen tersebut akan dipaparkan secara bertahap pada masing-masing sub-bab berikut ini.

4.1	Implementasi Manajemen Multi-School dan Hak Akses
Implementasi manajemen *Multi-School* (Multi-Tenant) dilakukan pada lapisan *backend* dengan memastikan bahwa setiap profil pengguna (*User*) diikat secara dinamis menggunakan model pivot/penghubung agar bisa memiliki hak akses (*Role*) yang berbeda-beda di setiap batas ruang lingkup tenant/sekolah yang berbeda pula. Logika perlindungan hak akses ini dibentengi oleh utilitas *Middleware* khusus yang ditugaskan secara proaktif mencegah dan memutus setiap permintaan akses (*request*) HTTP sebelum masuk ke *Controller*, guna memeriksa apakah identitas pengguna tersebut memang terdaftar dan memiliki wewenang sah untuk memanipulasi sumber daya di sekolah tersebut.

4.1.1	Tampilan Antarmuka
Tampilan yang berkaitan dengan fitur ini meliputi:

- Screenshot Halaman Registrasi & Pembuatan Sekolah (/auth/register)
  Penjelasan: Halaman ini merupakan antarmuka awal bagi Kepala Sekolah untuk mendaftarkan institusi baru ke dalam sistem. Tampilannya dirancang sederhana dengan formulir pengisian identitas dasar sekolah (nama, NPSN) guna memfasilitasi pembuatan ruang kerja (*tenant*) baru secara otomatis.

- Screenshot Halaman Selector Sekolah (/select-school)
  Penjelasan: Antarmuka gerbang transisi ini muncul secara otomatis setelah proses *login* apabila seorang pengguna terdeteksi bernaung di bawah lebih dari satu institusi sekolah. Tampilannya berupa daftar kartu instansi yang memudahkan pengguna memilih ruang kerja sekolah mana yang ingin mereka masuki saat itu.

- Screenshot Halaman Manajemen Anggota Sekolah (/teachers)
  Penjelasan: Berfungsi sebagai pusat kontrol sumber daya manusia bagi Kepala Sekolah. Melalui halaman ini, Kepala Sekolah dapat memantau, menambahkan, atau mencopot hak akses guru yang terdaftar di dalam ruang lingkup instansinya.

- Screenshot Halaman Pengaturan Profil Sekolah (/school/profile, /school/settings)
  Penjelasan: Merupakan panel administrasi terpusat untuk menyesuaikan identitas publik institusi. Antarmukanya menyediakan formulir untuk mengubah logo, memperbarui kontak, hingga melihat status dan sisa waktu tenggat paket langganan sistem SaaS PAUD ini.

4.1.2	Implementasi Kode Utama
Berikut adalah keseluruhan baris kode esensial yang menyusun logika arsitektur tenant dan sistem identitas profil pengguna:

File Code: `app/Models/SchoolMember.php` (Line 40 – 64)
```php
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByRole($query, RoleType $role)
    {
        return $query->where('role_type', $role);
    }

    public function scopeHeadmasters($query)
    {
        return $query->where('role_type', RoleType::HEADMASTER);
    }

    public function scopeTeachers($query)
    {
        return $query->where('role_type', RoleType::TEACHER);
    }

    public function scopeParents($query)
    {
        return $query->where('role_type', RoleType::PARENT);
    }
```
**Penjelasan:** Kode di atas menggunakan fitur *Local Scopes* bawaan dari *framework* Laravel (Eloquent) yang diletakkan pada kelas objek model `SchoolMember`. Fungsi-fungsi ini bertujuan memfilter kueri pencarian anggota sekolah di dalam database secara dinamis dan efisien. Dengan adanya inisialisasi *scope* spesifik seperti `Headmasters`, `Teachers`, dan `Parents`, sistem secara pintar dapat menyortir dan memisahkan jabatan peranan (*Role*) dari para pengguna meskipun secara fisik seluruh pengguna tersebut bernaung berbaur pada satu tabel induk yang sama.

File Code: `app/Models/User.php` (Line 131 – 147)
```php
    public function getSchools()
    {
        return $this->schoolMemberships()
                    ->with('school')
                    ->get()
                    ->pluck('school');
    }

    public function hasSchoolRole(int $schoolId, string $roleName): bool
    {
        return $this->schoolMemberships()
                    ->where('school_id', $schoolId)
                    ->whereHas('role', function($query) use ($roleName) {
                        $query->where('name', $roleName);
                    })
                    ->exists();
    }
```
**Penjelasan:** Fungsi tambahan pembantu pada model `User` ini menjadi jembatan utama untuk menelusuri data antar tabel. Fungsi `getSchools()` secara khusus akan mencari dan menarik daftar utuh yang berisi institusi sekolah mana saja yang bisa diakses oleh sebuah akun pengguna. Sedangkan fungsi `hasSchoolRole()` bertanggung jawab untuk melakukan pengecekan kepastian apakah pada sekolah spesifik tersebut (`$schoolId`), pengguna yang bersangkutan benar-benar memegang jabatan atau posisi hak akses yang dipertanyakan (`$roleName`).

File Code: `app/Http/Middleware/CheckPermission.php` (Line 26 – 34)
```php
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (!$request->user() || !$request->user()->can($permission)) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
```
**Penjelasan:** Blok kode di atas memikul fungsi esensial sebagai pintu gerbang penjaga keamanan sistem. Setiap permintaan akses yang masuk akan ditahan sementara waktu untuk memeriksa ketersediaan identitas autentikasi pada sistem. Apabila sesi identitas tersebut terbukti sah, sistem akan memeriksa kembali apakah peran pengguna tersebut di dalam ruang lingkup sekolah memiliki atribut stempel izin khusus (misalnya izin untuk mengubah data siswa). Apabila tidak memiliki izin, aplikasi melempar respon peringatan penolakan akses.

File Code: `app/Http/Middleware/CheckProPlan.php` (Line 26 – 43)
```php
        // Get school from request or user's active school
        $schoolId = $request->route('school_id') ?? $request->input('school_id');
        
        if (!$schoolId) {
            // Get user's first school membership
            $membership = $user->schoolMemberships()->first();
            $schoolId = $membership?->school_id;
        }

        if (!$schoolId) {
            abort(403, 'No school associated with this account.');
        }

        $school = \App\Models\School::find($schoolId);

        if (!$school || !$school->isPro()) {
            abort(403, 'This feature requires Pro Plan subscription. Please upgrade your plan.');
        }
```
**Penjelasan:** Potongan keamanan *Middleware* kedua ini dirancang semata-mata untuk mengontrol fitur eksklusif berbasis paket langganan (Paket PRO). *Script* ini awalnya mendeteksi *tenant id* yang aktif melalui rute URL. Setelah sekolah berhasil ditemukan, sistem akan memvalidasi fungsi ekstensi `$school->isPro()`. Apabila tenggat waktu paket institusi sudah berstatus kedaluwarsa atau statusnya adalah non-pro, sistem otomatis mencegat tindakan pengguna dan menyuguhkan peringatan blokir akses.

4.2	Implementasi Manajemen Data Siswa dan Orang Tua
Manajemen form registrasi data diri siswa PAUD serta penautan profil otentikasi orang tua diimplementasikan sedemikian rupa dengan memisahkan wilayah data fiktif (administratif) dengan wilayah identitas masuk (autentikasi). Pada sistem SIAKAD PAUD ini, seorang subjek murid (`Student`) tidak memegang kunci identitas masuk (*password*/*email*) pribadi ke dalam sistem, melainkan akses mereka bergantung total mutlak kepada penelusuran kontrol antarmuka pada satu payung dasbor profil sang wali/orang tua (`ParentProfile`). 

4.2.1	Tampilan Antarmuka
Tampilan yang berkaitan dengan fitur ini meliputi:

- Screenshot Halaman Data Induk Siswa (/students)
  Penjelasan: Halaman ini menyajikan tabel direktori seluruh murid yang terdaftar di sekolah bersangkutan. Tampilannya dilengkapi dengan fitur pencarian dan penyaringan data terpadu untuk memudahkan staf atau Kepala Sekolah melacak rekam jejak siswa secara efisien.

- Screenshot Formulir Tambah Siswa (/students/create)
  Penjelasan: Antarmuka formulir ini dirancang untuk merekam biodata anak secara komprehensif. Selain mencatat informasi personal anak, formulir ini secara langsung memuat bidang kolom untuk pemilihan kelas serta fitur pencarian/penautan identitas wali murid secara terintegrasi.

- Screenshot Halaman Manajemen Profil Orang Tua (/parents)
  Penjelasan: Panel ini menampilkan daftar akun sekunder wali murid yang memiliki otoritas untuk masuk ke sistem dasbor khusus orang tua. Kepala sekolah menggunakan halaman ini untuk memantau aktivitas akses keluarga serta memverifikasi perikatan antara profil orang tua dengan profil anak didiknya.

- Screenshot Halaman Profil Siswa atau Detail (/students/:id)
  Penjelasan: Berfungsi sebagai wujud buku induk riwayat siswa dalam lembaran digital. Halaman ini merekap keseluruhan identitas anak, rekam absensi, hingga rekam jejak performa akademiknya secara rapi di satu halaman terpusat.

- Screenshot Dashboard Parent (/children)
  Penjelasan: Merupakan portal khusus yang hak aksesnya sangat dibatasi khusus untuk keluarga. Dasbor ini menampilkan kartu-kartu profil anak yang diasuh oleh pengguna yang bersangkutan. Melalui antarmuka inilah orang tua akan memantau segala perkembangan dan notifikasi mengenai anak mereka secara eksklusif dan privat.

4.2.2	Implementasi Kode Utama
Berikut adalah struktur inti pemrograman relasi manajemen identitas, keamanan modifikasi data media, hingga penelusuran logika absensi siswa:

File Code: `app/Models/Student.php` (Line 42 – 61)
```php
    protected static function boot()
    {
        parent::boot();

        // Clean up old photo when photo_url changes or student is deleted
        static::updating(function (Student $student) {
            if ($student->isDirty('photo_url')) {
                $oldPhoto = $student->getOriginal('photo_url');
                if ($oldPhoto && Storage::disk('public')->exists($oldPhoto)) {
                    Storage::disk('public')->delete($oldPhoto);
                }
            }
        });

        static::deleting(function (Student $student) {
            if ($student->photo_url && Storage::disk('public')->exists($student->photo_url)) {
                Storage::disk('public')->delete($student->photo_url);
            }
        });
    }
```
**Penjelasan:** Blok kode pengaturan sistem (metode `boot`) pada data anak PAUD bertugas secara senyap mengelola memori penyimpanan sistem peladen. Ketika berkas foto diri anak diubah atau seluruh riwayat pendidikan seorang anak dibuang, sistem secara mekanis akan mendeteksi apakah berkas pas foto lamanya masih tersimpan. Tautan dari foto masa lalu itu kemudian dilacak, lantas dihapus secara fisik seketika guna melakukan pembersihan data otomatis sehingga merampingkan sisa kapasitas penyimpanan peladen tanpa harus campur tangan manusia.

File Code: `app/Models/User.php` (Line 52 – 71)
```php
    protected static function boot()
    {
        parent::boot();

        // Clean up old avatar when avatar_url changes or user is deleted
        static::updating(function (User $user) {
            if ($user->isDirty('avatar_url')) {
                $oldAvatar = $user->getOriginal('avatar_url');
                if ($oldAvatar && Storage::disk('public')->exists($oldAvatar)) {
                    Storage::disk('public')->delete($oldAvatar);
                }
            }
        });

        static::deleting(function (User $user) {
            if ($user->avatar_url && Storage::disk('public')->exists($user->avatar_url)) {
                Storage::disk('public')->delete($user->avatar_url);
            }
        });
    }
```
**Penjelasan:** Serupa dengan logika perlindungan berkas pada murid di atas, kode pengaturan pada tabel identitas utama pengguna (`User`) ini menargetkan pembersihan penyimpanan media otomatis pada foto profil. Prosedur pencegahan ini memastikan tidak akan pernah ada sisa berkas rongsokan gambar profil milik wali orang tua maupun profil guru jika sewaktu-waktu akun mereka dihapus dari sistem.

File Code: `app/Models/User.php` (Line 121 – 129)
```php
    public function isTeacher(): bool
    {
        return $this->teacher !== null;
    }

    public function isParent(): bool
    {
        return $this->parentProfile !== null;
    }
```
**Penjelasan:** Pada tabel akun sentral `User`, ekstensi pengecekan dipasang guna memvalidasi secara logis menggunakan nilai benar atau salah, apakah sang identitas tersebut merupakan staf Pengajar atau bertindak sebagai Wali Murid. Kondisi jawaban pasti `true` atau `false` ini sangat meringankan beban kalkulasi program tatkala tampilan web perlu menyuguhkan menu aplikasi spesifik yang hanya boleh dilihat oleh guru saja.

File Code: `app/Models/Student.php` (Line 126 – 146)
```php
    public function getAttendancePercentage(?string $month = null, ?string $year = null): float
    {
        $query = $this->attendance();
        
        if ($month && $year) {
            $query->whereMonth('date', $month)->whereYear('date', $year);
        } elseif ($month) {
            $query->whereMonth('date', $month);
        } elseif ($year) {
            $query->whereYear('date', $year);
        }
        
        $total = $query->count();
        if ($total === 0) {
            return 0;
        }
        
        $present = $query->where('status', 'present')->count();
        
        return round(($present / $total) * 100, 2);
    }
```
**Penjelasan:** Fungsi tambahan analisis kehadiran harian (`getAttendancePercentage()`) dibuat untuk merangkum riwayat absensi. Logika pencariannya dirangkai agar mampu menerima nilai target waktu bebas (seperti parameter bulan dan tahun). Sistem akan mengelompokkan jumlah total seluruh aktivitas kehadiran siswa tersebut di bulan terpilih. Jumlah hari kehadiran murni kemudian dibagi dengan angka pembagi dari keseluruhan beban hari kelas. Proses ini menghasilkan angka desimal berupa persentase pasti riwayat absensi anak, yang nantinya siap ditampilkan di dalam grafik antarmuka dasbor Orang Tua.

4.3	Implementasi Sistem Penilaian dan Skala PAUD
Penyusunan sistem skoring rapot/buku penghubung pada tingkat Pendidikan Anak Usia Dini (PAUD) menerapkan pola standar kurikulum nasional yang tidak merujuk pada skor angka metrik standar (1-100), melainkan mengadopsi indikator pencapaian tingkat perkembangan anak dengan skala nilai akronim khusus (BB, MB, BSH, BSB). Di dalam implementasinya, standardisasi tersebut dikunci secara absolut ke dalam arsitektur bahasa pemrograman tingkat konfigurasi objek konstan (*Enums*).

4.3.1	Tampilan Antarmuka
Tampilan yang berkaitan dengan pemantauan belajar meliputi:

- Screenshot Halaman Input Absensi Harian (/attendance)
  Penjelasan: Tampilan yang ditujukan bagi guru untuk melakukan proses absensi murid secara massal dan cepat. Desainnya berupa tabel baris yang memuat daftar nama anak di suatu kelas beserta serangkaian tombol *toggle* opsi presensi (Hadir, Sakit, Izin, Alfa).

- Screenshot Halaman Input Nilai Asesmen (/assessments)
  Penjelasan: Halaman ini menjadi kanvas kerja para guru dalam memberikan nilai observatif indikator perkembangan anak. Mengingat PAUD tidak menggunakan rentang angka mutlak, antarmukanya berwujud *dropdown* pemilih skala huruf (BB, MB, BSH, BSB) yang disertai kolom kotak teks (*text box*) untuk menyematkan deskripsi catatan naratif singkat.

- Screenshot Halaman Rekapitulasi Rapor (/reports, /reports/:studentId) ---- INI BELOM
  Penjelasan: Halaman ini menyuguhkan kalkulasi kompilasi dari seluruh jejak penilaian kualitatif anak di sepanjang satu semester berjalan. Tampilannya dirancang terstruktur selayaknya draf transkrip buku rapor fisik sebelum akhirnya dikunci, dicetak, dan dikonversi menjadi dokumen PDF.

- Screenshot Halaman Pantauan Akademik Parent (/children/:id)
  Penjelasan: Tampilan ini adalah ekstensi dari dasbor orang tua. Pada layar ini disajikan grafis diagram batang interaktif yang merepresentasikan progres laju pembelajaran keseharian sang anak secara waktu-nyata (*real-time*), lengkap dengan jejak arsip umpan balik evaluasi dari wali kelas.

4.3.2	Implementasi Kode Utama
Keseluruhan landasan fondasi kode untuk menyokong proses pengurutan matriks kurikulum abjad dikonstruksikan sebagai berikut:

File Code: `app/Enums/AssessmentScale.php` (Line 5 – 23)
```php
enum AssessmentScale: string
{
    case BB = 'BB';   // Belum Berkembang
    case MB = 'MB';   // Mulai Berkembang
    case BSH = 'BSH'; // Berkembang Sesuai Harapan
    case BSB = 'BSB'; // Berkembang Sangat Baik

    /**
     * Get scale display name (Indonesian)
     */
    public function label(): string
    {
        return match($this) {
            self::BB => 'Belum Berkembang',
            self::MB => 'Mulai Berkembang',
            self::BSH => 'Berkembang Sesuai Harapan',
            self::BSB => 'Berkembang Sangat Baik',
        };
    }
```
**Penjelasan:** File di atas mendemonstrasikan perancangan fungsional menggunakan fitur struktur data konstan statis pada pemrograman inti. Empat skala abjad penilaian kurikulum PAUD (BB, MB, BSH, dan BSB) dikonfigurasi dan ditanamkan secara langsung ke dalam kode agar menunjang pencegahan masuknya huruf acak yang tidak valid ke pangkalan data. Struktur statis ini juga membungkus fungsi penterjemah pelengkap bernama `label()` yang bertugas mengubah huruf singkatan tersebut menjadi kalimat informatif utuh berbahasa Indonesia untuk kebutuhan bacaan wali murid.

File Code: `app/Enums/AssessmentScale.php` (Line 51 – 75)
```php
    public function numericValue(): int
    {
        return match($this) {
            self::BB => 1,
            self::MB => 2,
            self::BSH => 3,
            self::BSB => 4,
        };
    }

    public function percentageRange(): array
    {
        return match($this) {
            self::BB => ['min' => 0, 'max' => 25],
            self::MB => ['min' => 26, 'max' => 50],
            self::BSH => ['min' => 51, 'max' => 75],
            self::BSB => ['min' => 76, 'max' => 100],
        };
    }
```
**Penjelasan:** Kendala komputasi sering muncul apabila sistem di masa depan diwajibkan untuk merekap perbandingan, membuat urutan peringkat, atau menggambar grafik rata-rata progres belajar siswa, mengingat nilai acuan murninya hanya dicatat berbasis huruf alfabet semata. Demi menanggulanginya, pengubah internal seperti fungsi `numericValue()` diciptakan agar huruf abjad tadi dapat dikonversi sementara menjadi bobot skor angka 1 hingga 4. Tak hanya itu, sebuah fungsi pembantu `percentageRange()` juga disediakan dalam format rentang angka minimum dan maksimum (0 hingga 100), memastikan proses logika kalkulator laporan dapat beroperasi lancar tanpa perlu merusak susunan abjad aslinya.

File Code: `app/Models/Assessment.php` (Line 50 – 68)
```php
    public function scopeBySemester($query, Semester $semester)
    {
        return $query->where('semester', $semester);
    }

    public function scopeByAspect($query, string $aspect)
    {
        return $query->where('aspect', $aspect);
    }

    public function scopeByScale($query, AssessmentScale $scale)
    {
        return $query->where('scale', $scale);
    }

    public function scopePassing($query)
    {
        return $query->whereIn('scale', [AssessmentScale::BSH, AssessmentScale::BSB]);
    }
```
**Penjelasan:** Blok tambahan pada riwayat tabel penilaian ini dirancang khusus untuk memfasilitasi algoritma perhitungan rekap jejak pendidikan anak secara lebih ringkas. Penulisan filter pencarian yang disesuaikan ini mempermudah sistem agar secara dinamis menyusun daftar ranking rapot. Terdapat beberapa corong penyaringan yang spesifik, seperti pencarian berdasarkan kriteria Semester, pencarian khusus Indikator Kognitif tertentu, maupun fungsi kelulusan (`Passing`). Khusus untuk penyaringan kelulusan, aplikasi secara mutlak mematok bahwa batas bawah tingkat standar kompetensi kelayakan seorang anak PAUD adalah mereka yang berhasil mencapai rentang performa BSH (Berkembang Sesuai Harapan).

File Code: `app/Models/Assessment.php` (Line 70 – 79)
```php
    // Helper Methods
    public function isPassing(): bool
    {
        return $this->scale->isPassing();
    }

    public function getNumericScore(): int
    {
        return $this->scale->numericValue();
    }
```
**Penjelasan:** Fungsi pembantu yang terakhir ini bertugas sebagai jalur pintas jalan masuk program. Fungsi ini mengarahkan pelacakan nilainya dari baris data nilai rapor yang ditarik, untuk dipertemukan dengan deklarasi skala huruf statis yang sudah dibahas sebelumnya. Kehadiran fungsi pintas semacam `getNumericScore()` dan pemeriksaan kepastian status kelulusan `isPassing()` ini bertugas meringankan kerumitan perhitungan program tatkala peramban web pengguna sedang mengolah tampilan tabel grafik yang butuh diproses saat itu juga.

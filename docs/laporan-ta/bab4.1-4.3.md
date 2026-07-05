BAB 4	IMPLEMENTASI
Bab ini menguraikan tahapan krusial dalam pengembangan perangkat lunak, yaitu fase implementasi sistem. Pada tahap ini, seluruh rancangan teoretis dan arsitektur logis yang telah didefinisikan secara komprehensif pada bab-bab sebelumnya mulai direalisasikan ke dalam bentuk produk nyata. Implementasi merupakan proses konversi dari desain pemodelan—seperti *Entity Relationship Diagram* (ERD), skema struktur basis data, hingga alur pemrosesan *Activity Diagram*—menjadi baris-baris kode program (*source code*) yang fungsional. Tujuan utama dari bab ini adalah untuk memberikan pembuktian bahwa spesifikasi kebutuhan sistem dapat dieksekusi secara teknis dan beroperasi dengan baik sesuai dengan batasan ekspektasi yang telah ditetapkan.

Lebih lanjut, pembahasan pada bab ini akan merincikan perwujudan sistem dari tiga sudut pandang utama per subsistem dan modul fungsionalnya, yakni identitas tabel inti terkait pada database, penjabaran logika pemrograman inti (*back-end*), serta pengembangan tampilan antarmuka (*front-end*). Dari sisi struktur *back-end*, pembahasan akan menitikberatkan pada penjabaran potongan baris kode (*code snippets*) untuk memperlihatkan bagaimana arsitektur keamanan *multi-tenant*, pengelolaan data relasional temporal, serta algoritma komputasi diimplementasikan di balik layar. Sementara itu, dari sisi antarmuka, penjabaran akan berfokus pada hasil translasi *wireframe* menjadi tata letak visual yang interaktif guna menjamin pengalaman pengguna (*User Experience*) yang optimal. Seluruh integrasi dari elemen-elemen tersebut akan dipaparkan secara bertahap pada masing-masing sub-bab berikut ini.

4.1	Implementasi Manajemen Multi-School dan Hak Akses
Implementasi manajemen *Multi-School* (Multi-Tenant) dilakukan pada lapisan *backend* dengan memastikan bahwa setiap profil pengguna (*User*) diikat secara dinamis menggunakan model pivot/penghubung agar bisa memiliki hak akses (*Role*) yang berbeda-beda di setiap batas ruang lingkup tenant/sekolah yang berbeda pula. Logika perlindungan hak akses ini dibentengi oleh utilitas *Middleware* khusus yang ditugaskan secara proaktif mencegah dan memutus setiap permintaan akses (*request*) HTTP sebelum masuk ke *Controller*, guna memeriksa apakah identitas pengguna tersebut memang terdaftar dan memiliki wewenang sah untuk memanipulasi sumber daya di sekolah tersebut.

4.1.1	Tabel Inti Terkait pada Database
Fitur manajemen *multi-school* dan pembagian hak akses ini bertumpu pada relasi tiga tabel utama di dalam basis data:
1. `schools` : Tabel induk yang menyimpan identitas unik dari setiap tenant sekolah mitra (NPSN, nama, alamat, status paket berlangganan Pro/Free, dan masa berlaku).
2. `users` : Tabel akun sentral yang menampung kredensial autentikasi global seluruh pengguna platform (email, *password hash*, no. telepon, dan status aktif).
3. `school_members` : Tabel pivot yang menjembatani relasi *Many-to-Many* antara `users` dan `schools`. Tabel ini menyimpan atribut spesifik `role_type` yang mengklasifikasikan jabatan pengguna di suatu tenant apakah sebagai *Headmaster* (Kepala Sekolah), *Operator* (Staff Admin Sekolah), *Teacher* (Guru), atau *Parent* (Orang Tua).

4.1.2	Implementasi Kode Utama
Berikut adalah keseluruhan baris kode esensial yang menyusun logika arsitektur tenant dan sistem identitas profil pengguna, termasuk integrasi peran Operator Sekolah:

File Code: `app/Models/SchoolMember.php` (Line 40 – 70)
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

    public function scopeOperators($query)
    {
        return $query->where('role_type', RoleType::OPERATOR);
    }

    public function scopeTeachers($query)
    {
        return $query->where('role_type', RoleType::TEACHER);
    }

    public function scopeParents($query)
    {
        return $query->where('role_type', RoleType::PARENT);
    }

    public function isManager(): bool
    {
        return in_array($this->role_type, [RoleType::HEADMASTER, RoleType::OPERATOR]);
    }
```
**Penjelasan:** Kode di atas menggunakan fitur *Local Scopes* serta pembantu otorisasi pada kelas model `SchoolMember`. Fungsi-fungsi ini bertujuan memfilter kueri pencarian anggota sekolah di dalam database secara dinamis dan efisien. Dengan adanya inisialisasi *scope* spesifik seperti `Headmasters`, `Operators`, `Teachers`, dan `Parents`, sistem secara pintar dapat menyortir dan memisahkan jabatan peranan (*Role*) pengguna. Tambahan metode `isManager()` dirancang sebagai jalur pembukti otorisasi ganda untuk endpoint API yang berhak diakses oleh Kepala Sekolah maupun Operator Sekolah dalam pengurusan administrasi instansi.

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

4.1.3	Tampilan Antarmuka
Tampilan yang berkaitan dengan fitur ini meliputi:

- Screenshot Halaman Registrasi & Pembuatan Sekolah (/auth/register)
  Penjelasan: Halaman ini merupakan antarmuka awal bagi Kepala Sekolah untuk mendaftarkan institusi baru ke dalam sistem. Tampilannya dirancang sederhana dengan formulir pengisian identitas dasar sekolah (nama, NPSN) guna memfasilitasi pembuatan ruang kerja (*tenant*) baru secara otomatis.

- Screenshot Halaman Selector Sekolah (/select-school)
  Penjelasan: Antarmuka gerbang transisi ini muncul secara otomatis setelah proses *login* apabila seorang pengguna terdeteksi bernaung di bawah lebih dari satu institusi sekolah. Tampilannya berupa daftar kartu instansi yang memudahkan pengguna memilih ruang kerja sekolah mana yang ingin mereka masuki saat itu.

- Screenshot Halaman Manajemen Anggota Sekolah (/teachers)
  Penjelasan: Berfungsi sebagai pusat kontrol sumber daya manusia bagi Kepala Sekolah dan Operator. Melalui halaman ini, Kepala Sekolah dan Operator dapat memantau, menambahkan, atau mencopot hak akses guru dan staf yang terdaftar di dalam ruang lingkup instansinya.

- Screenshot Halaman Pengaturan Profil Sekolah (/school/profile, /school/settings)
  Penjelasan: Merupakan panel administrasi terpusat untuk menyesuaikan identitas publik institusi. Antarmukanya menyediakan formulir untuk mengubah logo, memperbarui kontak, hingga melihat status dan sisa waktu tenggat paket langganan sistem SaaS PAUD ini. Akses perubahan konfigurasi profil dan berlangganan ini dikhususkan bagi Kepala Sekolah.

4.2	Implementasi Manajemen Data Siswa dan Orang Tua
Manajemen form registrasi data diri siswa PAUD serta penautan profil otentikasi orang tua diimplementasikan sedemikian rupa dengan memisahkan wilayah data fiktif (administratif) dengan wilayah identitas masuk (autentikasi). Pada sistem SIAKAD PAUD ini, seorang subjek murid (`Student`) tidak memegang kunci identitas masuk (*password*/*email*) pribadi ke dalam sistem, melainkan akses mereka bergantung total mutlak kepada penelusuran kontrol antarmuka pada satu payung dasbor profil sang wali/orang tua (`ParentProfile`). Seluruh pengolahan data administrasi ini kini dikendalikan secara operasional oleh peranan **Operator Sekolah**.

4.2.1	Tabel Inti Terkait pada Database
Pengelolaan biodata akademik anak dan wali murid dimodelkan dalam empat entitas database yang berkaitan erat:
1. `students` : Tabel induk biodata murid (NISN, NIK, nama lengkap, tanggal lahir, jenis kelamin, foto profil, status aktif, serta *foreign key* ke kelas dan profil orang tua).
2. `parent_profiles` : Tabel profil sekunder wali murid yang menampung identitas lengkap orang tua (nama ayah/ibu/wali, pekerjaan, alamat rumah, serta *foreign key* ke tabel akun login `users`).
3. `classes` / `class_rooms` : Tabel pengelompokan rombongan belajar (rombel) yang menampung siswa dalam tahun ajaran tertentu beserta guru wali kelas pengampunya.
4. `attendances` : Tabel transaksional log absensi harian yang mencatat kehadiran setiap siswa per tanggal (status: hadir, sakit, izin, atau tanpa keterangan).

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
**Penjelasan:** Pada tabel akun sentral `User`, ekstensi pengecekan dipasang guna memvalidasi secara logis menggunakan nilai benar atau salah, apakah sang identitas tersebut merupakan staf Pengajar atau bertindak sebagai Wali Murid. Kondisi jawaban pasti `true` atau `false` ini sangat meringankan beban kalkulasi program tatkala tampilan web perlu menyuguhkan menu aplikasi spesifik yang hanya boleh dilihat oleh guru atau orang tua saja.

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

4.2.3	Tampilan Antarmuka
Tampilan yang berkaitan dengan fitur ini meliputi:

- Screenshot Halaman Data Induk Siswa (/students)
  Penjelasan: Halaman ini menyajikan tabel direktori seluruh murid yang terdaftar di sekolah bersangkutan. Tampilannya dikelola oleh Operator Sekolah dengan fitur pencarian dan penyaringan data terpadu untuk memudahkan pencatatan rekam jejak siswa secara efisien. Guru dan Kepala Sekolah memiliki hak akses *read-only* untuk memantau daftar murid.

- Screenshot Formulir Tambah Siswa (/students/create)
  Penjelasan: Antarmuka formulir ini dirancang bagi Operator Sekolah untuk merekam biodata anak secara komprehensif. Selain mencatat informasi personal anak, formulir ini secara langsung memuat bidang kolom untuk pemilihan kelas rombel serta fitur pencarian/penautan identitas wali murid secara terintegrasi.

- Screenshot Halaman Manajemen Profil Orang Tua (/parents)
  Penjelasan: Panel yang dikelola oleh Operator Sekolah ini menampilkan daftar akun sekunder wali murid yang memiliki otoritas untuk masuk ke sistem dasbor khusus orang tua. Operator menggunakan halaman ini untuk memverifikasi perikatan antara profil orang tua dengan biodata anak didiknya.

- Screenshot Halaman Profil Siswa atau Detail (/students/:id)
  Penjelasan: Berfungsi sebagai wujud buku induk riwayat siswa dalam lembaran digital. Halaman ini merekap keseluruhan identitas anak, rekam absensi, hingga rekam jejak performa akademiknya secara rapi di satu halaman terpusat.

- Screenshot Dashboard Parent (/children)
  Penjelasan: Merupakan portal khusus yang hak aksesnya sangat dibatasi khusus untuk keluarga. Dasbor ini menampilkan kartu-kartu profil anak yang diasuh oleh pengguna yang bersangkutan. Melalui antarmuka inilah orang tua akan memantau segala perkembangan dan notifikasi mengenai anak mereka secara eksklusif dan privat.

4.3	Implementasi Sistem Penilaian dan Skala PAUD
Penyusunan sistem skoring rapot/buku penghubung pada tingkat Pendidikan Anak Usia Dini (PAUD) menerapkan pola standar kurikulum nasional yang tidak merujuk pada skor angka metrik standar (1-100), melainkan mengadopsi indikator pencapaian tingkat perkembangan anak dengan skala nilai akronim khusus (BB, MB, BSH, BSB). Di dalam implementasi terkininya, sistem ini diperkuat oleh tiga mekanisme arsitektur keandalan data guna mencegah anomali atau kerusakan rekam jejak lampau akibat perubahan kurikulum oleh Operator Sekolah:

1. **Temporal Assessment Integrity (Integritas Penilaian Berbasis Waktu)**
   Merupakan mekanisme perlindungan konsistensi data berdasarkan garis waktu pembuatan (*timestamp*). Ketika Operator Sekolah menambahkan indikator penilaian baru di tengah atau akhir semester berjalan (misalnya pada bulan November), sistem memastikan bahwa indikator baru tersebut tidak akan berlaku surut (*non-retroactive*). Artinya, indikator baru itu tidak akan muncul dan menuntut pengisian nilai pada form absensi/penilaian di bulan-bulan yang telah berlalu (seperti Juli atau Agustus). Hal ini melindungi guru agar tidak terbebani kewajiban mengisi nilai masa lalu untuk indikator yang saat itu memang belum dilahirkan.

2. **Soft Deletes & Non-Aktif Logis (`is_active`)**
   Merupakan mekanisme perlindungan terhadap penghapusan kurikulum lama. Ketika sebuah indikator perkembangan dihapus atau dinonaktifkan oleh Operator Sekolah, sistem tidak akan memusnahkan baris data tersebut secara fisik dari basis data (*hard delete*). Statusnya hanya diubah menjadi tidak aktif (`is_active = false`) atau diberi stempel waktu penghapusan (`deleted_at`). Mekanisme ini menjamin seluruh rekam jejak nilai siswa pada semester atau bulan-bulan lampau tetap sah dan utuh sebagai bukti sejarah pendidikan, sekaligus mencegah kehilangan data berantai (*cascading data loss*).

3. **Smart Omission (Penyaringan Cerdas pada Rapor)**
   Merupakan algoritma penyaringan otomatis pada saat pencetakan dokumen resmi rapor akhir semester (PDF) maupun pada penghitungan validasi kelengkapan rapor. Sistem secara cerdas akan mengabaikan (*omit*) indikator atau aspek kurikulum yang tidak memiliki catatan nilai sama sekali (nilai = 0 atau kosong). Alhasil, dokumen cetak rapor yang diterima orang tua tetap terlihat rapi, padat, dan tidak dipenuhi oleh baris-baris indikator kosong yang tidak diamati selama proses belajar mengajar. Selain itu, aturan ini mencegah rapor terkunci (*deadlock*) dari pembuatan kesimpulan akhir naratif hanya gara-gara terdapat indikator baru yang ditambahkan di penghujung semester.

4.3.1	Tabel Inti Terkait pada Database
Arsitektur kurikulum dan penilaian perkembangan anak usia dini dibangun atas lima tabel yang berelasi:
1. `development_programs` : Tabel master yang menyimpan aspek atau program pengembangan PAUD (contoh: Nilai Agama & Moral, Fisik-Motorik, Kognitif, Bahasa, Sosial-Emosional), dilengkapi kolom `is_active` dan *timestamps*.
2. `development_indicators` : Tabel master rincian kompetensi pencapaian yang terikat pada `development_programs`. Tabel ini memegang peranan dalam validasi temporal lewat penanda `created_at` dan `is_active`.
3. `assessments` : Tabel transaksional rekapitulasi penilaian bulanan yang mencatat evaluasi observasi guru terhadap siswa per indikator, dengan tipe data *enum scale* (BB, MB, BSH, BSB).
4. `student_reports` : Tabel header/induk rapor naratif akhir semester yang menyimpan catatan pengantar, kesimpulan, dan rekomendasi wali kelas.
5. `student_report_details` : Tabel rincian narasi perkembangan siswa per aspek program yang dicetak ke dokumen resmi rapor PDF.

4.3.2	Implementasi Kode Utama
Keseluruhan landasan fondasi kode untuk menyokong proses pengurutan matriks kurikulum abjad serta integritas kurikulum dikonstruksikan sebagai berikut:

File Code: `app/Models/DevelopmentIndicator.php` (Line 11 – 28)
```php
class DevelopmentIndicator extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'development_program_id',
        'code',
        'name',
        'description',
        'age_group',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
```
**Penjelasan:** Pada model master indikator di atas, diterapkan pemanfaatan sifat `SoftDeletes` dan kolom status `is_active`. Saat Operator Sekolah menghapus atau menonaktifkan indikator kurikulum yang sudah tidak berlaku, sistem tidak akan memusnahkan baris data tersebut secara permanen dari *database*. Hal ini melindungi rekam jejak penilaian siswa di semester atau bulan-bulan lampau dari ancaman *cascading delete*, sehingga keutuhan sejarah pendidikan anak tetap sah dan dapat diamati sewaktu-waktu.

File Code: `app/Enums/AssessmentScale.php` (Line 5 – 28)
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

4.3.3	Tampilan Antarmuka
Tampilan yang berkaitan dengan pemantauan belajar meliputi:

- Screenshot Halaman Input Absensi Harian (/attendance)
  Penjelasan: Tampilan yang ditujukan bagi guru untuk melakukan proses absensi murid secara massal dan cepat. Desainnya berupa tabel baris yang memuat daftar nama anak di suatu kelas beserta serangkaian tombol *toggle* opsi presensi (Hadir, Sakit, Izin, Alfa).

- Screenshot Halaman Pengaturan Master Penilaian (/settings/assessments)
  Penjelasan: Antarmuka khusus yang dikelola oleh Operator Sekolah untuk merancang katalog Program Perkembangan dan Indikator Kurikulum PAUD. Dilengkapi kontrol sakelar aktif/non-aktif (`is_active`) untuk mengakomodasi pembaruan kurikulum tanpa merusak rekam jejak nilai lama.

- Screenshot Halaman Input Nilai Asesmen (/assessments)
  Penjelasan: Halaman ini menjadi kanvas kerja para guru dalam memberikan nilai observatif indikator perkembangan anak per bulan. Mengingat PAUD tidak menggunakan rentang angka mutlak, antarmukanya berwujud *dropdown* pemilih skala huruf (BB, MB, BSH, BSB) yang disertai kolom kotak teks (*text box*) untuk menyematkan deskripsi catatan naratif singkat. Form ini menerapkan filter temporal yang hanya menyuguhkan indikator yang sudah valid eksis pada bulan penilaian tersebut.

- Screenshot Halaman Rekapitulasi & Pembuatan Rapor (/reports, /reports/:studentId)
  Penjelasan: Halaman ini menyuguhkan kalkulasi kompilasi dari seluruh jejak penilaian kualitatif anak di sepanjang satu semester berjalan. Tampilannya dirancang terstruktur bagi Wali Kelas untuk menyusun catatan pengantar dan rekomendasi sebelum akhirnya dokumen PDF dicetak. Logika sistem menerapkan *Smart Omission*, di mana indikator yang tidak memiliki nilai (0) tidak akan dipaksakan muncul pada lembar cetak akhir rapot agar tetap ringkas dan rapi.

- Screenshot Halaman Pantauan Akademik Parent (/children/:id)
  Penjelasan: Tampilan ini adalah ekstensi dari dasbor orang tua. Pada layar ini disajikan grafis diagram batang interaktif yang merepresentasikan progres laju pembelajaran keseharian sang anak secara waktu-nyata (*real-time*), lengkap dengan jejak arsip umpan balik evaluasi dari wali kelas serta fasilitas unduh rapor akhir semester.

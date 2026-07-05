4.4	Implementasi Manajemen Keuangan: SPP dan Tabungan
Implementasi modul manajemen keuangan sekolah dibangun berlandaskan prinsip sentralisasi log transaksi dan berfungsi murni sebagai buku besar pencatatan administratif (*Ledger/Log*). Semua bentuk pencatatan arus masuk dan keluar dana, baik itu berupa pembayaran Sumbangan Pembinaan Pendidikan (SPP) bulanan maupun penyetoran/penarikan tabungan siswa, diintegrasikan ke dalam satu tabel tunggal. Arsitektur ini memudahkan sistem pemrograman inti untuk merangkum arus kas harian tanpa perlu menelusuri banyak tabel yang berbeda. Sesuai batasan sistem, pembayaran riil dilakukan secara konvensional di luar sistem, kemudian disahkan statusnya ke dalam sistem oleh **Operator Sekolah** atau **Guru Wali Kelas** yang berwenang.

4.4.1	Tabel Inti Terkait pada Database
Modul keuangan sekolah disokong oleh relasi antar entitas database berikut:
1. `finances` : Tabel transaksional utama yang merekam setiap aktivitas aliran dana, mencakup atribut `type` (enum: SPP atau Tabungan), `transaction_type` (deposit atau withdrawal), `amount`, `month`, `is_paid`, `payment_method` (cash atau transfer), serta *foreign key* ke siswa (`student_id`) dan petugas penginput (`recorded_by`).
2. `students` : Tabel referensi siswa yang menjadi subjek pemilik tagihan atau rekening tabungan sekolah.
3. `classes` : Tabel referensi kelas rombel untuk memfasilitasi filter tagihan massal (*batch billing*) oleh Operator atau Guru.

4.4.2	Implementasi Kode Utama
Berikut adalah struktur dasar logika pelacakan rekam keuangan dan pemformatan data tampilannya:

File Code: `app/Models/Finance.php` (Line 55 – 84)
```php
    public function scopeByType($query, FinanceType $type)
    {
        return $query->where('type', $type);
    }

    public function scopeSpp($query)
    {
        return $query->where('type', FinanceType::SPP);
    }

    public function scopeTabungan($query)
    {
        return $query->where('type', FinanceType::TABUNGAN);
    }

    public function scopeByMonth($query, string $month)
    {
        return $query->where('month', $month);
    }

    public function scopePaid($query)
    {
        return $query->where('is_paid', true);
    }

    public function scopeUnpaid($query)
    {
        return $query->where('is_paid', false);
    }
```
**Penjelasan:** Rangkaian fungsi penyaringan di atas ditulis di dalam tabel model transaksi `Finance`. Mengingat semua jenis transaksi bertumpuk di satu tabel, fungsi penyaringan ini menjadi sangat krusial. Sistem dapat dengan mudah memanggil kueri pencarian dinamis untuk memilah mana baris yang merupakan data pemasukan SPP murni, data Tabungan murni, data tagihan pada bulan tertentu (`ByMonth`), hingga rekap mana saja tagihan yang sudah lunas (`Paid`) dan mana yang masih tertunggak (`Unpaid`).

File Code: `app/Models/Finance.php` (Line 86 – 105)
```php
    public function isSpp(): bool
    {
        return $this->type === FinanceType::SPP;
    }

    public function isTabungan(): bool
    {
        return $this->type === FinanceType::TABUNGAN;
    }

    public function isPaid(): bool
    {
        return $this->is_paid;
    }

    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }
```
**Penjelasan:** Blok fungsi pelengkap ini diciptakan guna mempermudah pemrograman di sisi antarmuka halaman pengguna. Tiga fungsi pertama bertugas menguji secara matematis apakah baris transaksi yang ditarik berjenis SPP, Tabungan, atau sudah dilunasi. Sementara itu, fungsi pembantu `getFormattedAmountAttribute()` secara proaktif memformat angka riil mata uang dari tabel basis data (contoh: 500000) agar seketika berubah menjadi teks mata uang Rupiah standar yang rapi (contoh: Rp 500.000) secara otomatis sebelum diteruskan ke halaman web.

4.4.3	Tampilan Antarmuka
Tampilan yang berkaitan dengan fitur ini meliputi:

- Screenshot Halaman Ringkasan Keuangan/Overview (/finances)
  Penjelasan: Merupakan layar utama pusat kendali uang masuk institusi. Antarmuka dasbor ini menyuguhkan ruang pandang luas berupa kumpulan infografis, statistik akumulasi pendapatan SPP bulanan, serta total saldo agregat tabungan murid. Kehadiran antarmuka ringkas ini ditujukan agar Kepala Sekolah dan Operator dapat meninjau kesehatan finansial sekolah secara menyeluruh dalam satu lirikan mata.

- Screenshot Halaman Manajemen Pembayaran SPP (/finances/spp)
  Penjelasan: Halaman khusus yang dikelola oleh Operator dan Guru untuk mencatat log aktivitas pelunasan iuran wajib bulanan sekolah. Tampilannya berwujud tabel transaksi interaktif yang memisahkan baris nama tagihan siswa yang masih tertunggak (*Unpaid*) dan mana yang sudah dikonfirmasi lunas (*Paid*). Dilengkapi fasilitas pembuatan tagihan massal (*batch*) per rombel dan pencetakan bukti kuitansi.

- Screenshot Halaman Manajemen Saldo Tabungan (/finances/savings)
  Penjelasan: Antarmuka yang difungsikan layaknya pembukuan buku tabungan bank mini di dalam ruang lingkup sekolah. Operator atau Guru dapat memasukkan histori uang masuk (setoran debit) maupun uang keluar (penarikan kredit) anak secara terperinci dengan validasi saldo real-time agar penarikan tidak melebihi sisa tabungan.

- Screenshot Dasbor Pantauan Keuangan Orang Tua (di dalam /children/:id)
  Penjelasan: Bagian dari aplikasi sisi wali murid yang dirancang transparan. Halaman dasbor ini memperlihatkan indikator saldo sisa tabungan sang anak terkini secara gamblang, serta menyuguhkan notifikasi visual apabila ada tunggakan iuran SPP yang belum dibayar di bulan berjalan.

4.5	Implementasi Marketplace dan Manajemen Transaksi
Untuk mendukung lini bisnis edukasi institusi, modul *Marketplace* diimplementasikan untuk menangani proses komersial B2C (Business to Consumer). Penerapannya mencakup kalkulasi penyusunan tagihan keranjang belanja, penerapan potongan harga dari kode promo (*Voucher*), hingga manajemen penerbitan nomor faktur pemesanan secara unik. Berhubung barang yang diperjualbelikan bervariasi jenisnya (Kursus Mandiri, Webinar Langsung, atau Produk Digital), struktur pemrogramannya menggunakan arsitektur relasi yang sangat dinamis dan fleksibel (*Polymorphic Relations*) sehingga tidak terkunci pada satu tabel produk tunggal saja.

4.5.1	Tabel Inti Terkait pada Database
Proses perniagaan digital B2C melibatkan entitas-entitas transaksional berikut:
1. `orders` : Tabel utama transaksi pembelian yang mencatat nomor faktur unik (`order_number`), total belanja, potongan diskon, total akhir, status pembayaran Midtrans, serta *foreign key* pembeli (`user_id`).
2. `order_items` : Tabel rincian barang yang dibeli di dalam suatu pesanan. Menggunakan skema relasi polimorfik (`item_type` dan `item_id`) untuk menautkan baris pesanan ke tabel `courses`, `webinars`, atau `products`.
3. `carts` & `cart_items` : Tabel penampungan sementara keranjang belanja aktif milik pengguna sebelum masuk ke tahap *checkout*.
4. `promo_codes` : Tabel katalog kupon diskon (potongan persentase atau nominal tetap) yang dapat disuntikkan pada proses validasi pembayaran.
5. `products`, `webinars`, `courses` : Tabel-tabel katalog produk komersial yang menjadi target item perdagangan.

4.5.2	Implementasi Kode Utama
Landasan perumusan pesanan dan keranjang digital disusun menggunakan baris instruksi berikut:

File Code: `app/Models/Order.php` (Line 82 – 89)
```php
    public static function generateOrderNumber(): string
    {
        $prefix = 'ORD';
        $date = now()->format('Ymd');
        $random = strtoupper(substr(md5(uniqid()), 0, 6));
        
        return "{$prefix}-{$date}-{$random}";
    }
```
**Penjelasan:** Pada baris konstruksi model transaksi utama (`Order`), aplikasi mewajibkan pembuatan Nomor Faktur (Invoice) acak yang unik sebagai parameter referensi. Fungsi pembuat nomor ini mengombinasikan komponen kata sandi awal `ORD`, waktu rill penciptaan keranjang (`Ymd`), dan memotong nilai algoritma acak yang dihasilkan oleh *server* menjadi enam digit terakhir. Nomor pelacakan inilah yang kelak akan diteruskan ke gerbang penyedia pembayaran (*Payment Gateway*) dan resi pembeli.

File Code: `app/Models/Order.php` (Line 128 – 142)
```php
    public function calculateTotal(): void
    {
        $this->total_amount = $this->items()->sum('subtotal');
        
        // Apply discount if promo code exists
        $discount = 0;
        if ($this->promoCode && $this->promoCode->isValid()) {
            $discount = $this->promoCode->calculateDiscount($this->total_amount);
        }
        
        $this->discount_amount = $discount;
        $this->final_amount = $this->total_amount - $discount;
        
        $this->save();
    }
```
**Penjelasan:** Sebelum pengguna diarahkan ke layar pembayaran, sistem wajib memanggil fungsi kalkulasi total otomatis di atas. Proses ini dimulai dengan menjumlahkan beban harga tiap rincian barang di dalam keranjang, kemudian mencari apakah pengguna melampirkan kupon promosi yang valid. Apabila ditemukan, sistem mendelegasikan tugas perhitungan nominal potongannya, lalu menyimpannya sebagai dua nilai terpisah: total kotor dan total tagihan bersih (*final_amount*) yang siap dibayar.

File Code: `app/Models/OrderItem.php` (Line 38 – 41)
```php
    public function item(): MorphTo
    {
        return $this->morphTo('item', 'item_type', 'item_id');
    }
```
**Penjelasan:** Deklarasi di atas adalah inti dari kecanggihan relasi tabel fleksibel (*Polymorphic Relations*) dalam merajut rincian keranjang belanja. Melalui fungsi bawaan `morphTo()`, satu tabel rincian pemesanan (*Order Item*) mampu menyimpan tautan secara bebas ke banyak tabel produk lainnya, terlepas dari apakah barang yang di-klik pengguna berasal dari tabel produk fisik/digital, tabel acara webinar, maupun tabel kursus edukasi daring.

4.5.3	Tampilan Antarmuka
Tampilan yang berkaitan dengan fitur ini meliputi:

- Screenshot Katalog Produk, Webinar, dan Kursus (/products, /webinars, /courses)
  Penjelasan: Bertindak layaknya etalase muka (*storefront*) toko digital, halaman ini menghidangkan hamparan kartu-kartu visual berjejer (*grid cards*) yang menjajakan beraneka penawaran edukasi *B2C* kepada publik. Tampilannya dirancang interaktif dan dilengkapi fitur emblem potongan diskon berupa angka coret (harga asli disilang berdampingan dengan harga promo).

- Screenshot Detail Item Katalog (/[category]/[slug])
  Penjelasan: Laman pendaratan (*landing page*) per rincian produk yang menjabarkan deskripsi spesifikasi barang dagangan secara memanjang. Jika produk tersebut berupa kursus edukasi berseri, maka antarmukanya akan menyorot rincian detail daftar isi kurikulum silabus secara transparan dan memaparkan biografi pengajarnya.

- Screenshot Halaman Keranjang Belanja (/cart)
  Penjelasan: Tempat persinggahan area penampungan barang digital sesaat yang hendak dipesan. Desainnya amat interaktif tanpa harus memuat ulang laman (*AJAX-based*), memungkinkan pengunjung memanipulasi rentetan jumlah kuantitas produk atau menghapusnya sebelum melangkah ke tahapan kasir.

- Screenshot Halaman Checkout & Pembayaran (/checkout)
  Penjelasan: Laman pengunci kalkulasi keranjang sekaligus penagihan akhir sebelum pembayaran disahkan. Pada antarmuka pelunasan inilah pengunjung disajikan kolom validasi dinamis untuk menyuntikkan kode promo. Begitu pengguna menekan tombol sepakat, *script* di balik layar seketika memicu kemunculan gerbang pembayaran pihak ketiga (*Payment Gateway Midtrans Snap*) dalam bentuk *pop-up iframe*.

- Screenshot Halaman Kelola Pesanan dan Produk yang Dibeli (/account/orders, /account/products)
  Penjelasan: Ruang dasbor akun privat eksklusif bagi para konsumen (B2C) setelah mereka sukses merampungkan transaksi checkout. Halaman manampilkan informasi Produk / Webinar / Kursus yang sudah dibeli atau didaftar oleh pengguna.

4.6	Implementasi Learning Management System dan Sertifikasi
Pembangunan ruang edukasi terpadu atau *Learning Management System* (LMS) memerlukan susunan fungsi pemantauan yang berlapis-lapis. Modul implementasi di sini tidak hanya memetakan silabus secara hierarkis (dari materi video hingga ujian interaktif), tetapi juga difokuskan pada mesin pelacakan kemajuan (*Progress Tracker*). Fitur andalan yang ditanamkan adalah kalkulasi algoritma ketat yang mewajibkan seluruh materi video telah disaksikan dan ujian telah dilalui dengan standar kelulusan, barulah pada akhirnya sistem dapat menerbitkan sertifikat pencapaian otomatis secara utuh.

4.6.1	Tabel Inti Terkait pada Database
Aktivitas pembelajaran daring asinkron dicatat dan diolah dalam tabel-tabel berikut:
1. `course_enrollments` : Tabel kepemilikan lisensi kursus oleh pengguna yang merekam persentase kemajuan (`progress_percentage`), status kelulusan, tanggal tamat (`completed_at`), serta tautan berkas sertifikat (`certificate_url`).
2. `lesson_progress` : Tabel log rekam jejak yang mencatat status penyelesaian per satu materi pelajaran video/PDF oleh seorang pengguna (`is_completed`).
3. `quiz_attempts` : Tabel log riwayat percobaan ujian kuis yang mencatat skor persentase kelulusan (`score`) dan status lulus/gagal (`is_passed`).
4. `quizzes`, `quiz_questions`, `quiz_answers` : Tabel-tabel relasional yang menyusun instrumen evaluasi pilihan ganda pada setiap modul kursus.
5. `courses`, `modules`, `lessons` : Tabel-tabel hierarki silabus kurikulum pembelajaran.

4.6.2	Implementasi Kode Utama
Mesin algoritma di belakang layar pemantauan pembelajaran beroperasi dengan urutan validasi sebagai berikut:

File Code: `app/Models/CourseEnrollment.php` (Line 66 – 80)
```php
    public function updateProgress(): void
    {
        $totalLessons = $this->course->total_lessons ?? 0;
        
        if ($totalLessons === 0) {
            $this->progress_percentage = 0;
            $this->save();
            return;
        }

        $completedLessons = $this->lessonProgress()->where('is_completed', true)->count();
        $percentage = ($completedLessons / $totalLessons) * 100;

        $this->progress_percentage = (int) round($percentage);
```
**Penjelasan:** Setiap kali pengguna menekan tombol "Selesai" pada sebuah halaman bacaan materi, blok fungsi pembaharuan progres ini akan terpanggil. Sistem mengambil beban jumlah keseluruhan isi materi video terlebih dahulu, lalu mengukur jumlah rekam jejak materi mana saja yang sudah dituntaskan sang pengguna. Hasil persilangan desimal ini lalu dibulatkan menjadi persentase bilangan bulat (contohnya 85%) dan direkam kembali ke pangkalan data sebagai acuan garis indikator belajar visual.

File Code: `app/Models/CourseEnrollment.php` (Line 81 – 114)
```php
        // Required Quiz Validation logic
        $hasQuizzes = $this->course->modules()->has('quiz')->exists();
        $allQuizzesPassed = true;

        if ($hasQuizzes) {
            $courseQuizIds = Quiz::whereHas('module', function ($query) {
                $query->where('course_id', $this->course_id);
            })->pluck('id');

            $passedUniqueQuizzes = QuizAttempt::where('user_id', $this->user_id)
                ->whereIn('quiz_id', $courseQuizIds)
                ->where('is_passed', true)
                ->select('quiz_id')
                ->distinct()
                ->count('quiz_id');

            if ($passedUniqueQuizzes < $courseQuizIds->count()) {
                $allQuizzesPassed = false;
            }
        }

        // Auto-complete if 100% and passed required quizzes
        if ($this->progress_percentage >= 100 && $allQuizzesPassed && !$this->isCompleted()) {
            $this->completed_at = now();
        }

        $this->save();
```
**Penjelasan:** Selain memvalidasi tontonan materi, algoritma ini juga membentengi kurikulum dengan blok validasi pencapaian Ujian (Kuis). Program akan mencari dan mendaftarkan ID kuis apa saja yang secara wajib mengikat silabus kelas tersebut. Kemudian, sistem mengecek kecocokan riwayat pengerjaan tes dengan mencari nilai kelulusan unik pengguna. Jika persentase tontonan materi telah genap menyentuh angka seratus persen (100%) DAN pengguna berhasil lulus di seluruh ujian wajib yang ada, maka program otomasi akan mengganti status kursus tersebut menjadi Tamat Penuh (`isCompleted()`).

File Code: `app/Models/CourseEnrollment.php` (Line 115 – 132)
```php
        if ($this->progress_percentage >= 100 && $allQuizzesPassed && empty($this->certificate_url)) {
            try {
                $this->loadMissing(['user', 'course']);

                $path = app(CertificateGeneratorService::class)->generateForEnrollment($this);

                if ($path) {
                    $this->certificate_url = $path;
                    $this->save();
                }
            } catch (\Throwable $e) {
                Log::error('Failed generating certificate for enrollment', [
                    'enrollment_id' => $this->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
```
**Penjelasan:** Pada tahap penghujung penelusuran silabus, blok pemrograman penutup ini secara reaktif mengambil alih perintah bilamana parameter kelulusan tercapai namun sistem melihat kolom unduhan sertifikat masih kosong. Kode tersebut akan membangunkan servis peladen eksternal pencetak dokumen (`CertificateGeneratorService`) guna menaruh nama dan nilai siswa ke dalam sebuah berkas desain lembaran cetak PDF. Usai dicetak di balik layar, tautan dokumennya akan dikaitkan pada pangkalan data profil peserta agar siap diunduh sewaktu-waktu.

4.6.3	Tampilan Antarmuka
Tampilan yang berkaitan dengan fitur ini meliputi:

- Screenshot Halaman Induk Pembelajaran Kursus (/account/courses)
  Penjelasan: Antarmuka yang menampilkan daftar kelas atau kursus yang telah dibeli pengguna. Tampilannya dilengkapi indikator *progress bar* untuk menunjukkan persentase penyelesaian materi.

- Screenshot Antarmuka LMS (/learn/[courseSlug])
  Penjelasan: Halaman utama pembelajaran video/PDF. Tampilannya dibagi dua, yaitu layar pemutar media utama di bagian tengah dan bilah samping (*sidebar*) untuk navigasi daftar materi silabus.

- Screenshot Formulir Kuis (Di dalam komponen /learn/[courseSlug])
  Penjelasan: Halaman evaluasi berisi soal pilihan ganda di akhir bab. Sistem akan langsung mengalkulasi skor kelulusan secara otomatis saat formulir dikumpulkan.

- Screenshot Halaman Direktori Sertifikat (/account/certificates)
  Penjelasan: Halaman yang mendaftarkan seluruh sertifikat digital dari kursus yang telah diselesaikan 100%. Dilengkapi dengan tombol untuk mengunduh dokumen PDF resmi.

- Screenshot Panel Manajemen Konten Kursus (Berada pada Panel Admin/Filament)
  Penjelasan: Panel khusus bagi Admin atau Moderator untuk memanajemen konten kursus seperti silabus, video, menyusun urutan materi, dan membuat kuis interaktif per kursus.

4.7	Implementasi Portal Informasi dan Konten Statis
Platform juga dilengkapi dengan modul operasional portal publik yang mengelola penyajian wacana dan konfigurasi dasar laman pengunjung. Implementasi pada pengolahan artikel (*blog*) ditekankan pada penyajian fitur otomatisasi perkiraan masa waktu membaca konten serta strategi menekan konsumsi kecepatan pengolahan data tatkala memuat daftar pencarian konten web. Lebih jauh, pengelolaan konfigurasi fundamental keseluruhan arsitektur web juga dirancang terpusat lewat satu jembatan tabel independen yang mengatur injeksi jenis nilai konfigurasi global (berformat struktur teks JSON).

4.7.1	Tabel Inti Terkait pada Database
Penyajian konten statis dan literasi edukasi publik diatur melalui entitas berikut:
1. `articles` : Tabel katalog blog dan berita edukasi yang menyimpan judul, *slug*, konten *rich text*, cuplikan (*excerpt*), penulis, kategori, tag, waktu publikasi, serta atribut komputasi durasi waktu baca (`reading_time`).
2. `mentors` : Tabel direktori instruktur atau narasumber yang mengajar pada webinar maupun kursus daring platform.
3. `testimonials` : Tabel ulasan kepuasan pengguna yang telah divalidasi dan disetujui untuk ditampilkan pada beranda situs.
4. `categories` : Tabel taksonomi yang mengelompokkan artikel, webinar, kursus, maupun produk digital.
5. `site_settings` : Tabel *key-value store* universal yang merekam parameter konfigurasi publik situs (logo, kontak layanan, teks footer, dan metadata SEO).

4.7.2	Implementasi Kode Utama
Teknik optimalisasi artikel berita serta penanganan sistem konfigurasi global dipaparkan pada blok kode di bawah ini:

File Code: `app/Models/Article.php` (Line 46 – 53)
```php
        // Auto-compute reading_time whenever content changes
        static::saving(function (Article $article) {
            if ($article->isDirty('content') && $article->content) {
                $plainText = strip_tags($article->content);
                $wordCount = str_word_count($plainText);
                $article->reading_time = max(1, (int) ceil($wordCount / 200));
            }
        });
```
**Penjelasan:** Untuk menghadirkan fitur modern layaknya media portal profesional, artikel publik selalu menginformasikan estimasi menit durasi waktu baca di lamannya. Fitur ini diimplementasikan dengan memanfaatkan penyegaran otomatis. Setiap kali staf penulis menyimpan teks materi berita, sistem secara mandiri akan melucuti semua kerangka kode tebal/miring dari teks (`strip_tags`) lalu menghitung total jumlah katanya. Aturan standarnya mengasumsikan kecepatan tempo baca manusia adalah 200 kata per menitnya, kemudian membaginya secara dinamis.

File Code: `app/Models/Article.php` (Line 110 – 121)
```php
    /**
     * Select only the columns needed for list/card views (excludes heavy content column).
     */
    public function scopeListColumns($query)
    {
        return $query->select([
            'id', 'category_id', 'author_id', 'title', 'slug',
            'excerpt', 'featured_image_url', 'tags', 'view_count',
            'reading_time', 'is_featured', 'is_published', 'published_at',
            'created_at', 'updated_at', 'deleted_at',
        ]);
    }
```
**Penjelasan:** Tulisan berita pastinya akan memuat rentetan paragraf panjang dan memakan memori berlebih saat dipanggil. Melalui kode di atas, pengembang menyematkan batas penyaringan khusus pemuatan halaman antarmuka indeks di depan. Saat laman web pengunjung ingin menarik deretan daftar artikel rekomendasi, sistem diinstruksikan untuk tidak menarik bagian isi utama konten artikel itu secara penuh. Ia hanya diizinkan menarik komponen ringan layaknya rincian Judul, URL Sampul, maupun Waktu Unggah guna mempertahankan performa kecepatan mesin pangkalan data tetap prima.

File Code: `app/Models/SiteSetting.php` (Line 24 – 44)
```php
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();
        
        if (!$setting) {
            return $default;
        }

        return static::castValue($setting->value, $setting->type);
    }

    public static function set(string $key, mixed $value, string $type = 'string'): void
    {
        static::updateOrCreate(
            ['key' => $key],
            [
                'value' => static::prepareValue($value, $type),
                'type' => $type,
            ]
        );
    }
```
**Penjelasan:** Pada tabel konfigurasi platform universal yang tidak terkait oleh relasi entitas manapun, diterapkan pola metode Get dan Set untuk menyederhanakan cara aplikasi memanggil pengaturan web sewaktu waktu (misalnya menanyakan alamat sosial media institusi). Skrip di atas memusatkan instruksi penciptaan, perubahan, hingga penarikan variabel sistem berdasarkan label kunci (`$key`) dengan sigap, lengkap beserta opsi penyuntikan cadangan darurat (`$default`) apabila variabel pengaturan yang ditanyakan belum pernah disetel sebelumnya.

File Code: `app/Models/SiteSetting.php` (Line 46 – 64)
```php
    protected static function castValue(string $value, ?string $type): mixed
    {
        return match($type) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $value,
            'float', 'decimal' => (float) $value,
            'array', 'json' => json_decode($value, true),
            default => $value,
        };
    }

    public static function prepareValue(mixed $value, string $type): string
    {
        return match($type) {
            'boolean' => $value ? '1' : '0',
            'array', 'json' => json_encode($value),
            default => (string) $value,
        };
    }
```
**Penjelasan:** Mengingat konfigurasi website memiliki rentang bentuk yang sangat campur aduk (seperti susunan pengaturan bertipe bilangan bulat, angka pecahan, label aktif benar-salah, hingga struktur panjang terstruktur teks kustom), model perlu menseragamkannya. Fungsi pendukung di atas menjamin kepastian proses tersebut. Sebelum disimpan ke pangkalan data, variabel pengaturan yang kompleks dibungkus ke dalam rentetan karakter teks tunggal murni (`prepareValue`). Begitu data tersebut hendak dipanggil kembali menuju halaman web yang membutuhkannya, fungsi pembaca akan mengekstrak bungkusan teks tadi untuk merekonstruksinya utuh kembali ke bentuk format kerangka asalnya (`castValue`).

4.7.3	Tampilan Antarmuka
Tampilan yang berkaitan dengan fitur ini meliputi:

- Screenshot Halaman Utama atau Landing Page (/)
  Penjelasan: Halaman beranda utama situs yang difokuskan untuk pemasaran (*marketing*). Dilengkapi dengan elemen karousel interaktif, tombol *Call to Action*, dan testimoni pengguna.

- Screenshot Halaman Artikel & Edukasi (/articles)
  Penjelasan: Halaman *blog* untuk menampilkan artikel edukasi. Menggunakan tampilan kartu *grid* dengan kategorisasi materi dan estimasi waktu baca untuk pengguna.

- Screenshot Halaman Direktori Profil Mentor (/mentors)
  Penjelasan: Halaman direktori yang menampilkan profil dan spesialisasi para pengajar. Halaman ini juga menghubungkan profil mentor dengan kursus yang mereka ajar.

- Screenshot Halaman Tanya Jawab atau FAQ (/faq)
  Penjelasan: Laman statis yang memuat panduan dan jawaban dari pertanyaan umum pelanggan. Menggunakan gaya desain *accordion* (tutup-buka) untuk menghemat ruang tampilan layar.

- Screenshot Halaman Kontak & Bantuan (/contact)
  Penjelasan: Antarmuka yang menyediakan formulir kontak, alamat operasional, surel, tautan media sosial, serta peta lokasi integrasi dari Google Maps.

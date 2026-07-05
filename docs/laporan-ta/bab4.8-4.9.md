4.8	Implementasi Manajemen Konten dan Katalog Item
Modul manajemen katalog memikul tanggung jawab besar dalam menata etalase produk pada ruang belanja digital, baik itu berupa kelas edukasi, tiket jadwal acara daring (Webinar), hingga produk dagangan digital lainnya. Pemrograman untuk modul ini difokuskan pada upaya otomatisasi kalkulasi tampilan harga diskon coret, perumusan algoritma tingkat kepuasan materi, serta upaya perlindungan dini untuk menyensor data-data vital (seperti tautan rapat daring Zoom) agar tidak bocor pada antarmuka publik yang dapat diakses oleh pengunjung tanpa identitas.

4.8.1	Tabel Inti Terkait pada Database
Manajemen katalog komersial dan konten publik bersandar pada entitas-entitas berikut:
1. `courses`, `webinars`, `products` : Tabel-tabel utama yang menyimpan katalog komersial B2C, dilengkapi atribut harga asli (`original_price`), harga jual (`price`), status publikasi (`is_published`), dan slug URL. Khusus `webinars`, menyimpan kredensial sensitif seperti *Meeting ID* dan *Password Zoom*.
2. `articles` : Tabel direktori artikel edukasi yang dikelola redaksi.
3. `mentors` : Tabel pengajar yang dikauntukan dengan kursus dan webinar.
4. `categories` : Tabel taksonomi untuk pengelompokan item etalase.
5. `testimonials` : Tabel ulasan pelanggan yang ditampilkan di halaman beranda.

4.8.2	Implementasi Kode Utama
Berikut ini adalah cuplikan fondasi pemrograman pelengkap yang mendukung operasional etalase belanja digital tersebut:

File Code: `app/Models/Course.php` (Line 107 – 119)
```php
    // Helper Methods
    public function hasDiscount(): bool
    {
        return $this->original_price && $this->original_price > $this->price;
    }

    public function getDiscountPercentageAttribute(): ?int
    {
        if (!$this->hasDiscount()) {
            return null;
        }

        return (int) round((($this->original_price - $this->price) / $this->original_price) * 100);
    }
```
**Penjelasan:** Tampilan antarmuka e-commerce akan terasa hambar jika tidak menampilkan perhitungan diskon harga (*coret harga*). Menggunakan fungsi bantuan di atas, basis data kelas kursus tidak perlu secara repot menyimpan angka pasti dari potongan harga diskon ke dalam kolom tabel. Saat halaman katalog memanggil harga sebuah kelas, fungsi ini di balik layar akan bertugas menghitung selisih harga asli dengan harga diskon, lalu membulatkannya menjadi bentuk angka persentase murni untuk disuguhkan sebagai stiker kupon di pojok gambar etalase produk.

File Code: `app/Models/Webinar.php` (Line 93 – 105)
```php
    /**
     * Select only the columns needed for list views.
     * Excludes sensitive Zoom credentials not needed in public listings.
     */
    public function scopeListColumns($query)
    {
        return $query->select([
            'id', 'mentor_id', 'title', 'slug', 'description',
            'thumbnail_url', 'price', 'original_price',
            'scheduled_at', 'duration_minutes', 'max_participants',
            'is_active', 'created_at', 'updated_at', 'deleted_at',
        ]);
    }
```
**Penjelasan:** Pada tabel produk berwujud Webinar, secara logis tersimpan komponen akses vital di dalamnya (seperti *Zoom Link*, *Meeting ID*, dan kata sandinya). Fitur corong penyaring pemuatan halaman muka (`scopeListColumns`) ini menjadi garis pertahanan sistem keamanan katalog. Ketika pengunjung awam membuka daftar katalog acara Webinar, fungsi ini memastikan sistem tidak akan pernah menarik data kredensial akses vital *Zoom* ke dalam jaringan komputer pengunjung, sehingga upaya pencurian tautan rapat ilegal dapat dicegah mutlak secara mandiri oleh kode.

File Code: `app/Models/Course.php` (Line 138 – 146)
```php
    public function getCompletionRateAttribute(): float
    {
        $total = $this->getTotalEnrollmentsAttribute();
        if ($total === 0) {
            return 0;
        }

        return round(($this->getTotalCompletionsAttribute() / $total) * 100, 2);
    }
```
**Penjelasan:** Untuk menghadirkan komponen sosial (*Social Proof*) pada detail informasi kelas, ditambahkan sebuah fungsi rumusan matematis pengukur tingkat keaktifan kurikulum (`getCompletionRateAttribute`). Fungsi ini akan memanggil jumlah mutlak partisipan kelas, lalu mengadunya dengan variabel pembagi dari rekap catatan jumlah partisipan yang sukses menamatkan gelar silabus secara 100%. Hasil komparasi ini kemudian akan memicu pengembalian struktur data desimal yang menandakan persentase angka tingkat kelulusan dari suatu kelas materi.

4.8.3	Tampilan Antarmuka
Tampilan yang berkaitan dengan fitur ini meliputi:

- Screenshot Dasbor Manajemen Konten Kursus (Admin Panel) (/admin)
  Penjelasan: Tabel antarmuka bagi Admin dan Moderator untuk mengelola seluruh produk *marketplace*. Dilengkapi dengan fitur *toggle* status untuk menerbitkan produk (Draft ke Published).

- Screenshot Formulir Buat/Edit Produk Digital (/admin/products/create)
  Penjelasan: Formulir untuk mengatur detail produk dagangan, khususnya pengaturan variabel harga tunai dan harga asli (*original price*) guna memunculkan visualisasi harga diskon coret.

- Screenshot Formulir Buat/Edit Kursus (/admin/courses/builder)
  Penjelasan: Halaman berkonsep *Wizard* bertahap bagi Moderator untuk merancang kursus baru, mulai dari informasi dasar, penyusunan bab, hingga unggah video.

- Screenshot Formulir Buat/Edit Webinar (/admin/webinars/create)
  Penjelasan: Formulir pembuatan acara daring yang dilengkapi batasan kuota partisipan dan isolasi penyimpanan kredensial akses sensitif seperti *Meeting ID* dan kata sandi Zoom.

- Screenshot Formulir Buat/Edit Artikel (/admin/articles/create)
  Penjelasan: Antarmuka *WYSIWYG* (*What You See Is What You Get*) bagi redatur/moderator untuk menulis dan memformat artikel berita secara dinamis beserta pengaturan meta URL (Slug).

4.9	Implementasi Administrasi Platform dan Monitoring (Super Admin)
Fokus implementasi tingkat atas (Super Admin) ditugaskan untuk mengawasi kesehatan infrastruktur dari seluruh payung organisasi yang terafiliasi dengan platform. Mengingat platform ini bersifat langganan multi-lembaga (*Multi-Tenant Software as a Service*), arsitektur utamanya sangat menjunjung tinggi perihal keamanan pemisahan data mutlak antar satu sekolah dengan yang lainnya (Isolasi Data). Selain itu, model data pada tingkatan ini juga bertugas memvalidasi batasan layanan (*limit*) jumlah murid yang bisa didaftarkan sebuah institusi berdasarkan kuota paket berlangganan.

4.9.1	Tabel Inti Terkait pada Database
Pengawasan tingkat peladen dan administrasi pusat bertumpu pada pemeriksaan entitas-entitas berikut:
1. `schools` : Target pengawasan utama yang menentukan batas kuota siswa (`student_limit`), kuota guru (`teacher_limit`), serta masa aktif paket Pro institusi.
2. `users` : Tabel direktori pengguna global yang memetakan otorisasi tingkat atas (peran *Super Admin* dan *Moderator* platform).
3. `orders` : Tabel agregasi pendapatan platform dari penjualan B2C yang dianalisis dalam grafik omset keuangan.
4. `site_settings` : Tabel konfigurasi sistem statis universal.

4.9.2	Implementasi Kode Utama
Fondasi keamanan tembok pemisah data dan regulasi kuota berlangganan diatur dengan algoritma di bawah ini:

File Code: `app/Models/School.php` (Line 46 – 50)
```php
    // Relationships
    public function members(): HasMany
    {
        return $this->hasMany(SchoolMember::class)
            ->where('school_id', $this->id); // 🔒 DATA ISOLATION
    }
```
**Penjelasan:** Baris kode singkat di atas merupakan implementasi krusial pada tabel institusi. Perintah logika `where` yang secara keras (*hard-coded*) mengunci referensi tautan antar tabel (`school_id`) bertindak sebagai gembok pemisah data yang mengikat. Teknik relasi ini memaksa setiap permintaan penarikan data keanggotaan agar selalu terkunci mutlak hanya pada nomor ID institusi yang dituju saja. Mekanisme ini menjamin mustahil bagi seorang pengguna atau operator di sekolah A untuk tersesat memanggil rekaman rahasia dari sekolah B.

File Code: `app/Models/School.php` (Line 228 – 235)
```php
    public function canAddStudent(): bool
    {
        if ($this->isPro()) {
            return true;
        }
        
        return $this->students()->where('status', 'active')->count() < $this->student_limit;
    }
```
**Penjelasan:** Fungsi ini menjadi palang pintu penjaga batas model bisnis langganan pada platform. Ketika Operator Sekolah ingin memasukkan murid baru, fungsi validasi ini terlebih dahulu menanyakan status pembayaran keaktifan sekolah (`isPro()`). Jika mereka tidak sedang berlangganan (versi gratis), program secara ketat menghitung akumulasi seluruh data siswa yang aktif, lalu mengkomparasinya dengan nilai izin batasan maksimal jumlah anak yang boleh terdaftar. Jika jumlah batas kuota itu telah penuh, maka prosedur pendaftaran akan tertolak hingga institusi beralih paket ke level Pro.

File Code: `app/Models/School.php` (Line 120 – 125)
```php
    // Accessors
    protected function totalStudents(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->students()->count(),
        );
    }
```
**Penjelasan:** Dasbor metrik khusus tingkat atas (*Super Admin*) senantiasa membutuhkan penyajian grafis laporan jumlah murid untuk seluruh sekolah. Fungsi ini mengadopsi standar pemformatan akses modern Laravel (*Accessors*) yang akan merubah fungsi tarikan basis data yang lumayan memberatkan tadi, menjadi seakan-akan merupakan variabel atribut sederhana `total_students`. Hal ini tak cuma membuat penulisan perintah peramalan grafik terlihat sangat rapi di mata pengembang, namun juga merampingkan rutinitas permintaan data saat sistem pengawasan platform menarik kalkulasi rekap siswa jutaan lembaga secara bergantian.

4.9.3	Tampilan Antarmuka
Tampilan yang berkaitan dengan fitur pengawasan tingkat peladen ini meliputi:

- Screenshot Dasbor Analitik & Grafik Statistik Utama (/admin/analytics)
  Penjelasan: Panel kontrol terpusat bagi *Super Admin* yang menampilkan grafik kurva dan diagram untuk mengukur tren pendaftaran sekolah baru dan omset penjualan platform secara komprehensif.

- Screenshot Halaman Manajemen Pengguna (/admin/users)
  Penjelasan: Tabel *database* sentral untuk mengelola seluruh akun pengguna dari berbagai *tenant*. Admin memiliki akses untuk mengedit detail atau membekukan akun yang bermasalah.

- Screenshot Halaman Manajemen Tenant Sekolah (/admin/schools)
  Penjelasan: Halaman untuk memantau pendaftaran sekolah klien (*tenant*), yang dilengkapi kontrol sakelar bagi admin untuk memperpanjang durasi langganan secara manual (*bypass*) atau memodifikasi limit kuota.

- Screenshot Halaman Pengaturan Sistem (/admin/settings)
  Penjelasan: Antarmuka berpasangan (*key-value*) yang memungkinkan Super Admin untuk mengubah data statis eksternal situs (seperti nomor WA kontak layanan atau SEO meta) tanpa harus menyunting kode HTML server.

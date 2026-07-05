# BAB 6 KESIMPULAN DAN SARAN

## 6.1 Kesimpulan
Berdasarkan hasil analisis, perancangan, implementasi, hingga tahap pengujian yang telah dilakukan pada pengembangan platform PaudPedia, dapat ditarik beberapa kesimpulan utama sebagai berikut:

1. Telah berhasil dibangun sebuah platform *Software as a Service* (SaaS) multi-sekolah (*multi-tenant*) yang mampu mewadahi manajemen operasional berbagai instansi Pendidikan Anak Usia Dini (PAUD) secara terpusat, dengan struktur pangkalan data yang diisolasi secara aman antar-institusi menggunakan gembok relasional (*Tenant Data Isolation*).
2. Platform ini berhasil mendigitalisasi proses inti administrasi akademik PAUD dengan pembagian peran yang jelas; di mana **Operator Sekolah** memegang kendali atas manajemen data induk (siswa, rombel, dan relasi wali murid), sedangkan **Guru Wali Kelas** berfokus pada perekaman absensi harian dan penilaian observasi perkembangan anak. Sistem penilaian ini juga telah diperkuat oleh mekanisme *Temporal Assessment Integrity* dan *Smart Omission* yang menjaga keutuhan riwayat nilai lampau serta menghasilkan cetakan rapor naratif akhir semester yang rapi secara otomatis.
3. Integrasi modul manajemen keuangan telah terbukti sanggup memfasilitasi tata kelola institusi melalui sistem pembukuan buku besar administratif (*ledger*) untuk tagihan Sumbangan Pembinaan Pendidikan (SPP) dan tabungan celengan siswa yang dikelola oleh Operator dan Guru, serta secara transparan terhubung dengan dasbor pemantauan eksklusif keluarga (*Parent Dashboard*).
4. Pengembangan ekosistem *Marketplace B2C* dan *Learning Management System* (LMS) telah sukses diwujudkan. Ekosistem ini menjembatani transaksi komersial kursus *online* publik, memfasilitasi kegiatan pemutaran video belajar asinkron dan kuis interaktif, serta dilengkapi dengan sistem validasi kelulusan 100% yang secara otomatis menerbitkan dokumen sertifikasi (*e-certificate*).
5. Hasil dari eksekusi serangkaian skenario uji menggunakan metode **Pengujian Fungsional (*Functional Testing*)** berbasis simulasi antarmuka mensahkan bahwa keseluruhan fitur arsitektur sistem—baik validasi elemen UI, integrasi alur lintas modul, maupun tameng perlindungan aturan bisnis—telah beroperasi secara presisi, tangguh, dan selaras dengan standar operasional proses bisnis yang dirancang dengan tingkat keberhasilan sempurna (*100% Passed*).

## 6.2 Saran
Berdasarkan proses perancangan, pengembangan, dan evaluasi sistem yang telah dilalui, terdapat sejumlah saran dan rekomendasi yang dapat ditindaklanjuti sebagai bahan pengembangan platform pada fase berikutnya, maupun sebagai wawasan bagi pihak akademisi.

### 6.2.1 Saran Pengembangan Sistem
Berpijak pada analisis batasan desain dan implementasi sistem saat ini, saran pengembangan yang dapat dilakukan untuk penyempurnaan platform PaudPedia di masa mendatang adalah sebagai berikut:

1. **Pengembangan Aplikasi Seluler Native dan Mode Luring (*Offline Mode*):**
   Mengingat saat ini sistem beroperasi secara eksklusif sebagai aplikasi berbasis *Web Browser* yang bergantung pada koneksi internet *real-time*, pengembangan selanjutnya disarankan untuk membangun aplikasi *mobile native* (Android/iOS) atau teknologi *Progressive Web App* (PWA) yang mendukung penyimpanan sementara (*offline caching*). Hal ini akan memungkinkan guru dan orang tua di daerah dengan koneksi internet terbatas untuk tetap dapat melakukan presensi harian atau membaca rapor anak tanpa kendala jaringan.

2. **Integrasi Pembayaran Otomatis pada Modul Keuangan SIAKAD:**
   Modul keuangan SPP dan tabungan saat ini berfungsi sebagai buku besar pencatatan administratif (*ledger*) di mana verifikasi pembayaran dilakukan secara konvensional dan disahkan (*marked as paid*) oleh Operator atau Guru. Untuk meningkatkan kepraktisan, disarankan mengintegrasikan layanan *Payment Gateway* (seperti Midtrans) secara langsung ke dalam dasbor Orang Tua, sehingga pembayaran iuran bulanan sekolah dapat dilakukan instan melalui transfer *Virtual Account*, QRIS, atau *e-Wallet* dengan sinkronisasi status otomatis.

3. **Ekspansi E-Commerce dan Manajemen Logistik Pengiriman:**
   Saat ini, integrasi *Payment Gateway* pada portal *Marketplace* B2C murni mendistribusikan barang dan akses digital (kursus daring, tiket webinar, dan *e-book*). Pengembangan ke depan dapat mengekspansi lini bisnis platform untuk mendistribusikan produk fisik edukatif (seperti Alat Peraga Edukatif/APE atau buku cetak), yang dilengkapi dengan integrasi API penghitungan ongkos kirim dan pelacakan resi kurir pengiriman otomatis.

4. **Ekspansi Instrumen Penilaian dan Rubrik Analitik Tumbuh Kembang Anak:**
   Sistem evaluasi saat ini mencatat observasi perkembangan dalam bentuk rekapitulasi bulanan dengan instrumen kualitatif skala Kurikulum Merdeka PAUD (Belum Berkembang, Mulai Berkembang, Sesuai Harapan, Sangat Baik) sesuai standar nasional Kementerian Pendidikan, Kebudayaan, Riset, dan Teknologi (Kemendikbudristek, 2022) yang tidak menerapkan kalkulasi angka mutlak (0-100). Pada fase berikutnya, sistem dapat melengkapi instrumen ini dengan fitur catatan harian anekdotal (*anecdotal records*) serta rubrik analitik klinis psikologis anak berbasis grafik perkembangan agar pemantauan tumbuh kembang dapat dilakukan lebih mendalam.

5. **Integrasi Ruang Kelas Virtual Terintegrasi (*Built-in Video Conferencing*):**
   Platform LMS saat ini menunjang pembelajaran asinkron (video sematan, modul PDF, dan kuis) dan masih mengandalkan tautan pihak ketiga eksternal (Zoom/Google Meet) untuk pelaksanaan sesi tatap muka Webinar. Pemutakhiran berikutnya disarankan menanamkan teknologi konferensi video interaktif secara langsung (*built-in live streaming* seperti WebRTC atau BigBlueButton) ke dalam ekosistem LMS platform agar interaksi belajar menjadi lebih terpusat.

6. **Pengembangan Penugasan Esai dan Portofolio Karya pada LMS B2C:**
   Sertifikasi kelulusan digital (*e-certificate*) saat ini dikalkulasi berdasarkan penyelesaian progres materi 100% dan kelulusan kuis objektif tertutup (Pilihan Ganda). Guna meningkatkan bobot pedagogis kursus daring, disarankan menambahkan fitur pengumpulan tugas (*assignment/project upload*) bertipe esai atau portofolio karya seni anak, yang dilengkapi dengan dasbor pengoreksian dan pemberian umpan balik (*feedback*) manual oleh instruktur mentor.

7. **Peningkatan Skalabilitas dan Keamanan Arsitektur Database Multi-Tenant:**
   Meskipun isolasi data antar-sekolah saat ini telah dibentengi dengan kuat menggunakan *foreign-key binding* pada tingkat ORM dan *Middleware*, seiring bertumbuhnya jumlah sekolah klien (*tenant*), arsitektur basis data disarankan untuk ditingkatkan menuju skema *database sharding* atau pemisahan basis data fisik per-*tenant* berskala besar (*Enterprise Tenant*). Selain itu, pelaksanaan audit pengujian penetrasi (*penetration testing*) secara berkala sangat dianjurkan untuk mempertahankan standar keamanan tertinggi.

8. **Integrasi Kecerdasan Buatan (AI) dan Sinkronisasi Dapodik:**
   Pemanfaatan *Artificial Intelligence* (AI) dapat disematkan pada ranah LMS sebagai agen rekomendasi pintar (*smart recommendation*) yang menyarankan kursus relevan berdasarkan minat belajar pengguna, serta pada tingkat *Super Admin* untuk prakiraan (*forecasting*) tren finansial platform. Selain itu, pembangunan jalur pipa API yang terintegrasi dengan peladen Data Pokok Pendidikan (Dapodik) Kemendikbudristek amat disarankan agar Operator Sekolah terbebas dari pengisian ganda biodata siswa baru.

### 6.2.2 Saran Bagi Mahasiswa
Berdasarkan pengalaman penulis selama menempuh pengerjaan perancangan Tugas Akhir berskala raksasa ini, keterlibatan secara menyeluruh di setiap fase *Software Development Life Cycle* (SDLC) memberikan suntikan pembelajaran yang amat berharga, baik dari segi teknikalitas bahasa pemrograman maupun kematangan dalam mengorkestrasi logika relasi basis data. Oleh karena itu, penulis sangat menyarankan agar rekan-rekan mahasiswa penerus dapat lebih berani mengeksekusi topik Tugas Akhir berbasis permasalahan konkret dan berarsitektur rumit (*Software as a Service*), serta memperbanyak keterlibatan kolaborasi riil di lingkungan industri. Langkah eksploratif ini niscaya akan mempertajam intuisi teknis, memperluas cakrawala pemahaman perancangan rekayasa perangkat lunak (*Software Engineering*), dan membentuk kesiapan mental di panggung dunia kerja profesional.

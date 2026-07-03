# Penanganan Kasus Negatif (Negative Cases) Modul Penilaian PaudPedia

Dokumen ini merangkum seluruh skenario kasus negatif (*edge cases & negative scenarios*) pada siklus penilaian peserta didik beserta mekanisme proteksi sistem yang telah diimplementasikan dalam kode (*to the point* & padat).

---

## 1. Master Data Penilaian (Program & Indikator)

| No | Kasus Negatif | Potensi Masalah | Logika & Mekanisme Penanganan Sistem |
|:---:| :--- | :--- | :--- |
| **1.1** | **Hapus Program Perkembangan** (yang sudah memiliki riwayat nilai/rapor) | Data nilai siswa terhapus permanen (*Cascading Delete*), rapor lampau error. | **Restrict Delete Guard (`HTTP 422`)**. Backend menolak penghapusan jika ID program tercatat di tabel `assessments` atau `student_report_details`. Sistem mengarahkan operator menggunakan fitur nonaktifkan. |
| **1.2** | **Nonaktifkan Program Perkembangan** (`is_active = false`) | Program hilang dari rapor lama, atau tetap muncul mengganggu di bulan baru. | **Temporal Active Split**. Program disembunyikan dari form penilaian bulan baru. Pada matriks & rapor semester lampau, program tetap ditampilkan sebagai dokumen arsip *Read-Only* jika sempat dinilai. |
| **1.3** | **Hapus Indikator Perkembangan** (yang sudah memiliki riwayat nilai) | Riwayat observasi hilang, merubah rata-rata capaian akhir semester lampau. | **Restrict Delete Guard (`HTTP 422`)**. Penghapusan ditolak otomatis jika ID indikator sudah memiliki relasi data penilaian. |
| **1.4** | **Nonaktifkan Indikator Perkembangan** (`is_active = false`) | Guru bingung apakah masih wajib menilai, atau riwayat lama hilang. | **Temporal Read-Only Guard**. Indikator disembunyikan dari form input bulan berjalan/baru. Di semester lampau, indikator tetap tampil sebagai bukti sejarah dengan status terkunci (*Read-Only*). |
| **1.5** | **Edit Nama Program Perkembangan** | Khawatir merusak relasi data nilai siswa di database. | **Relational Auto-Sync**. Karena data diikat oleh Primary Key (`program_id`), perbaikan redaksi kata/typo otomatis tersinkronisasi ke seluruh sistem tanpa merusak nilai siswa. |
| **1.6** | **Edit Nama Indikator Perkembangan** | Khawatir merusak skor skalar (BB/MB/BSH/BSB) lampau. | **Relational Auto-Sync**. Perubahan nama indikator langsung tercermin di seluruh tabel matriks dan cetakan PDF tanpa mengubah riwayat skor skalar siswa. |

---

## 2. Input Penilaian Bulanan (Guru Kelas)

| No | Kasus Negatif | Potensi Masalah | Logika & Mekanisme Penanganan Sistem |
|:---:| :--- | :--- | :--- |
| **2.1** | **Operator menambah Indikator baru di pertengahan bulan** (setelah guru menyimpan penilaian bulan tersebut) | Guru melihat ada kolom kosong baru saat membuka kembali form bulan lampau. | **Optional Monthly Filling + Temporal Filter**. Sistem memuat indikator dengan filter `created_at <= akhir_bulan`. Indikator baru tampil dengan sel kosong (`-`). Guru **tidak wajib** mengisinya dan sistem tidak menganggapnya sebagai error. |
| **2.2** | **Guru sengaja mengosongkan beberapa indikator di bulan tertentu** (siswa sakit / materi tidak diajarkan) | Sistem menolak simpan atau menganggap data tidak lengkap. | **Partial Month Allowance**. Sistem mengizinkan penyimpanan bulanan parsial, sesuai prinsip asesmen autentik harian Kurikulum Merdeka PAUD yang tidak mewajibkan 100% indikator diukur setiap bulan. |

---

## 3. Penyusunan Rapor Naratif (Draft Rapor)

| No | Kasus Negatif | Potensi Masalah | Logika & Mekanisme Penanganan Sistem |
|:---:| :--- | :--- | :--- |
| **3.1** | **Operator menambah Indikator baru setelah Guru selesai menyusun Draft Rapor Naratif** | Form narasi mendadak terkunci (*disabled*) karena sistem menganggap matriks rekapitulasi belum 100% lengkap. | **Draft Protection Guard (`hasExistingReport`)**. Jika draft rapor siswa sudah pernah disimpan di database, form narasi **tidak akan pernah terkunci lagi** berapapun indikator baru yang ditambahkan operator di kemudian hari. |
| **3.2** | **Siswa baru pindah/masuk di pertengahan semester** (misal bulan ke-4) | Siswa memiliki nilai kosong di bulan 1-3, sehingga rapor tidak bisa dibuat jika syarat kelengkapan terlalu kaku. | **Smart Completeness Rule**. Syarat kelengkapan diubah dari "100% sel penuh" menjadi: **"Setiap Program Perkembangan minimal memiliki 1 observasi penilaian pada indikator apa pun selama semester tersebut"**. |
| **3.3** | **Guru mencoba menyusun rapor padahal 0 penilaian di seluruh 6 bulan** | Guru membuat narasi fiktif tanpa bukti observasi sama sekali. | **Empty Matrix Block**. Form narasi baru akan dikunci (*disabled*) dengan peringatan: *"Silakan lengkapi penilaian bulanan siswa terlebih dahulu."* |

---

## 4. Cetak Rapor PDF & Tampilan Matriks Rekapitulasi

| No | Kasus Negatif | Potensi Masalah | Logika & Mekanisme Penanganan Sistem |
|:---:| :--- | :--- | :--- |
| **4.1** | **Indikator dibuat di bulan ke-4** (Bulan 1-3 kosong, Bulan 4-6 dinilai) | Rata-rata Capaian Akhir siswa anjlok jika total skor tetap dibagi 6 bulan. | **Dynamic Average Calculation**. Capaian Akhir dihitung hanya dari pembagi bulan yang dinilai (dibagi 3 bulan, bukan 6). Di tabel PDF tertulis: `- | - | - | MB | BSH | BSB | Capaian: BSH`. |
| **4.2** | **Indikator baru ditambahkan setelah semester berakhir** (0 nilai di seluruh 6 bulan semester lampau) | PDF Rapor lampau menampilkan baris kosong `- | - | - | - | - | -` yang membuat orang tua mengira guru lalai menilai. | **Smart Omission (Penghilangan Otomatis)**. Pada generator PDF (`ReportController`), indikator yang bernilai 0 di seluruh 6 bulan pada semester tersebut **otomatis dihilangkan dari tabel PDF**. Cetakan rapor tetap bersih 100%. |
| **4.3** | **Program Perkembangan baru ditambahkan, namun seluruh indikator di bawahnya memiliki 0 nilai** | PDF menampilkan Judul Program Perkembangan yang kosong tanpa isi indikator. | **Program Omission**. Jika seluruh indikator di bawah Program X dihilangkan oleh Smart Omission, maka Judul Program X juga otomatis **tidak dicetak** di tabel matriks PDF. |

---

## 5. Ringkasan Pilar Algoritma Penanganan
Seluruh proteksi di atas dibangun atas 5 pilar arsitektur tanpa memerlukan migrasi database baru:
1. **Restrict Delete Guard**: Proteksi relational integrity di level Controller (`AssessmentSettingController`).
2. **Temporal Filter (`created_at`)**: Pemanfaatan timestamp kelahiran data sebagai batas waktu berlaku efektif kurikulum.
3. **Draft Protection (`hasExistingReport`)**: Bypass validasi kelengkapan matriks untuk rapor yang sudah dalam tahap penyusunan/terbit.
4. **Smart Matrix Completeness**: Validasi minimal 1 observasi per Aspek Program Perkembangan.
5. **Smart Omission**: Penyaringan dinamis item ber-skor 0 pada generator laporan cetak (`ReportController`).

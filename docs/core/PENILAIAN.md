# Alur Kerja Sistem Penilaian & Pelaporan PAUD (Dinamis & Deskriptif)

Dokumen ini menjelaskan alur kerja (*user journey*) dari sistem penilaian PAUD yang telah disesuaikan dengan panduan Kurikulum Kemendikdasmen (Modul4_PSF), di mana penilaian bersifat periodik (bulanan) dan pelaporan berfokus pada narasi deskriptif yang strukturnya digeneralisasi (dinamis) berdasarkan database.

---

## 1. Persiapan Hierarki Master Data (Level Admin/Kepala Sekolah)
Sebelum guru bisa memberikan nilai, kerangka dasar kurikulum harus disiapkan terlebih dahulu. Proses ini sangat **dinamis** dan tidak di-*hardcode*, sehingga sekolah bebas menambah/mengurangi kerangka.

1. Admin atau Kepala Sekolah membuka menu **Master Data -> Pengaturan Penilaian**.
2. **Setup Program Perkembangan (Level 1)**: Admin membuat/mendaftarkan pilar-pilar utama.
   - *Contoh yang di-input*: "Nilai Agama dan Moral", "Fisik Motorik", "Kognitif", "Bahasa", "Seni".
3. **Setup Indikator Perkembangan (Level 2)**: Di dalam setiap Program, Admin menambahkan turunan indikator spesifik.
   - *Contoh*: Di bawah program "Nilai Agama", Admin menambahkan indikator: *"Terbiasa berdoa sebelum makan"*, *"Bisa menyebutkan ciptaan Tuhan"*.

---

## 2. Penilaian Periodik Bulanan (Level Guru)
Penilaian tidak dilakukan sekaligus di akhir semester, melainkan diobservasi setiap bulan untuk membentuk tren (*track record*).

1. Setiap menjelang akhir bulan, Guru Kelas masuk ke menu **Input Penilaian**.
2. Guru memilih nama siswa (Misal: Hira) dan memilih parameter bulan penilaian (Misal: "Januari 2024").
3. **Tampilan Dinamis**: Layar akan menampilkan daftar kelompok (*Tabs* / *Accordion*) yang **otomatis di-load** dari tabel Program Perkembangan yang diset pada Tahap 1.
   - Guru meng-klik kelompok "Nilai Agama dan Moral".
   - Sekumpulan Indikator di bawahnya akan muncul.
4. Guru memberikan nilai Skala (**BB / MB / BSH / BSB**) pada indikator-indikator tersebut dengan cara menekan tombol pilihan.
5. Guru mengulangi proses yang sama di bulan-bulan berikutnya (Februari, Maret, dst.).

> **Hasil Sementara**: Di *database*, sistem kini memiliki tabel jejak matriks (Hira -> Agama -> Terbiasa Berdoa -> Jan: BSH, Feb: BSB, Mar: BSB).

---

## 3. Pembuatan Rapor Naratif Semesteran (Level Guru)
Di akhir semester, tiba saatnya membuat pelaporan utuh. Guru **tidak perlu lagi mengarang dari kertas kosong**. Sistem akan memandu mereka menggunakan gabungan "Contekan Data" dan "Borang Dinamis".

1. Guru membuka menu **Pembuatan Rapor Semester** untuk siswa Hira.
2. Layar kerja akan terbagi dua (Split Screen / Kolom Kiri-Kanan) atau memiliki desain bantuan agar mudah melirik data:
   - **Panel Contekan (Kiri)**: Menampilkan rangkuman tabel matriks riwayat Hira dari Bulan 1 s/d 6 untuk seluruh Indikator. Guru bisa melihat dengan cepat, *"Oh, Hira untuk Agama nilainya mayoritas BSH dan BSB"*.
   - **Lembar Narasi (Kanan)**: Berisi susunan kotak isian teks (*Text Area*).
3. **Generasi Form Otomatis**: Kotak isian teks di sebelah kanan ini **bukanlah layout statis**, melainkan di-*looping* (di-generate) sesuai dengan jumlah Program Perkembangan yang terdaftar di sistem.
   - Jika di database ada 5 Program (Agama, Fisik, Kognitif, Bahasa, Seni), maka sistem akan me-*render*:
     - 1 Text Area untuk *Narasi Nilai Agama dan Moral*
     - 1 Text Area untuk *Narasi Fisik Motorik*
     - 1 Text Area untuk *Narasi Kognitif*
     - 1 Text Area untuk *Narasi Bahasa*
     - 1 Text Area untuk *Narasi Seni*
   - Di ujung paling bawah form, akan ada tambahan kotak paten untuk:
     - 1 Text Area *Penutup*
     - 1 Text Area *Rekomendasi*
4. Guru mulai menyusun cerita kalimat demi kalimat ke dalam tiap-tiap kotak berdasarkan panduan matriks di sebelahnya.
5. Setelah lengkap, Guru menekan "Simpan Rapor".

---

## 4. Publikasi & Penjelasan Kriteria (Level Sistem/Orang Tua)
Tahap akhir adalah penyajian dokumen utuh.

1. Sistem (Backend) akan menggabungkan seluruh potongan narasi dari Guru tadi menjadi satu lembar dokumen panjang (PDF atau halaman Web). Susunannya rapi dari atas ke bawah: Narasi Agama -> Fisik -> ... -> Penutup -> Rekomendasi.
2. Ketika orang tua mencetak rapor atau masuk ke portal aplikasi mereka, mereka akan membaca cerita perkembangan ini secara runtut.
3. **Panduan Kriteria Dinamis**: Jika di dalam laporan tercantum istilah BB, MB, BSH, BSB, orang tua (maupun guru) bisa membaca halaman panduan yang mendeskripsikan arti dari istilah-istilah tersebut. Konten penjelasan ini murni dikendalikan oleh **Super Admin** melalui menu *System Settings*, sehingga jika suatu saat Kemendikdasmen mengubah redaksi definisinya, pihak sekolah cukup memperbaruinya lewat Admin tanpa harus menyentuh kode aplikasi.

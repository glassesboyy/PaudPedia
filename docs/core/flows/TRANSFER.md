# Dokumentasi Alur Transfer Kepemilikan (Headmaster) SIAKAD

Dokumen ini menjelaskan logika bisnis utama dari sistem pergantian Kepala Sekolah (Transfer Ownership) untuk memastikan transisi jabatan yang aman dan tidak merusak integritas data.

## 1. Bagaimana Syarat dan Logika Inisiasi Transfer Berjalan?
*   **Hanya Kepala Sekolah:** Fitur ini terdapat di halaman *Pengaturan Sekolah (Danger Zone)* dan hanya bisa diakses oleh *user* dengan *role* `headmaster`.
*   **Strict Fresh User Policy:** Sistem mencegah potensi kerancuan peran (*Multi-Role Conflict*). Kandidat (calon Kepala Sekolah baru) **wajib** merupakan akun yang belum terafiliasi/terdaftar di sekolah tersebut (bukan Guru maupun Orang Tua aktif di sekolah itu).
*   **Generate Token:** Ketika undangan dikirim, sistem akan membuat *record* di tabel `school_transfer_requests` dengan *token* acak yang unik, status `pending`, dan batas waktu (*expired*) 3 hari. Email undangan dikirim berisi tautan khusus.

## 2. Bagaimana Nasib Undangan Jika Kepsek Berubah Pikiran (Double Transfer)?
*   **Sistem Auto-Reject:** Jika Kepala Sekolah A mengundang User B, lalu berubah pikiran dan mengundang User C, maka detik ketika undangan untuk User C dibuat, **sistem akan secara otomatis mengubah status undangan User B menjadi `rejected`**.
*   **Mencegah Race Condition:** Jika User B baru mengeklik tautan emailnya setelah undangan untuk User C terkirim, sistem akan memblokir User B dengan pesan "Undangan tidak valid atau sudah kedaluwarsa". Hal ini menjamin bahwa **tidak akan pernah ada dua orang yang bisa mengklaim jabatan secara bersamaan**.

## 3. Bagaimana Logika Saat Undangan Diterima (Acceptance)?
Saat kandidat mengeklik "Terima Jabatan", sistem mengeksekusi perpindahan dalam satu transaksi database (`DB::transaction`) untuk menjamin keamanan:
*   **Promosi (User Baru):** Kandidat (User B) diberikan *role* `headmaster` pada tabel `school_members` dan langsung mendapatkan akses penuh ke dasbor sekolah.
*   **Demosi (Kepsek Lama):** Kepala Sekolah lama (User A) diturunkan jabatannya menjadi `teacher` (Guru) dengan status **`is_active = false` (dinonaktifkan)**.
*   **Integritas Data Terjaga:** Mengapa diturunkan menjadi Guru non-aktif dan bukan dihapus? Tujuannya adalah menjaga rekam jejak. Jika Kepala Sekolah lama pernah melakukan pencatatan finansial atau absensi, data tersebut (beserta *foreign key*-nya) tidak akan error atau hilang (*Referential Integrity*).
*   **Logout Paksa:** Sistem otomatis mencabut (menghapus) *Personal Access Token* (Sanctum) milik Kepala Sekolah lama, sehingga ia akan ter-logout paksa dari sesi aktifnya seketika itu juga (*Separation of Duties*).

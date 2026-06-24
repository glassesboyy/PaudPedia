# Dokumentasi Alur Berlangganan (Upgrade & Downgrade) SIAKAD

Dokumen ini menjelaskan logika bisnis utama dari sistem paket berlangganan (Free vs Pro) untuk mengantisipasi pertanyaan teknis.

## 1. Bagaimana Logika Upgrade (Perpanjangan) Berjalan?
*   **Pembuatan Order:** Saat user menekan tombol "Upgrade/Perpanjang", sistem membuat *record* di tabel `subscription_orders` dengan status `pending` dan nominal harga (*amount*) dikunci (diambil dari pengaturan saat itu).
*   **Midtrans Integration:** Order ID dikirim ke Midtrans untuk mendapatkan `snap_token`. Jika ada transaksi `pending` sebelumnya, transaksi tersebut dibatalkan (Cancel/Expire) untuk menghindari duplikasi tagihan.
*   **Pembayaran Sukses (Webhook / Callback):** Ketika dibayar, Midtrans mengirimkan notifikasi. Sistem memperbarui status menjadi `paid`.
*   **Logika Stacking (Akumulasi Sisa Hari):** Jika user memperpanjang padahal **masih memiliki sisa masa aktif Pro**, durasi baru (misal: 1 bulan) akan **ditambahkan ke atas sisa hari tersebut** (*Grandfathering*). Hari sisa tidak akan hangus.

## 2. Bagaimana Nasib Akun dan Data Saat Downgrade (Pro -> Free)?
*   **Auto-Downgrade:** Sistem memiliki *Cron Job / Scheduler* (`downgrade_expired_schools`) yang berjalan setiap tengah malam. Jika `subscription_ended_at` sudah terlewati, status akun langsung turun ke paket `Free` (tanpa *grace period*).
*   **Integritas Data:** Data siswa dan guru yang sudah di-input selama masa Pro **TIDAK PERNAH DIHAPUS**, meskipun jumlahnya melebih limit paket Free.
*   **Core Module Lock (Soft Restriction):** Jika akun berada di paket Free dan data melebihi batas (misal: punya 100 siswa, padahal limit Free hanya 20), akun akan terkena **Pembatasan Fungsional (Lock)**.
    *   Pengguna **tidak bisa menambah, mengubah, atau melakukan transaksi operasional** (Absensi, SPP, Nilai, dll) akan diblokir oleh *middleware backend* (menghasilkan status `403 Forbidden`).
    *   Menu premium di *sidebar* akan disembunyikan.
    *   Sistem hanya berjalan dalam mode **Read-Only**.
*   **Cara Membuka Kunci (Unlock):** Untuk menggunakan sistem secara normal kembali, Kepala Sekolah **wajib** melakukan salah satu dari dua opsi ini:
    1.  Upgrade kembali ke paket Pro.
    2.  Menghapus/menonaktifkan siswa/guru hingga jumlahnya berada di bawah limit paket Free.

## 3. Sistem Peringatan (Warning System)
*   Sistem akan menghitung sisa hari secara otomatis (*frontend computing*).
*   **H-7 s/d H-4:** Kepala sekolah mendapat *banner* kuning peringatan.
*   **H-3 s/d H-1:** Kepala sekolah mendapat *banner* merah kritis peringatan agar sistem tidak sampai terkunci.
*   Hanya pengguna dengan *role* **Headmaster** (Kepala Sekolah) yang melihat *banner* ini dan tombol "Perpanjang". Guru tidak dapat melihat tagihan/peringatan.

## 4. Perubahan Harga oleh Admin
*   Harga `Pro` dan Limit `Free` ditarik secara dinamis dari `site_settings`. 
*   Jika Super Admin menaikkan harga langganan esok hari, riwayat pembayaran lama tidak akan terpengaruh karena harga (*amount*) di- *snapshot* ke dalam tabel `subscription_orders` saat transaksi dibentuk.

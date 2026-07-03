<x-mail::message>
# Selamat Datang, {{ $user->name }}!

Akun Anda telah didaftarkan sebagai **Orang Tua / Wali Murid** di **{{ $school->name }}** oleh Kepala Sekolah.

Berikut adalah kredensial login Anda untuk mengakses SIAKAD dan memantau perkembangan anak Anda:

- **Email:** {{ $user->email }}
- **Password:** `{{ $password }}`

Silakan login melalui tautan di bawah ini:

<x-mail::button :url="config('app.siakad_url') . '/login'">
Masuk ke SIAKAD
</x-mail::button>

Terima kasih,<br>
Tim {{ config('app.name') }}
</x-mail::message>

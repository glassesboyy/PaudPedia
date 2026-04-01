<x-mail::message>
# Selamat Datang, {{ $user->name }}!

Akun Anda telah didaftarkan sebagai **Guru** di **{{ $school->name }}** oleh Kepala Sekolah.

Berikut adalah kredensial login Anda untuk mengakses SIAKAD:

- **Email:** {{ $user->email }}
- **Password:** `{{ $password }}`

Silakan login melalui tautan di bawah ini:

<x-mail::button :url="config('app.siakad_url') . '/login'">
Masuk ke SIAKAD
</x-mail::button>

*Catatan: Demi keamanan, silakan ganti password Anda segera setelah berhasil login di menu Profil.*

Terima kasih,<br>
Tim {{ config('app.name') }}
</x-mail::message>

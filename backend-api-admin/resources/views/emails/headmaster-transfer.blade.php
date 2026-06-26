<x-mail::message>
# Undangan Menjadi Kepala Sekolah

Halo,

Anda telah diundang oleh **{{ $transferRequest->fromUser->name }}** untuk mengambil alih kepemilikan (menjadi Kepala Sekolah) untuk sekolah **{{ $transferRequest->school->name }}** di sistem SIAKAD Paudpedia.

Dengan menerima undangan ini, Anda akan memiliki hak akses penuh untuk mengelola data sekolah, tagihan, dan pengaturan sistem.

<x-mail::button :url="$frontEndUrl . '/transfer/accept/' . $transferRequest->token">
Tinjau Undangan
</x-mail::button>

*Catatan: Jika Anda tidak merasa mengharapkan undangan ini, abaikan saja email ini.*

Terima kasih,<br>
Tim {{ config('app.name') }}
</x-mail::message>

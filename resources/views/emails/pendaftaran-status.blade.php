{{-- resources/views/emails/pendaftaran-status.blade.php --}}

<x-mail::message>
{{-- Header kosong untuk menghilangkan logo default --}}
<x-slot:header>
</x-slot:header>

# Pemberitahuan Status Pendaftaran Magang

Kepada Yth.<br>
**{{ $pendaftaran->nama_pendaftar }}**

Terima kasih atas minat Anda untuk mengikuti program magang di {{ config('app.name') }}. Kami telah meninjau pendaftaran Anda dan dengan ini menyampaikan informasi sebagai berikut:

---

@if ($pendaftaran->status == 'approved')
<x-mail::panel>
## ✓ PENDAFTARAN DITERIMA

Selamat! Kami dengan senang hati menginformasikan bahwa pendaftaran magang Anda telah **disetujui**.
</x-mail::panel>

### Informasi Program Magang

<x-mail::table>
| Keterangan | Detail |
|:-----------|:-------|
| **Nama Peserta** | {{ $pendaftaran->nama_pendaftar }} |
| **Periode Magang** | {{ \Carbon\Carbon::parse($pendaftaran->tanggal_mulai)->format('d F Y') }} - {{ \Carbon\Carbon::parse($pendaftaran->tanggal_selesai)->format('d F Y') }} |
| **Status** | Diterima |
</x-mail::table>

@if ($pendaftaran->remarks)
### Catatan Penting

<x-mail::panel>
{{ $pendaftaran->remarks }}
</x-mail::panel>
@endif

### Langkah Selanjutnya

Silakan melakukan hal-hal berikut:
1. Hadir di kantor {{ config('app.name') }} pada tanggal **{{ \Carbon\Carbon::parse($pendaftaran->tanggal_mulai)->format('d F Y') }}** pukul 07.30 WIB
2. Berpakaian rapi dan sopan
3. Konfirmasi kehadiran Anda melalui tombol di bawah ini

<x-mail::button :url="route('daftar.konfirmasi', $pendaftaran)" color="success">
Konfirmasi Kehadiran
</x-mail::button>

@elseif ($pendaftaran->status == 'conditional')
<x-mail::panel>
## ⚠ PENDAFTARAN DISETUJUI BERSYARAT

Pendaftaran Anda telah kami terima dengan beberapa persyaratan tambahan yang perlu dipenuhi.
</x-mail::panel>

### Persyaratan yang Harus Dipenuhi

<x-mail::panel>
{{ $pendaftaran->remarks }}
</x-mail::panel>

### Tindak Lanjut

Mohon untuk:
1. Memenuhi persyaratan yang tercantum di atas
2. Menghubungi kami untuk konfirmasi pemenuhan persyaratan
3. Mengirimkan dokumen tambahan (jika diperlukan)

<x-mail::button :url="'https://wa.me/6282170003402'" color="primary">
Hubungi Kami via WhatsApp
</x-mail::button>

@elseif ($pendaftaran->status == 'rejected')
<x-mail::panel>
## ✕ PENDAFTARAN BELUM DAPAT DISETUJUI

Terima kasih atas antusiasme Anda untuk bergabung dalam program magang di {{ config('app.name') }}.
</x-mail::panel>

Setelah meninjau dengan saksama berkas pendaftaran Anda, dengan berat hati kami belum dapat menerima pendaftaran Anda pada periode ini.

### Informasi Tambahan

<x-mail::panel>
{{ $pendaftaran->remarks }}
</x-mail::panel>

Kami menghargai minat Anda dan Terimakasih telah mendaftar di sini 
@endif

---

### Informasi Kontak

Jika Anda memiliki pertanyaan atau memerlukan informasi lebih lanjut, silakan menghubungi kami:

- **Email:** bps3402@bps.go.id
- **WhatsApp:** [+6282170003402](https://wa.me/6282170003402)

<br>

Hormat kami,

**Tim Kepegawaian**<br>
{{ config('app.name') }}<br>
*Badan Pusat Statistik Kabupaten Bantul (Statistics Bantul)Jl. Jendral Gatot Subroto No. 3 Bantul*

---

<small style="color: #6c757d;">
Email ini dikirim secara otomatis. Mohon tidak membalas email ini. Untuk pertanyaan, silakan hubungi kontak yang tertera di atas.
</small>
</x-mail::message>
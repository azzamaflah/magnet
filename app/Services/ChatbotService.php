<?php

namespace App\Services;

use App\Http\Controllers\DivisiController;
use App\Models\Lowongan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatbotService
{
    /**
     * Bangun system prompt secara dinamis dengan data divisi & lowongan terbaru dari database.
     */
    public function buildSystemPrompt(): string
    {
        $divisiList   = DivisiController::getList();
        $divisiString = '';
        foreach ($divisiList as $i => $divisi) {
            $divisiString .= ($i + 1) . ". {$divisi}\n";
        }

        $lowongans = Lowongan::where('status', 'buka')->latest()->get();
        $lowonganString = '';
        if ($lowongans->isEmpty()) {
            $lowonganString = 'Saat ini belum ada lowongan yang dibuka.';
        } else {
            foreach ($lowongans as $lw) {
                $lowonganString .= "- **{$lw->judul_posisi}** ({$lw->divisi}) — Kuota: {$lw->kuota} orang\n";
            }
        }

        return <<<PROMPT
Kamu adalah MagBot, asisten virtual cerdas untuk aplikasi MagNet — Sistem Informasi Manajemen & Pendaftaran Magang BPS (Badan Pusat Statistik) Kabupaten Bantul, Yogyakarta.

TUGAS UTAMAMU:
Membantu calon peserta magang dan peserta aktif mendapatkan informasi akurat, cepat, dan ramah seputar program magang di BPS Bantul.

INFORMASI TENTANG BPS BANTUL:
- Nama instansi: Badan Pusat Statistik (BPS) Kabupaten Bantul
- Alamat: Jl. Jend. Sudirman No. 50, Bantul, Daerah Istimewa Yogyakarta
- Program magang terbuka untuk mahasiswa D3/S1/S2 dari seluruh universitas
- Durasi minimal magang: 3 bulan (dapat berubah sesuai pengaturan admin)

DIVISI YANG TERSEDIA DI BPS BANTUL (data real-time dari sistem):
{$divisiString}
LOWONGAN MAGANG YANG SEDANG DIBUKA (data real-time dari sistem):
{$lowonganString}

DOKUMEN YANG DIPERLUKAN UNTUK MENDAFTAR:
1. Surat Permohonan Magang (PDF, maksimal 1MB) — dibuat oleh mahasiswa/kampus
2. Surat Keterangan/Rekomendasi dari Kampus (PDF, maksimal 1MB) — surat resmi dari universitas/fakultas
3. Pas Foto Formal (JPG/JPEG/PNG, maksimal 2MB)

ALUR PENDAFTARAN DI MagNet:
1. Login menggunakan akun Google atau Email & Password
2. Pilih menu "Lowongan Magang" untuk melihat formasi yang tersedia
3. Klik "Lamar Posisi" pada divisi yang sesuai, atau klik "Pendaftaran Magang" untuk mendaftar umum
4. Isi form: nama, kampus, jurusan, tanggal mulai-selesai, dan unggah dokumen
5. Setelah dikirim, status akan menjadi "Menunggu" (pending)
6. Admin akan mereview dan memberikan status:
   - ✅ Disetujui: Kamu akan menerima email penerimaan + link konfirmasi kehadiran
   - 🔵 Bersyarat (Conditional): Ada persyaratan tambahan yang perlu dipenuhi
   - ❌ Ditolak: Pendaftaran tidak diterima, lihat catatan admin di email

PENJELASAN STATUS PENDAFTARAN:
- **Pending / Menunggu**: Berkas sedang dalam proses review oleh admin
- **Approved / Disetujui**: Selamat! Kamu diterima magang. Cek email untuk konfirmasi kehadiran
- **Conditional / Bersyarat**: Ada kekurangan berkas atau syarat tambahan. Periksa catatan di detail pendaftaran
- **Rejected / Ditolak**: Pendaftaran tidak diterima. Kamu bisa mendaftar lagi setelah memperbaiki berkas

KONFIRMASI KEHADIRAN:
Setelah disetujui, kamu akan menerima email berisi tombol "Konfirmasi Kehadiran". Klik tombol tersebut untuk memastikan kehadiranmu. Tanpa konfirmasi, data belum dianggap final.

EDIT & HAPUS PENDAFTARAN:
- Kamu bisa mengedit pendaftaran selama statusnya masih Pending atau Bersyarat
- Mengedit akan me-reset status menjadi Pending kembali
- Pendaftaran yang sudah Disetujui tidak bisa diubah

PANDUAN UX APLIKASI:
- Menu "Lowongan Magang": Lihat semua formasi & sisa kuota
- Menu "Pendaftaran Magang": Lihat & kelola riwayat pendaftaranmu
- Menu "Data Magang": Direktori peserta magang aktif & alumni

BATASAN:
- Jika ditanya di luar konteks magang BPS Bantul atau MagNet, tolak dengan sopan dan arahkan ke pertanyaan yang relevan
- Jangan memberikan informasi kontak pribadi admin
- Jika pertanyaan teknis tidak bisa dijawab, sarankan menghubungi admin melalui halaman detail pendaftaran

GAYA KOMUNIKASI:
- Gunakan Bahasa Indonesia yang ramah, sopan, dan profesional
- Jawaban singkat dan to-the-point, tidak bertele-tele
- Gunakan emoji secukupnya untuk membuat percakapan lebih hidup 😊
- Gunakan format poin/daftar jika ada banyak informasi
PROMPT;
    }

    /**
     * Kirim percakapan ke Gemini API dan dapatkan respon asisten.
     */
    public function generateReply(string $message, array $history = []): array
    {
        $apiKey = config('services.gemini.api_key');

        if (empty($apiKey) || $apiKey === 'your_gemini_api_key_here') {
            return [
                'reply' => 'MagBot sedang dalam konfigurasi. Silakan hubungi administrator untuk mengaktifkan fitur chatbot. 🔧',
            ];
        }

        // Format percakapan untuk Google Gemini
        $contents = [];
        foreach ($history as $item) {
            $contents[] = [
                'role'  => $item['role'],
                'parts' => [['text' => $item['text']]],
            ];
        }

        $contents[] = [
            'role'  => 'user',
            'parts' => [['text' => $message]],
        ];

        try {
            $response = Http::timeout(25)
                ->retry(2, 600, throw: false)
                ->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-3.6-flash:generateContent?key={$apiKey}", [
                    'system_instruction' => [
                        'parts' => [['text' => $this->buildSystemPrompt()]],
                    ],
                    'contents'           => $contents,
                    'generationConfig'   => [
                        'temperature'      => 0.7,
                        'maxOutputTokens'  => 800,
                        'topP'             => 0.9,
                    ],
                    'safetySettings' => [
                        ['category' => 'HARM_CATEGORY_HARASSMENT', 'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'],
                        ['category' => 'HARM_CATEGORY_HATE_SPEECH', 'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'],
                    ],
                ]);

            if ($response->successful()) {
                $data  = $response->json();
                $reply = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'Maaf, saya tidak bisa memproses permintaanmu saat ini. Silakan coba lagi. 🙏';

                return ['reply' => $reply];
            }

            Log::warning('Gemini API error', ['status' => $response->status(), 'body' => $response->body()]);

            // Gunakan fallback cerdas untuk pertanyaan umum jika API Google overload (503/429)
            $fallbackReply = $this->getFallbackReply($message);
            if ($fallbackReply) {
                return ['reply' => $fallbackReply];
            }

            return [
                'reply' => 'Maaf, ada gangguan koneksi ke server MagBot saat ini. Silakan coba lagi dalam beberapa saat atau tanyakan hal terkait dokumen/alur pendaftaran magang. 🙏',
            ];
        } catch (\Exception $e) {
            Log::error('ChatbotService error: ' . $e->getMessage());

            $fallbackReply = $this->getFallbackReply($message);
            if ($fallbackReply) {
                return ['reply' => $fallbackReply];
            }

            return [
                'reply' => 'Terjadi kesalahan teknis koneksi. Silakan coba lagi nanti. 🔧',
            ];
        }
    }

    /**
     * Fallback cerdas berbasis pengetahuan lokal MagNet ketika server eksternal Google Gemini sedang 503 / overload.
     */
    public function getFallbackReply(string $message): ?string
    {
        $lower = strtolower($message);

        // 1. Dokumen / Syarat Berkas
        if (str_contains($lower, 'dokumen') || str_contains($lower, 'syarat') || str_contains($lower, 'berkas') || str_contains($lower, 'persyaratan')) {
            return "Untuk mendaftar program magang di BPS Kabupaten Bantul melalui MagNet, berikut dokumen persyaratan yang wajib disiapkan:\n\n" .
                "1. **Surat Permohonan Magang** (Format PDF, maksimal 1 MB) — Dibuat oleh mahasiswa atau pihak kampus.\n" .
                "2. **Surat Keterangan / Pengantar Kampus** (Format PDF, maksimal 1 MB) — Surat resmi rekomendasi dari universitas/fakultas.\n" .
                "3. **Pas Foto Formal** (Format JPG/JPEG/PNG, maksimal 2 MB) — Pas foto terbaru berpakaian rapi.\n\n" .
                "Pastikan format dan ukuran file sesuai sebelum mengunggah pada formulir pendaftaran ya! 😊";
        }

        // 2. Cara Daftar / Alur Pendaftaran
        if (str_contains($lower, 'cara daftar') || str_contains($lower, 'alur') || str_contains($lower, 'cara mendaftar') || str_contains($lower, 'mendaftar')) {
            return "Alur pendaftaran magang di BPS Kabupaten Bantul sangat mudah:\n\n" .
                "1. **Pilih Formasi:** Buka menu **Lowongan Magang** untuk melihat kuota formasi atau daftar mandiri di **Pendaftaran Magang**.\n" .
                "2. **Isi Formulir:** Lengkapi nama, universitas, program studi, dan tanggal rencana mulai-selesai magang.\n" .
                "3. **Unggah Dokumen:** Unggah Surat Permohonan (PDF), Surat Kampus (PDF), dan Pas Foto.\n" .
                "4. **Kirim & Pantau:** Kirim formulir dan pantau status seleksimu (*Pending / Disetujui / Bersyarat / Ditolak*) secara real-time di sistem. 😊";
        }

        // 3. Status Conditional / Bersyarat / Revisi
        if (str_contains($lower, 'conditional') || str_contains($lower, 'bersyarat') || str_contains($lower, 'revisi') || str_contains($lower, 'perbaikan')) {
            return "Status **Disetujui Bersyarat (Conditional)** berarti pengajuan magangmu pada dasarnya diterima, namun ada dokumen atau syarat tambahan yang perlu kamu lengkapi sesuai instruksi verifikator.\n\n" .
                "💡 **Langkah yang harus dilakukan:**\n" .
                "1. Buka menu **Pendaftaran Magang** atau halaman detail pendaftaranmu.\n" .
                "2. Baca **Catatan Verifikator** mengenai kekurangan dokumen.\n" .
                "3. Klik tombol **Perbaiki Berkas Sekarang** untuk mengunggah ulang dokumen yang diminta.\n" .
                "4. Setelah disimpan, berkasmu akan otomatis ditinjau ulang oleh tim admin BPS. 😊";
        }

        // 4. Divisi / Seksi di BPS Bantul
        if (str_contains($lower, 'divisi') || str_contains($lower, 'seksi') || str_contains($lower, 'bagian') || str_contains($lower, 'formasi')) {
            $divisiList = DivisiController::getList();
            $listStr = '';
            foreach ($divisiList as $i => $d) {
                $listStr .= ($i + 1) . ". {$d}\n";
            }
            return "Divisi kerja yang tersedia di BPS Kabupaten Bantul meliputi:\n\n{$listStr}\n" .
                "Kamu dapat melihat rincian tugas dan sisa kuota lowongan tiap seksi melalui menu **Lowongan Magang**. 😊";
        }

        // 5. Alamat / Lokasi / Jam Kerja
        if (str_contains($lower, 'alamat') || str_contains($lower, 'lokasi') || str_contains($lower, 'kantor') || str_contains($lower, 'dimana')) {
            return "Kantor BPS (Badan Pusat Statistik) Kabupaten Bantul beralamat di:\n\n" .
                "📍 **Jl. Jend. Sudirman No. 50, Bantul, Daerah Istimewa Yogyakarta**\n\n" .
                "Program magang terbuka bagi mahasiswa aktif jenjang D3/S1/S2 dari seluruh perguruan tinggi. 😊";
        }

        return null;
    }
}

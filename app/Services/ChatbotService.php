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
            $response = Http::timeout(30)
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

            return [
                'reply' => 'Maaf, ada gangguan koneksi ke server MagBot. Silakan coba lagi dalam beberapa saat. 🙏',
            ];
        } catch (\Exception $e) {
            Log::error('ChatbotService error: ' . $e->getMessage());

            return [
                'reply' => 'Terjadi kesalahan teknis. Silakan coba lagi nanti. 🔧',
            ];
        }
    }
}

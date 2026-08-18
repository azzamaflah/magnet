<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    /**
     * System prompt yang mendefinisikan konteks MagBot untuk BPS Bantul.
     */
    private string $systemPrompt = <<<PROMPT
Kamu adalah MagBot, asisten virtual cerdas untuk aplikasi MagNet — Sistem Informasi Manajemen & Pendaftaran Magang BPS (Badan Pusat Statistik) Kabupaten Bantul, Yogyakarta.

TUGAS UTAMAMU:
Membantu calon peserta magang dan peserta aktif mendapatkan informasi akurat, cepat, dan ramah seputar program magang di BPS Bantul.

INFORMASI TENTANG BPS BANTUL:
- Nama instansi: Badan Pusat Statistik (BPS) Kabupaten Bantul
- Alamat: Jl. Jend. Sudirman No. 50, Bantul, Daerah Istimewa Yogyakarta
- Program magang terbuka untuk mahasiswa D3/S1/S2 dari seluruh universitas
- Durasi minimal magang: 3 bulan (dapat berubah sesuai pengaturan admin)

DIVISI YANG TERSEDIA DI BPS BANTUL:
1. Seksi IPDS (Integrasi Pengolahan & Diseminasi Statistik) — cocok untuk mahasiswa IT, Informatika, Sistem Informasi, Teknik Komputer
2. Seksi IPDS & Nerwilis — cocok untuk mahasiswa IT + Statistik
3. Seksi Statistik Sosial — cocok untuk mahasiswa Statistika, Sosiologi, Kesehatan Masyarakat
4. Seksi Statistik Distribusi — cocok untuk mahasiswa Ekonomi, Manajemen, Statistika
5. Seksi Statistik Produksi — cocok untuk mahasiswa Pertanian, Ekonomi, Statistika
6. Seksi Neraca Wilayah & Analisis Statistik (Nerwilis) — cocok untuk mahasiswa Ekonomi Pembangunan, Perencanaan Wilayah
7. Subbagian Umum — cocok untuk mahasiswa Administrasi, Manajemen, Hukum

DOKUMEN YANG DIPERLUKAN UNTUK MENDAFTAR:
1. Surat Permohonan Magang (PDF, maksimal 2MB) — dibuat oleh mahasiswa/kampus
2. Surat Keterangan/Rekomendasi dari Kampus (PDF, maksimal 2MB) — surat resmi dari universitas/fakultas
3. Pas Foto Formal (JPG/JPEG/PNG, maksimal 1MB)

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

    /**
     * Handle chatbot message from user.
     */
    public function chat(Request $request)
    {
        $request->validate([
            'message'  => 'required|string|max:1000',
            'history'  => 'nullable|array|max:20',
            'history.*.role'    => 'required|in:user,model',
            'history.*.text'    => 'required|string|max:2000',
        ]);

        $apiKey = config('services.gemini.api_key');

        if (empty($apiKey) || $apiKey === 'your_gemini_api_key_here') {
            return response()->json([
                'reply' => 'MagBot sedang dalam konfigurasi. Silakan hubungi administrator untuk mengaktifkan fitur chatbot. 🔧',
            ]);
        }

        // Bangun riwayat percakapan dalam format yang diterima Gemini
        $contents = [];

        // Tambahkan riwayat sebelumnya
        foreach ($request->input('history', []) as $item) {
            $contents[] = [
                'role'  => $item['role'],
                'parts' => [['text' => $item['text']]],
            ];
        }

        // Tambahkan pesan terbaru dari user
        $contents[] = [
            'role'  => 'user',
            'parts' => [['text' => $request->input('message')]],
        ];

        try {
            $response = Http::timeout(30)
                ->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-3.6-flash:generateContent?key={$apiKey}", [
                    'system_instruction' => [
                        'parts' => [['text' => $this->systemPrompt]],
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

                return response()->json(['reply' => $reply]);
            }

            Log::warning('Gemini API error', ['status' => $response->status(), 'body' => $response->body()]);

            return response()->json([
                'reply' => 'Maaf, ada gangguan koneksi ke server MagBot. Silakan coba lagi dalam beberapa saat. 🙏',
            ]);

        } catch (\Exception $e) {
            Log::error('ChatbotController error: ' . $e->getMessage());

            return response()->json([
                'reply' => 'Terjadi kesalahan teknis. Silakan coba lagi nanti. 🔧',
            ], 500);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Lowongan;
use Illuminate\Database\Seeder;

class LowonganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lowongans = [
            [
                'judul_posisi' => 'Junior Web & Application Developer',
                'divisi'       => 'Seksi IPDS (Integrasi Pengolahan & Diseminasi Statistik)',
                'deskripsi'    => 'Membantu pengembangan sistem informasi internal dan diseminasi data statistik BPS Kabupaten Bantul, maintenance aplikasi web, dan optimalisasi database.',
                'kualifikasi'  => 'Mahasiswa aktif S1/D4/D3 Teknik Informatika, Sistem Informasi, atau Ilmu Komputer. Memahami PHP/Laravel, JavaScript/Tailwind, Git, dan MySQL.',
                'kuota'        => 2,
                'status'       => 'buka',
            ],
            [
                'judul_posisi' => 'Data Analyst & Visualisasi Statistik',
                'divisi'       => 'Seksi IPDS & Nerwilis',
                'deskripsi'    => 'Melakukan pengolahan data mentah survei, analisis statistik deskriptif dan inferensial, serta pembuatan infografis / dashboard visualisasi data untuk publikasi BPS Bantul.',
                'kualifikasi'  => 'Mahasiswa aktif Statistika, Sains Data, Matematika, atau Ekonomi. Menguasai R/Python/SPSS, Excel tingkat lanjut, atau tools visualisasi data (Tableau/Power BI/Canva).',
                'kuota'        => 3,
                'status'       => 'buka',
            ],
            [
                'judul_posisi' => 'Pengolahan & Validasi Survei Statistik Sosial',
                'divisi'       => 'Seksi Statistik Sosial',
                'deskripsi'    => 'Mendukung tim dalam verifikasi dokumen survei sosial ekonomi (seperti Susenas, Sakernas, dan Potensi Desa), entri data, serta pembersihan anomali data lapangan.',
                'kualifikasi'  => 'Mahasiswa aktif Statistika, Sosiologi, Ekonomi Pembangunan, Manajemen, atau bidang terkait. Teliti, disiplin, dan mampu bekerja dengan dataset terstruktur.',
                'kuota'        => 2,
                'status'       => 'buka',
            ],
            [
                'judul_posisi' => 'Pengolahan Data Survei Distribusi & Jasa',
                'divisi'       => 'Seksi Statistik Distribusi',
                'deskripsi'    => 'Membantu pengolahan data statistik harga konsumen (inflasi), survei perdagangan besar, pariwisata, transportasi, dan perhotelan di wilayah Kabupaten Bantul.',
                'kualifikasi'  => 'Mahasiswa aktif Ekonomi, Manajemen, Statistika, atau Akuntansi. Memiliki pemahaman dasar mengenai indikator ekonomi dan harga pasar.',
                'kuota'        => 2,
                'status'       => 'buka',
            ],
            [
                'judul_posisi' => 'Administrasi Perkantoran & Kearsipan Digital',
                'divisi'       => 'Subbagian Umum',
                'deskripsi'    => 'Mendukung kelancaran operasional administrasi perkantoran BPS Bantul, tata kelola persuratan dinas digital, dokumentasi kepegawaian, dan pelayanan terpadu satu pintu (PST).',
                'kualifikasi'  => 'Mahasiswa aktif Administrasi Publik/Perkantoran, Manajemen, Ilmu Komunikasi, Kearsipan, atau Semua Jurusan. Menguasai Microsoft Office (Word, Excel) dan komunikasi publik yang baik.',
                'kuota'        => 2,
                'status'       => 'buka',
            ],
        ];

        foreach ($lowongans as $item) {
            Lowongan::firstOrCreate(
                ['judul_posisi' => $item['judul_posisi']],
                $item
            );
        }
    }
}

<?php

namespace Tests\Feature;

use App\Http\Controllers\DivisiController;
use App\Models\Lowongan;
use App\Models\Magang;
use App\Models\Pendaftaran;
use App\Models\Setting;
use App\Models\User;
use App\Services\ChatbotService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MagNetFunctionalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Support YEAR() and MONTH() functions in SQLite in-memory test database
        if (DB::getDriverName() === 'sqlite') {
            DB::connection()->getPdo()->sqliteCreateFunction('YEAR', function ($date) {
                return $date ? (int)date('Y', strtotime($date)) : null;
            });
            DB::connection()->getPdo()->sqliteCreateFunction('MONTH', function ($date) {
                return $date ? (int)date('m', strtotime($date)) : null;
            });
        }

        // Seed default setting
        Setting::create([
            'key'   => 'min_durasi_magang',
            'value' => '3',
            'type'  => 'bulan',
        ]);

        Storage::fake('public');
        Mail::fake();
    }

    /** 1. Login dengan kredensial valid */
    public function test_1_login_berhasil_dengan_kredensial_valid(): void
    {
        $user = User::factory()->create([
            'email'    => 'peserta@bpsbantul.test',
            'password' => Hash::make('password123'),
            'role'     => 'user',
        ]);

        $response = $this->post('/login', [
            'email'    => 'peserta@bpsbantul.test',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    /** 1b. Login gagal dengan password salah */
    public function test_1b_login_gagal_dengan_kredensial_salah(): void
    {
        User::factory()->create([
            'email'    => 'peserta@bpsbantul.test',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/login', [
            'email'    => 'peserta@bpsbantul.test',
            'password' => 'passwordsalah',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    /** 1c. Login gagal dengan kolom kosong */
    public function test_1c_login_validasi_kolom_kosong(): void
    {
        $response = $this->post('/login', [
            'email'    => '',
            'password' => '',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors(['email', 'password']);
    }

    /** 4. Proteksi hak akses AdminMiddleware (role user ditolak) */
    public function test_4_proteksi_hak_akses_admin_dari_user_biasa(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get('/settings');
        $response->assertStatus(403);
    }

    /** 4b. Proteksi rute dari tamu yang belum login */
    public function test_4b_proteksi_rute_dari_tamu_belum_login(): void
    {
        $response = $this->get('/pendaftaran-magang');
        $response->assertRedirect('/login');
    }

    /** 5. Katalog lowongan magang dapat diakses */
    public function test_5_katalog_lowongan_magang_dapat_diakses(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        Lowongan::create([
            'judul_posisi'       => 'Statistisi Muda',
            'divisi'             => 'Seksi IPDS',
            'kuota'              => 5,
            'kualifikasi'        => 'Mahasiswa aktif Statistika / IT',
            'deskripsi'          => 'Membantu pengolahan dan diseminasi data statistik.',
            'status'             => 'buka',
            'tanggal_buka'       => now()->subDays(5),
            'tanggal_tutup'      => now()->addDays(20),
        ]);

        $response = $this->actingAs($user)->get('/lowongan');
        $response->assertOk();
        $response->assertSee('Statistisi Muda');
        $response->assertSee('Seksi IPDS');
    }

    /** 6. Pendaftaran magang berhasil dengan berkas lengkap */
    public function test_6_pendaftaran_magang_berhasil_dengan_data_lengkap(): void
    {
        $user = User::factory()->create(['role' => 'user', 'email' => 'calon@bpsbantul.test']);

        $pdfPermohonan = UploadedFile::fake()->create('permohonan.pdf', 500, 'application/pdf');
        $pdfKampus     = UploadedFile::fake()->create('kampus.pdf', 500, 'application/pdf');
        $foto          = UploadedFile::fake()->image('pasfoto.jpg', 400, 600)->size(800);

        $response = $this->actingAs($user)->post('/pendaftaran-magang', [
            'nama_pendaftar'   => 'Ahmad Zaki',
            'asal_kampus'      => 'Universitas Gadjah Mada',
            'prodi'            => 'Teknologi Informasi',
            'tanggal_mulai'    => Carbon::now()->addDays(5)->format('Y-m-d'),
            'tanggal_selesai'  => Carbon::now()->addDays(5)->addMonths(4)->format('Y-m-d'),
            'surat_permohonan' => $pdfPermohonan,
            'surat_kampus'     => $pdfKampus,
            'pas_foto'         => $foto,
        ]);

        $response->assertRedirect(route('daftar.index'));
        $this->assertDatabaseHas('pendaftarans', [
            'user_id'        => $user->id,
            'nama_pendaftar' => 'Ahmad Zaki',
            'status'         => 'pending',
        ]);
    }

    /** 6b. Validasi durasi magang minimal (< 3 bulan ditolak) */
    public function test_6b_pendaftaran_magang_validasi_durasi_minimal(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->post('/pendaftaran-magang', [
            'nama_pendaftar'   => 'Ahmad Zaki',
            'asal_kampus'      => 'Universitas Gadjah Mada',
            'prodi'            => 'Teknologi Informasi',
            'tanggal_mulai'    => Carbon::now()->addDays(5)->format('Y-m-d'),
            'tanggal_selesai'  => Carbon::now()->addDays(20)->format('Y-m-d'), // Hanya 15 hari
        ]);

        $response->assertSessionHasErrors('tanggal_selesai');
    }

    /** Helper untuk membuat instance Pendaftaran dengan atribut wajib lengkap */
    protected function createPendaftaran(array $attributes = []): Pendaftaran
    {
        $defaultUser = $attributes['user_id'] ?? User::factory()->create(['role' => 'user'])->id;

        return Pendaftaran::create(array_merge([
            'user_id'          => $defaultUser,
            'nama_pendaftar'   => 'Peserta Magang Test',
            'email'            => 'peserta.test@bpsbantul.test',
            'asal_kampus'      => 'Universitas Gadjah Mada',
            'prodi'            => 'Teknologi Informasi',
            'tanggal_mulai'    => now()->addDays(10)->format('Y-m-d'),
            'tanggal_selesai'  => now()->addMonths(4)->format('Y-m-d'),
            'surat_permohonan' => 'surat_permohonan/dummy_surat.pdf',
            'surat_kampus'     => 'surat_kampus/dummy_kampus.pdf',
            'pas_foto'         => 'pas_foto/dummy_foto.jpg',
            'status'           => 'pending',
            'remarks'          => null,
        ], $attributes));
    }

    /** 7. Pelacak status pendaftaran dapat diakses oleh pemilik */
    public function test_7_pelacak_status_pendaftaran_dapat_diakses_oleh_pemilik(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $pendaftaran = $this->createPendaftaran([
            'user_id'         => $user->id,
            'nama_pendaftar'  => 'Peserta Magang Test',
            'email'           => $user->email,
            'asal_kampus'     => 'UNY',
            'prodi'           => 'Pendidikan Teknik Informatika',
            'status'          => 'pending',
        ]);

        $response = $this->actingAs($user)->get('/pendaftaran-magang');
        $response->assertOk();
        $response->assertSee('Peserta Magang Test');
        $response->assertSee('Menunggu');
    }

    /** 7b. Calon peserta dapat membatalkan pendaftaran saat status pending */
    public function test_7b_user_dapat_membatalkan_pendaftaran_status_pending(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $pendaftaran = $this->createPendaftaran([
            'user_id'         => $user->id,
            'nama_pendaftar'  => 'Peserta Batal',
            'email'           => $user->email,
            'status'          => 'pending',
        ]);

        $response = $this->actingAs($user)->delete("/pendaftaran-magang/{$pendaftaran->id}");
        $response->assertRedirect(route('daftar.index'));
        $this->assertDatabaseMissing('pendaftarans', ['id' => $pendaftaran->id]);
    }

    /** 8. Calon peserta dapat mengakses form revisi saat bersyarat (conditional) */
    public function test_8_user_dapat_mengakses_form_revisi_berkas_bersyarat(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $pendaftaran = $this->createPendaftaran([
            'user_id'         => $user->id,
            'nama_pendaftar'  => 'Peserta Revisi',
            'email'           => $user->email,
            'status'          => 'conditional',
            'remarks'         => 'Surat kampus kurang stempel basah',
        ]);

        $response = $this->actingAs($user)->get("/pendaftaran-magang/{$pendaftaran->id}/edit");
        $response->assertOk();
        $response->assertSee('Disetujui Bersyarat');
        $response->assertSee('Surat kampus kurang stempel basah');
    }

    /** 9. Konfirmasi kehadiran peserta magang berstatus approved */
    public function test_9_konfirmasi_kehadiran_peserta_berstatus_approved(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $pendaftaran = $this->createPendaftaran([
            'user_id'         => $user->id,
            'nama_pendaftar'  => 'Peserta Konfirmasi',
            'email'           => $user->email,
            'status'          => 'approved',
        ]);

        $response = $this->get("/konfirmasi/{$pendaftaran->id}");
        $response->assertOk();

        $pendaftaran->refresh();
        $this->assertNotNull($pendaftaran->konfirmasi_at);
    }

    /** 10. Admin dapat melihat daftar verifikasi pendaftaran */
    public function test_10_admin_dapat_melihat_verifikasi_pendaftaran(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->createPendaftaran([
            'user_id'         => $admin->id,
            'nama_pendaftar'  => 'Pelamar Magang BPS',
            'email'           => 'pelamar@test.com',
            'status'          => 'pending',
        ]);

        $response = $this->actingAs($admin)->get('/pendaftaran-magang');
        $response->assertOk();
        $response->assertSee('Pelamar Magang BPS');
    }

    /** 11. Admin menyetujui pendaftaran dan otomatis membuat data magang */
    public function test_11_admin_menyetujui_pendaftaran_dan_membuat_data_magang(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $mhs   = User::factory()->create(['role' => 'user', 'email' => 'mhs@test.com']);

        // Fake foto file di storage
        $fotoPath = 'pas_foto/test_foto.jpg';
        Storage::disk('public')->put($fotoPath, 'dummy content');

        $pendaftaran = $this->createPendaftaran([
            'user_id'         => $mhs->id,
            'nama_pendaftar'  => 'Budi Santoso',
            'email'           => $mhs->email,
            'pas_foto'        => $fotoPath,
            'status'          => 'pending',
        ]);

        $response = $this->actingAs($admin)->post("/pendaftaran-magang/{$pendaftaran->id}/status", [
            'status'  => 'approved',
            'remarks' => 'Selamat berkas lengkap dan diterima',
        ]);

        $response->assertRedirect(route('daftar.index'));

        $pendaftaran->refresh();
        $this->assertEquals('approved', $pendaftaran->status);

        // Data Magang otomatis terbentuk
        $this->assertDatabaseHas('magangs', [
            'user_id' => $mhs->id,
            'nama'    => 'Budi Santoso',
            'status'  => 'belum_aktif',
        ]);
    }

    /** 11b. Admin memberikan status bersyarat dengan catatan evaluasi */
    public function test_11b_admin_memberikan_status_bersyarat_dengan_catatan(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $mhs   = User::factory()->create(['role' => 'user', 'email' => 'mhs2@test.com']);

        $pendaftaran = $this->createPendaftaran([
            'user_id'         => $mhs->id,
            'nama_pendaftar'  => 'Citra Lestari',
            'email'           => $mhs->email,
            'status'          => 'pending',
        ]);

        $response = $this->actingAs($admin)->post("/pendaftaran-magang/{$pendaftaran->id}/status", [
            'status'  => 'conditional',
            'remarks' => 'Mohon unggah surat rekomendasi fakultas bertanda tangan dekan',
        ]);

        $response->assertRedirect(route('daftar.index'));

        $pendaftaran->refresh();
        $this->assertEquals('conditional', $pendaftaran->status);
        $this->assertEquals('Mohon unggah surat rekomendasi fakultas bertanda tangan dekan', $pendaftaran->remarks);
    }

    /** 12. Admin dapat mengelola formasi lowongan magang */
    public function test_12_admin_dapat_mengelola_formasi_lowongan_magang(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post('/lowongan', [
            'judul_posisi'       => 'Pengolah Data Sensus',
            'divisi'             => 'Seksi Produksi',
            'kuota'              => 4,
            'kualifikasi'        => 'D3/S1 Statistika/IT',
            'deskripsi'          => 'Pengolahan data survei statistik produksi tahunan.',
            'status'             => 'buka',
            'tanggal_buka'       => now()->format('Y-m-d'),
            'tanggal_tutup'      => now()->addDays(30)->format('Y-m-d'),
        ]);

        $response->assertRedirect(route('lowongan.index'));
        $this->assertDatabaseHas('lowongans', [
            'judul_posisi' => 'Pengolah Data Sensus',
            'divisi'       => 'Seksi Produksi',
            'kuota'        => 4,
        ]);
    }

    /** 13. Admin dapat mengelola master divisi */
    public function test_13_admin_dapat_mengelola_master_divisi(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->from('/divisi')->post('/divisi', [
            'nama_divisi' => 'Seksi Evaluasi Statistik Khusus',
        ]);

        $response->assertRedirect('/divisi');
        $this->assertTrue(collect(DivisiController::getList())->contains('Seksi Evaluasi Statistik Khusus'));
    }

    /** 14. Admin dapat mengubah pengaturan durasi minimal magang */
    public function test_14_admin_dapat_mengubah_pengaturan_durasi_minimal(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->from('/settings')->post('/settings', [
            'value' => 2,
            'type'  => 'bulan',
        ]);

        $response->assertRedirect('/settings');
        $this->assertDatabaseHas('settings', [
            'key'   => 'min_durasi_magang',
            'value' => '2',
        ]);
    }

    /** 15. Direktori data magang dapat dilihat oleh pengguna */
    public function test_15_direktori_data_magang_dapat_dilihat(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        Magang::create([
            'user_id'         => $user->id,
            'nama'            => 'Alumni Magang BPS',
            'email'           => 'alumni@test.com',
            'asal_kampus'     => 'UGM',
            'prodi'           => 'Ilmu Komunikasi',
            'tanggal_mulai'   => now()->subMonths(6),
            'tanggal_selesai' => now()->subMonths(2),
            'status'          => 'selesai',
            'periode_bulan'   => 4,
            'is_hidden'       => false,
        ]);

        $response = $this->actingAs($user)->get('/magang');
        $response->assertOk();
        $response->assertSee('Alumni Magang BPS');
    }

    /** 16. Dashboard analitik admin menyajikan statistik ringkasan */
    public function test_16_dashboard_admin_menyajikan_statistik_ringkasan(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/dashboard');
        $response->assertOk();
        $response->assertSee('Dashboard Admin');
        $response->assertSee('Menunggu Review');
        $response->assertSee('Sedang Magang');
    }

    /** 17. Layanan asisten cerdas MagBot merespon pertanyaan umum */
    public function test_17_layanan_asisten_cerdas_magbot_merespon_pertanyaan(): void
    {
        $service = app(ChatbotService::class);
        $reply = $service->getFallbackReply('Dokumen apa saja yang perlu disiapkan?');

        $this->assertNotNull($reply);
        $this->assertStringContainsString('Surat Permohonan Magang', $reply);
        $this->assertStringContainsString('Surat Keterangan / Pengantar Kampus', $reply);
        $this->assertStringContainsString('Pas Foto Formal', $reply);
    }

    /** 18. Logout akun berhasil dan sesi dibersihkan */
    public function test_18_logout_berhasil_dan_sesi_dibersihkan(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}

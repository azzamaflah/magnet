<?php

namespace App\Services;

use App\Mail\PendaftaranStatusMail;
use App\Models\Magang;
use App\Models\Pendaftaran;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PendaftaranService
{
    public function __construct(
        protected FileUploadService $fileService
    ) {}

    /**
     * Dapatkan daftar pendaftaran dengan filter, paginasi, dan status counters.
     */
    public function getFilteredPendaftarans(User $user, array $filters): array
    {
        $selectedStatus     = $filters['status'] ?? 'all';
        $selectedLowongan   = $filters['lowongan_id'] ?? 'all';
        $selectedKampus     = $filters['kampus'] ?? 'all';
        $selectedKonfirmasi = $filters['konfirmasi'] ?? 'all';
        $selectedYear       = $filters['year'] ?? 'all';
        $selectedMonth      = $filters['month'] ?? 'all';
        $search             = $filters['search'] ?? null;
        $sort               = $filters['sort'] ?? 'latest';

        $baseQuery = $user->isAdmin()
            ? Pendaftaran::with('lowongan')
            : Pendaftaran::with('lowongan')->where('user_id', $user->id);

        // Hitung Tab Status Counters
        $statusCounts = (clone $baseQuery)
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        $counts = [
            'all'         => (clone $baseQuery)->count(),
            'pending'     => $statusCounts->get('pending', 0),
            'approved'    => $statusCounts->get('approved', 0),
            'conditional' => $statusCounts->get('conditional', 0),
            'rejected'    => $statusCounts->get('rejected', 0),
        ];

        // Query Utama dengan Filter
        $query = clone $baseQuery;

        if ($selectedStatus !== 'all') {
            $query->where('status', $selectedStatus);
        }

        if ($selectedLowongan !== 'all') {
            if ($selectedLowongan === 'umum') {
                $query->whereNull('lowongan_id');
            } else {
                $query->where('lowongan_id', $selectedLowongan);
            }
        }

        if ($selectedKampus !== 'all') {
            $query->where('asal_kampus', $selectedKampus);
        }

        if ($selectedKonfirmasi === 'confirmed') {
            $query->whereNotNull('konfirmasi_at');
        } elseif ($selectedKonfirmasi === 'unconfirmed') {
            $query->where('status', 'approved')->whereNull('konfirmasi_at');
        }

        if ($selectedYear !== 'all') {
            $query->whereYear('created_at', $selectedYear);
        }

        if ($selectedMonth !== 'all') {
            $query->whereMonth('created_at', $selectedMonth);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_pendaftar', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('asal_kampus', 'like', '%' . $search . '%')
                    ->orWhere('prodi', 'like', '%' . $search . '%');
            });
        }

        if ($sort === 'oldest') {
            $query->oldest('created_at');
        } elseif ($sort === 'name_asc') {
            $query->orderBy('nama_pendaftar', 'asc');
        } elseif ($sort === 'name_desc') {
            $query->orderBy('nama_pendaftar', 'desc');
        } else {
            $query->latest('created_at');
        }

        $pendaftarans = $query->paginate(10)->withQueryString();

        // Opsi Filter
        $kampusList = (clone $baseQuery)->distinct()->pluck('asal_kampus')->filter();
        $yearsList  = (clone $baseQuery)
            ->selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->filter();

        return [
            'pendaftarans'       => $pendaftarans,
            'counts'             => $counts,
            'kampusList'         => $kampusList,
            'yearsList'          => $yearsList,
            'selectedStatus'     => $selectedStatus,
            'selectedLowongan'   => $selectedLowongan,
            'selectedKampus'     => $selectedKampus,
            'selectedKonfirmasi' => $selectedKonfirmasi,
            'selectedYear'       => $selectedYear,
            'selectedMonth'      => $selectedMonth,
            'search'             => $search,
            'sort'               => $sort,
        ];
    }

    /**
     * Validasi durasi magang terhadap batas minimal di tabel Setting.
     */
    public function validateDuration(string $startDate, string $endDate): ?string
    {
        $setting    = Setting::where('key', 'min_durasi_magang')->first();
        $minDurasi  = (int) ($setting->value ?? 3);
        $tipeDurasi = $setting->type ?? 'bulan';

        $mulai   = Carbon::parse($startDate);
        $selesai = Carbon::parse($endDate);

        if ($tipeDurasi === 'bulan') {
            $durasiUser = $mulai->diffInMonths($selesai);
            if ($durasiUser < $minDurasi) {
                return "Durasi magang minimal adalah {$minDurasi} bulan. Pilihan Anda saat ini adalah {$durasiUser} bulan.";
            }
        } elseif ($tipeDurasi === 'hari') {
            $durasiUser = $mulai->diffInDays($selesai);
            if ($durasiUser < $minDurasi) {
                return "Durasi magang minimal adalah {$minDurasi} hari. Pilihan Anda saat ini adalah {$durasiUser} hari.";
            }
        }

        return null;
    }

    /**
     * Simpan pendaftaran baru dan upload berkas pendukung.
     */
    public function createRegistration(User $user, array $validated, array $files): Pendaftaran
    {
        $validated['user_id'] = $user->id;
        $validated['status']  = 'pending';

        if (!empty($files['surat_permohonan'])) {
            $validated['surat_permohonan'] = $this->fileService->upload($files['surat_permohonan'], 'surat_permohonan');
        }

        if (!empty($files['surat_kampus'])) {
            $validated['surat_kampus'] = $this->fileService->upload($files['surat_kampus'], 'surat_kampus');
        }

        if (!empty($files['pas_foto'])) {
            $validated['pas_foto'] = $this->fileService->upload($files['pas_foto'], 'pas_foto');
        }

        return Pendaftaran::create($validated);
    }

    /**
     * Update pendaftaran yang ada (replace file jika ada baru dan reset status).
     */
    public function updateRegistration(Pendaftaran $pendaftaran, array $validated, array $files): Pendaftaran
    {
        if (!empty($files['surat_permohonan'])) {
            $validated['surat_permohonan'] = $this->fileService->replace(
                $pendaftaran->surat_permohonan,
                $files['surat_permohonan'],
                'surat_permohonan'
            );
        }

        if (!empty($files['surat_kampus'])) {
            $validated['surat_kampus'] = $this->fileService->replace(
                $pendaftaran->surat_kampus,
                $files['surat_kampus'],
                'surat_kampus'
            );
        }

        if (!empty($files['pas_foto'])) {
            $validated['pas_foto'] = $this->fileService->replace(
                $pendaftaran->pas_foto,
                $files['pas_foto'],
                'pas_foto'
            );
        }

        // Reset status ke pending saat pemohon mengedit kembali berkas
        $validated['status']        = 'pending';
        $validated['remarks']       = null;
        $validated['konfirmasi_at'] = null;

        $pendaftaran->update($validated);
        return $pendaftaran;
    }

    /**
     * Update status persetujuan pendaftar oleh Admin (Approved/Rejected/Conditional).
     * Melakukan DB Transaction, copy foto, pembuatan data Magang, dan pengiriman email.
     */
    public function updateApprovalStatus(Pendaftaran $pendaftaran, string $status, ?string $remarks): array
    {
        return DB::transaction(function () use ($pendaftaran, $status, $remarks) {
            if ($status === 'approved') {
                if ($pendaftaran->status === 'approved') {
                    return [
                        'success' => true,
                        'message' => 'Pendaftar ini sudah disetujui sebelumnya.',
                    ];
                }

                // Copy Foto ke folder foto_magang
                $sourcePath = $pendaftaran->pas_foto;
                $fileName   = Str::afterLast($sourcePath, '/');
                $newPath    = 'foto_magang/' . $fileName;

                if ($this->fileService->exists($sourcePath)) {
                    $this->fileService->copy($sourcePath, $newPath);
                } else {
                    throw new \Exception('File foto pendaftar tidak ditemukan di storage. Approval dibatalkan.');
                }

                // Hitung durasi bulan
                $tanggal_mulai   = Carbon::parse($pendaftaran->tanggal_mulai);
                $tanggal_selesai = Carbon::parse($pendaftaran->tanggal_selesai);

                $periode_bulan = (int) round($tanggal_mulai->diffInDays($tanggal_selesai) / 30);
                if ($periode_bulan === 0) {
                    $periode_bulan = 1;
                }

                // Buat data Magang baru
                Magang::create([
                    'user_id'         => $pendaftaran->user_id,
                    'lowongan_id'     => $pendaftaran->lowongan_id,
                    'nama'            => $pendaftaran->nama_pendaftar,
                    'email'           => $pendaftaran->email,
                    'asal_kampus'     => $pendaftaran->asal_kampus,
                    'prodi'           => $pendaftaran->prodi,
                    'tanggal_mulai'   => $pendaftaran->tanggal_mulai,
                    'tanggal_selesai' => $pendaftaran->tanggal_selesai,
                    'foto'            => $newPath,
                    'status'          => 'belum_aktif',
                    'periode_bulan'   => $periode_bulan,
                ]);

                $pendaftaran->status  = 'approved';
                $pendaftaran->remarks = $remarks;
                $pendaftaran->save();

                // Kirim notifikasi email
                Mail::to($pendaftaran->email)->send(new PendaftaranStatusMail($pendaftaran));

                return [
                    'success' => true,
                    'message' => 'Pendaftaran disetujui! Data telah dipindahkan ke daftar peserta magang & notifikasi email terkirim.',
                ];
            }

            // Status Rejected / Conditional
            $pendaftaran->status  = $status;
            $pendaftaran->remarks = $remarks;
            $pendaftaran->save();

            Mail::to($pendaftaran->email)->send(new PendaftaranStatusMail($pendaftaran));

            $message = $status === 'rejected'
                ? 'Pendaftaran ditolak. Notifikasi email terkirim.'
                : 'Pendaftaran disetujui dengan syarat. Notifikasi email terkirim.';

            return [
                'success' => true,
                'message' => $message,
            ];
        });
    }

    /**
     * Konfirmasi kehadiran peserta magang dari email link.
     */
    public function confirmAttendance(Pendaftaran $pendaftaran): array
    {
        $isError = false;

        if ($pendaftaran->status !== 'approved') {
            $message = 'Konfirmasi gagal. Status pendaftaran Anda belum disetujui.';
            $isError = true;
        } elseif ($pendaftaran->konfirmasi_at) {
            $message = 'Anda sudah melakukan konfirmasi pada tanggal ' . Carbon::parse($pendaftaran->konfirmasi_at)->format('d F Y, H:i') . ' WIB.';
        } else {
            $pendaftaran->konfirmasi_at = now();
            $pendaftaran->save();
            $message = 'Terima kasih! Kehadiran Anda telah berhasil dikonfirmasi.';
        }

        return [
            'pendaftaran' => $pendaftaran,
            'message'     => $message,
            'isError'     => $isError,
        ];
    }

    /**
     * Hapus pendaftaran dan seluruh berkas fisiknya.
     */
    public function deleteRegistration(Pendaftaran $pendaftaran): void
    {
        $files = array_filter([
            $pendaftaran->surat_permohonan,
            $pendaftaran->surat_kampus,
            $pendaftaran->pas_foto,
        ]);

        $this->fileService->deleteMultiple($files);
        $pendaftaran->delete();
    }

    /**
     * Download berkas terlampir.
     */
    public function downloadDocument(Pendaftaran $pendaftaran, string $field): StreamedResponse
    {
        $allowedFields = ['surat_permohonan', 'surat_kampus', 'pas_foto'];

        if (!in_array($field, $allowedFields)) {
            abort(404, 'Jenis berkas tidak valid.');
        }

        $filePath = $pendaftaran->{$field};
        if (!$filePath) {
            abort(404, 'Berkas tidak ditemukan.');
        }

        $cleanName = $pendaftaran->nama_pendaftar . '_' . $field;
        return $this->fileService->download($filePath, $cleanName);
    }
}

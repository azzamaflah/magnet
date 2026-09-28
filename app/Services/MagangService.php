<?php

namespace App\Services;

use App\Models\Magang;
use App\Models\Pendaftaran;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class MagangService
{
    public function __construct(
        protected FileUploadService $fileService
    ) {}

    /**
     * Dapatkan daftar data magang dengan filter dan hak akses visibilitas.
     */
    public function getFilteredMagangs(?User $user, array $filters): array
    {
        $query = Magang::with('lowongan');

        // Jika bukan Admin, hanya tampilkan data magang yang TIDAK disembunyikan (atau milik user sendiri)
        if (!$user || !$user->isAdmin()) {
            $query->where(function ($q) use ($user) {
                $q->where('is_hidden', false);
                if ($user) {
                    $q->orWhere('user_id', $user->id);
                }
            });
        } else {
            // Filter visibilitas untuk Admin
            $selectedVisibility = $filters['visibility'] ?? 'all';
            if ($selectedVisibility === 'visible') {
                $query->where('is_hidden', false);
            } elseif ($selectedVisibility === 'hidden') {
                $query->where('is_hidden', true);
            }
        }

        $availableYears = (clone $query)
            ->selectRaw('YEAR(tanggal_mulai) as year')
            ->distinct()
            ->whereNotNull('tanggal_mulai')
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->filter();

        $selectedYear       = $filters['year'] ?? 'all';
        $selectedVisibility = $filters['visibility'] ?? 'all';
        $search             = $filters['search'] ?? null;
        $kampus             = $filters['kampus'] ?? null;

        if ($search) {
            $query->where('nama', 'like', '%' . $search . '%');
        }

        if ($kampus) {
            $query->where('asal_kampus', $kampus);
        }

        if ($selectedYear !== 'all') {
            $query->whereYear('tanggal_mulai', $selectedYear);
        }

        $kampusList = Magang::select('asal_kampus')
            ->distinct()
            ->orderBy('asal_kampus', 'asc')
            ->pluck('asal_kampus')
            ->filter();

        $magangs = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return [
            'magangs'            => $magangs,
            'kampusList'         => $kampusList,
            'availableYears'     => $availableYears,
            'selectedYear'       => $selectedYear,
            'selectedVisibility' => $selectedVisibility,
        ];
    }

    /**
     * Simpan data peserta magang baru.
     * Catatan: periode_bulan & status dihitung otomatis oleh Magang::boot() saat save().
     */
    public function createMagang(array $validated, $fotoFile): Magang
    {
        if ($fotoFile) {
            $validated['foto'] = $this->fileService->upload($fotoFile, 'foto_magang');
        }

        return Magang::create($validated);
    }

    /**
     * Update data peserta magang dan sinkronisasi foto dengan pendaftaran terkait.
     * Catatan: periode_bulan & status dihitung otomatis oleh Magang::boot() saat save().
     */
    public function updateMagang(Magang $magang, array $validated, $fotoFile): Magang
    {
        return DB::transaction(function () use ($magang, $validated, $fotoFile) {
            if ($fotoFile) {
                $oldMagangPhoto = $magang->foto;
                $newPhotoPath   = $this->fileService->upload($fotoFile, 'foto_magang');
                $validated['foto'] = $newPhotoPath;

                // Sinkronkan foto ke pendaftaran approved yang terkait jika ada
                $pendaftaran = Pendaftaran::where('user_id', $magang->user_id)
                    ->where('status', 'approved')
                    ->first();
                if ($pendaftaran) {
                    $oldPendaftaranPhoto = $pendaftaran->pas_foto;
                    $pendaftaran->update(['pas_foto' => $newPhotoPath]);

                    if ($oldPendaftaranPhoto && $oldPendaftaranPhoto !== $oldMagangPhoto) {
                        $this->fileService->delete($oldPendaftaranPhoto);
                    }
                }

                $this->fileService->delete($oldMagangPhoto);
            }

            $magang->update($validated);
            return $magang;
        });
    }

    /**
     * Toggle status visibilitas data magang (sembunyikan / tampilkan di direktori publik).
     */
    public function toggleVisibility(Magang $magang): bool
    {
        $magang->is_hidden = !$magang->is_hidden;
        $magang->save();

        return $magang->is_hidden;
    }

    /**
     * Hapus data magang beserta foto fisiknya dan perbarui status pendaftaran terkait.
     */
    public function deleteMagang(Magang $magang): void
    {
        DB::transaction(function () use ($magang) {
            if ($magang->foto) {
                $this->fileService->delete($magang->foto);
            }

            // Fix Bug #1: cari pendaftaran yang approved milik user ini,
            // bukan sekadar ->first() yang bisa mengambil pendaftaran yang salah.
            $pendaftaran = Pendaftaran::where('user_id', $magang->user_id)
                ->where('status', 'approved')
                ->first();

            if ($pendaftaran) {
                $pendaftaran->update([
                    'status'  => 'rejected',
                    'remarks' => 'Data dihapus dari daftar peserta Magang aktif oleh Admin.',
                ]);
            }

            $magang->delete();
        });
    }
}

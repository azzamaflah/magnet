<?php

namespace App\Services;

use App\Models\Lowongan;
use App\Models\Magang;
use App\Models\Pendaftaran;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardAnalyticsService
{
    /**
     * Dapatkan seluruh data statistik analitik untuk dashboard Admin.
     */
    public function getAdminDashboardData(string $selectedYear = 'all'): array
    {
        $currentYear   = Carbon::now()->year;
        $jsInitialYear = ($selectedYear === 'all') ? $currentYear : (int) $selectedYear;

        // Ambil tahun-tahun yang tersedia dari Magang dan Pendaftaran
        $yearsMagangMulai      = Magang::select(DB::raw('YEAR(tanggal_mulai) as year'))->distinct()->pluck('year');
        $yearsMagangSelesai    = Magang::select(DB::raw('YEAR(tanggal_selesai) as year'))->distinct()->pluck('year');
        $yearsPendaftarMulai   = Pendaftaran::select(DB::raw('YEAR(tanggal_mulai) as year'))->distinct()->pluck('year');
        $yearsPendaftarCreated = Pendaftaran::select(DB::raw('YEAR(created_at) as year'))->distinct()->pluck('year');

        $availableYears = $yearsMagangMulai
            ->merge($yearsMagangSelesai)
            ->merge($yearsPendaftarMulai)
            ->merge($yearsPendaftarCreated)
            ->push($currentYear)
            ->unique()
            ->whereNotNull()
            ->sortDesc()
            ->values();

        // Status Pendaftar (Donut/Pie Chart)
        $pendaftarQuery = Pendaftaran::query();
        if ($selectedYear !== 'all') {
            $pendaftarQuery->whereYear('created_at', $selectedYear);
        }

        $statusCounts = (clone $pendaftarQuery)
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        $statusChartData = [
            $statusCounts->get('pending', 0),
            $statusCounts->get('approved', 0),
            $statusCounts->get('rejected', 0),
            $statusCounts->get('conditional', 0),
        ];

        // Tren Pendaftaran & Magang per Bulan (Jan - Des)
        $chartLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $pendaftaranPerBulan = array_fill(1, 12, 0);
        $magangPerBulan      = array_fill(1, 12, 0);

        if ($selectedYear !== 'all') {
            $pendaftarData = Pendaftaran::whereYear('created_at', $selectedYear)
                ->select(DB::raw('MONTH(created_at) as month'), DB::raw('COUNT(*) as count'))
                ->groupBy('month')
                ->pluck('count', 'month');

            $magangData = Magang::whereYear('tanggal_mulai', $selectedYear)
                ->select(DB::raw('MONTH(tanggal_mulai) as month'), DB::raw('COUNT(*) as count'))
                ->groupBy('month')
                ->pluck('count', 'month');
        } else {
            $pendaftarData = Pendaftaran::select(DB::raw('MONTH(created_at) as month'), DB::raw('COUNT(*) as count'))
                ->groupBy('month')
                ->pluck('count', 'month');

            $magangData = Magang::select(DB::raw('MONTH(tanggal_mulai) as month'), DB::raw('COUNT(*) as count'))
                ->groupBy('month')
                ->pluck('count', 'month');
        }

        foreach ($pendaftarData as $month => $count) {
            $pendaftaranPerBulan[$month] = $count;
        }
        foreach ($magangData as $month => $count) {
            $magangPerBulan[$month] = $count;
        }

        $pendaftaranChartData = array_values($pendaftaranPerBulan);
        $magangChartData      = array_values($magangPerBulan);

        // Peserta Aktif Saat Ini
        $now = Carbon::now();
        $activeUsers = Magang::with('lowongan')
            ->whereDate('tanggal_mulai', '<=', $now)
            ->whereDate('tanggal_selesai', '>=', $now)
            ->orderBy('tanggal_selesai', 'asc')
            ->get()
            ->map(function ($magang) use ($now) {
                $start = Carbon::parse($magang->tanggal_mulai);
                $end   = Carbon::parse($magang->tanggal_selesai);

                $duration      = $start->diffInMonths($end);
                $remainingDays = $now->diffInDays($end, false);

                $magang->nama_pendaftar  = $magang->nama;
                $magang->remaining_days  = floor($remainingDays);
                $magang->duration_months = ($duration < 1) ? '< 1' : $duration;

                return $magang;
            });

        // Statistik Asal Kampus
        $nowDate = $now->toDateString();
        $kampusQuery = Magang::query();

        if ($selectedYear !== 'all') {
            $kampusQuery->where(function ($q) use ($selectedYear) {
                $q->whereYear('tanggal_mulai', $selectedYear)
                  ->orWhereYear('tanggal_selesai', $selectedYear);
            });
        }

        $kampusStats = (clone $kampusQuery)
            ->select(
                'asal_kampus',
                DB::raw("SUM(CASE WHEN tanggal_mulai <= '{$nowDate}' AND tanggal_selesai >= '{$nowDate}' THEN 1 ELSE 0 END) as sedang_magang"),
                DB::raw("SUM(CASE WHEN tanggal_selesai < '{$nowDate}' THEN 1 ELSE 0 END) as selesai_magang"),
                DB::raw("SUM(CASE WHEN tanggal_mulai > '{$nowDate}' THEN 1 ELSE 0 END) as belum_mulai"),
                DB::raw("COUNT(*) as total_peserta")
            )
            ->whereNotNull('asal_kampus')
            ->where('asal_kampus', '!=', '')
            ->groupBy('asal_kampus')
            ->orderByDesc('total_peserta')
            ->get();

        $kampusChartLabels = $kampusStats->pluck('asal_kampus')->toArray();
        $kampusSedangData  = $kampusStats->pluck('sedang_magang')->map(fn ($v) => (int) $v)->toArray();
        $kampusSelesaiData = $kampusStats->pluck('selesai_magang')->map(fn ($v) => (int) $v)->toArray();

        return [
            'role'                   => 'admin',
            'statusChartData'        => $statusChartData,
            'pendaftaranChartLabels' => $chartLabels,
            'pendaftaranChartData'   => $pendaftaranChartData,
            'magangChartLabels'      => $chartLabels,
            'magangChartData'        => $magangChartData,
            'availableYears'         => $availableYears,
            'selectedYear'           => $selectedYear,
            'calendarEvents'         => [],
            'activeUsers'            => $activeUsers,
            'jsInitialYear'          => $jsInitialYear,
            'totalLowonganBuka'      => Lowongan::where('status', 'buka')->count(),
            'totalAlumni'            => Magang::whereDate('tanggal_selesai', '<', $nowDate)->count(),
            'totalKampusCount'       => Magang::distinct('asal_kampus')->whereNotNull('asal_kampus')->where('asal_kampus', '!=', '')->count('asal_kampus'),
            'totalPendaftarAll'      => Pendaftaran::count(),
            'kampusStats'            => $kampusStats,
            'kampusChartLabels'      => $kampusChartLabels,
            'kampusSedangData'       => $kampusSedangData,
            'kampusSelesaiData'      => $kampusSelesaiData,
        ];
    }

    /**
     * Dapatkan data dashboard untuk User biasa.
     */
    public function getUserDashboardData(User $user): array
    {
        $pendaftaran = Pendaftaran::with('lowongan')
            ->where('user_id', $user->id)
            ->latest()
            ->first();

        $now = Carbon::now();
        $activeUsers = Pendaftaran::where('status', 'approved')
            ->whereDate('tanggal_mulai', '<=', $now)
            ->whereDate('tanggal_selesai', '>=', $now)
            ->orderBy('tanggal_selesai', 'asc')
            ->get()
            ->map(function ($p) use ($now) {
                $start = Carbon::parse($p->tanggal_mulai);
                $end   = Carbon::parse($p->tanggal_selesai);

                $duration      = $start->diffInMonths($end);
                $remainingDays = $now->diffInDays($end, false);

                $p->remaining_days  = floor($remainingDays);
                $p->duration_months = ($duration < 1) ? '< 1' : $duration;

                return $p;
            });

        $lowongans = Lowongan::withCount([
            'pendaftarans as disetujui_count' => function ($q) {
                $q->where('status', 'approved');
            }
        ])
        ->where('status', 'buka')
        ->latest()
        ->get();

        return [
            'role'        => 'user',
            'pendaftaran' => $pendaftaran,
            'activeUsers' => $activeUsers,
            'lowongans'   => $lowongans,
        ];
    }
}

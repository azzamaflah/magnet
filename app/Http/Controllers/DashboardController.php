<?php

namespace App\Http\Controllers;

use App\Models\Lowongan;
use App\Models\Magang;
use App\Models\Pendaftaran;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $role = Auth::user()->role;

        // ===========================================
        //  JIKA USER ADALAH ADMIN
        // ===========================================
        if ($role === 'admin') {
            $currentYear = Carbon::now()->year;
            $selectedYear = $request->input('year', 'all'); 
            $jsInitialYear = ($selectedYear == 'all') ? $currentYear : (int)$selectedYear;
            
            // Mengambil semua tahun yang tersedia dari Magang dan Pendaftaran
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

            // Status Pendaftar
            $pendaftarQuery = Pendaftaran::query();
            if ($selectedYear != 'all') {
                $pendaftarQuery->whereYear('created_at', $selectedYear);
            }
            $statusCounts = (clone $pendaftarQuery)
                ->select('status', DB::raw('COUNT(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status');

            $totalPending     = $statusCounts->get('pending', 0);
            $totalApproved    = $statusCounts->get('approved', 0);
            $totalRejected    = $statusCounts->get('rejected', 0);
            $totalConditional = $statusCounts->get('conditional', 0);
            
            $statusChartData = [$totalPending, $totalApproved, $totalRejected, $totalConditional];

            // Data Pendaftaran & Magang per Bulan (Jan - Des)
            $pendaftaranChartLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            $pendaftaranPerBulan    = array_fill(1, 12, 0);
            $magangPerBulan         = array_fill(1, 12, 0);
            
            if ($selectedYear != 'all') {
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
            
            // Peserta Aktif Saat Ini (dari data Magang aktif)
            $now = Carbon::now();
            $activeUsers = Magang::with('lowongan')
                ->whereDate('tanggal_mulai', '<=', $now)
                ->whereDate('tanggal_selesai', '>=', $now)
                ->orderBy('tanggal_selesai', 'asc')
                ->get();

            $activeUsers = $activeUsers->map(function ($magang) use ($now) {
                $start = Carbon::parse($magang->tanggal_mulai);
                $end   = Carbon::parse($magang->tanggal_selesai);

                $duration      = $start->diffInMonths($end);
                $remainingDays = $now->diffInDays($end, false);
                
                $magang->nama_pendaftar  = $magang->nama;
                $magang->remaining_days  = floor($remainingDays);
                $magang->duration_months = ($duration < 1) ? '< 1' : $duration;
                
                return $magang;
            });

            // Statistik Kampus (Sedang Magang vs Selesai)
            $nowDate = Carbon::now()->toDateString();
            $kampusQuery = Magang::query();

            if ($selectedYear != 'all') {
                $kampusQuery->where(function($q) use ($selectedYear) {
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

            $kampusChartLabels  = $kampusStats->pluck('asal_kampus')->toArray();
            $kampusSedangData   = $kampusStats->pluck('sedang_magang')->map(fn($v) => (int)$v)->toArray();
            $kampusSelesaiData  = $kampusStats->pluck('selesai_magang')->map(fn($v) => (int)$v)->toArray();

            $totalLowonganBuka  = Lowongan::where('status', 'buka')->count();
            $totalAlumni        = Magang::whereDate('tanggal_selesai', '<', $nowDate)->count();
            $totalKampusCount   = Magang::distinct('asal_kampus')->whereNotNull('asal_kampus')->where('asal_kampus', '!=', '')->count('asal_kampus');
            $totalPendaftarAll  = Pendaftaran::count();

            return view('dashboard', [
                'role'                   => 'admin',
                'statusChartData'        => $statusChartData,
                'pendaftaranChartLabels' => $pendaftaranChartLabels,
                'pendaftaranChartData'   => $pendaftaranChartData,
                'magangChartLabels'      => $pendaftaranChartLabels, 
                'magangChartData'        => $magangChartData, 
                'availableYears'         => $availableYears,
                'selectedYear'           => $selectedYear,
                'calendarEvents'         => [],
                'activeUsers'            => $activeUsers,
                'jsInitialYear'          => $jsInitialYear,
                'totalLowonganBuka'      => $totalLowonganBuka,
                'totalAlumni'            => $totalAlumni,
                'totalKampusCount'       => $totalKampusCount,
                'totalPendaftarAll'      => $totalPendaftarAll,
                'kampusStats'            => $kampusStats,
                'kampusChartLabels'      => $kampusChartLabels,
                'kampusSedangData'       => $kampusSedangData,
                'kampusSelesaiData'      => $kampusSelesaiData,
            ]);

        // ===========================================
        //  JIKA USER ADALAH USER BIASA
        // ===========================================
        } elseif ($role === 'user') {
            
            $pendaftaran = Pendaftaran::with('lowongan')
                ->where('user_id', Auth::id())
                ->latest() 
                ->first(); 

            // Peserta Aktif
            $activeUsers = Pendaftaran::where('status', 'approved')
                ->whereDate('tanggal_mulai', '<=', Carbon::now())
                ->whereDate('tanggal_selesai', '>=', Carbon::now())
                ->orderBy('tanggal_selesai', 'asc')
                ->get();

            $activeUsers = $activeUsers->map(function ($user) {
                $start = Carbon::parse($user->tanggal_mulai);
                $end   = Carbon::parse($user->tanggal_selesai);
                $now   = Carbon::now();

                $duration      = $start->diffInMonths($end);
                $remainingDays = $now->diffInDays($end, false);
                
                $user->remaining_days  = floor($remainingDays);
                $user->duration_months = ($duration < 1) ? '< 1' : $duration;
                
                return $user;
            });

            // Ambil lowongan magang yang sedang buka
            $lowongans = Lowongan::withCount([
                'pendaftarans as disetujui_count' => function ($q) {
                    $q->where('status', 'approved');
                }
            ])
            ->where('status', 'buka')
            ->latest()
            ->get();
            
            return view('dashboard', [
                'role'        => 'user', 
                'pendaftaran' => $pendaftaran, 
                'activeUsers' => $activeUsers,
                'lowongans'   => $lowongans,
            ]);
        }

        Auth::logout();
        return redirect()->route('login');
    }
}
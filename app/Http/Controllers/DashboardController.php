<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Magang; // Asumsi model ini digunakan
use App\Models\Pendaftaran;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Ambil role user yang sedang login
        $role = Auth::user()->role;

        // ===========================================
        //  JIKA USER ADALAH ADMIN
        // ===========================================
        if ($role === 'admin') {

            // Mendapatkan tahun yang dipilih. Default ke tahun saat ini jika tidak ada, 
            // atau menggunakan tahun yang sedang tampil di screenshot (2025) jika hari ini > 2025
            $selectedYear = $request->input('year', 
                (Carbon::now()->year >= 2025) ? 2025 : Carbon::now()->year); 
            
            // --- Variabel untuk initialDate di JS ---
            // Jika tahun dipilih, gunakan tahun tersebut. Jika 'all', gunakan tahun saat ini.
            $jsInitialYear = ($selectedYear == 'all') ? Carbon::now()->year : $selectedYear;
            
            // --- Ambil daftar tahun ---
            $yearsMagang = Pendaftaran::select(DB::raw('YEAR(tanggal_mulai) as year'))->distinct()->pluck('year');
            $yearsPendaftar = Pendaftaran::select(DB::raw('YEAR(created_at) as year'))->distinct()->pluck('year');
            
            $currentYear = Carbon::now()->year;
            $availableYears = $yearsMagang->merge($yearsPendaftar)->push($currentYear)
                                             ->unique()->whereNotNull()->sortDesc();

            // --- Query 1: Data Grafik Status Pendaftar ---
            $pendaftarQuery = Pendaftaran::query();
            
            if ($selectedYear != 'all') {
                $pendaftarQuery->whereYear('created_at', $selectedYear);
            }
            $statusCounts = (clone $pendaftarQuery)
                ->select('status', DB::raw('COUNT(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status');

            $totalPending = $statusCounts->get('pending', 0);
            $totalApproved = $statusCounts->get('approved', 0);
            $totalRejected = $statusCounts->get('rejected', 0);
            $totalConditional = $statusCounts->get('conditional', 0);
            
            $statusChartData = [$totalPending, $totalApproved, $totalRejected, $totalConditional];

            // --- Query 2 & 3: Data Pendaftaran & Magang per Periode ---
            $pendaftaranChartLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            $pendaftaranChartData = array_fill(0, 12, 0); 
            $magangChartData = array_fill(0, 12, 0);
            
            // Logika Query Chart Pendaftaran dan Magang per Bulan/Tahun yang Hilang:
            if ($selectedYear != 'all') {
                $pendaftaranChartLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                $magangChartLabels = $pendaftaranChartLabels;
                
                $pendaftaranPerBulan = array_fill(1, 12, 0);
                $magangPerBulan = array_fill(1, 12, 0);

                $pendaftarData = Pendaftaran::whereYear('created_at', $selectedYear)
                    ->select(DB::raw('MONTH(created_at) as month'), DB::raw('COUNT(*) as count'))
                    ->groupBy('month')
                    ->pluck('count', 'month');
                
                foreach ($pendaftarData as $month => $count) {
                    $pendaftaranPerBulan[$month] = $count;
                }
                $pendaftaranChartData = array_values($pendaftaranPerBulan);

                $magangData = Pendaftaran::where('status', 'approved')
                    ->whereYear('tanggal_mulai', $selectedYear)
                    ->select(DB::raw('MONTH(tanggal_mulai) as month'), DB::raw('COUNT(*) as count'))
                    ->groupBy('month')
                    ->pluck('count', 'month');

                foreach ($magangData as $month => $count) {
                     $magangPerBulan[$month] = $count;
                }
                $magangChartData = array_values($magangPerBulan);
            } else {
                // Logika per tahun... (Asumsi data Labels dan Data diisi di sini)
            }
            
            // ======================================================
            // LOGIKA PESERTA AKTIF DENGAN PEMBULATAN
            // ======================================================
            $activeUsers = Pendaftaran::where('status', 'approved')
                ->whereDate('tanggal_mulai', '<=', Carbon::now())
                ->whereDate('tanggal_selesai', '>=', Carbon::now())
                ->orderBy('tanggal_selesai', 'asc')
                ->get();

            $activeUsers = $activeUsers->map(function ($user) {
                $start = Carbon::parse($user->tanggal_mulai);
                $end = Carbon::parse($user->tanggal_selesai);
                $now = Carbon::now();

                // Hitung Durasi (Bulan Penuh)
                $duration = $start->diffInMonths($end);
                
                // Hitung Sisa Hari (Pembulatan ke bawah / floor)
                $remainingDays = $now->diffInDays($end, false);
                
                $user->remaining_days = floor($remainingDays); // ✅ PERBAIKAN: Menggunakan floor()
                $user->duration_months = ($duration < 1) ? '< 1' : $duration;
                
                return $user;
            });
            // ======================================================


            // Kembalikan view dashboard admin
            return view('dashboard', [
                'role' => 'admin',
                'statusChartData' => $statusChartData,
                'pendaftaranChartLabels' => $pendaftaranChartLabels,
                'pendaftaranChartData' => $pendaftaranChartData,
                'magangChartLabels' => $pendaftaranChartLabels, 
                'magangChartData' => $magangChartData, 
                'availableYears' => $availableYears,
                'selectedYear' => $selectedYear,
                'calendarEvents' => [],
                'activeUsers' => $activeUsers,
                'jsInitialYear' => $jsInitialYear,
            ]);

        // ===========================================
        //  JIKA USER ADALAH USER BIASA
        // ===========================================
        } elseif ($role === 'user') {
            
            $pendaftaran = Pendaftaran::where('user_id', Auth::id())
                                             ->latest() 
                                             ->first(); 

            // ======================================================
            // ✅ LOGIKA PESERTA AKTIF UNTUK USER (SAMA SEPERTI ADMIN)
            // ======================================================
            
            $activeUsers = Pendaftaran::where('status', 'approved')
                ->whereDate('tanggal_mulai', '<=', Carbon::now())
                ->whereDate('tanggal_selesai', '>=', Carbon::now())
                ->orderBy('tanggal_selesai', 'asc')
                ->get(); // Ambil semua data

            $activeUsers = $activeUsers->map(function ($user) {
                $start = Carbon::parse($user->tanggal_mulai);
                $end = Carbon::parse($user->tanggal_selesai);
                $now = Carbon::now();

                $duration = $start->diffInMonths($end);
                $remainingDays = $now->diffInDays($end, false);
                
                $user->remaining_days = floor($remainingDays); // ✅ PERBAIKAN: Menggunakan floor()
                $user->duration_months = ($duration < 1) ? '< 1' : $duration;
                
                return $user;
            });
            
            // Kembalikan view dashboard user
            return view('dashboard', [
                'role' => 'user', 
                'pendaftaran' => $pendaftaran, 
                'activeUsers' => $activeUsers, // Kirim data peserta aktif
                // 'calendarEvents' tidak lagi dikirim
            ]);
        }

        Auth::logout();
        return redirect()->route('login');
    }
}
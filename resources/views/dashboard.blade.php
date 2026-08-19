{{-- resources/views/dashboard.blade.php --}}
<x-main-layout>

    {{-- =========================================== --}}
    {{-- BAGIAN 1: KUMPULAN STYLES (KONDISIONAL) --}}
    {{-- =========================================== --}}
    @push('styles')
        @if($role == 'admin')
            <style>
                .kpi-card {
                    background: rgba(42, 42, 42, 0.6);
                    backdrop-filter: blur(16px);
                    -webkit-backdrop-filter: blur(16px);
                    border: 1px solid rgba(58, 58, 58, 0.8);
                    border-radius: 16px;
                    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                }
                .kpi-card:hover {
                    border-color: rgba(217, 119, 87, 0.5);
                    transform: translateY(-2px);
                    box-shadow: 0 12px 24px -6px rgba(0, 0, 0, 0.3);
                }
                .chart-card {
                    background: rgba(42, 42, 42, 0.6);
                    backdrop-filter: blur(16px);
                    -webkit-backdrop-filter: blur(16px);
                    border: 1px solid rgba(58, 58, 58, 0.8);
                    border-radius: 16px;
                    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.2);
                }
                .pulse-dot {
                    display: inline-block;
                    width: 8px;
                    height: 8px;
                    border-radius: 50%;
                    background-color: #22c55e;
                    box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7);
                    animation: pulseLive 2s infinite;
                }
                @keyframes pulseLive {
                    0% {
                        transform: scale(0.95);
                        box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7);
                    }
                    70% {
                        transform: scale(1);
                        box-shadow: 0 0 0 8px rgba(34, 197, 94, 0);
                    }
                    100% {
                        transform: scale(0.95);
                        box-shadow: 0 0 0 0 rgba(34, 197, 94, 0);
                    }
                }
                .ranking-badge-1 {
                    background: linear-gradient(135deg, #f59e0b, #d97706);
                    color: white;
                }
                .ranking-badge-2 {
                    background: linear-gradient(135deg, #94a3b8, #64748b);
                    color: white;
                }
                .ranking-badge-3 {
                    background: linear-gradient(135deg, #b45309, #78350f);
                    color: white;
                }
                .ranking-badge-default {
                    background: rgba(255, 255, 255, 0.1);
                    color: #e2e8f0;
                }
                .quick-btn {
                    display: inline-flex;
                    align-items: center;
                    gap: 0.5rem;
                    padding: 0.6rem 1rem;
                    border-radius: 10px;
                    font-size: 0.8125rem;
                    font-weight: 600;
                    transition: all 0.2s ease;
                    border: 1px solid rgba(255, 255, 255, 0.1);
                    background: rgba(255, 255, 255, 0.05);
                    color: #e2e8f0;
                    text-decoration: none;
                }
                .quick-btn:hover {
                    background: rgba(217, 119, 87, 0.2);
                    border-color: rgba(217, 119, 87, 0.4);
                    color: #ffffff;
                    transform: translateY(-1px);
                }
            </style>
        @elseif($role == 'user')
            <style>
                .status-badge {
                    display: inline-flex; align-items: center; padding: 0.5rem 1rem;
                    border-radius: 9999px; font-weight: 600; font-size: 0.875rem;
                }
            </style>
        @endif
    @endpush


    {{-- =========================================== --}}
    {{-- BAGIAN 2: KUMPULAN SCRIPTS (KONDISIONAL) --}}
    {{-- =========================================== --}}
    @push('scripts')
        @if($role == 'admin')
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const pendaftaranLabels = @json($pendaftaranChartLabels);
                    const pendaftaranData   = @json($pendaftaranChartData);
                    const magangData        = @json($magangChartData);
                    const statusData        = @json($statusChartData);
                    const kampusLabels      = @json($kampusChartLabels ?? []);
                    const kampusSedang      = @json($kampusSedangData ?? []);
                    const kampusSelesai     = @json($kampusSelesaiData ?? []);

                    function isLightMode() {
                        return document.documentElement.classList.contains('light-theme');
                    }

                    function applyChartTheme() {
                        const light = isLightMode();
                        Chart.defaults.color = light ? '#475569' : '#9ca3af';
                        Chart.defaults.borderColor = light ? 'rgba(0, 0, 0, 0.06)' : 'rgba(255, 255, 255, 0.08)';
                    }

                    applyChartTheme();

                    const charts = [];

                    // 1. GRAFIK TREN GABUNGAN: PENDAFTARAN & PESERTA MAGANG
                    const ctxTren = document.getElementById('trenChart');
                    if (ctxTren) {
                        const c1 = new Chart(ctxTren, {
                            type: 'line',
                            data: {
                                labels: pendaftaranLabels,
                                datasets: [
                                    {
                                        label: 'Pendaftaran Masuk',
                                        data: pendaftaranData,
                                        borderColor: '#d97757',
                                        backgroundColor: 'rgba(217, 119, 87, 0.15)',
                                        borderWidth: 2.5,
                                        tension: 0.35,
                                        fill: true,
                                        pointBackgroundColor: '#d97757',
                                        pointRadius: 4,
                                        pointHoverRadius: 6
                                    },
                                    {
                                        label: 'Peserta Mulai Magang',
                                        data: magangData,
                                        borderColor: '#3b82f6',
                                        backgroundColor: 'rgba(59, 130, 246, 0.15)',
                                        borderWidth: 2.5,
                                        tension: 0.35,
                                        fill: true,
                                        pointBackgroundColor: '#3b82f6',
                                        pointRadius: 4,
                                        pointHoverRadius: 6
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        position: 'top',
                                        labels: {
                                            boxWidth: 12,
                                            padding: 14,
                                            font: { size: 12, weight: '500' }
                                        }
                                    },
                                    tooltip: {
                                        padding: 10,
                                        callbacks: {
                                            label: function(context) {
                                                return ' ' + context.dataset.label + ': ' + context.raw + ' orang';
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        ticks: { stepSize: 1, precision: 0 }
                                    },
                                    x: {
                                        grid: { display: false }
                                    }
                                }
                            }
                        });
                        charts.push(c1);
                    }

                    // 2. GRAFIK STATUS PENDAFTAR (DOUGHNUT)
                    const ctxStatus = document.getElementById('statusChart');
                    if (ctxStatus) {
                        const c2 = new Chart(ctxStatus, {
                            type: 'doughnut',
                            data: {
                                labels: ['Menunggu Review', 'Disetujui', 'Ditolak', 'Bersyarat'],
                                datasets: [{
                                    label: 'Jumlah Berkas',
                                    data: statusData,
                                    backgroundColor: [
                                        'rgba(245, 158, 11, 0.85)', 
                                        'rgba(34, 197, 94, 0.85)', 
                                        'rgba(239, 68, 68, 0.85)', 
                                        'rgba(59, 130, 246, 0.85)'
                                    ],
                                    borderColor: isLightMode() ? '#ffffff' : '#2a2a2a',
                                    borderWidth: 3,
                                    hoverOffset: 4
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                cutout: '68%',
                                plugins: {
                                    legend: {
                                        position: 'bottom',
                                        labels: {
                                            boxWidth: 12,
                                            padding: 12,
                                            font: { size: 11 }
                                        }
                                    },
                                    tooltip: {
                                        callbacks: {
                                            label: function(context) {
                                                return ' ' + context.label + ': ' + context.raw + ' berkas';
                                            }
                                        }
                                    }
                                }
                            }
                        });
                        charts.push(c2);
                    }

                    // 3. GRAFIK KAMPUS (HORIZONTAL GROUPED BAR)
                    const ctxKampus = document.getElementById('kampusChart');
                    if (ctxKampus) {
                        const c3 = new Chart(ctxKampus, {
                            type: 'bar',
                            data: {
                                labels: kampusLabels,
                                datasets: [
                                    {
                                        label: 'Sedang Aktif Magang',
                                        data: kampusSedang,
                                        backgroundColor: 'rgba(34, 197, 94, 0.8)',
                                        borderColor: 'rgba(34, 197, 94, 1)',
                                        borderWidth: 1,
                                        borderRadius: 6,
                                        barPercentage: 0.8,
                                        categoryPercentage: 0.8
                                    },
                                    {
                                        label: 'Selesai Magang',
                                        data: kampusSelesai,
                                        backgroundColor: 'rgba(59, 130, 246, 0.8)',
                                        borderColor: 'rgba(59, 130, 246, 1)',
                                        borderWidth: 1,
                                        borderRadius: 6,
                                        barPercentage: 0.8,
                                        categoryPercentage: 0.8
                                    }
                                ]
                            },
                            options: {
                                indexAxis: 'y',
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        position: 'top',
                                        labels: {
                                            boxWidth: 12,
                                            padding: 12,
                                            font: { size: 12, weight: '500' }
                                        }
                                    },
                                    tooltip: {
                                        callbacks: {
                                            label: function(context) {
                                                return ' ' + context.dataset.label + ': ' + context.raw + ' mahasiswa';
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    x: {
                                        beginAtZero: true,
                                        ticks: { stepSize: 1, precision: 0 }
                                    },
                                    y: {
                                        ticks: { font: { size: 12 } },
                                        grid: { display: false }
                                    }
                                }
                            }
                        });
                        charts.push(c3);
                    }

                    // Re-render theme
                    window.addEventListener('magnet-theme-changed', function(e) {
                        applyChartTheme();
                        charts.forEach(chart => {
                            if (chart) {
                                if (chart.config.type === 'doughnut') {
                                    chart.data.datasets[0].borderColor = (e.detail.theme === 'light') ? '#ffffff' : '#2a2a2a';
                                }
                                chart.update();
                            }
                        });
                    });
                });
            </script>
        @endif
    @endpush


    {{-- =========================================== --}}
    {{-- BAGIAN 3: TAMPILAN UNTUK ADMIN (HTML) --}}
    {{-- =========================================== --}}
    @if($role == 'admin')

        <div class="claude-container">

            {{-- Header Section & Filter Tahun --}}
            <div class="border-b border-[#3a3a3a] header-section">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 py-5">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <div>
                            <div class="flex items-center gap-3">
                                <h2 class="claude-title text-2xl sm:text-3xl text-white">
                                    {{ __('Dashboard Admin') }}
                                </h2>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-500/15 border border-green-500/30 text-green-400">
                                    <span class="pulse-dot"></span>
                                    <span>Live Monitoring</span>
                                </span>
                            </div>
                            <p class="text-xs sm:text-sm text-gray-400 mt-1">
                                Panel pemantauan program & statistik magang BPS Kabupaten Bantul
                            </p>
                        </div>

                        {{-- FORM FILTER TAHUN --}}
                        <form method="GET" action="{{ route('dashboard') }}" class="flex flex-wrap items-center gap-2.5">
                            <div class="flex items-center gap-2 bg-[#2a2a2a] border border-[#4a4a4a] rounded-xl px-3 py-1.5 shadow-sm">
                                <i class="fas fa-calendar-alt text-[#e88968] text-xs"></i>
                                <label for="year" class="text-xs text-gray-300 font-medium whitespace-nowrap mb-0">Periode:</label>
                                <select name="year" id="year" class="bg-transparent text-xs text-white font-semibold focus:outline-none cursor-pointer pr-2" onchange="this.form.submit()">
                                    <option value="all" class="bg-[#2a2a2a] text-white" {{ $selectedYear == 'all' ? 'selected' : '' }}>Semua Periode</option>
                                    @foreach($availableYears as $year)
                                        @if($year)
                                            <option value="{{ $year }}" class="bg-[#2a2a2a] text-white" {{ $selectedYear == $year ? 'selected' : '' }}>
                                                Tahun {{ $year }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            @if($selectedYear != 'all')
                                <a href="{{ route('dashboard', ['year' => 'all']) }}"
                                    class="px-3 py-1.5 bg-[#2a2a2a] hover:bg-[#3a3a3a] text-gray-300 hover:text-white text-xs font-medium rounded-xl border border-[#4a4a4a] transition-colors flex items-center gap-1.5" title="Tampilkan Semua Periode">
                                    <i class="fas fa-undo text-[10px]"></i>
                                    <span>Reset Filter</span>
                                </a>
                            @endif
                        </form>
                    </div>
                </div>
            </div>

            {{-- Konten utama halaman admin --}}
            <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 space-y-8">

                {{-- 1. 4 KARTU KPI UTAMA --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    
                    {{-- KPI 1: Lowongan Dibuka --}}
                    <div class="kpi-card p-5 relative overflow-hidden flex flex-col justify-between group">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Lowongan Magang</span>
                            <div class="w-10 h-10 rounded-xl bg-[#d97757]/15 text-[#e88968] flex items-center justify-center group-hover:scale-110 transition-transform">
                                <i class="fas fa-briefcase text-lg"></i>
                            </div>
                        </div>
                        <div class="mt-4">
                            <h3 class="text-3xl font-extrabold text-white tracking-tight">{{ $totalLowonganBuka ?? 0 }}</h3>
                            <div class="flex items-center justify-between mt-2 pt-2 border-t border-[#3a3a3a]">
                                <span class="text-xs text-green-400 font-medium flex items-center gap-1">
                                    <i class="fas fa-door-open text-[10px]"></i> Formasi Buka
                                </span>
                                <a href="{{ route('lowongan.index') }}" class="text-xs text-[#e88968] hover:text-white font-semibold inline-flex items-center gap-1">
                                    <span>Kelola</span>
                                    <i class="fas fa-arrow-right text-[10px]"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- KPI 2: Menunggu Review --}}
                    <div class="kpi-card p-5 relative overflow-hidden flex flex-col justify-between group">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Menunggu Review</span>
                            <div class="w-10 h-10 rounded-xl bg-yellow-500/15 text-yellow-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                                <i class="fas fa-clock text-lg"></i>
                            </div>
                        </div>
                        <div class="mt-4">
                            <h3 class="text-3xl font-extrabold text-yellow-400 tracking-tight">{{ $statusChartData[0] ?? 0 }}</h3>
                            <div class="flex items-center justify-between mt-2 pt-2 border-t border-[#3a3a3a]">
                                <span class="text-xs text-gray-400">Berkas pendaftar baru</span>
                                <a href="{{ route('daftar.index') }}" class="text-xs text-yellow-400 hover:text-yellow-300 font-semibold inline-flex items-center gap-1">
                                    <span>Periksa</span>
                                    <i class="fas fa-arrow-right text-[10px]"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- KPI 3: Peserta Sedang Magang --}}
                    <div class="kpi-card p-5 relative overflow-hidden flex flex-col justify-between group">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Sedang Magang</span>
                            <div class="w-10 h-10 rounded-xl bg-green-500/15 text-green-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                                <i class="fas fa-user-check text-lg"></i>
                            </div>
                        </div>
                        <div class="mt-4">
                            <h3 class="text-3xl font-extrabold text-green-400 tracking-tight">{{ count($activeUsers ?? []) }}</h3>
                            <div class="flex items-center justify-between mt-2 pt-2 border-t border-[#3a3a3a]">
                                <span class="text-xs text-gray-400">Mahasiswa bertugas saat ini</span>
                                <a href="{{ route('magang.index') }}" class="text-xs text-green-400 hover:text-green-300 font-semibold inline-flex items-center gap-1">
                                    <span>Lihat</span>
                                    <i class="fas fa-arrow-right text-[10px]"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- KPI 4: Total Alumni & Kampus --}}
                    <div class="kpi-card p-5 relative overflow-hidden flex flex-col justify-between group">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Alumni & Mitra</span>
                            <div class="w-10 h-10 rounded-xl bg-blue-500/15 text-blue-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                                <i class="fas fa-graduation-cap text-lg"></i>
                            </div>
                        </div>
                        <div class="mt-4">
                            <h3 class="text-3xl font-extrabold text-white tracking-tight">{{ $totalAlumni ?? 0 }}</h3>
                            <div class="flex items-center justify-between mt-2 pt-2 border-t border-[#3a3a3a]">
                                <span class="text-xs text-blue-400 font-medium">
                                    <i class="fas fa-university text-[10px] mr-1"></i>{{ $totalKampusCount ?? 0 }} Kampus Mitra
                                </span>
                                <a href="{{ route('magang.index') }}" class="text-xs text-blue-400 hover:text-white font-semibold inline-flex items-center gap-1">
                                    <span>Direktori</span>
                                    <i class="fas fa-arrow-right text-[10px]"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- 2. TOOLBAR AKSI CEPAT ADMIN --}}
                <div class="flex flex-wrap items-center gap-3 p-4 bg-[#2a2a2a]/40 border border-[#3a3a3a] rounded-xl backdrop-blur-md">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider mr-2 flex items-center gap-1.5">
                        <i class="fas fa-bolt text-[#e88968]"></i>
                        <span>Aksi Cepat:</span>
                    </span>
                    <a href="{{ route('lowongan.create') }}" class="quick-btn">
                        <i class="fas fa-plus text-[#e88968]"></i>
                        <span>Buka Formasi Lowongan</span>
                    </a>
                    <a href="{{ route('magang.create') }}" class="quick-btn">
                        <i class="fas fa-user-plus text-green-400"></i>
                        <span>Tambah Data Magang</span>
                    </a>
                    <a href="{{ route('daftar.index') }}" class="quick-btn">
                        <i class="fas fa-list-check text-yellow-400"></i>
                        <span>Review Pendaftaran</span>
                    </a>
                    <a href="{{ route('settings.index') }}" class="quick-btn">
                        <i class="fas fa-sliders text-blue-400"></i>
                        <span>Atur Durasi Magang</span>
                    </a>
                </div>

                {{-- 3. BARIS GRAFIK ANALITIK 1: TREN BULANAN & STATUS BERKAS --}}
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                    {{-- Grafik Tren Bulanan (7 Kolom) --}}
                    <div class="lg:col-span-7 chart-card p-6 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <h3 class="claude-title text-xl text-white flex items-center gap-2">
                                        <i class="fas fa-chart-line text-[#e88968]"></i>
                                        <span>Tren MagNet ({{ $selectedYear != 'all' ? 'Tahun ' . $selectedYear : 'Semua Periode' }})</span>
                                    </h3>
                                    <p class="text-xs text-gray-400 mt-0.5">
                                        Perbandingan arus pendaftaran baru vs mahasiswa yang aktif mulai magang per bulan
                                    </p>
                                </div>
                            </div>
                            <div class="h-72 w-full relative">
                                <canvas id="trenChart"></canvas>
                            </div>
                        </div>
                    </div>

                    {{-- Grafik Status Pendaftar (5 Kolom) --}}
                    <div class="lg:col-span-5 chart-card p-6 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <h3 class="claude-title text-xl text-white flex items-center gap-2">
                                        <i class="fas fa-chart-pie text-yellow-400"></i>
                                        <span>Status Berkas Pendaftar</span>
                                    </h3>
                                    <p class="text-xs text-gray-400 mt-0.5">
                                        Distribusi kelulusan dan seleksi pendaftaran
                                    </p>
                                </div>
                                <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-[#1a1a1a] text-gray-300 border border-[#4a4a4a]">
                                    Total: {{ array_sum($statusChartData) }}
                                </span>
                            </div>

                            <div class="h-60 w-full relative my-2">
                                <canvas id="statusChart"></canvas>
                            </div>

                            {{-- Mini Summary Pills --}}
                            <div class="grid grid-cols-2 gap-2 mt-4 pt-3 border-t border-[#3a3a3a] text-xs">
                                <div class="p-2 bg-[#1a1a1a]/60 rounded-lg flex items-center justify-between border border-[#3a3a3a]">
                                    <span class="text-yellow-400 font-medium">⏳ Menunggu:</span>
                                    <strong class="text-white">{{ $statusChartData[0] ?? 0 }}</strong>
                                </div>
                                <div class="p-2 bg-[#1a1a1a]/60 rounded-lg flex items-center justify-between border border-[#3a3a3a]">
                                    <span class="text-green-400 font-medium">✅ Disetujui:</span>
                                    <strong class="text-white">{{ $statusChartData[1] ?? 0 }}</strong>
                                </div>
                                <div class="p-2 bg-[#1a1a1a]/60 rounded-lg flex items-center justify-between border border-[#3a3a3a]">
                                    <span class="text-red-400 font-medium">❌ Ditolak:</span>
                                    <strong class="text-white">{{ $statusChartData[2] ?? 0 }}</strong>
                                </div>
                                <div class="p-2 bg-[#1a1a1a]/60 rounded-lg flex items-center justify-between border border-[#3a3a3a]">
                                    <span class="text-blue-400 font-medium">ℹ️ Bersyarat:</span>
                                    <strong class="text-white">{{ $statusChartData[3] ?? 0 }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- 4. BARIS STATISTIK KAMPUS: GRAFIK BATANG & PERINGKAT INSTITUSI --}}
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                    
                    {{-- Kolom Kiri: Grafik Batang Asal Kampus --}}
                    <div class="lg:col-span-7 chart-card p-6 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <h3 class="claude-title text-xl text-white flex items-center gap-2">
                                        <i class="fas fa-university text-[#e88968]"></i>
                                        <span>Statistik Kampus ({{ $selectedYear != 'all' ? 'Tahun ' . $selectedYear : 'Semua Periode' }})</span>
                                    </h3>
                                    <p class="text-xs text-gray-400 mt-0.5">
                                        Perbandingan mahasiswa yang sedang aktif magang vs selesai per universitas
                                    </p>
                                </div>
                            </div>

                            @if(isset($kampusStats) && $kampusStats->count() > 0)
                                <div class="w-full relative" style="min-height: 320px; height: {{ max(320, count($kampusChartLabels ?? []) * 42) }}px;">
                                    <canvas id="kampusChart"></canvas>
                                </div>
                            @else
                                <div class="text-center py-16 text-gray-500">
                                    <i class="fas fa-school text-3xl mb-2 text-gray-600"></i>
                                    <p class="text-sm">Belum ada data magang untuk periode ini.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Kolom Kanan: Rincian & Peringkat Kampus --}}
                    <div class="lg:col-span-5 chart-card p-6 flex flex-col">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="claude-title text-lg text-white flex items-center gap-2">
                                    <i class="fas fa-trophy text-yellow-400"></i>
                                    <span>Peringkat & Rincian Kampus</span>
                                </h3>
                                <p class="text-xs text-gray-400 mt-0.5">Total sebaran asal institusi peserta magang</p>
                            </div>
                            <span class="text-xs font-semibold text-[#e88968] bg-[#d97757]/15 border border-[#d97757]/30 px-2.5 py-0.5 rounded-full">
                                {{ $kampusStats->count() ?? 0 }} Kampus
                            </span>
                        </div>

                        @if(isset($kampusStats) && $kampusStats->count() > 0)
                            <div class="overflow-y-auto max-h-[380px] space-y-3 pr-1">
                                @foreach($kampusStats as $stat)
                                    @php
                                        $badgeClass = 'ranking-badge-default';
                                        if ($loop->iteration === 1) $badgeClass = 'ranking-badge-1';
                                        elseif ($loop->iteration === 2) $badgeClass = 'ranking-badge-2';
                                        elseif ($loop->iteration === 3) $badgeClass = 'ranking-badge-3';
                                    @endphp
                                    <div class="p-3.5 bg-[#1a1a1a]/70 rounded-xl border border-[#3a3a3a] hover:border-[#d97757]/40 transition-all hover:bg-[#1a1a1a]">
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="flex items-center gap-2.5 overflow-hidden">
                                                <div class="w-6 h-6 rounded-md {{ $badgeClass }} flex items-center justify-center text-xs font-extrabold flex-shrink-0 shadow-sm">
                                                    {{ $loop->iteration }}
                                                </div>
                                                <span class="text-sm font-semibold text-white truncate" title="{{ $stat->asal_kampus }}">
                                                    {{ $stat->asal_kampus }}
                                                </span>
                                            </div>
                                            <span class="text-xs font-bold text-gray-200 bg-[#2a2a2a] px-2.5 py-0.5 rounded-full border border-[#4a4a4a] whitespace-nowrap ml-2">
                                                {{ $stat->total_peserta }} Mahasiswa
                                            </span>
                                        </div>

                                        {{-- Badges Status --}}
                                        <div class="flex items-center gap-2 text-[11px] pt-2 border-t border-[#2a2a2a]">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-green-500/15 text-green-400 border border-green-500/25">
                                                <i class="fas fa-user-clock text-[10px]"></i> Aktif: <strong>{{ $stat->sedang_magang }}</strong>
                                            </span>
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-blue-500/15 text-blue-400 border border-blue-500/25">
                                                <i class="fas fa-user-check text-[10px]"></i> Selesai: <strong>{{ $stat->selesai_magang }}</strong>
                                            </span>
                                            @if($stat->belum_mulai > 0)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-yellow-500/15 text-yellow-400 border border-yellow-500/25">
                                                    <i class="fas fa-hourglass-start text-[10px]"></i> Belum: <strong>{{ $stat->belum_mulai }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-16 text-gray-500">
                                <p class="text-sm">Tidak ada data untuk ditampilkan.</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- 5. KARTU PEMANTAUAN PESERTA MAGANG AKTIF SAAT INI --}}
                <div class="chart-card p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
                        <div>
                            <h3 class="claude-title text-xl text-white flex items-center gap-2">
                                <i class="fas fa-users-viewfinder text-green-400"></i>
                                <span>Peserta Magang Aktif Bertugas Saat Ini</span>
                            </h3>
                            <p class="text-xs text-gray-400 mt-0.5">
                                Pemantauan mahasiswa yang saat ini sedang aktif menjalankan program magang di BPS Bantul
                            </p>
                        </div>
                        <span class="text-xs font-semibold text-green-400 bg-green-500/15 border border-green-500/30 px-3 py-1 rounded-full self-start sm:self-auto flex items-center gap-1.5">
                            <span class="pulse-dot"></span>
                            <span>{{ count($activeUsers ?? []) }} Sedang Bertugas</span>
                        </span>
                    </div>
                    
                    @if(isset($activeUsers) && $activeUsers->count())
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                            @foreach($activeUsers as $user)
                                <div class="p-4 bg-[#1a1a1a]/80 rounded-xl border border-[#3a3a3a] hover:border-green-500/40 shadow-md transition-all flex flex-col justify-between group">
                                    <div>
                                        <div class="flex items-center justify-between mb-3">
                                            <div class="flex items-center gap-3 min-w-0">
                                                @if($user->foto)
                                                    <img src="{{ asset('storage/' . $user->foto) }}" alt="{{ $user->nama_pendaftar }}" class="w-11 h-11 rounded-full object-cover border-2 border-green-500/40 flex-shrink-0 shadow">
                                                @else
                                                    <div class="w-11 h-11 rounded-full bg-green-500/20 text-green-400 flex items-center justify-center text-sm font-bold flex-shrink-0 shadow">
                                                        {{ substr($user->nama_pendaftar, 0, 1) }}
                                                    </div>
                                                @endif
                                                <div class="min-w-0">
                                                    <p class="text-sm font-bold text-white truncate group-hover:text-green-300 transition-colors" title="{{ $user->nama_pendaftar }}">{{ $user->nama_pendaftar }}</p>
                                                    <p class="text-xs text-[#e88968] truncate" title="{{ $user->asal_kampus }}">
                                                        <i class="fas fa-university text-[10px] mr-0.5"></i> {{ $user->asal_kampus }}
                                                    </p>
                                                </div>
                                            </div>
                                            <span class="text-[11px] text-green-400 bg-green-500/15 border border-green-500/30 px-2 py-0.5 rounded-full font-medium whitespace-nowrap ml-2 flex-shrink-0">
                                                Aktif
                                            </span>
                                        </div>

                                        <div class="text-xs border-t border-[#333] pt-3 space-y-1.5">
                                            @if($user->lowongan)
                                                <p class="text-xs text-gray-300 truncate" title="{{ $user->lowongan->judul_posisi }} ({{ $user->lowongan->divisi }})">
                                                    <i class="fas fa-briefcase text-[10px] text-[#e88968] mr-1"></i>{{ $user->lowongan->divisi }}
                                                </p>
                                            @endif
                                            <p class="text-xs text-gray-300">
                                                <i class="fas fa-calendar-alt text-[10px] text-gray-400 mr-1"></i>
                                                <span class="font-medium text-white">{{ date('d/m/y', strtotime($user->tanggal_mulai)) }}</span> s.d. 
                                                <span class="font-medium text-white">{{ date('d/m/y', strtotime($user->tanggal_selesai)) }}</span>
                                            </p>
                                        </div>
                                    </div>

                                    <div class="mt-3 pt-2.5 border-t border-[#333] flex items-center justify-between text-xs">
                                        <span class="font-semibold {{ ($user->remaining_days <= 7 && $user->remaining_days > 0) ? 'text-red-400' : 'text-yellow-400' }}">
                                            <i class="fas fa-clock text-[10px] mr-1"></i>Sisa: {{ $user->remaining_days }} hari
                                        </span>
                                        @if(isset($user->id))
                                            <a href="{{ route('magang.show', $user->id) }}" class="text-[#e88968] hover:text-white font-medium text-[11px] inline-flex items-center gap-1">
                                                <span>Profil</span>
                                                <i class="fas fa-chevron-right text-[9px]"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12 text-gray-400 bg-[#1a1a1a]/50 rounded-xl border border-[#3a3a3a]">
                            <i class="fas fa-info-circle mb-2 text-2xl text-gray-500"></i>
                            <p class="text-sm">Tidak ada mahasiswa yang sedang dalam masa magang aktif saat ini.</p>
                        </div>
                    @endif
                </div>

            </div>
        </div>

    {{-- =========================================== --}}
    {{-- BAGIAN 4: TAMPILAN UNTUK USER BIASA (HTML) --}}
    {{-- =========================================== --}}
    @elseif($role == 'user')

        <div class="claude-container">

            {{-- Header Section User --}}
            <div class="border-b border-[#3a3a3a] header-section">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 py-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h2 class="claude-title text-xl sm:text-2xl text-white">
                            {{ __('Portal Magang BPS Bantul') }}
                        </h2>
                        <p class="text-xs text-gray-400 mt-0.5">
                            Sistem Pendaftaran & Informasi Magang BPS Kabupaten Bantul
                        </p>
                    </div>
                    <a href="{{ route('lowongan.index') }}" class="claude-button px-4 py-2 text-sm inline-flex items-center gap-2 self-start sm:self-auto">
                        <i class="fas fa-briefcase"></i>
                        <span>Lihat Semua Lowongan</span>
                    </a>
                </div>
            </div>

            {{-- Konten utama halaman user --}}
            <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 space-y-8">

                {{-- Kartu Welcome --}}
                <div class="bg-[#2a2a2a]/60 backdrop-blur-md border border-[#3a3a3a] rounded-xl overflow-hidden shadow-lg p-6 text-gray-300 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-bold text-white mb-1">
                            Halo, {{ Auth::user()->name }}! 👋
                        </h3>
                        <p class="text-sm text-gray-400">
                            Selamat datang di portal MagNet. Pilih formasi divisi yang sesuai dan ajukan pendaftaran magang Anda.
                        </p>
                    </div>
                    @if(!$pendaftaran)
                        <a href="{{ route('daftar.create') }}" class="claude-button px-5 py-2.5 text-sm font-medium inline-flex items-center gap-2 whitespace-nowrap shadow-lg shadow-[#d97757]/20">
                            <i class="fas fa-paper-plane"></i>
                            <span>Daftar Magang Sekarang</span>
                        </a>
                    @endif
                </div>

                {{-- SECTION LOWONGAN MAGANG TERSEDIA (FITUR BARU) --}}
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="claude-title text-xl text-white flex items-center gap-2">
                                <i class="fas fa-fire text-[#e88968]"></i>
                                <span>Lowongan Magang Tersedia di BPS Bantul</span>
                            </h3>
                            <p class="text-xs text-gray-400 mt-0.5">
                                Pilih formasi divisi sesuai minat dan keahlian akademik Anda
                            </p>
                        </div>
                        <a href="{{ route('lowongan.index') }}" class="text-xs text-[#e88968] hover:text-white font-medium flex items-center gap-1 transition-colors">
                            <span>Katalog Lengkap</span>
                            <i class="fas fa-chevron-right text-[10px]"></i>
                        </a>
                    </div>

                    @if(isset($lowongans) && $lowongans->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($lowongans->take(3) as $lowongan)
                                <div class="bg-[#2a2a2a]/60 backdrop-blur-md border border-[#3a3a3a] hover:border-[#d97757]/60 rounded-xl p-5 flex flex-col justify-between transition-all duration-300 hover:shadow-xl hover:shadow-[#d97757]/5 group">
                                    <div>
                                        <div class="flex items-center justify-between gap-2 mb-2">
                                            <span class="text-[11px] px-2 py-0.5 rounded-full font-medium bg-green-500/20 text-green-400 border border-green-500/30">
                                                <i class="fas fa-door-open mr-1"></i> Dibuka
                                            </span>
                                            <span class="text-xs text-gray-400 bg-[#1a1a1a] px-2 py-0.5 rounded-full border border-[#4a4a4a]">
                                                Sisa: <strong class="text-[#e88968]">{{ $lowongan->kuota_tersisa }}</strong> / {{ $lowongan->kuota }} Kursi
                                            </span>
                                        </div>

                                        <h4 class="text-base font-bold text-white group-hover:text-[#e88968] transition-colors mb-1">
                                            {{ $lowongan->judul_posisi }}
                                        </h4>
                                        <p class="text-xs text-[#d97757] font-medium mb-3 flex items-center gap-1">
                                            <i class="fas fa-sitemap"></i>
                                            <span>{{ $lowongan->divisi }}</span>
                                        </p>
                                        <p class="text-xs text-gray-300 line-clamp-2 mb-4 leading-relaxed">
                                            {{ $lowongan->deskripsi }}
                                        </p>
                                    </div>

                                    <div class="border-t border-[#3a3a3a] pt-3 flex items-center justify-between gap-2 mt-2">
                                        <a href="{{ route('lowongan.show', $lowongan) }}" class="text-xs text-gray-400 hover:text-white font-medium">
                                            Detail Posisi
                                        </a>
                                        <a href="{{ route('daftar.create', ['lowongan_id' => $lowongan->id]) }}" class="claude-button text-xs px-3.5 py-1.5 inline-flex items-center gap-1.5">
                                            <i class="fas fa-paper-plane text-[10px]"></i>
                                            <span>Lamar</span>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="bg-[#2a2a2a]/60 backdrop-blur-md border border-[#3a3a3a] rounded-xl p-8 text-center text-gray-400">
                            <i class="fas fa-info-circle text-xl mb-2 text-gray-500"></i>
                            <p class="text-sm">Saat ini belum ada formasi lowongan magang yang dibuka.</p>
                        </div>
                    @endif
                </div>

                {{-- Grid untuk menampung kartu status dan peserta aktif --}}
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    {{-- Kartu Status Pendaftaran --}}
                    <div class="lg:col-span-1 bg-[#2a2a2a]/60 backdrop-blur-md border border-[#3a3a3a] rounded-xl shadow-lg p-6">

                        @if($pendaftaran)
                            <h3 class="claude-title text-xl text-white mb-4 flex items-center gap-2">
                                <i class="fas fa-file-alt text-[#e88968]"></i>
                                <span>Status Pendaftaran Anda</span>
                            </h3>

                            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Nama Pendaftar:</p>
                            <p class="text-white text-base font-semibold mb-3">{{ $pendaftaran->nama_pendaftar }}</p>

                            @if($pendaftaran->lowongan)
                                <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Posisi / Divisi Dilamar:</p>
                                <p class="text-[#e88968] text-sm font-semibold mb-4 flex items-center gap-1.5">
                                    <i class="fas fa-briefcase text-xs"></i>
                                    <span>{{ $pendaftaran->lowongan->judul_posisi }}</span>
                                </p>
                            @endif

                            <p class="text-xs text-gray-400 uppercase tracking-wider mb-2">Status Terkini:</p>
                            <div>
                                @if($pendaftaran->status == 'pending')
                                    <span class="status-badge bg-yellow-500/20 text-yellow-300 border border-yellow-500/30">
                                        <i class="fas fa-clock mr-1.5"></i> Menunggu Review Admin
                                    </span>
                                @elseif($pendaftaran->status == 'approved')
                                    <span class="status-badge bg-green-500/20 text-green-300 border border-green-500/30">
                                        <i class="fas fa-check-circle mr-1.5"></i> Pendaftaran Disetujui
                                    </span>
                                @elseif($pendaftaran->status == 'rejected')
                                    <span class="status-badge bg-red-500/20 text-red-300 border border-red-500/30">
                                        <i class="fas fa-times-circle mr-1.5"></i> Pendaftaran Ditolak
                                    </span>
                                @elseif($pendaftaran->status == 'conditional')
                                    <span class="status-badge bg-blue-500/20 text-blue-300 border border-blue-500/30">
                                        <i class="fas fa-exclamation-circle mr-1.5"></i> Disetujui Bersyarat
                                    </span>
                                @endif
                            </div>

                            <p class="text-xs text-gray-400 mt-6 leading-relaxed">
                                Cek menu <a href="{{ route('daftar.index') }}" class="text-[#e88968] underline hover:text-white">"Pendaftaran Magang"</a> untuk melihat detail berkas atau riwayat perubahan.
                            </p>

                        @else
                            <h3 class="claude-title text-xl text-white mb-3">Anda belum mendaftar magang</h3>
                            <p class="text-sm text-gray-400 mb-6 leading-relaxed">
                                Silakan ajukan pendaftaran magang mandiri atau pilih formasi lowongan di atas.
                            </p>
                            <a href="{{ route('daftar.create') }}" class="claude-button w-full py-2.5 inline-flex items-center justify-center gap-2">
                                <i class="fas fa-plus"></i>
                                <span>Formulir Pendaftaran Magang</span>
                            </a>
                        @endif

                    </div>

                    {{-- KARTU PESERTA AKTIF --}}
                    <div class="lg:col-span-2 bg-[#2a2a2a]/60 backdrop-blur-md border border-[#3a3a3a] rounded-xl shadow-lg p-6">
                        <h3 class="claude-title text-xl text-white mb-4 flex items-center gap-2">
                            <i class="fas fa-users text-green-400"></i>
                            <span>Peserta Magang Aktif Saat Ini</span>
                        </h3>
                        
                        @if(isset($activeUsers) && $activeUsers->count())
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-h-[350px] overflow-y-auto hide-scrollbar">
                                @foreach($activeUsers as $user)
                                    <div class="p-4 bg-[#3a3a3a] rounded-xl border border-[#4a4a4a] shadow-md">
                                        <div class="flex items-center justify-between mb-3">
                                            <div class="flex items-center gap-3">
                                                <div class="w-9 h-9 rounded-full bg-green-500/20 text-green-400 flex items-center justify-center text-sm font-bold flex-shrink-0">
                                                    {{ substr($user->nama_pendaftar, 0, 1) }}
                                                </div>
                                                <div>
                                                    <p class="text-sm font-semibold text-white truncate">{{ $user->nama_pendaftar }}</p>
                                                    <p class="text-xs text-gray-400">Durasi: {{ $user->duration_months }} bulan</p>
                                                </div>
                                            </div>
                                            <span class="text-xs text-green-400 font-medium whitespace-nowrap">
                                                <i class="fas fa-running mr-1"></i> Aktif
                                            </span>
                                        </div>

                                        <div class="text-xs border-t border-[#4a4a4a] pt-2.5">
                                            <p class="text-gray-300">
                                                Periode: 
                                                <span class="font-medium text-white">{{ date('d/m/Y', strtotime($user->tanggal_mulai)) }}</span> - 
                                                <span class="font-medium text-white">{{ date('d/m/Y', strtotime($user->tanggal_selesai)) }}</span>
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8 text-gray-400 bg-[#3a3a3a] rounded-xl">
                                <i class="fas fa-info-circle mb-2 text-xl text-gray-500"></i>
                                <p class="text-sm">Tidak ada peserta magang yang aktif saat ini.</p>
                            </div>
                        @endif
                    </div>

                </div>

            </div>
        </div>

    @endif

</x-main-layout>
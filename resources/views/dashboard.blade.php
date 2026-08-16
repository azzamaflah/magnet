{{-- resources/views/dashboard.blade.php --}}
<x-main-layout>

    {{-- =========================================== --}}
    {{-- BAGIAN 1: KUMPULAN STYLES (KONDISIONAL) --}}
    {{-- =========================================== --}}
    @push('styles')
        @if($role == 'admin')
            {{-- PUSH STYLE HANYA UNTUK ADMIN --}}
            <style>
                .fc-icon-container { position: relative; display: inline-block; }
                .fc-mobile-popup {
                    position: absolute; bottom: 100%; left: 50%;
                    transform: translateX(-50%) translateY(-5px); 
                    background-color: #3a3a3a; color: #ffffff;
                    padding: 6px 10px; border-radius: 6px; font-size: 0.75rem; 
                    font-weight: 500; white-space: nowrap; z-index: 50; 
                    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.4);
                }
                @media (min-width: 1024px) { .fc-mobile-popup { display: none !important; } }
            </style>
        @elseif($role == 'user')
            {{-- Helper CSS untuk status badge --}}
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
            {{-- SCRIPT UNTUK GRAFIK ADMIN --}}
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const pendaftaranLabels = @json($pendaftaranChartLabels);
                    const pendaftaranData = @json($pendaftaranChartData);
                    const magangData = @json($magangChartData);
                    const statusData = @json($statusChartData);

                    Chart.defaults.color = '#9ca3af';
                    Chart.defaults.borderColor = 'rgba(255, 255, 255, 0.1)';

                    // 1. GRAFIK PENDAFTARAN
                    const ctxPendaftaran = document.getElementById('pendaftaranChart');
                    if (ctxPendaftaran) {
                        new Chart(ctxPendaftaran, {
                            type: 'bar',
                            data: {
                                labels: pendaftaranLabels,
                                datasets: [{
                                    label: 'Jumlah Pendaftar',
                                    data: pendaftaranData,
                                    backgroundColor: 'rgba(217, 119, 87, 0.6)',
                                    borderColor: 'rgba(217, 119, 87, 1)',
                                    borderWidth: 1
                                }]
                            }, options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
                        });
                    }

                    // 2. GRAFIK STATUS
                    const ctxStatus = document.getElementById('statusChart');
                    if (ctxStatus) {
                        new Chart(ctxStatus, {
                            type: 'doughnut',
                            data: {
                                labels: ['Menunggu', 'Disetujui', 'Ditolak', 'Bersyarat'],
                                datasets: [{
                                    label: 'Status Pendaftar',
                                    data: statusData,
                                    backgroundColor: ['rgba(250, 204, 21, 0.7)', 'rgba(74, 222, 128, 0.7)', 'rgba(248, 113, 113, 0.7)', 'rgba(96, 165, 250, 0.7)'],
                                    borderColor: '#3a3a3a',
                                    borderWidth: 2
                                }]
                            }, options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
                        });
                    }

                    // 3. GRAFIK MAGANG
                    const ctxMagang = document.getElementById('magangChart');
                    if (ctxMagang) {
                        new Chart(ctxMagang, {
                            type: 'line',
                            data: {
                                labels: pendaftaranLabels,
                                datasets: [{
                                    label: 'Peserta Mulai Magang',
                                    data: magangData,
                                    backgroundColor: 'rgba(96, 165, 250, 0.2)',
                                    borderColor: 'rgba(96, 165, 250, 1)',
                                    borderWidth: 2,
                                    fill: true,
                                    tension: 0.3
                                }]
                            }, options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
                        });
                    }
                });
            </script>
        @endif
    @endpush


    {{-- =========================================== --}}
    {{-- BAGIAN 3: TAMPILAN UNTUK ADMIN (HTML) --}}
    {{-- =========================================== --}}
    @if($role == 'admin')

        <div class="claude-container">

            {{-- Header Section dengan Filter Tahun --}}
            <div class="border-b border-[#3a3a3a] header-section">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 py-4">
                    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                        <div>
                            <h2 class="claude-title text-xl sm:text-2xl text-white">
                                {{ __('Dashboard Admin') }}
                            </h2>
                            <p class="text-xs text-gray-400 mt-0.5">
                                Panel pemantauan program magang BPS Kabupaten Bantul
                            </p>
                        </div>

                        {{-- FORM FILTER TAHUN --}}
                        <form method="GET" action="{{ route('dashboard') }}" class="flex items-center gap-3">
                            <label for="year" class="filter-label mb-0">Tampilkan Tahun:</label>
                            <select name="year" id="year" class="filter-select w-40" onchange="this.form.submit()">
                                <option value="all" {{ $selectedYear == 'all' ? 'selected' : '' }}>Semua Tahun</option>
                                @foreach($availableYears as $year)
                                    @if($year)
                                        <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>

                            @if($selectedYear != 'all')
                                <a href="{{ route('dashboard', ['year' => 'all']) }}"
                                    class="text-gray-400 hover:text-white text-sm" title="Tampilkan Semua Tahun">
                                    <i class="fas fa-times"></i> Tampilkan Semua
                                </a>
                            @endif
                        </form>
                    </div>
                </div>
            </div>

            {{-- Konten utama halaman admin --}}
            <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6">

                {{-- Banner Quick Actions --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                    <div class="bg-[#2a2a2a]/60 backdrop-blur-md border border-[#3a3a3a] rounded-xl p-5 shadow-lg flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Lowongan Dibuka</p>
                            <h3 class="text-2xl font-bold text-white mt-1">{{ $totalLowonganBuka ?? 0 }} Formasi</h3>
                        </div>
                        <a href="{{ route('lowongan.index') }}" class="w-10 h-10 rounded-full bg-[#d97757]/20 text-[#e88968] flex items-center justify-center hover:scale-110 transition-transform">
                            <i class="fas fa-briefcase"></i>
                        </a>
                    </div>

                    <div class="bg-[#2a2a2a]/60 backdrop-blur-md border border-[#3a3a3a] rounded-xl p-5 shadow-lg flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Menunggu Review</p>
                            <h3 class="text-2xl font-bold text-yellow-400 mt-1">{{ $statusChartData[0] ?? 0 }} Berkas</h3>
                        </div>
                        <a href="{{ route('daftar.index') }}" class="w-10 h-10 rounded-full bg-yellow-500/20 text-yellow-400 flex items-center justify-center hover:scale-110 transition-transform">
                            <i class="fas fa-clock"></i>
                        </a>
                    </div>

                    <div class="bg-[#2a2a2a]/60 backdrop-blur-md border border-[#3a3a3a] rounded-xl p-5 shadow-lg flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Peserta Aktif</p>
                            <h3 class="text-2xl font-bold text-green-400 mt-1">{{ count($activeUsers ?? []) }} Orang</h3>
                        </div>
                        <a href="{{ route('magang.index') }}" class="w-10 h-10 rounded-full bg-green-500/20 text-green-400 flex items-center justify-center hover:scale-110 transition-transform">
                            <i class="fas fa-user-check"></i>
                        </a>
                    </div>
                </div>

                {{-- KUMPULAN GRAFIK --}}
                <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

                    {{-- Pendaftaran Masuk --}}
                    <div class="lg:col-span-3 bg-[#2a2a2a]/60 backdrop-blur-md border border-[#3a3a3a] rounded-xl shadow-lg p-6">
                        <h3 class="claude-title text-xl text-white mb-4">
                            Pendaftaran Masuk ({{ $selectedYear != 'all' ? $selectedYear : 'Semua Tahun' }})
                        </h3>
                        <canvas id="pendaftaranChart"></canvas>
                    </div>

                    {{-- Status Pendaftar --}}
                    <div class="lg:col-span-2 bg-[#2a2a2a]/60 backdrop-blur-md border border-[#3a3a3a] rounded-xl shadow-lg p-6">
                        <h3 class="claude-title text-xl text-white mb-4">
                            Status Pendaftar ({{ $selectedYear != 'all' ? $selectedYear : 'Semua Tahun' }})
                        </h3>
                        <canvas id="statusChart"></canvas>
                    </div>

                    {{-- Peserta Mulai Magang --}}
                    <div class="lg:col-span-5 bg-[#2a2a2a]/60 backdrop-blur-md border border-[#3a3a3a] rounded-xl shadow-lg p-6">
                        <h3 class="claude-title text-xl text-white mb-4">
                            Peserta Mulai Magang ({{ $selectedYear != 'all' ? $selectedYear : 'Semua Tahun' }})
                        </h3>
                        <canvas id="magangChart"></canvas>
                    </div>
                </div>

                {{-- KARTU PESERTA AKTIF --}}
                <div class="grid grid-cols-1 mt-6">
                    <div class="lg:col-span-5 bg-[#2a2a2a]/60 backdrop-blur-md border border-[#3a3a3a] rounded-xl shadow-lg p-6">
                        <h3 class="claude-title text-xl text-white mb-4">
                            Peserta Magang Aktif Saat Ini
                        </h3>
                        
                        @if(isset($activeUsers) && $activeUsers->count())
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                                @foreach($activeUsers as $user)
                                    <div class="p-4 bg-[#3a3a3a] rounded-xl border border-[#4a4a4a] shadow-md">
                                        <div class="flex items-center justify-between mb-3">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-full bg-green-500/20 text-green-400 flex items-center justify-center text-md font-bold flex-shrink-0">
                                                    {{ substr($user->nama_pendaftar, 0, 1) }}
                                                </div>
                                                <div>
                                                    <p class="text-md font-semibold text-white truncate">{{ $user->nama_pendaftar }}</p>
                                                    <p class="text-xs text-gray-400">Durasi: {{ $user->duration_months }} bulan</p>
                                                </div>
                                            </div>
                                            <span class="text-xs text-green-400 font-medium whitespace-nowrap">
                                                <i class="fas fa-running mr-1"></i> Aktif
                                            </span>
                                        </div>

                                        <div class="text-sm border-t border-[#4a4a4a] pt-3">
                                            <p class="text-gray-300">
                                                Periode: 
                                                <span class="font-medium text-white">{{ date('d/m/Y', strtotime($user->tanggal_mulai)) }}</span> - 
                                                <span class="font-medium text-white">{{ date('d/m/Y', strtotime($user->tanggal_selesai)) }}</span>
                                            </p>
                                            <p class="mt-1 font-semibold {{ ($user->remaining_days <= 7 && $user->remaining_days > 0) ? 'text-red-400' : 'text-yellow-400' }}">
                                                Sisa: {{ $user->remaining_days }} hari
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8 text-gray-400 bg-[#3a3a3a] rounded-xl">
                                <i class="fas fa-info-circle mb-2 text-xl text-gray-500"></i>
                                <p>Tidak ada peserta magang yang aktif saat ini.</p>
                            </div>
                        @endif
                        
                    </div>
                </div>

            </div>
        </div>

    {{-- =========================================== --}}
    {{-- BAGIAN 4: TAMPILAN UNTUK USER BIASA (HTML) --}}
    {{-- =========================================== --}}
    @elseif($role == 'user')

        <div class="claude-container">

            {{-- Header Section Sederhana untuk User --}}
            <div class="border-b border-[#3a3a3a] header-section">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h2 class="claude-title text-xl sm:text-2xl text-white">
                            {{ __('Dashboard Mahasiswa') }}
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
                                            <p class="mt-1 font-semibold {{ ($user->remaining_days <= 7 && $user->remaining_days > 0) ? 'text-red-400' : 'text-yellow-400' }}">
                                                Sisa Waktu: {{ $user->remaining_days }} hari
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
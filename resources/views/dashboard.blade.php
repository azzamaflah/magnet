{{-- resources/views/dashboard.blade.php --}}
<x-main-layout>

    {{-- =========================================== --}}
    {{-- BAGIAN 1: KUMPULAN STYLES (KONDISIONAL) --}}
    {{-- =========================================== --}}
    @push('styles')
        @if($role == 'admin')
            {{-- PUSH STYLE HANYA UNTUK ADMIN (CSS KALENDER ADMIN DIHAPUS) --}}
            <style>
                /* --- CSS Alpine Pop-up Mobile (Admin) --- */
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
                
                /* SEMUA CSS KALENDER, FC, DAN POPUP TELAH DIHAPUS DARI USER */
            </style>
        @endif
    @endpush


    {{-- =========================================== --}}
    {{-- BAGIAN 2: KUMPULAN SCRIPTS (KONDISIONAL) --}}
    {{-- =========================================== --}}
    @push('scripts')
        {{-- Logika Alpine Global (Dihapus karena kalender tidak ada lagi di kedua role) --}}
        {{-- <script> ... </script> --}}

        @if($role == 'admin')
            {{-- SCRIPT UNTUK GRAFIK ADMIN --}}
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    // === Bagian Chart.js ===
                    const pendaftaranLabels = @json($pendaftaranChartLabels);
                    const pendaftaranData = @json($pendaftaranChartData);
                    const magangData = @json($magangChartData);
                    const statusData = @json($statusChartData);

                    Chart.defaults.color = '#9ca3af';
                    Chart.defaults.borderColor = 'rgba(255, 255, 255, 0.1)';

                    // ✅ 1. KODE GRAFIK PENDAFTARAN (LENGKAP)
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

                    // ✅ 2. KODE GRAFIK STATUS (LENGKAP)
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

                    // ✅ 3. KODE GRAFIK MAGANG (LENGKAP)
                    const ctxMagang = document.getElementById('magangChart');
                    if (ctxMagang) {
                        new Chart(ctxMagang, {
                            type: 'line',
                            data: {
                                labels: pendaftaranLabels, // Menggunakan label yang sama
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
        @elseif($role == 'user')
            {{-- SCRIPT KALENDER UNTUK USER TELAH DIHAPUS --}}
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
                        <h2 class="claude-title text-xl sm:text-2xl text-white">
                            {{ __('Dashboard Admin') }}
                        </h2>

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

                <div class="bg-[#2a2a2a]/60 backdrop-blur-md border border-[#3a3a3a] rounded-xl overflow-hidden shadow-lg mb-6"
                    style="animation: slideDown 0.5s ease-out;">
                    <div class="p-6 text-gray-300">
                        {{ __("You're logged in as Admin!") }}

                        @if($selectedYear && $selectedYear != 'all')
                            <span class="ml-2 text-gray-400">Menampilkan statistik untuk tahun {{ $selectedYear }}.</span>
                        @else
                            <span class="ml-2 text-gray-400">Menampilkan statistik untuk semua tahun.</span>
                        @endif
                    </div>
                </div>

                {{-- KUMPULAN GRAFIK --}}
                <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

                    {{-- Pendaftaran Masuk --}}
                    <div
                        class="lg:col-span-3 bg-[#2a2a2a]/60 backdrop-blur-md border border-[#3a3a3a] rounded-xl shadow-lg p-6">
                        <h3 class="claude-title text-xl text-white mb-4">
                            Pendaftaran Masuk ({{ $selectedYear != 'all' ? $selectedYear : 'Semua Tahun' }})
                        </h3>
                        <canvas id="pendaftaranChart"></canvas>
                    </div>

                    {{-- Status Pendaftar --}}
                    <div
                        class="lg:col-span-2 bg-[#2a2a2a]/60 backdrop-blur-md border border-[#3a3a3a] rounded-xl shadow-lg p-6">
                        <h3 class="claude-title text-xl text-white mb-4">
                            Status Pendaftar ({{ $selectedYear != 'all' ? $selectedYear : 'Semua Tahun' }})
                        </h3>
                        <canvas id="statusChart"></canvas>
                    </div>

                    {{-- Peserta Mulai Magang --}}
                    <div
                        class="lg:col-span-5 bg-[#2a2a2a]/60 backdrop-blur-md border border-[#3a3a3a] rounded-xl shadow-lg p-6">
                        <h3 class="claude-title text-xl text-white mb-4">
                            Peserta Mulai Magang ({{ $selectedYear != 'all' ? $selectedYear : 'Semua Tahun' }})
                        </h3>
                        <canvas id="magangChart"></canvas>
                    </div>
                </div>

                {{-- KARTU PESERTA AKTIF (5 kolom penuh) --}}
                <div class="grid grid-cols-1 mt-6">
                    <div class="lg:col-span-5 bg-[#2a2a2a]/60 backdrop-blur-md border border-[#3a3a3a] rounded-xl shadow-lg p-6">
                        <h3 class="claude-title text-xl text-white mb-4">
                            Peserta Magang Aktif Saat Ini
                        </h3>
                        
                        @if(isset($activeUsers) && $activeUsers->count())
                            {{-- Container Utama Daftar Peserta --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                                @foreach($activeUsers as $user)
                                    <div class="p-4 bg-[#3a3a3a] rounded-xl border border-[#4a4a4a] shadow-md">
                                        <div class="flex items-center justify-between mb-3">
                                            <div class="flex items-center gap-3">
                                                {{-- Avatar --}}
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

                                        {{-- Detail Periode dan Sisa Hari --}}
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
                        
                    </div> {{-- Akhir Kartu Peserta Aktif --}}

                </div> {{-- Akhir grid KARTU PESERTA AKTIF --}}

            </div>
        </div>

    {{-- =========================================== --}}
    {{-- BAGIAN 4: TAMPILAN UNTUK USER BIASA (HTML) --}}
    {{-- =========================================== --}}
    @elseif($role == 'user')

        <div class="claude-container">

            {{-- Header Section Sederhana untuk User --}}
            <div class="border-b border-[#3a3a3a] header-section">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 py-4">
                    <h2 class="claude-title text-xl sm:text-2xl text-white">
                        {{ __('Dashboard') }}
                    </h2>
                </div>
            </div>

            {{-- Konten utama halaman user --}}
            <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6">

                {{-- Kartu Welcome --}}
                <div
                    class="bg-[#2a2a2a]/60 backdrop-blur-md border border-[#3a3a3a] rounded-xl overflow-hidden shadow-lg mb-6">
                    <div class="p-6 text-gray-300">
                        Selamat datang, <span class="font-semibold text-white">{{ Auth::user()->name }}</span>!
                    </div>
                </div>

                {{-- Grid untuk menampung kartu status dan kalender --}}
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    {{-- Kartu Status Pendaftaran --}}
                    <div
                        class="lg:col-span-1 bg-[#2a2a2a]/60 backdrop-blur-md border border-[#3a3a3a] rounded-xl shadow-lg p-6">

                        @if($pendaftaran)
                            {{-- JIKA USER SUDAH PERNAH MENDAFTAR --}}
                            <h3 class="claude-title text-xl text-white mb-4">Status Pendaftaran Anda</h3>

                            <p class="text-gray-400 mb-2">Nama Pendaftar:</p>
                            <p class="text-white text-lg font-semibold mb-4">{{ $pendaftaran->nama_pendaftar }}</p>

                            <p class="text-gray-400 mb-2">Status Saat Ini:</p>
                            <div>
                                @if($pendaftaran->status == 'pending')
                                    <span class="status-badge bg-yellow-500/20 text-yellow-300">
                                        <i class="fas fa-clock mr-1"></i> Menunggu Review
                                    </span>
                                @elseif($pendaftaran->status == 'approved')
                                    <span class="status-badge bg-green-500/20 text-green-300">
                                        <i class="fas fa-check-circle mr-1"></i> Disetujui
                                    </span>
                                @elseif($pendaftaran->status == 'rejected')
                                    <span class="status-badge bg-red-500/20 text-red-300">
                                        <i class="fas fa-times-circle mr-1"></i> Ditolak
                                    </span>
                                @elseif($pendaftaran->status == 'conditional')
                                    <span class="status-badge bg-blue-500/20 text-blue-300">
                                        <i class="fas fa-exclamation-circle mr-1"></i> Diterima Bersyarat
                                    </span>
                                @endif
                            </div>

                            <p class="text-gray-400 mt-6">
                                Silakan cek menu "Pendaftaran Magang" di sidebar untuk melihat detail atau riwayat pendaftaran
                                Anda.
                            </p>

                        @else
                            {{-- JIKA USER BELUM PERNAH MENDAFTAR --}}
                            <h3 class="claude-title text-xl text-white mb-4">Anda belum mendaftar magang</h3>
                            <p class="text-gray-400 mb-6">
                                Silakan ajukan pendaftaran magang Anda melalui tombol di bawah ini.
                            </p>
                            <a href="{{ route('daftar.create') }}" class="claude-button">
                                <i class="fas fa-plus mr-2"></i> Daftar Magang Sekarang
                            </a>
                        @endif

                    </div>

                    {{-- ✅ KARTU PESERTA AKTIF (MENGGANTIKAN KALENDER) --}}
                    <div
                        class="lg:col-span-2 bg-[#2a2a2a]/60 backdrop-blur-md border border-[#3a3a3a] rounded-xl shadow-lg p-6">
                        <h3 class="claude-title text-xl text-white mb-4">
                            Peserta Magang Aktif Saat Ini
                        </h3>
                        
                        @if(isset($activeUsers) && $activeUsers->count())
                            {{-- Container Utama Daftar Peserta (Disesuaikan untuk 2 kolom) --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-h-[400px] overflow-y-auto hide-scrollbar">
                                @foreach($activeUsers as $user)
                                    <div class="p-4 bg-[#3a3a3a] rounded-xl border border-[#4a4a4a] shadow-md">
                                        <div class="flex items-center justify-between mb-3">
                                            <div class="flex items-center gap-3">
                                                {{-- Avatar --}}
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

                                        {{-- Detail Periode dan Sisa Hari --}}
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

                </div> {{-- Akhir grid --}}

            </div>
        </div>

    @endif

</x-main-layout>
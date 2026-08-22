{{-- resources/views/daftar/index.blade.php --}}
<x-main-layout>
    <style>
        .status-tab {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.65rem 1.15rem;
            border-radius: 12px;
            font-size: 0.8125rem;
            font-weight: 600;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            color: #9ca3af;
            text-decoration: none;
            white-space: nowrap;
        }
        .status-tab:hover {
            background: rgba(255, 255, 255, 0.08);
            color: #ffffff;
            border-color: rgba(255, 255, 255, 0.2);
            transform: translateY(-1px);
        }
        .status-tab.active-all {
            background: rgba(217, 119, 87, 0.2);
            border-color: #d97757;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(217, 119, 87, 0.2);
        }
        .status-tab.active-pending {
            background: rgba(245, 158, 11, 0.2);
            border-color: #f59e0b;
            color: #fef3c7;
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.2);
        }
        .status-tab.active-approved {
            background: rgba(34, 197, 94, 0.2);
            border-color: #22c55e;
            color: #dcfce7;
            box-shadow: 0 4px 12px rgba(34, 197, 94, 0.2);
        }
        .status-tab.active-conditional {
            background: rgba(59, 130, 246, 0.2);
            border-color: #3b82f6;
            color: #dbeafe;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
        }
        .status-tab.active-rejected {
            background: rgba(239, 68, 68, 0.2);
            border-color: #ef4444;
            color: #fee2e2;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2);
        }
        .tab-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.15rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 700;
            background: rgba(255, 255, 255, 0.1);
            color: inherit;
        }
    </style>

    <div class="claude-container">
        
        {{-- Header Section --}}
        <div class="border-b border-[#3a3a3a] header-section">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 py-5">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <div class="flex items-center gap-3">
                            <h2 class="claude-title text-2xl sm:text-3xl text-white">
                                {{ auth()->user()->isAdmin() ? 'Pendaftaran Magang' : 'Pendaftaran Magang Saya' }}
                            </h2>
                            @if(auth()->user()->isAdmin())
                                <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-[#d97757]/20 border border-[#d97757]/40 text-[#e88968]">
                                    Panel Seleksi Admin
                                </span>
                            @else
                                <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-blue-500/20 border border-blue-500/40 text-blue-400">
                                    Portal Pemohon Magang
                                </span>
                            @endif
                        </div>
                        <p class="text-xs sm:text-sm text-gray-400 mt-1">
                            @if(auth()->user()->isAdmin())
                                Kelola, seleksi berkas, dan verifikasi status pendaftaran calon peserta magang BPS Kabupaten Bantul
                            @else
                                Pantau status seleksi berkas dan konfirmasi penerimaan magang Anda di BPS Kabupaten Bantul
                            @endif
                        </p>
                    </div>
                    
                    <div class="flex items-center gap-3 w-full sm:w-auto">
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('daftar.create') }}" 
                               class="claude-button px-4 py-2.5 inline-flex items-center gap-2 flex-shrink-0 w-full sm:w-auto justify-center shadow-lg shadow-[#d97757]/15">
                                <i class="fas fa-plus"></i>
                                <span>Tambah Pendaftar</span>
                            </a>
                        @else
                            @if($pendaftarans->isEmpty())
                                <a href="{{ route('daftar.create') }}" 
                                   class="claude-button px-4 py-2.5 inline-flex items-center gap-2 flex-shrink-0 w-full sm:w-auto justify-center shadow-lg shadow-[#d97757]/15">
                                    <i class="fas fa-paper-plane"></i>
                                    <span>Ajukan Pendaftaran</span>
                                </a>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 space-y-6">

            {{-- ========================================================= --}}
            {{-- A. TAMPILAN KHUSUS ADMIN (FULL FILTER & DATABASE TABLE) --}}
            {{-- ========================================================= --}}
            @if(auth()->user()->isAdmin())

                {{-- 1. STATUS QUICK TABS DENGAN COUNTER --}}
                <div class="flex items-center gap-2.5 overflow-x-auto pb-2 hide-scrollbar">
                    <a href="{{ route('daftar.index', array_merge(request()->except(['status', 'page']), ['status' => 'all'])) }}"
                       class="status-tab {{ ($selectedStatus ?? 'all') === 'all' ? 'active-all' : '' }}">
                        <span>Semua Berkas</span>
                        <span class="tab-badge">{{ $countAll }}</span>
                    </a>

                    <a href="{{ route('daftar.index', array_merge(request()->except(['status', 'page']), ['status' => 'pending'])) }}"
                       class="status-tab {{ ($selectedStatus ?? '') === 'pending' ? 'active-pending' : '' }}">
                        <i class="fas fa-clock text-yellow-400"></i>
                        <span>Menunggu Review</span>
                        <span class="tab-badge bg-yellow-500/20 text-yellow-300 border border-yellow-500/30">{{ $countPending }}</span>
                    </a>

                    <a href="{{ route('daftar.index', array_merge(request()->except(['status', 'page']), ['status' => 'approved'])) }}"
                       class="status-tab {{ ($selectedStatus ?? '') === 'approved' ? 'active-approved' : '' }}">
                        <i class="fas fa-check-circle text-green-400"></i>
                        <span>Disetujui</span>
                        <span class="tab-badge bg-green-500/20 text-green-300 border border-green-500/30">{{ $countApproved }}</span>
                    </a>

                    <a href="{{ route('daftar.index', array_merge(request()->except(['status', 'page']), ['status' => 'conditional'])) }}"
                       class="status-tab {{ ($selectedStatus ?? '') === 'conditional' ? 'active-conditional' : '' }}">
                        <i class="fas fa-exclamation-circle text-blue-400"></i>
                        <span>Bersyarat</span>
                        <span class="tab-badge bg-blue-500/20 text-blue-300 border border-blue-500/30">{{ $countConditional }}</span>
                    </a>

                    <a href="{{ route('daftar.index', array_merge(request()->except(['status', 'page']), ['status' => 'rejected'])) }}"
                       class="status-tab {{ ($selectedStatus ?? '') === 'rejected' ? 'active-rejected' : '' }}">
                        <i class="fas fa-times-circle text-red-400"></i>
                        <span>Ditolak</span>
                        <span class="tab-badge bg-red-500/20 text-red-300 border border-red-500/30">{{ $countRejected }}</span>
                    </a>
                </div>

                {{-- 2. BILAH FILTER MULTI-KRITERIA (ADMIN) --}}
                <div class="filter-container">
                    <form method="GET" action="{{ route('daftar.index') }}" class="filter-form" id="filterForm">
                        @if(($selectedStatus ?? 'all') !== 'all')
                            <input type="hidden" name="status" value="{{ $selectedStatus }}">
                        @endif

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            
                            {{-- Search Box --}}
                            <div class="filter-item">
                                <label class="filter-label flex items-center gap-1.5 mb-1.5">
                                    <i class="fas fa-search text-[#e88968]"></i>
                                    <span>Cari Pendaftar</span>
                                </label>
                                <div class="relative">
                                    <input type="text" name="search" id="searchInput" class="filter-input w-full pr-8" 
                                           placeholder="Nama, email, kampus, prodi..." value="{{ $search ?? '' }}">
                                    @if($search)
                                        <a href="{{ route('daftar.index', array_merge(request()->except('search'), ['page' => 1])) }}" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-white p-1" title="Hapus Pencarian">
                                            <i class="fas fa-times text-xs"></i>
                                        </a>
                                    @else
                                        <button type="submit" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-[#d97757] p-1">
                                            <i class="fas fa-search text-xs"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>

                            {{-- Formasi / Divisi Lowongan --}}
                            <div class="filter-item">
                                <label class="filter-label flex items-center gap-1.5 mb-1.5">
                                    <i class="fas fa-briefcase text-[#e88968]"></i>
                                    <span>Formasi / Divisi</span>
                                </label>
                                <select name="lowongan_id" id="lowonganSelect" class="filter-select w-full" onchange="this.form.submit()">
                                    <option value="all" {{ ($selectedLowongan ?? 'all') == 'all' ? 'selected' : '' }}>Semua Formasi</option>
                                    <option value="umum" {{ ($selectedLowongan ?? '') == 'umum' ? 'selected' : '' }}>Magang Umum (Tanpa Divisi)</option>
                                    @foreach($lowongans as $l)
                                        <option value="{{ $l->id }}" {{ ($selectedLowongan ?? '') == $l->id ? 'selected' : '' }}>
                                            {{ $l->judul_posisi }} ({{ $l->divisi }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Asal Kampus --}}
                            <div class="filter-item">
                                <label class="filter-label flex items-center gap-1.5 mb-1.5">
                                    <i class="fas fa-university text-[#e88968]"></i>
                                    <span>Asal Kampus</span>
                                </label>
                                <select name="kampus" id="kampusSelect" class="filter-select w-full" onchange="this.form.submit()">
                                    <option value="all" {{ ($selectedKampus ?? 'all') == 'all' ? 'selected' : '' }}>Semua Kampus</option>
                                    @foreach($kampusList as $k)
                                        <option value="{{ $k }}" {{ ($selectedKampus ?? '') == $k ? 'selected' : '' }}>
                                            {{ $k }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Konfirmasi Kehadiran --}}
                            <div class="filter-item">
                                <label class="filter-label flex items-center gap-1.5 mb-1.5">
                                    <i class="fas fa-envelope-circle-check text-[#e88968]"></i>
                                    <span>Konfirmasi Hadir</span>
                                </label>
                                <select name="konfirmasi" id="konfirmasiSelect" class="filter-select w-full" onchange="this.form.submit()">
                                    <option value="all" {{ ($selectedKonfirmasi ?? 'all') == 'all' ? 'selected' : '' }}>Semua Status</option>
                                    <option value="confirmed" {{ ($selectedKonfirmasi ?? '') == 'confirmed' ? 'selected' : '' }}>✅ Sudah Dikonfirmasi</option>
                                    <option value="unconfirmed" {{ ($selectedKonfirmasi ?? '') == 'unconfirmed' ? 'selected' : '' }}>⏳ Belum Konfirmasi</option>
                                </select>
                            </div>

                            {{-- Bulan --}}
                            <div class="filter-item">
                                <label class="filter-label flex items-center gap-1.5 mb-1.5">
                                    <i class="fas fa-calendar-alt text-[#e88968]"></i>
                                    <span>Bulan Masuk</span>
                                </label>
                                <select name="month" id="monthSelect" class="filter-select w-full" onchange="this.form.submit()">
                                    <option value="all" {{ ($selectedMonth ?? 'all') == 'all' ? 'selected' : '' }}>Semua Bulan</option>
                                    @for ($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}" {{ ($selectedMonth ?? '') == $m ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                        </option>
                                    @endfor
                                </select>
                            </div>

                            {{-- Tahun --}}
                            <div class="filter-item">
                                <label class="filter-label flex items-center gap-1.5 mb-1.5">
                                    <i class="fas fa-calendar text-[#e88968]"></i>
                                    <span>Tahun</span>
                                </label>
                                <select name="year" id="yearSelect" class="filter-select w-full" onchange="this.form.submit()">
                                    <option value="all" {{ ($selectedYear ?? 'all') == 'all' ? 'selected' : '' }}>Semua Tahun</option>
                                    @foreach($availableYears as $year)
                                        @if($year)
                                            <option value="{{ $year }}" {{ ($selectedYear ?? '') == $year ? 'selected' : '' }}>
                                                Tahun {{ $year }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            {{-- Urutan --}}
                            <div class="filter-item">
                                <label class="filter-label flex items-center gap-1.5 mb-1.5">
                                    <i class="fas fa-sort text-[#e88968]"></i>
                                    <span>Urutkan</span>
                                </label>
                                <select name="sort" id="sortSelect" class="filter-select w-full" onchange="this.form.submit()">
                                    <option value="latest" {{ ($sort ?? 'latest') == 'latest' ? 'selected' : '' }}>Pendaftaran Terbaru</option>
                                    <option value="oldest" {{ ($sort ?? '') == 'oldest' ? 'selected' : '' }}>Pendaftaran Terlama</option>
                                    <option value="name_asc" {{ ($sort ?? '') == 'name_asc' ? 'selected' : '' }}>Nama Pendaftar (A - Z)</option>
                                    <option value="name_desc" {{ ($sort ?? '') == 'name_desc' ? 'selected' : '' }}>Nama Pendaftar (Z - A)</option>
                                </select>
                            </div>

                            {{-- Reset Button --}}
                            <div class="filter-item flex items-end">
                                <a href="{{ route('daftar.index') }}" 
                                   class="filter-btn filter-btn-secondary w-full justify-center flex items-center gap-2 h-[42px] hover:border-red-500/40 hover:text-red-300 transition-colors" title="Reset Semua Filter ke Awal">
                                    <i class="fas fa-undo text-xs"></i>
                                    <span>Reset Filter</span>
                                </a>
                            </div>

                        </div>
                    </form>
                </div>

                {{-- 3. TABEL DESKTOP ADMIN --}}
                <div class="hidden lg:block bg-[#2a2a2a]/60 backdrop-blur-md border border-[#3a3a3a] rounded-2xl shadow-2xl">
                    <div>
                        <table class="w-full text-sm text-left text-gray-300 table-fixed">
                            <colgroup>
                                <col class="w-10">
                                <col class="w-[22%]">
                                <col class="w-[17%]">
                                <col class="w-[21%]">
                                <col class="w-[16%]">
                                <col class="w-20">
                                <col class="w-24">
                                <col class="w-[14%]">
                            </colgroup>
                            <thead class="text-[11px] text-gray-400 uppercase bg-[#1a1a1a]/80 border-b border-[#3a3a3a] tracking-wider">
                                <tr>
                                    <th scope="col" class="px-3 py-3 text-center">No</th>
                                    <th scope="col" class="px-3 py-3">Pendaftar</th>
                                    <th scope="col" class="px-3 py-3">Formasi</th>
                                    <th scope="col" class="px-3 py-3">Kampus / Prodi</th>
                                    <th scope="col" class="px-3 py-3">Periode Magang</th>
                                    <th scope="col" class="px-2 py-3 text-center">Berkas</th>
                                    <th scope="col" class="px-2 py-3 text-center">Status</th>
                                    <th scope="col" class="px-2 py-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#3a3a3a]/80">
                                @forelse ($pendaftarans as $pendaftar)
                                    <tr class="hover:bg-white/[0.04] transition-colors duration-150 group">
                                        {{-- 1. No --}}
                                        <td class="px-3 py-3 text-center text-gray-500 text-xs font-semibold align-middle">
                                            {{ $pendaftarans->firstItem() + $loop->index }}
                                        </td>

                                        {{-- 2. Pendaftar --}}
                                        <td class="px-3 py-3 align-middle">
                                            <div class="flex items-center gap-2.5">
                                                @if($pendaftar->pas_foto)
                                                    <img src="{{ asset('storage/' . $pendaftar->pas_foto) }}" alt="{{ $pendaftar->nama_pendaftar }}" 
                                                         class="w-9 h-9 rounded-full object-cover border border-[#4a4a4a] group-hover:border-[#d97757]/60 flex-shrink-0 shadow-sm transition-colors">
                                                @else
                                                    <div class="w-9 h-9 rounded-full bg-[#d97757]/20 text-[#e88968] flex items-center justify-center font-bold text-sm flex-shrink-0 border border-[#d97757]/30 shadow-sm">
                                                        {{ substr($pendaftar->nama_pendaftar, 0, 1) }}
                                                    </div>
                                                @endif
                                                <div class="min-w-0 overflow-hidden">
                                                    <div class="font-semibold text-sm text-white group-hover:text-[#e88968] transition-colors truncate" title="{{ $pendaftar->nama_pendaftar }}">
                                                        {{ $pendaftar->nama_pendaftar }}
                                                    </div>
                                                    <div class="text-[11px] text-gray-400 truncate flex items-center gap-1 mt-0.5" title="{{ $pendaftar->email ?? '-' }}">
                                                        <i class="fas fa-envelope text-[9px] text-gray-500"></i>
                                                        <span>{{ $pendaftar->email ?? '-' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        {{-- 3. Formasi Lowongan --}}
                                        <td class="px-3 py-3 align-middle">
                                            @if($pendaftar->lowongan)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-semibold bg-[#d97757]/15 border border-[#d97757]/35 text-[#f69d7f]">
                                                    <i class="fas fa-briefcase text-[9px]"></i>
                                                    <span class="truncate max-w-[100px]" title="{{ $pendaftar->lowongan->divisi }}">{{ $pendaftar->lowongan->divisi }}</span>
                                                </span>
                                                <p class="text-[11px] text-gray-400 mt-0.5 truncate" title="{{ $pendaftar->lowongan->judul_posisi }}">
                                                    {{ $pendaftar->lowongan->judul_posisi }}
                                                </p>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-white/5 border border-white/10 text-gray-300">
                                                    <i class="fas fa-user-graduate text-[9px]"></i>
                                                    <span>Magang Umum</span>
                                                </span>
                                            @endif
                                        </td>

                                        {{-- 4. Asal Kampus & Prodi --}}
                                        <td class="px-3 py-3 align-middle overflow-hidden">
                                            <div class="font-medium text-sm text-white truncate" title="{{ $pendaftar->asal_kampus }}">
                                                {{ $pendaftar->asal_kampus }}
                                            </div>
                                            <div class="text-[11px] text-gray-400 truncate mt-0.5" title="{{ $pendaftar->prodi ?? '-' }}">
                                                {{ $pendaftar->prodi ?? '-' }}
                                            </div>
                                        </td>

                                        {{-- 5. Rencana Periode --}}
                                        <td class="px-3 py-3 text-xs align-middle">
                                            @php
                                                $tMulai  = \Carbon\Carbon::parse($pendaftar->tanggal_mulai);
                                                $tSelesai = \Carbon\Carbon::parse($pendaftar->tanggal_selesai);
                                                $durasi  = max(1, (int) round($tMulai->diffInDays($tSelesai) / 30));
                                            @endphp
                                            <div class="text-white font-medium whitespace-nowrap">
                                                {{ $tMulai->format('d/m/y') }} &ndash; {{ $tSelesai->format('d/m/y') }}
                                            </div>
                                            <span class="inline-block text-[11px] text-gray-400 bg-white/5 px-1.5 py-0.5 rounded mt-0.5 border border-white/5 whitespace-nowrap">
                                                {{ $durasi }} Bulan
                                            </span>
                                        </td>

                                        {{-- 6. Berkas PDF + Status + Kehadiran + Aksi (compact) --}}
                                        <td class="px-2 py-3 text-center align-middle">
                                            <div class="flex flex-col items-center gap-1">
                                                @if($pendaftar->surat_permohonan)
                                                    <a href="{{ asset('storage/' . $pendaftar->surat_permohonan) }}" target="_blank" 
                                                       class="w-7 h-7 rounded-lg bg-red-500/15 border border-red-500/30 text-red-400 hover:bg-red-500/30 flex items-center justify-center transition-all"
                                                       title="Preview Surat Permohonan">
                                                        <i class="fas fa-file-pdf text-[10px]"></i>
                                                    </a>
                                                @endif
                                                @if($pendaftar->surat_kampus)
                                                    <a href="{{ asset('storage/' . $pendaftar->surat_kampus) }}" target="_blank" 
                                                       class="w-7 h-7 rounded-lg bg-blue-500/15 border border-blue-500/30 text-blue-400 hover:bg-blue-500/30 flex items-center justify-center transition-all"
                                                       title="Preview Surat Kampus">
                                                        <i class="fas fa-university text-[10px]"></i>
                                                    </a>
                                                @endif
                                                @if(!$pendaftar->surat_permohonan && !$pendaftar->surat_kampus)
                                                    <span class="text-gray-600 text-xs">-</span>
                                                @endif
                                            </div>
                                        </td>

                                        {{-- 7. Status --}}
                                        <td class="px-2 py-3 text-center align-middle">
                                            @if ($pendaftar->status == 'pending')
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-yellow-500/15 text-yellow-300 border border-yellow-500/30 whitespace-nowrap">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-yellow-400 animate-pulse flex-shrink-0"></span>
                                                    Menunggu
                                                </span>
                                            @elseif ($pendaftar->status == 'approved')
                                                <div class="space-y-1">
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-green-500/15 text-green-300 border border-green-500/30 whitespace-nowrap">
                                                        <i class="fas fa-check-circle text-[9px]"></i>
                                                        Disetujui
                                                    </span>
                                                    {{-- Kehadiran inline --}}
                                                    @if ($pendaftar->konfirmasi_at)
                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-medium text-green-400 bg-green-500/10 border border-green-500/20 whitespace-nowrap">
                                                            <i class="fas fa-check text-[9px]"></i> Hadir
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-medium text-gray-400 bg-white/5 border border-white/5 whitespace-nowrap">
                                                            <i class="fas fa-clock text-[9px]"></i> Belum
                                                        </span>
                                                    @endif
                                                </div>
                                            @elseif ($pendaftar->status == 'conditional')
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-blue-500/15 text-blue-300 border border-blue-500/30 whitespace-nowrap">
                                                    <i class="fas fa-info-circle text-[9px]"></i>
                                                    Bersyarat
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-red-500/15 text-red-300 border border-red-500/30 whitespace-nowrap">
                                                    <i class="fas fa-times-circle text-[9px]"></i>
                                                    Ditolak
                                                </span>
                                            @endif
                                        </td>

                                        {{-- 8. Aksi (Review + Hapus) --}}
                                        <td class="px-2 py-3 text-center align-middle whitespace-nowrap">
                                            <div class="flex items-center justify-center gap-1.5">
                                                <a href="{{ route('daftar.show', $pendaftar) }}" 
                                                   class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[11px] font-semibold bg-[#d97757]/15 hover:bg-[#d97757]/30 text-[#f69d7f] border border-[#d97757]/30 hover:border-[#d97757]/60 transition-all shadow-sm" 
                                                   title="Tinjau Detail Berkas">
                                                    <i class="fas fa-eye text-[10px]"></i>
                                                    <span>Review</span>
                                                </a>
                                                <form action="{{ route('daftar.destroy', $pendaftar->id) }}" method="POST" class="inline-block delete-form" data-name="{{ $pendaftar->nama_pendaftar }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="w-7 h-7 rounded-lg text-gray-400 hover:text-red-400 bg-white/5 hover:bg-red-500/15 border border-white/5 hover:border-red-500/30 flex items-center justify-center transition-colors btn-delete" title="Hapus">
                                                        <i class="fas fa-trash-alt text-[10px]"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-16 text-gray-400">
                                            <div class="flex flex-col items-center justify-center gap-2">
                                                <div class="w-16 h-16 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center mb-1">
                                                    <i class="fas fa-folder-open text-2xl text-gray-500"></i>
                                                </div>
                                                <p class="text-base font-bold text-white">Tidak Ada Data Pendaftaran</p>
                                                <p class="text-xs text-gray-400 max-w-sm text-center">
                                                    Coba sesuaikan kata kunci pencarian atau reset filter untuk menampilkan data pendaftaran lainnya.
                                                </p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    {{-- Pagination Footer Bar (Fixed to Card Container, Tidak ikut tergeser saat tabel di-scroll) --}}
                    @if($pendaftarans->hasPages())
                        <div class="border-t border-[#3a3a3a] bg-[#1a1a1a]/80 px-6 py-4">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                <div class="flex items-center gap-2.5 text-xs text-gray-400">
                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-xl bg-white/5 border border-white/10 text-[#e88968]">
                                        <i class="fas fa-users text-xs"></i>
                                    </span>
                                    <span>
                                        Menampilkan <strong class="text-white font-semibold">{{ $pendaftarans->firstItem() ?? 0 }} - {{ $pendaftarans->lastItem() ?? 0 }}</strong> dari <strong class="text-[#e88968] font-bold">{{ $pendaftarans->total() }}</strong> pendaftar
                                    </span>
                                </div>
                                <div class="flex-shrink-0">
                                    {{ $pendaftarans->onEachSide(1)->links('vendor.pagination.magnet') }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- 4. VERSI MOBILE ADMIN --}}
                <div class="block lg:hidden space-y-4">
                    @forelse ($pendaftarans as $pendaftar)
                        <div class="bg-[#2a2a2a]/60 backdrop-blur-md border border-[#3a3a3a] rounded-xl shadow-lg p-5 relative overflow-hidden">
                            <div class="absolute left-0 top-0 bottom-0 w-1.5 {{ $pendaftar->status == 'pending' ? 'bg-yellow-500' : ($pendaftar->status == 'approved' ? 'bg-green-500' : ($pendaftar->status == 'conditional' ? 'bg-blue-500' : 'bg-red-500')) }}"></div>

                            <div class="flex justify-between items-start mb-3 pl-2">
                                <div class="flex items-center gap-3">
                                    @if($pendaftar->pas_foto)
                                        <img src="{{ asset('storage/' . $pendaftar->pas_foto) }}" alt="{{ $pendaftar->nama_pendaftar }}" class="w-10 h-10 rounded-full object-cover border border-[#4a4a4a] flex-shrink-0 shadow-sm">
                                    @else
                                        <div class="w-10 h-10 rounded-full bg-[#d97757]/20 text-[#e88968] flex items-center justify-center font-bold text-sm flex-shrink-0 border border-[#d97757]/30 shadow-sm">
                                            {{ substr($pendaftar->nama_pendaftar, 0, 1) }}
                                        </div>
                                    @endif
                                    <div>
                                        <h3 class="font-bold text-white text-base leading-tight">{{ $pendaftar->nama_pendaftar }}</h3>
                                        <p class="text-xs text-gray-400">{{ $pendaftar->email ?? '-' }}</p>
                                    </div>
                                </div>

                                <div>
                                    @if ($pendaftar->status == 'pending')
                                        <span class="px-2.5 py-1 text-[11px] font-semibold rounded-full bg-yellow-500/20 text-yellow-300 border border-yellow-500/30">Menunggu</span>
                                    @elseif ($pendaftar->status == 'approved')
                                        <span class="px-2.5 py-1 text-[11px] font-semibold rounded-full bg-green-500/20 text-green-300 border border-green-500/30">Disetujui</span>
                                    @elseif ($pendaftar->status == 'conditional')
                                        <span class="px-2.5 py-1 text-[11px] font-semibold rounded-full bg-blue-500/20 text-blue-300 border border-blue-500/30">Bersyarat</span>
                                    @else
                                        <span class="px-2.5 py-1 text-[11px] font-semibold rounded-full bg-red-500/20 text-red-300 border border-red-500/30">Ditolak</span>
                                    @endif
                                </div>
                            </div>

                            <div class="space-y-2.5 text-xs pl-2 mb-4 border-t border-[#3a3a3a] pt-3">
                                @if($pendaftar->lowongan)
                                    <div class="flex items-center justify-between">
                                        <span class="text-gray-400">Formasi:</span>
                                        <span class="text-[#e88968] font-semibold">{{ $pendaftar->lowongan->divisi }}</span>
                                    </div>
                                @endif
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-400">Kampus:</span>
                                    <span class="text-white font-medium truncate max-w-[200px]">{{ $pendaftar->asal_kampus }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-400">Periode:</span>
                                    <span class="text-white font-medium">
                                        {{ \Carbon\Carbon::parse($pendaftar->tanggal_mulai)->format('d/m/y') }} - 
                                        {{ \Carbon\Carbon::parse($pendaftar->tanggal_selesai)->format('d/m/y') }}
                                    </span>
                                </div>
                            </div>

                            <div class="pt-3 border-t border-[#3a3a3a] flex justify-between items-center pl-2">
                                <div>
                                    @if ($pendaftar->status == 'approved' && $pendaftar->konfirmasi_at)
                                        <span class="text-xs text-green-400 font-semibold flex items-center gap-1">
                                            <i class="fas fa-check-circle"></i> Hadir Dikonfirmasi
                                        </span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-2">
                                    <form action="{{ route('daftar.destroy', $pendaftar->id) }}" method="POST" class="delete-form" data-name="{{ $pendaftar->nama_pendaftar }}">
                                        @csrf @method('DELETE')
                                        <button type="button" class="px-3 py-1.5 text-xs text-red-400 bg-red-500/10 rounded-lg hover:bg-red-500/20 btn-delete">
                                            <i class="fas fa-trash-alt mr-1"></i> Hapus
                                        </button>
                                    </form>
                                    <a href="{{ route('daftar.show', $pendaftar) }}" class="claude-button text-xs px-3.5 py-1.5">
                                        <i class="fas fa-eye mr-1"></i> Detail
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12 text-gray-400 bg-[#2a2a2a]/60 backdrop-blur-md border border-[#3a3a3a] rounded-xl p-6">
                            <p class="text-sm font-semibold text-white">Tidak ada data pendaftar yang cocok.</p>
                        </div>
                    @endforelse

                    @if($pendaftarans->hasPages())
                        <div class="mt-4 pt-3 border-t border-[#3a3a3a] flex justify-center">
                            {{ $pendaftarans->onEachSide(1)->links('vendor.pagination.magnet') }}
                        </div>
                    @endif
                </div>

            {{-- ========================================================= --}}
            {{-- B. TAMPILAN KHUSUS USER (APPLICATION TRACKER HUB)        --}}
            {{-- ========================================================= --}}
            @else

                @if($pendaftarans->isEmpty())
                    {{-- 1. USER BELUM MENDAFTAR (WELCOME & CTA GUIDE) --}}
                    <div class="bg-[#2a2a2a]/60 backdrop-blur-md border border-[#3a3a3a] rounded-2xl p-8 sm:p-12 text-center shadow-xl">
                        <div class="w-20 h-20 rounded-3xl bg-[#d97757]/15 text-[#e88968] flex items-center justify-center text-3xl mx-auto mb-6 border border-[#d97757]/30 shadow-lg">
                            <i class="fas fa-clipboard-check"></i>
                        </div>
                        <h3 class="claude-title text-2xl sm:text-3xl text-white mb-3">
                            Mulai Perjalanan Magang Anda di BPS Bantul
                        </h3>
                        <p class="text-sm sm:text-base text-gray-300 max-w-xl mx-auto mb-8 leading-relaxed">
                            Anda belum memiliki riwayat pengajuan pendaftaran magang. Pilih formasi divisi yang Anda minati dan unggah berkas persyaratan sekarang.
                        </p>

                        {{-- 3 Langkah Mudah --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 max-w-3xl mx-auto text-left mb-10">
                            <div class="p-5 bg-[#1a1a1a]/80 rounded-xl border border-[#3a3a3a]">
                                <div class="w-8 h-8 rounded-lg bg-[#d97757]/20 text-[#e88968] font-bold text-sm flex items-center justify-center mb-3">1</div>
                                <h4 class="text-white font-bold text-sm mb-1">Pilih Formasi Lowongan</h4>
                                <p class="text-xs text-gray-400">Pilih divisi yang sesuai dengan jurusan dan minat keahlian Anda di BPS.</p>
                            </div>
                            <div class="p-5 bg-[#1a1a1a]/80 rounded-xl border border-[#3a3a3a]">
                                <div class="w-8 h-8 rounded-lg bg-[#d97757]/20 text-[#e88968] font-bold text-sm flex items-center justify-center mb-3">2</div>
                                <h4 class="text-white font-bold text-sm mb-1">Siapkan Dokumen PDF</h4>
                                <p class="text-xs text-gray-400">Siapkan Surat Permohonan & Surat Rekomendasi/Pengantar dari Kampus.</p>
                            </div>
                            <div class="p-5 bg-[#1a1a1a]/80 rounded-xl border border-[#3a3a3a]">
                                <div class="w-8 h-8 rounded-lg bg-[#d97757]/20 text-[#e88968] font-bold text-sm flex items-center justify-center mb-3">3</div>
                                <h4 class="text-white font-bold text-sm mb-1">Kirim & Pantau Seleksi</h4>
                                <p class="text-xs text-gray-400">Kirim formulir dan pantau hasil verifikasi berkas secara transparan di sini.</p>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                            <a href="{{ route('daftar.create') }}" class="claude-button px-8 py-3 text-sm font-semibold inline-flex items-center gap-2 shadow-xl shadow-[#d97757]/20">
                                <i class="fas fa-paper-plane"></i>
                                <span>Ajukan Pendaftaran Magang</span>
                            </a>
                            <a href="{{ route('lowongan.index') }}" class="claude-button-secondary px-6 py-3 text-sm font-semibold inline-flex items-center gap-2">
                                <i class="fas fa-briefcase"></i>
                                <span>Lihat Katalog Formasi</span>
                            </a>
                        </div>
                    </div>

                @else
                    {{-- 2. USER SUDAH MENDAFTAR (APPLICATION TRACKER) --}}
                    @foreach($pendaftarans as $pendaftar)
                        <div class="space-y-6">

                            {{-- STATUS STEPPER TIMELINE --}}
                            <div class="bg-[#2a2a2a]/60 backdrop-blur-md border border-[#3a3a3a] rounded-2xl p-6 shadow-xl">
                                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-6 flex items-center gap-2">
                                    <i class="fas fa-timeline text-[#e88968]"></i>
                                    <span>Alur Tahapan Seleksi Magang Anda</span>
                                </h3>

                                <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 relative">
                                    
                                    {{-- Tahap 1: Pengajuan Berkas --}}
                                    <div class="p-4 rounded-xl border {{ $pendaftar ? 'bg-green-500/10 border-green-500/30' : 'bg-[#1a1a1a] border-[#3a3a3a]' }}">
                                        <div class="flex items-center gap-2 mb-1">
                                            <i class="fas fa-check-circle text-green-400 text-sm"></i>
                                            <span class="text-xs font-bold text-white">1. Berkas Terkirim</span>
                                        </div>
                                        <p class="text-[11px] text-gray-400">{{ \Carbon\Carbon::parse($pendaftar->created_at)->translatedFormat('d M Y, H:i') }}</p>
                                    </div>

                                    {{-- Tahap 2: Verifikasi BPS --}}
                                    <div class="p-4 rounded-xl border {{ in_array($pendaftar->status, ['pending', 'approved', 'conditional', 'rejected']) ? 'bg-blue-500/10 border-blue-500/30' : 'bg-[#1a1a1a] border-[#3a3a3a]' }}">
                                        <div class="flex items-center gap-2 mb-1">
                                            @if($pendaftar->status == 'pending')
                                                <i class="fas fa-spinner fa-spin text-yellow-400 text-sm"></i>
                                                <span class="text-xs font-bold text-yellow-300">2. Proses Verifikasi</span>
                                            @else
                                                <i class="fas fa-check-circle text-green-400 text-sm"></i>
                                                <span class="text-xs font-bold text-white">2. Verifikasi Selesai</span>
                                            @endif
                                        </div>
                                        <p class="text-[11px] text-gray-400">Tim Seleksi BPS Bantul</p>
                                    </div>

                                    {{-- Tahap 3: Keputusan Seleksi --}}
                                    <div class="p-4 rounded-xl border 
                                        {{ $pendaftar->status == 'approved' ? 'bg-green-500/10 border-green-500/30' : 
                                           ($pendaftar->status == 'conditional' ? 'bg-blue-500/10 border-blue-500/30' : 
                                           ($pendaftar->status == 'rejected' ? 'bg-red-500/10 border-red-500/30' : 'bg-[#1a1a1a] border-[#3a3a3a]')) }}">
                                        <div class="flex items-center gap-2 mb-1">
                                            @if($pendaftar->status == 'approved')
                                                <i class="fas fa-check-circle text-green-400 text-sm"></i>
                                                <span class="text-xs font-bold text-green-300">3. Disetujui</span>
                                            @elseif($pendaftar->status == 'conditional')
                                                <i class="fas fa-exclamation-circle text-blue-400 text-sm"></i>
                                                <span class="text-xs font-bold text-blue-300">3. Bersyarat</span>
                                            @elseif($pendaftar->status == 'rejected')
                                                <i class="fas fa-times-circle text-red-400 text-sm"></i>
                                                <span class="text-xs font-bold text-red-300">3. Belum Lolos</span>
                                            @else
                                                <i class="fas fa-clock text-gray-500 text-sm"></i>
                                                <span class="text-xs font-bold text-gray-400">3. Pengumuman</span>
                                            @endif
                                        </div>
                                        <p class="text-[11px] text-gray-400">
                                            {{ $pendaftar->status == 'pending' ? 'Menunggu keputusan' : 'Hasil telah keluar' }}
                                        </p>
                                    </div>

                                    {{-- Tahap 4: Konfirmasi Kehadiran --}}
                                    <div class="p-4 rounded-xl border {{ $pendaftar->konfirmasi_at ? 'bg-green-500/10 border-green-500/30' : 'bg-[#1a1a1a] border-[#3a3a3a]' }}">
                                        <div class="flex items-center gap-2 mb-1">
                                            @if($pendaftar->konfirmasi_at)
                                                <i class="fas fa-check-double text-green-400 text-sm"></i>
                                                <span class="text-xs font-bold text-green-300">4. Siap Magang</span>
                                            @else
                                                <i class="fas fa-user-check {{ $pendaftar->status == 'approved' ? 'text-yellow-400' : 'text-gray-500' }} text-sm"></i>
                                                <span class="text-xs font-bold {{ $pendaftar->status == 'approved' ? 'text-yellow-300' : 'text-gray-400' }}">4. Konfirmasi Hadir</span>
                                            @endif
                                        </div>
                                        <p class="text-[11px] text-gray-400">
                                            {{ $pendaftar->konfirmasi_at ? 'Kehadiran terkonfirmasi' : ($pendaftar->status == 'approved' ? 'Wajib konfirmasi' : 'Tahap akhir') }}
                                        </p>
                                    </div>

                                </div>
                            </div>

                            {{-- STATUS BANNER AKTIF --}}
                            @if($pendaftar->status == 'pending')
                                <div class="bg-yellow-500/10 border border-yellow-500/30 rounded-2xl p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-lg">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 rounded-xl bg-yellow-500/20 text-yellow-400 flex items-center justify-center text-xl flex-shrink-0">
                                            <i class="fas fa-clock"></i>
                                        </div>
                                        <div>
                                            <h4 class="text-base font-bold text-yellow-200">Berkas Sedang Ditinjau Admin BPS</h4>
                                            <p class="text-xs text-yellow-300/80 mt-0.5 leading-relaxed">
                                                Pendaftaran Anda telah berhasil terkirim dan saat ini sedang dalam antrean verifikasi oleh tim BPS Kabupaten Bantul. Silakan cek berkala halaman ini.
                                            </p>
                                        </div>
                                    </div>
                                    <form action="{{ route('daftar.destroy', $pendaftar->id) }}" method="POST" class="delete-form" data-name="{{ $pendaftar->nama_pendaftar }}">
                                        @csrf @method('DELETE')
                                        <button type="button" class="px-4 py-2 text-xs font-medium text-red-400 bg-red-500/10 border border-red-500/20 rounded-xl hover:bg-red-500/20 transition-colors btn-delete whitespace-nowrap">
                                            <i class="fas fa-trash-alt mr-1"></i> Batalkan Pendaftaran
                                        </button>
                                    </form>
                                </div>

                            @elseif($pendaftar->status == 'approved')
                                <div class="bg-green-500/10 border border-green-500/30 rounded-2xl p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-lg">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 rounded-xl bg-green-500/20 text-green-400 flex items-center justify-center text-xl flex-shrink-0">
                                            <i class="fas fa-check-circle"></i>
                                        </div>
                                        <div>
                                            <h4 class="text-base font-bold text-green-200">Selamat! Pendaftaran Anda Telah Disetujui</h4>
                                            <p class="text-xs text-green-300/80 mt-0.5 leading-relaxed">
                                                Anda telah resmi diterima untuk melaksanakan program magang di BPS Kabupaten Bantul.
                                                @if(!$pendaftar->konfirmasi_at)
                                                    Silakan klik tombol konfirmasi kehadiran di bawah untuk menyelesaikan proses administrasi.
                                                @else
                                                    Kehadiran Anda telah terkonfirmasi pada <strong>{{ \Carbon\Carbon::parse($pendaftar->konfirmasi_at)->translatedFormat('d F Y') }}</strong>.
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                    @if(!$pendaftar->konfirmasi_at)
                                        <a href="{{ route('daftar.konfirmasi', $pendaftar) }}" class="claude-button px-6 py-2.5 text-xs font-bold whitespace-nowrap shadow-lg shadow-green-500/20">
                                            <i class="fas fa-check mr-1.5"></i> Konfirmasi Kehadiran Sekarang
                                        </a>
                                    @else
                                        <span class="px-4 py-2 rounded-xl bg-green-500/20 text-green-300 border border-green-500/30 text-xs font-semibold whitespace-nowrap">
                                            <i class="fas fa-check-double mr-1"></i> Kehadiran Terkonfirmasi
                                        </span>
                                    @endif
                                </div>

                            @elseif($pendaftar->status == 'conditional')
                                <div class="bg-blue-500/10 border border-blue-500/30 rounded-2xl p-6 shadow-lg">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 rounded-xl bg-blue-500/20 text-blue-400 flex items-center justify-center text-xl flex-shrink-0">
                                            <i class="fas fa-info-circle"></i>
                                        </div>
                                        <div>
                                            <h4 class="text-base font-bold text-blue-200">Pendaftaran Disetujui Bersyarat</h4>
                                            <p class="text-xs text-blue-300/80 mt-0.5">
                                                Terdapat beberapa syarat tambahan yang perlu Anda penuhi:
                                            </p>
                                            @if($pendaftar->remarks)
                                                <div class="mt-3 p-3 bg-[#1a1a1a] rounded-xl border border-blue-500/30 text-xs text-white">
                                                    <strong>Catatan Admin:</strong> {{ $pendaftar->remarks }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                            @elseif($pendaftar->status == 'rejected')
                                <div class="bg-red-500/10 border border-red-500/30 rounded-2xl p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-lg">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 rounded-xl bg-red-500/20 text-red-400 flex items-center justify-center text-xl flex-shrink-0">
                                            <i class="fas fa-times-circle"></i>
                                        </div>
                                        <div>
                                            <h4 class="text-base font-bold text-red-200">Pendaftaran Belum Diterima</h4>
                                            <p class="text-xs text-red-300/80 mt-0.5 leading-relaxed">
                                                Mohon maaf, kuota formasi pada periode yang Anda pilih telah penuh atau berkas belum memenuhi kualifikasi.
                                            </p>
                                            @if($pendaftar->remarks)
                                                <div class="mt-2 text-xs text-red-300">
                                                    <em>"{{ $pendaftar->remarks }}"</em>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <a href="{{ route('daftar.create') }}" class="claude-button px-5 py-2.5 text-xs font-semibold whitespace-nowrap">
                                        <i class="fas fa-redo mr-1.5"></i> Ajukan Pendaftaran Baru
                                    </a>
                                </div>
                            @endif

                            {{-- KARTU RINCIAN DATA PENDAFTARAN SAYA --}}
                            <div class="bg-[#2a2a2a]/60 backdrop-blur-md border border-[#3a3a3a] rounded-2xl p-6 sm:p-8 shadow-xl">
                                <div class="flex items-center justify-between pb-5 border-b border-[#3a3a3a] mb-6">
                                    <h3 class="claude-title text-xl text-white flex items-center gap-2">
                                        <i class="fas fa-id-card text-[#e88968]"></i>
                                        <span>Rincian Berkas Pendaftaran Anda</span>
                                    </h3>
                                    <a href="{{ route('daftar.show', $pendaftar) }}" class="text-xs text-[#e88968] hover:text-white font-semibold flex items-center gap-1">
                                        <span>Lihat Halaman Detail</span>
                                        <i class="fas fa-arrow-right text-[10px]"></i>
                                    </a>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                    
                                    {{-- Kolom 1: Profil & Identitas --}}
                                    <div class="space-y-4">
                                        <div>
                                            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block mb-1">Nama Lengkap</span>
                                            <p class="text-base font-bold text-white">{{ $pendaftar->nama_pendaftar }}</p>
                                        </div>
                                        <div>
                                            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block mb-1">Email Aktif</span>
                                            <p class="text-sm text-gray-300">{{ $pendaftar->email ?? '-' }}</p>
                                        </div>
                                        <div>
                                            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block mb-1">Nomor WhatsApp</span>
                                            <p class="text-sm text-gray-300">{{ $pendaftar->whatsapp ?? '-' }}</p>
                                        </div>
                                    </div>

                                    {{-- Kolom 2: Formasi & Akademik --}}
                                    <div class="space-y-4">
                                        <div>
                                            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block mb-1">Formasi / Divisi Dilamar</span>
                                            @if($pendaftar->lowongan)
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold bg-[#d97757]/20 border border-[#d97757]/40 text-[#f69d7f]">
                                                    <i class="fas fa-briefcase"></i>
                                                    <span>{{ $pendaftar->lowongan->divisi }}</span>
                                                </span>
                                                <p class="text-xs text-gray-400 mt-1">{{ $pendaftar->lowongan->judul_posisi }}</p>
                                            @else
                                                <span class="text-sm text-gray-300 font-medium">Magang Umum</span>
                                            @endif
                                        </div>
                                        <div>
                                            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block mb-1">Asal Kampus & Jurusan</span>
                                            <p class="text-sm font-bold text-white">{{ $pendaftar->asal_kampus }}</p>
                                            <p class="text-xs text-gray-400">{{ $pendaftar->prodi ?? '-' }}</p>
                                        </div>
                                        <div>
                                            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block mb-1">Rencana Periode Magang</span>
                                            <p class="text-sm text-white font-medium">
                                                {{ \Carbon\Carbon::parse($pendaftar->tanggal_mulai)->format('d M Y') }} s.d. 
                                                {{ \Carbon\Carbon::parse($pendaftar->tanggal_selesai)->format('d M Y') }}
                                            </p>
                                        </div>
                                    </div>

                                    {{-- Kolom 3: Berkas Persyaratan Terunggah --}}
                                    <div class="space-y-3">
                                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block mb-1">Dokumen Terunggah</span>
                                        
                                        @if($pendaftar->surat_permohonan)
                                            <a href="{{ asset('storage/' . $pendaftar->surat_permohonan) }}" target="_blank" 
                                               class="p-3 bg-[#1a1a1a] rounded-xl border border-[#3a3a3a] hover:border-red-500/40 flex items-center justify-between transition-colors group">
                                                <div class="flex items-center gap-2.5">
                                                    <i class="fas fa-file-pdf text-red-400 text-lg"></i>
                                                    <span class="text-xs font-medium text-gray-200 group-hover:text-white">Surat Permohonan</span>
                                                </div>
                                                <i class="fas fa-external-link-alt text-xs text-gray-500 group-hover:text-red-400"></i>
                                            </a>
                                        @endif

                                        @if($pendaftar->surat_kampus)
                                            <a href="{{ asset('storage/' . $pendaftar->surat_kampus) }}" target="_blank" 
                                               class="p-3 bg-[#1a1a1a] rounded-xl border border-[#3a3a3a] hover:border-blue-500/40 flex items-center justify-between transition-colors group">
                                                <div class="flex items-center gap-2.5">
                                                    <i class="fas fa-university text-blue-400 text-lg"></i>
                                                    <span class="text-xs font-medium text-gray-200 group-hover:text-white">Surat Pengantar Kampus</span>
                                                </div>
                                                <i class="fas fa-external-link-alt text-xs text-gray-500 group-hover:text-blue-400"></i>
                                            </a>
                                        @endif

                                        @if($pendaftar->pas_foto)
                                            <a href="{{ asset('storage/' . $pendaftar->pas_foto) }}" target="_blank" 
                                               class="p-3 bg-[#1a1a1a] rounded-xl border border-[#3a3a3a] hover:border-[#d97757]/40 flex items-center justify-between transition-colors group">
                                                <div class="flex items-center gap-2.5">
                                                    <i class="fas fa-image text-[#e88968] text-lg"></i>
                                                    <span class="text-xs font-medium text-gray-200 group-hover:text-white">Pas Foto Formal</span>
                                                </div>
                                                <i class="fas fa-external-link-alt text-xs text-gray-500 group-hover:text-[#e88968]"></i>
                                            </a>
                                        @endif
                                    </div>

                                </div>
                            </div>

                        </div>
                    @endforeach
                @endif

            @endif

        </div>
    </div>
    
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                confirmButtonColor: '#d97757',
                background: '#1f2937',
                color: '#fff'
            });
        @endif

        document.addEventListener('click', function(e) {
            const button = e.target.closest('.btn-delete');
            if (button) {
                e.preventDefault();
                e.stopPropagation(); 

                const form = button.closest('.delete-form');
                if (!form) return;
                const name = form.getAttribute('data-name') || 'Data ini';

                Swal.fire({
                    title: 'Hapus Pendaftaran?',
                    text: `Anda yakin ingin menghapus data "${name}"? Berkas PDF lampiran juga akan dihapus.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444', 
                    cancelButtonColor: '#374151', 
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    background: '#1f2937', 
                    color: '#fff'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            }
        });
    </script>
    @endpush
</x-main-layout>
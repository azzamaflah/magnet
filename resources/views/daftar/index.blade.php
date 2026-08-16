<x-main-layout>
    <div class="claude-container">
        
        {{-- Header Section (SUDAH DIMODIFIKASI) --}}
        <div class="border-b border-[#3a3a3a] header-section">
            <div class="max-w-7xl mx-auto px-6 py-4">
                <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                    {{-- Judul --}}
                    <h2 class="claude-title text-2xl text-white w-full md:w-auto">
                        Pendaftaran Magang
                    </h2>
                    
                    {{-- Tombol Aksi (Filter sudah dipindah) --}}
                    <div class="flex flex-col md:flex-row items-center gap-4 w-full md:w-auto">
                        <a href="{{ route('daftar.create') }}" 
                           class="claude-button px-4 py-2.5 inline-flex items-center gap-2 flex-shrink-0 w-full sm:w-auto justify-center">
                            <i class="fas fa-user-plus"></i>
                            <span class="hidden sm:inline">Daftar</span> 
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6"> {{-- Menyamakan padding dengan magang.index --}}
            
            <!---- tidak dipakai > 
            <!--@if (session('success'))-->
            <!--    <div class="success-alert mb-6">-->
            <!--        <span>{{ session('success') }}</span>-->
            <!--    </div>-->
            <!--@endif-->

            {{-- ============================================== --}}
            {{-- === FILTER SECTION BARU (Gaya magang.index) === --}}
            {{-- ============================================== --}}
            @if(auth()->user()->isAdmin())
            <div class="filter-container mb-6">
                <form method="GET" action="{{ route('daftar.index') }}" class="filter-form" id="filterForm">
                    <div class="filter-grid">
                        
                        {{-- Filter Search (Item 1) --}}
                        <div class="filter-item search-item">
                            <div class="filter-label">
                                <i class="fas fa-search"></i>
                                <span>Cari Pendaftar</span>
                            </div>
                            <div style="position: relative;">
                                <input type="search" name="search" id="search" class="filter-input w-full" 
                                       placeholder="Cari nama/kampus..." value="{{ $search ?? '' }}">
                                {{-- Tombol submit untuk search (bukan AJAX) --}}
                                <button type="submit" class="absolute top-1/2 right-2 -translate-y-1/2 text-gray-400 hover:text-[#d97757] p-1.5 rounded-md focus:outline-none">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Filter Bulan (Item 2) --}}
                        <div class="filter-item">
                            <div class="filter-label">
                                <i class="fas fa-calendar-alt"></i>
                                <span>Bulan</span>
                            </div>
                            <div style="position: relative;">
                                <select name="month" id="month" class="filter-select w-full" onchange="this.form.submit()">
                                    <option value="all" {{ $selectedMonth == 'all' ? 'selected' : '' }}>Semua Bulan</option>
                                    @for ($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}" {{ $selectedMonth == $m ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        {{-- Filter Tahun (Item 3) --}}
                        <div class="filter-item">
                            <div class="filter-label">
                                <i class="fas fa-calendar"></i>
                                <span>Tahun</span>
                            </div>
                            <div style="position: relative;">
                                <select name="year" id="year" class="filter-select w-full" onchange="this.form.submit()">
                                    <option value="all" {{ $selectedYear == 'all' ? 'selected' : '' }}>Semua Tahun</option>
                                    @foreach($availableYears as $year)
                                        @if($year)
                                            <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                                                {{ $year }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Sort + Reset (Item 4) --}}
                        <div class="filter-item filter-actions">
                            <div class="filter-label">
                                <i class="fas fa-sort"></i>
                                <span>Urutkan</span>
                            </div>
                            
                            {{-- Container untuk toggle dan reset --}}
                            <div class="flex items-center gap-2">
                                {{-- Sort Toggle --}}
                                <div class="flex-grow flex items-center gap-1 p-1 bg-[#1a1a1a] border border-[#3a3a3a] rounded-lg">
                                    <a href="{{ route('daftar.index', array_merge(request()->query(), ['sort' => 'latest'])) }}"
                                       class="w-full text-center px-3 py-1.5 rounded-md text-xs font-medium transition-colors
                                              {{ ($sort ?? 'latest') == 'latest' ? 'bg-[#d97757] text-white shadow' : 'text-gray-400 hover:text-white' }}" title="Terbaru">
                                        <i class="fas fa-sort-amount-down fa-fw"></i>
                                    </a>
                                    <a href="{{ route('daftar.index', array_merge(request()->query(), ['sort' => 'oldest'])) }}"
                                       class="w-full text-center px-3 py-1.5 rounded-md text-xs font-medium transition-colors
                                              {{ $sort == 'oldest' ? 'bg-[#d97757] text-white shadow' : 'text-gray-400 hover:text-white' }}" title="Terlama">
                                        <i class="fas fa-sort-amount-up fa-fw"></i>
                                    </a>
                                </div>
                                
                                <!--{{-- Tombol Reset --}}-->
                                <!--@if($selectedYear != 'all' || $selectedMonth != 'all' || !empty($search) || ($sort ?? 'latest') == 'oldest')-->
                                <!--    <a href="{{ route('daftar.index') }}" class="filter-btn filter-btn-secondary" title="Reset Filter">-->
                                <!--        <i class="fas fa-times"></i>-->
                                <!--    </a>-->
                                <!--@else-->
                                <!--    {{-- Placeholder agar layout tidak bergeser --}}-->
                                <!--    <a class="filter-btn filter-btn-secondary invisible" href="#">-->
                                <!--        <i class="fas fa-times"></i>-->
                                <!--    </a>-->
                                <!--@endif-->
                            </div>
                        </div>

                    </div> {{-- End filter-grid --}}
                </form>
            </div>
            @endif
            {{-- ============================================== --}}
            {{-- === AKHIR FILTER SECTION BARU === --}}
            {{-- ============================================== --}}


            {{-- Lokasi Pesan Sukses LAMA (dihapus dari sini) --}}

            {{-- =============================================== --}}
            {{-- VERSI TABEL (DESKTOP) --}}
            {{-- =============================================== --}}
            <div class="hidden lg:block bg-[#2a2a2a]/60 backdrop-blur-md border border-[#3a3a3a] rounded-xl shadow-lg overflow-hidden">
                {{-- ... (Isi tabel tetap sama) ... --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-300">
                        <thead class="text-xs text-gray-400 uppercase bg-[#1a1a1a]/50">
                            <tr>
                                <th scope="col" class="px-6 py-3 w-12 text-center">No</th>
                                <th scope="col" class="px-6 py-3">Nama Pendaftar</th>
                                <th scope="col" class="px-6 py-3">Asal Kampus</th>
                                <th scope="col" class="px-6 py-3">Periode</th>
                                <th scope="col" class="px-6 py-3">Status</th>
                                <th scope="col" class="px-6 py-3">Konfirmasi Kehadiran</th>
                                <th scope="col" class="px-6 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pendaftarans as $pendaftar)
                                <tr class="border-b border-[#3a3a3a] hover:bg-[#ffffff]/5 transition-colors duration-150 group">
                                    <td class="px-6 py-4 text-center text-gray-400">
                                        {{ $pendaftarans->firstItem() + $loop->index }}
                                    </td>
                                    <td class="px-6 py-4 font-medium text-white whitespace-nowrap">
                                        <div>{{ $pendaftar->nama_pendaftar }}</div>
                                        @if($pendaftaran = $pendaftar->lowongan)
                                            <span class="text-[11px] text-[#e88968] font-normal block mt-0.5">
                                                <i class="fas fa-briefcase text-[10px] mr-0.5"></i> {{ $pendaftar->lowongan->judul_posisi }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">{{ $pendaftar->asal_kampus }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ \Carbon\Carbon::parse($pendaftar->tanggal_mulai)->format('d/m/Y') }} - 
                                        {{ \Carbon\Carbon::parse($pendaftar->tanggal_selesai)->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @if ($pendaftar->status == 'pending')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-900/50 text-yellow-300 border border-yellow-700">Menunggu</span>
                                        @elseif ($pendaftar->status == 'approved')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-900/50 text-green-300 border border-green-700">Disetujui</span>
                                        @elseif ($pendaftar->status == 'conditional')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-900/50 text-blue-300 border border-blue-700">Bersyarat</span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-900/50 text-red-300 border border-red-700">Ditolak</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if ($pendaftar->status == 'approved')
                                            @if ($pendaftar->konfirmasi_at)
                                                <span class="text-green-400 text-xs flex items-center gap-1">
                                                    <i class="fas fa-check-circle"></i> Dikonfirmasi
                                                </span>
                                            @else
                                                @if(auth()->user()->isAdmin())
                                                    <span class="text-gray-500 text-xs italic">Belum</span>
                                                @else
                                                    <a href="{{ route('daftar.konfirmasi', $pendaftar) }}" 
                                                       class="claude-button inline-flex items-center gap-1 px-2 py-1 text-[10px] h-6 relative z-10">
                                                        Konfirmasi
                                                    </a>
                                                @endif
                                            @endif
                                        @else
                                            <span class="text-gray-600">-</span>
                                        @endif
                                    </td>
                                    
                                    {{-- KOLOM AKSI (DESKTOP) --}}
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-4 relative z-10">
                                            
                                            {{-- Tombol Detail --}}
                                            @if(auth()->user()->isAdmin() || auth()->id() == $pendaftar->user_id)
                                                <a href="{{ route('daftar.show', $pendaftar) }}" class="font-medium text-[#d97757] hover:underline">
                                                    Detail
                                                </a>
                                            @endif

                                            {{-- Tombol Hapus --}}
                                            @if(auth()->user()->isAdmin() || (auth()->id() == $pendaftar->user_id && $pendaftar->status == 'pending'))
                                                <form action="{{ route('daftar.destroy', $pendaftar->id) }}" method="POST" class="inline-block delete-form" data-name="{{ $pendaftar->nama_pendaftar }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="font-medium text-red-500 hover:text-red-400 transition-colors btn-delete relative z-10">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr class="border-b border-[#3a3a3a]">
                                    <td colspan="6" class="text-center py-6 text-gray-500">
                                        @if($selectedYear == 'all' && $selectedMonth == 'all' && empty($search) && ($sort ?? 'latest') == 'latest')
                                            Belum ada data pendaftar.
                                        @else
                                            Tidak ada data pendaftar yang cocok dengan filter pencarian.
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                   <!--modifikasi - start-->
                    <div class="border-t border-[#3a3a3a] bg-[#1a1a1a]/20 px-4 py-3">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div class="text-xs text-gray-400">
                                Showing {{ $pendaftarans->firstItem() ?? 0 }} to {{ $pendaftarans->lastItem() ?? 0 }}
                                of {{ $pendaftarans->total() }} results
                            </div>
                    
                            <div class="max-w-full overflow-x-auto">
                                {{ $pendaftarans->onEachSide(1)->links('vendor.pagination.magnet') }}
                            </div>
                        </div>
                    </div>
                    <!--modifikasi - end-->




                </div>
            </div>

            {{-- ========================================== --}}
            {{-- VERSI KARTU (MOBILE) --}}
            {{-- ========================================== --}}
            <div class="block lg:hidden space-y-4">
                @forelse ($pendaftarans as $pendaftar)
                    {{-- ... (Isi kartu mobile tetap sama) ... --}}
                    <div class="bg-[#2a2a2a]/60 backdrop-blur-md border border-[#3a3a3a] rounded-xl shadow-lg p-4 relative overflow-hidden">
                        
                        <div class="absolute left-0 top-0 bottom-0 w-1 {{ $pendaftar->status == 'pending' ? 'bg-yellow-600' : ($pendaftar->status == 'approved' ? 'bg-green-600' : 'bg-red-600') }}"></div>

                        {{-- Baris Atas: Nama & Status --}}
                        <div class="flex justify-between items-start mb-3 pl-2">
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-gray-400">
                                    #{{ $pendaftarans->firstItem() + $loop->index }}
                                </span>
                                <h3 class="font-semibold text-white text-lg leading-tight">
                                    {{ $pendaftar->nama_pendaftar }}
                                </h3>
                                @if($pendaftar->lowongan)
                                    <span class="text-xs text-[#e88968] font-normal block mt-1">
                                        <i class="fas fa-briefcase text-[10px] mr-1"></i> {{ $pendaftar->lowongan->judul_posisi }}
                                    </span>
                                @endif
                            </div>
                            <div>
                                @if ($pendaftar->status == 'pending')
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-900 text-yellow-300">Menunggu</span>
                                @elseif ($pendaftar->status == 'approved')
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-900 text-green-300">Disetujui</span>
                                @elseif ($pendaftar->status == 'conditional')
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-900 text-blue-300">Bersyarat</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-900 text-red-300">Ditolak</span>
                                @endif
                            </div>
                        </div>

                        {{-- Detail Data --}}
                        <div class="space-y-3 text-sm pl-2 mb-4 border-t border-[#3a3a3a] pt-3">
                            <div>
                                <div class="text-xs text-gray-400 uppercase">Asal Kampus</div>
                                <div class="text-gray-200 font-medium">{{ $pendaftar->asal_kampus }}</div>
                            </div>
                            
                            <div>
                                <div class="text-xs text-gray-400 uppercase">Periode</div>
                                <div class="text-gray-200 font-medium whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($pendaftar->tanggal_mulai)->format('d/m/Y') }} - 
                                    {{ \Carbon\Carbon::parse($pendaftar->tanggal_selesai)->format('d/m/Y') }}
                                </div>
                            </div>

                            <div>
                                <div class="text-xs text-gray-400 uppercase">Konfirmasi Kehadiran</div>
                                <div class="text-gray-200 font-medium">
                                    @if ($pendaftar->status == 'approved')
                                        @if ($pendaftar->konfirmasi_at)
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-900 text-green-300">
                                                <i class="fas fa-check"></i> Dikonfirmasi
                                            </span>
                                        @else
                                            @if(auth()->user()->isAdmin())
                                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-700 text-gray-300">
                                                    Belum Konfirmasi
                                                </span>
                                            @else
                                                <a href="{{ route('daftar.konfirmasi', $pendaftar) }}" 
                                                   class="claude-button inline-flex items-center gap-2 px-3 py-1.5 text-xs mt-1 relative z-10">
                                                    <i class="fas fa-check"></i> Konfirmasi
                                                </a>
                                            @endif
                                        @endif
                                    @else
                                        <span class="text-gray-500">-</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Footer Kartu: Aksi --}}
                        <div class="mt-4 pt-4 border-t border-[#3a3a3a] flex justify-end items-center gap-3 pl-2 relative z-10">
                            
                             {{-- Tombol Hapus --}}
                             @if(auth()->user()->isAdmin() || (auth()->id() == $pendaftar->user_id && $pendaftar->status == 'pending'))
                                <form action="{{ route('daftar.destroy', $pendaftar->id) }}" method="POST" class="delete-form" data-name="{{ $pendaftar->nama_pendaftar }}">
                                    @csrf @method('DELETE')
                                    <button type="button" class="text-red-500 text-sm font-medium flex items-center gap-1 btn-delete relative z-10">
                                        <i class="fas fa-trash-alt"></i> Hapus
                                    </button>
                                </form>
                             @endif

                             {{-- Tombol Detail --}}
                             @if(auth()->user()->isAdmin() || auth()->id() == $pendaftar->user_id)
                                <a href="{{ route('daftar.show', $pendaftar) }}" class="font-medium text-[#d97757] hover:underline text-sm flex items-center gap-1 relative z-10">
                                    Detail
                                </a>
                             @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10 text-gray-500 bg-[#2a2a2a]/60 backdrop-blur-md border border-[#3a3a3a] rounded-xl">
                        @if($selectedYear == 'all' && $selectedMonth == 'all' && empty($search) && ($sort ?? 'latest') == 'latest')
                            Belum ada data pendaftar.
                        @else
                            Tidak ada data pendaftar yang cocok dengan filter pencarian.
                        @endif
                    </div>
                @endforelse
                <!--modifikais start-->
                @if($pendaftarans->hasPages())
                    <div class="mt-4 pt-3 border-t border-[#3a3a3a]">
                        <div class="flex flex-col gap-3">
                            <div class="text-xs text-gray-400 text-center">
                                Showing {{ $pendaftarans->firstItem() ?? 0 }}
                                – {{ $pendaftarans->lastItem() ?? 0 }}
                                of {{ $pendaftarans->total() }} results
                            </div>
            
                            <div class="overflow-x-auto flex justify-center">
                                {{ $pendaftarans->onEachSide(1)->links('vendor.pagination.magnet') }}
                            </div>
                        </div>
                    </div>
                @endif
                <!--modifikasi end-->
                
            </div>

        </div>
    </div>
    
    @push('scripts')
    {{-- CDN SweetAlert --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // 1. POP-UP SUKSES (PENGGANTI ALERT HIJAU)
        // Cek apakah ada session 'success' dari Controller
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                confirmButtonColor: '#d97757', // Warna oranye sesuai tema
                background: '#1f2937',         // Warna gelap
                color: '#fff'                  // Teks putih
            });
        @endif
        // 2. SweetAlert2 logic untuk Hapus Data (Event Delegation dengan fallback)
        document.addEventListener('click', function(e) {
            const button = e.target.closest('.btn-delete');
            
            if (button) {
                e.preventDefault();
                e.stopPropagation(); 

                const form = button.closest('.delete-form');
                if (!form) return;
                const name = form.getAttribute('data-name') || 'Data ini';

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Hapus Data?',
                        text: `Anda yakin ingin menghapus data "${name}"? Berkas file juga akan dihapus permanen.`,
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
                            Swal.fire({
                                title: 'Sedang memproses...',
                                text: 'Mohon tunggu sebentar',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                },
                                background: '#1f2937',
                                color: '#fff'
                            });
                            form.submit();
                        }
                    });
                } else {
                    if (confirm(`Anda yakin ingin menghapus data "${name}"?`)) {
                        form.submit();
                    }
                }
            }
        });
    </script>
    @endpush
</x-main-layout>
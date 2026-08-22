<x-main-layout>
    <div class="claude-container">

        {{-- Header Section --}}
        <div class="border-b border-[#3a3a3a] header-section">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 py-4">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                    <div>
                        <h2 class="claude-title text-2xl text-white flex items-center gap-3">
                            <i class="fas fa-briefcase text-[#e88968]"></i>
                            <span>Lowongan Magang</span>
                        </h2>
                        <p class="text-sm text-gray-400 mt-1">
                            Pilihan formasi dan divisi program magang di BPS Kabupaten Bantul
                        </p>
                    </div>

                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('lowongan.create') }}"
                            class="claude-button px-5 py-2.5 inline-flex items-center gap-2 w-full sm:w-auto justify-center">
                            <i class="fas fa-plus"></i>
                            <span>Tambah Lowongan</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6">
            @if (session('success'))
                <div class="bg-green-500/10 border border-green-500/30 text-green-400 p-4 rounded-xl mb-6 flex items-center gap-3">
                    <i class="fas fa-check-circle text-lg"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            {{-- FILTER SECTION --}}
            <div class="bg-[#2a2a2a]/60 backdrop-blur-md border border-[#3a3a3a] rounded-xl p-5 mb-8 shadow-lg">
                <form method="GET" action="{{ route('lowongan.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    {{-- Filter Search --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">
                            <i class="fas fa-search mr-1"></i> Cari Posisi / Kualifikasi
                        </label>
                        <input type="text" name="search" class="w-full bg-[#1a1a1a] border border-[#4a4a4a] text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-[#d97757]"
                            placeholder="Ketik judul posisi, keahlian..." value="{{ request('search') }}">
                    </div>

                    {{-- Filter Divisi --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">
                            <i class="fas fa-building mr-1"></i> Divisi / Seksi
                        </label>
                        <div class="relative">
                            <select name="divisi" class="w-full bg-[#1a1a1a] border border-[#4a4a4a] text-white rounded-lg px-4 py-2.5 pr-10 text-sm focus:outline-none focus:border-[#d97757] appearance-none cursor-pointer">
                                <option value="all" class="bg-[#1f1f1f] text-white">Semua Divisi</option>
                                @foreach($divisiList as $divisi)
                                    <option value="{{ $divisi }}" class="bg-[#1f1f1f] text-white" {{ request('divisi') == $divisi ? 'selected' : '' }}>
                                        {{ $divisi }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-gray-400">
                                <i class="fas fa-chevron-down text-xs"></i>
                            </div>
                        </div>
                    </div>

                    {{-- Tombol Filter & Reset --}}
                    <div class="flex items-end gap-2">
                        <button type="submit" class="flex-1 bg-[#d97757] hover:bg-[#e88968] text-white py-2.5 px-4 rounded-lg font-medium text-sm transition-all duration-200 flex items-center justify-center gap-2">
                            <i class="fas fa-filter"></i>
                            <span>Terapkan Filter</span>
                        </button>
                        @if(request()->hasAny(['search', 'divisi', 'status']))
                            <a href="{{ route('lowongan.index') }}" class="bg-white/10 hover:bg-white/20 text-gray-300 py-2.5 px-3 rounded-lg text-sm transition-all" title="Reset Filter">
                                <i class="fas fa-redo"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- GRID LOWONGAN --}}
            @if($lowongans->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($lowongans as $lowongan)
                        <div class="bg-[#2a2a2a]/60 backdrop-blur-md border border-[#3a3a3a] hover:border-[#d97757]/60 rounded-xl p-6 flex flex-col justify-between transition-all duration-300 hover:shadow-xl hover:shadow-[#d97757]/5 group relative">
                            
                            <div>
                                {{-- Header Card: Status & Kuota --}}
                                <div class="flex items-center justify-between gap-2 mb-3">
                                    <span class="text-xs px-2.5 py-1 rounded-full font-medium {{ $lowongan->status === 'buka' ? 'bg-green-500/20 text-green-400 border border-green-500/30' : 'bg-red-500/20 text-red-400 border border-red-500/30' }}">
                                        <i class="fas {{ $lowongan->status === 'buka' ? 'fa-door-open' : 'fa-door-closed' }} mr-1"></i>
                                        {{ $lowongan->status === 'buka' ? 'Pendaftaran Dibuka' : 'Ditutup' }}
                                    </span>

                                    <span class="text-xs text-gray-400 flex items-center gap-1 bg-[#1a1a1a] px-2.5 py-1 rounded-full border border-[#4a4a4a]">
                                        <i class="fas fa-users text-[#e88968]"></i>
                                        Kuota: <strong class="text-white">{{ $lowongan->kuota }}</strong> (Tersisa: {{ $lowongan->kuota_tersisa }})
                                    </span>
                                </div>

                                {{-- Judul & Divisi --}}
                                <h3 class="text-lg font-bold text-white group-hover:text-[#e88968] transition-colors mb-1">
                                    {{ $lowongan->judul_posisi }}
                                </h3>
                                <p class="text-xs text-[#d97757] font-medium mb-3 flex items-center gap-1.5">
                                    <i class="fas fa-sitemap"></i>
                                    <span>{{ $lowongan->divisi }}</span>
                                </p>

                                {{-- Deskripsi Singkat --}}
                                <p class="text-sm text-gray-300 line-clamp-3 mb-4 leading-relaxed">
                                    {{ $lowongan->deskripsi }}
                                </p>

                                {{-- Kualifikasi Singkat --}}
                                <div class="bg-[#1a1a1a]/80 rounded-lg p-3 border border-[#3a3a3a] mb-5">
                                    <p class="text-xs text-gray-400 font-semibold mb-1 uppercase tracking-wider">
                                        <i class="fas fa-graduation-cap text-yellow-400 mr-1"></i> Kualifikasi Utama:
                                    </p>
                                    <p class="text-xs text-gray-300 line-clamp-2 leading-relaxed">
                                        {{ $lowongan->kualifikasi }}
                                    </p>
                                </div>
                            </div>

                            {{-- Footer Aksi --}}
                            <div class="border-t border-[#3a3a3a] pt-4 mt-auto">
                                <div class="flex items-center justify-between gap-2">
                                    <a href="{{ route('lowongan.show', $lowongan) }}" class="text-xs text-gray-400 hover:text-white font-medium inline-flex items-center gap-1 transition-colors">
                                        <span>Lihat Detail</span>
                                        <i class="fas fa-arrow-right text-[10px]"></i>
                                    </a>

                                    @if(auth()->user()->isAdmin())
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('lowongan.edit', $lowongan) }}" class="text-xs bg-yellow-500/20 text-yellow-300 hover:bg-yellow-500/30 px-3 py-1.5 rounded-lg font-medium transition-colors">
                                                <i class="fas fa-edit mr-1"></i> Edit
                                            </a>
                                            <form action="{{ route('lowongan.destroy', $lowongan) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus lowongan ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-xs bg-red-500/20 text-red-400 hover:bg-red-500/30 px-2.5 py-1.5 rounded-lg transition-colors" title="Hapus Lowongan">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        @if($lowongan->status === 'buka' && $lowongan->kuota_tersisa > 0)
                                            <a href="{{ route('daftar.create', ['lowongan_id' => $lowongan->id]) }}" class="claude-button text-xs px-4 py-2 inline-flex items-center gap-1.5">
                                                <i class="fas fa-paper-plane"></i>
                                                <span>Lamar Posisi</span>
                                            </a>
                                        @else
                                            <span class="text-xs text-gray-500 font-medium italic">
                                                Kuota Penuh / Tutup
                                            </span>
                                        @endif
                                    @endif
                                </div>
                            </div>

                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-8">
                    {{ $lowongans->links() }}
                </div>
            @else
                <div class="bg-[#2a2a2a]/60 backdrop-blur-md border border-[#3a3a3a] rounded-xl p-12 text-center">
                    <div class="w-16 h-16 rounded-full bg-white/5 text-gray-400 flex items-center justify-center mx-auto mb-4 text-2xl">
                        <i class="fas fa-briefcase"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-white mb-2">Tidak Ada Lowongan Ditemukan</h3>
                    <p class="text-sm text-gray-400 max-w-md mx-auto mb-6">
                        Belum ada lowongan magang yang sesuai dengan kata kunci atau filter pencarian Anda.
                    </p>
                    <a href="{{ route('lowongan.index') }}" class="claude-button px-5 py-2.5 text-sm inline-flex items-center gap-2">
                        <i class="fas fa-redo"></i>
                        <span>Reset Filter</span>
                    </a>
                </div>
            @endif
        </div>

    </div>
</x-main-layout>

<x-main-layout>
    <div class="claude-container">

        {{-- Header Section --}}
        <div class="border-b border-[#3a3a3a] header-section">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 py-4">
                <div class="flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('lowongan.index') }}" class="text-gray-400 hover:text-white transition-colors p-2 rounded-lg hover:bg-white/5" title="Kembali ke Daftar Lowongan">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <div>
                            <h2 class="claude-title text-xl sm:text-2xl text-white">
                                Detail Lowongan Magang
                            </h2>
                            <p class="text-xs text-gray-400 mt-0.5">
                                {{ $lowongan->divisi }}
                            </p>
                        </div>
                    </div>

                    @if (auth()->user()->isAdmin())
                        <div class="flex items-center gap-2">
                            <a href="{{ route('lowongan.edit', $lowongan) }}" class="bg-yellow-500/20 text-yellow-300 hover:bg-yellow-500/30 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                <i class="fas fa-edit mr-1"></i> Edit Posisi
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                {{-- KONTEN UTAMA LOWONGAN (2 KOLOM) --}}
                <div class="lg:col-span-2 space-y-6">
                    
                    {{-- Card Header Posisi --}}
                    <div class="bg-[#2a2a2a]/60 backdrop-blur-md border border-[#3a3a3a] rounded-xl p-6 sm:p-8 shadow-lg">
                        <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                            <span class="text-xs px-3 py-1.5 rounded-full font-medium {{ $lowongan->status === 'buka' ? 'bg-green-500/20 text-green-400 border border-green-500/30' : 'bg-red-500/20 text-red-400 border border-red-500/30' }}">
                                <i class="fas {{ $lowongan->status === 'buka' ? 'fa-door-open' : 'fa-door-closed' }} mr-1"></i>
                                {{ $lowongan->status === 'buka' ? 'Status: Dibuka' : 'Status: Ditutup' }}
                            </span>
                            <span class="text-xs text-gray-400">
                                Diterbitkan: {{ $lowongan->created_at->format('d F Y') }}
                            </span>
                        </div>

                        <h1 class="text-2xl sm:text-3xl font-bold text-white mb-2">
                            {{ $lowongan->judul_posisi }}
                        </h1>
                        <p class="text-sm font-semibold text-[#e88968] flex items-center gap-2 mb-6">
                            <i class="fas fa-building"></i>
                            <span>{{ $lowongan->divisi }}</span>
                        </p>

                        {{-- Section Deskripsi --}}
                        <div class="border-t border-[#3a3a3a] pt-6 mb-6">
                            <h3 class="text-base font-bold text-white uppercase tracking-wider mb-3 flex items-center gap-2">
                                <i class="fas fa-align-left text-[#d97757]"></i>
                                <span>Deskripsi Tugas & Pekerjaan</span>
                            </h3>
                            <div class="text-gray-300 text-sm leading-relaxed whitespace-pre-line bg-[#1a1a1a]/50 p-4 rounded-xl border border-[#3a3a3a]">
                                {{ $lowongan->deskripsi }}
                            </div>
                        </div>

                        {{-- Section Kualifikasi --}}
                        <div class="border-t border-[#3a3a3a] pt-6">
                            <h3 class="text-base font-bold text-white uppercase tracking-wider mb-3 flex items-center gap-2">
                                <i class="fas fa-user-graduate text-yellow-400"></i>
                                <span>Kualifikasi & Persyaratan Khusus</span>
                            </h3>
                            <div class="text-gray-300 text-sm leading-relaxed whitespace-pre-line bg-[#1a1a1a]/50 p-4 rounded-xl border border-[#3a3a3a]">
                                {{ $lowongan->kualifikasi }}
                            </div>
                        </div>
                    </div>

                </div>

                {{-- SIDEBAR KANAN: RINGKASAN & CTA (1 KOLOM) --}}
                <div class="space-y-6">
                    
                    {{-- Card CTA Lamar --}}
                    <div class="bg-[#2a2a2a]/60 backdrop-blur-md border border-[#3a3a3a] rounded-xl p-6 shadow-lg text-center">
                        <div class="w-14 h-14 rounded-full bg-[#d97757]/20 text-[#e88968] flex items-center justify-center mx-auto mb-4 text-2xl">
                            <i class="fas fa-paper-plane"></i>
                        </div>
                        <h3 class="text-lg font-bold text-white mb-1">Tertarik dengan Posisi Ini?</h3>
                        <p class="text-xs text-gray-400 mb-6">
                            Lengkapi data diri dan unggah berkas surat permohonan magang Anda sekarang.
                        </p>

                        <div class="bg-[#1a1a1a] rounded-xl p-4 border border-[#3a3a3a] mb-6 text-left space-y-3">
                            <div class="flex justify-between text-xs">
                                <span class="text-gray-400">Total Kuota:</span>
                                <strong class="text-white">{{ $lowongan->kuota }} Peserta</strong>
                            </div>
                            <div class="flex justify-between text-xs">
                                <span class="text-gray-400">Telah Diterima:</span>
                                <strong class="text-green-400">{{ $lowongan->pelamar_disetujui }} Peserta</strong>
                            </div>
                            <div class="flex justify-between text-xs border-t border-[#3a3a3a] pt-2">
                                <span class="text-gray-400 font-semibold">Sisa Kuota Tersedia:</span>
                                <strong class="text-[#e88968] font-bold text-sm">{{ $lowongan->kuota_tersisa }} Kursi</strong>
                            </div>
                        </div>

                        @if(!auth()->user()->isAdmin())
                            @if($lowongan->status === 'buka' && $lowongan->kuota_tersisa > 0)
                                <a href="{{ route('daftar.create', ['lowongan_id' => $lowongan->id]) }}" class="claude-button w-full py-3 inline-flex items-center justify-center gap-2 font-semibold shadow-lg shadow-[#d97757]/20">
                                    <i class="fas fa-check"></i>
                                    <span>Lamar Posisi Sekarang</span>
                                </a>
                            @else
                                <button disabled class="w-full bg-gray-700 text-gray-400 py-3 rounded-lg text-sm font-medium cursor-not-allowed">
                                    Pendaftaran Ditutup / Kuota Penuh
                                </button>
                            @endif
                        @else
                            <div class="text-xs text-gray-400 bg-black/20 p-3 rounded-lg border border-[#3a3a3a]">
                                <i class="fas fa-info-circle mr-1"></i> Anda login sebagai Admin.
                            </div>
                        @endif
                    </div>

                    {{-- Card Lowongan Lainnya --}}
                    @if(isset($otherLowongans) && $otherLowongans->count() > 0)
                        <div class="bg-[#2a2a2a]/60 backdrop-blur-md border border-[#3a3a3a] rounded-xl p-6 shadow-lg">
                            <h4 class="text-sm font-bold text-white uppercase tracking-wider mb-4 flex items-center gap-2">
                                <i class="fas fa-briefcase text-[#e88968]"></i>
                                <span>Lowongan Lainnya</span>
                            </h4>
                            <div class="space-y-3">
                                @foreach($otherLowongans as $other)
                                    <a href="{{ route('lowongan.show', $other) }}" class="block p-3 rounded-lg bg-[#1a1a1a] hover:bg-white/5 border border-[#3a3a3a] transition-all group">
                                        <p class="text-xs font-semibold text-white group-hover:text-[#e88968] transition-colors truncate">
                                            {{ $other->judul_posisi }}
                                        </p>
                                        <p class="text-[11px] text-gray-400 truncate mt-0.5">
                                            {{ $other->divisi }}
                                        </p>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                </div>

            </div>
        </div>

    </div>
</x-main-layout>

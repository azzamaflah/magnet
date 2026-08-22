<x-main-layout>
    <div class="claude-container">

        {{-- Header Section --}}
        <div class="border-b border-[#3a3a3a] header-section">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 py-4">
                <div class="flex items-center gap-3">
                    {{-- Tombol Kembali --}}
                    <a href="{{ route('lowongan.index') }}"
                       class="w-9 h-9 rounded-xl bg-white/5 border border-white/10 hover:border-[#d97757]/60 hover:bg-[#d97757]/15 text-gray-400 hover:text-white flex items-center justify-center transition-all shadow-sm flex-shrink-0"
                       title="Kembali ke Lowongan">
                        <i class="fas fa-arrow-left text-sm"></i>
                    </a>

                    <div>
                        <div class="flex items-center gap-2 mb-0.5">
                            <span class="text-xs text-gray-400">Lowongan Magang &bull;</span>
                            <span class="text-xs font-semibold text-[#e88968]">Edit</span>
                        </div>
                        <h2 class="claude-title text-xl sm:text-2xl font-bold text-white tracking-tight">
                            Edit Lowongan: <span class="text-[#e88968]">{{ Str::limit($lowongan->judul_posisi, 40) }}</span>
                        </h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-4xl mx-auto py-8 px-4 sm:px-6">

            {{-- Error Alert --}}
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-500/10 border border-red-500/30 text-red-400 rounded-xl flex items-start gap-3">
                    <i class="fas fa-exclamation-triangle text-lg flex-shrink-0 mt-0.5"></i>
                    <div>
                        <p class="font-semibold text-sm mb-1">Terdapat kesalahan pada formulir:</p>
                        <ul class="list-disc list-inside text-xs space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <div class="bg-[#2a2a2a]/60 backdrop-blur-md border border-[#3a3a3a] rounded-2xl shadow-xl overflow-hidden">

                {{-- Card Header --}}
                <div class="px-6 sm:px-8 py-5 border-b border-[#3a3a3a] bg-[#1a1a1a]/60">
                    <h3 class="claude-title text-base font-bold text-white flex items-center gap-2">
                        <i class="fas fa-pen-to-square text-[#e88968]"></i>
                        <span>Perbarui Informasi Lowongan</span>
                    </h3>
                    <p class="text-xs text-gray-400 mt-1">
                        Perbarui detail posisi, kuota, atau status ketersediaan lowongan ini.
                        Untuk mengelola divisi, gunakan menu
                        <a href="{{ route('divisi.index') }}" class="text-[#e88968] hover:underline font-medium">Manajemen Divisi</a>.
                    </p>
                </div>

                {{-- Form --}}
                <form action="{{ route('lowongan.update', $lowongan) }}" method="POST" class="p-6 sm:p-8 space-y-6">
                    @csrf
                    @method('PUT')

                    {{-- Judul Posisi --}}
                    <div>
                        <label for="judul_posisi" class="filter-label mb-2">
                            Judul Posisi / Formasi <span class="text-red-400">*</span>
                        </label>
                        <input type="text" name="judul_posisi" id="judul_posisi" required
                            class="filter-input"
                            placeholder="Contoh: Web Developer &amp; Sistem Informasi"
                            value="{{ old('judul_posisi', $lowongan->judul_posisi) }}">
                    </div>

                    {{-- Divisi / Seksi (full width) --}}
                    <div>
                        <label for="divisi" class="filter-label mb-2">
                            Divisi / Seksi Penempatan <span class="text-red-400">*</span>
                        </label>
                        <div class="relative">
                            <select name="divisi" id="divisi" required class="filter-input pr-10 appearance-none cursor-pointer">
                                <option value="" class="bg-[#1f1f1f] text-gray-400">-- Pilih Divisi / Seksi --</option>
                                @foreach($divisiList as $divisi)
                                    <option value="{{ $divisi }}" class="bg-[#1f1f1f] text-white" {{ old('divisi', $lowongan->divisi) == $divisi ? 'selected' : '' }}>
                                        {{ $divisi }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-gray-400">
                                <i class="fas fa-chevron-down text-xs"></i>
                            </div>
                        </div>
                    </div>

                    {{-- Kuota & Status (2 kolom) --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        {{-- Kuota --}}
                        <div>
                            <label for="kuota" class="filter-label mb-2">
                                Kuota (Orang) <span class="text-red-400">*</span>
                            </label>
                            <input type="number" name="kuota" id="kuota" min="1" max="50" required
                                class="filter-input"
                                value="{{ old('kuota', $lowongan->kuota) }}">
                        </div>

                        {{-- Status --}}
                        <div>
                            <label for="status" class="filter-label mb-2">
                                Status <span class="text-red-400">*</span>
                            </label>
                            <div class="relative">
                                <select name="status" id="status" required class="filter-input pr-10 appearance-none cursor-pointer">
                                    <option value="buka" class="bg-[#1f1f1f] text-white" {{ old('status', $lowongan->status) == 'buka' ? 'selected' : '' }}>Dibuka</option>
                                    <option value="tutup" class="bg-[#1f1f1f] text-white" {{ old('status', $lowongan->status) == 'tutup' ? 'selected' : '' }}>Ditutup</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-gray-400">
                                    <i class="fas fa-chevron-down text-xs"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Deskripsi Tugas --}}
                    <div>
                        <label for="deskripsi" class="filter-label mb-2">
                            Deskripsi Tugas &amp; Pekerjaan <span class="text-red-400">*</span>
                        </label>
                        <textarea name="deskripsi" id="deskripsi" rows="5" required
                            class="filter-input resize-none"
                            placeholder="Jelaskan peran, tanggung jawab, dan gambaran umum pekerjaan peserta magang...">{{ old('deskripsi', $lowongan->deskripsi) }}</textarea>
                    </div>

                    {{-- Kualifikasi --}}
                    <div>
                        <label for="kualifikasi" class="filter-label mb-2">
                            Kualifikasi &amp; Persyaratan Keahlian <span class="text-red-400">*</span>
                        </label>
                        <textarea name="kualifikasi" id="kualifikasi" rows="4" required
                            class="filter-input resize-none"
                            placeholder="Sebutkan jurusan yang dicari, keahlian teknis (tools/software), dan kriteria lainnya...">{{ old('kualifikasi', $lowongan->kualifikasi) }}</textarea>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="border-t border-[#3a3a3a] pt-6 flex items-center justify-between gap-3">
                        <a href="{{ route('divisi.index') }}"
                           class="inline-flex items-center gap-2 text-xs text-gray-400 hover:text-[#e88968] transition-colors">
                            <i class="fas fa-sitemap text-[10px]"></i>
                            <span>Kelola Divisi</span>
                        </a>
                        <div class="flex items-center gap-3">
                            <a href="{{ route('lowongan.index') }}"
                               class="bg-white/5 hover:bg-white/10 text-gray-300 hover:text-white px-5 py-2.5 rounded-xl text-sm font-medium transition-all border border-white/10 hover:border-white/20">
                                Batal
                            </a>
                            <button type="submit" class="claude-button px-6 py-2.5 text-sm font-semibold inline-flex items-center gap-2">
                                <i class="fas fa-save text-xs"></i>
                                <span>Simpan Perubahan</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-main-layout>

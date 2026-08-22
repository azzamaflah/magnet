{{-- resources/views/divisi/index.blade.php --}}
<x-main-layout>
    <div class="claude-container">

        {{-- Header Section --}}
        <div class="border-b border-[#3a3a3a] header-section">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 py-5">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-3 mb-1">
                            <h2 class="claude-title text-2xl sm:text-3xl text-white">Manajemen Divisi</h2>
                            <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-[#d97757]/20 border border-[#d97757]/40 text-[#e88968]">
                                Panel Admin
                            </span>
                        </div>
                        <p class="text-xs sm:text-sm text-gray-400">
                            Kelola daftar divisi / seksi yang tersedia pada form tambah &amp; edit lowongan magang
                        </p>
                    </div>

                    {{-- Tombol Reset ke Default --}}
                    <form method="POST" action="{{ route('divisi.reset') }}"
                          onsubmit="return confirm('Reset semua divisi ke daftar default bawaan sistem? Perubahan yang belum disimpan akan hilang.')">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold
                                   bg-white/5 hover:bg-white/10 text-gray-300 border border-white/10
                                   hover:border-white/25 hover:text-white transition-all shadow-sm">
                            <i class="fas fa-undo-alt text-sm"></i>
                            <span>Reset ke Default</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Body --}}
        <div class="max-w-5xl mx-auto py-8 px-4 sm:px-6">

            {{-- Alert Success --}}
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-500/10 border border-green-500/30 text-green-400 rounded-xl flex items-center gap-3 shadow-sm">
                    <i class="fas fa-check-circle text-lg flex-shrink-0"></i>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            @endif

            {{-- Alert Error --}}
            @if($errors->any())
                <div class="mb-6 p-4 bg-red-500/10 border border-red-500/30 text-red-400 rounded-xl flex items-start gap-3 shadow-sm">
                    <i class="fas fa-exclamation-triangle text-lg flex-shrink-0 mt-0.5"></i>
                    <ul class="text-sm space-y-0.5 list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">

                {{-- ============================================================ --}}
                {{-- KOLOM KIRI: FORM TAMBAH + INFO                              --}}
                {{-- ============================================================ --}}
                <div class="lg:col-span-2 space-y-5">

                    {{-- Kartu Form Tambah --}}
                    <div class="bg-[#2a2a2a]/60 backdrop-blur-md border border-[#3a3a3a] rounded-2xl shadow-xl overflow-hidden">
                        <div class="px-6 py-4 border-b border-[#3a3a3a] bg-[#1a1a1a]/60">
                            <h3 class="claude-title text-base font-bold text-white flex items-center gap-2">
                                <i class="fas fa-plus-circle text-[#e88968]"></i>
                                <span>Tambah Divisi Baru</span>
                            </h3>
                        </div>

                        <div class="p-6">
                            <form method="POST" action="{{ route('divisi.store') }}" class="space-y-4">
                                @csrf
                                <div>
                                    <label for="nama_divisi" class="filter-label mb-2">
                                        Nama Divisi / Seksi
                                        <span class="text-red-400 ml-0.5">*</span>
                                    </label>
                                    <input type="text" name="nama_divisi" id="nama_divisi"
                                        class="filter-input"
                                        placeholder="Contoh: Seksi Statistik Khusus"
                                        value="{{ old('nama_divisi') }}"
                                        autofocus>
                                    <p class="text-[11px] text-gray-500 mt-1.5">
                                        Nama divisi akan langsung tersedia di form tambah &amp; edit lowongan.
                                    </p>
                                </div>

                                <button type="submit" class="claude-button w-full py-2.5 text-sm font-semibold flex items-center justify-center gap-2">
                                    <i class="fas fa-plus text-xs"></i>
                                    <span>Tambah Divisi</span>
                                </button>
                            </form>
                        </div>

                        {{-- Statistik --}}
                        <div class="border-t border-[#3a3a3a] bg-[#1a1a1a]/40 px-6 py-3.5 flex items-center justify-between">
                            <span class="text-xs text-gray-400">Total divisi terdaftar</span>
                            <span class="text-base font-bold text-[#e88968]">{{ count($divisiList) }}</span>
                        </div>
                    </div>

                    {{-- Kartu Info --}}
                    <div class="bg-[#d97757]/10 border border-[#d97757]/20 rounded-xl p-4">
                        <p class="text-xs text-[#e88968] font-semibold flex items-center gap-2 mb-2">
                            <i class="fas fa-info-circle"></i>
                            Catatan Penting
                        </p>
                        <ul class="text-[11px] text-[#f69d7f]/80 space-y-1.5 leading-relaxed">
                            <li>• Divisi yang ditambahkan langsung tersedia di form <strong class="text-[#e88968]">Tambah &amp; Edit Lowongan</strong>.</li>
                            <li>• Menghapus divisi <strong class="text-[#e88968]">tidak</strong> mempengaruhi data lowongan yang sudah tersimpan.</li>
                            <li>• Gunakan <strong class="text-[#e88968]">Reset ke Default</strong> untuk mengembalikan ke daftar awal bawaan BPS Bantul.</li>
                        </ul>
                    </div>
                </div>

                {{-- ============================================================ --}}
                {{-- KOLOM KANAN: DAFTAR DIVISI                                  --}}
                {{-- ============================================================ --}}
                <div class="lg:col-span-3">
                    <div class="bg-[#2a2a2a]/60 backdrop-blur-md border border-[#3a3a3a] rounded-2xl shadow-xl overflow-hidden">

                        {{-- Header Kartu --}}
                        <div class="px-6 py-4 border-b border-[#3a3a3a] bg-[#1a1a1a]/60 flex items-center justify-between">
                            <h3 class="claude-title text-base font-bold text-white flex items-center gap-2">
                                <i class="fas fa-list text-[#e88968]"></i>
                                <span>Daftar Divisi Tersedia</span>
                            </h3>
                            <span class="text-[11px] text-gray-500 italic">Hover untuk edit / hapus</span>
                        </div>

                        {{-- Daftar Item --}}
                        <div class="divide-y divide-[#3a3a3a]/50">
                            @forelse($divisiList as $index => $divisi)
                                <div class="group flex items-center gap-3 px-5 py-3.5 hover:bg-white/[0.03] transition-colors"
                                     x-data="{ editing: false }">

                                    {{-- Nomor --}}
                                    <span class="w-7 h-7 rounded-lg bg-white/5 border border-white/10 text-gray-500 text-xs font-bold flex items-center justify-center flex-shrink-0 group-hover:border-[#d97757]/30 group-hover:text-[#e88968] transition-colors">
                                        {{ $index + 1 }}
                                    </span>

                                    {{-- Tampilan Normal --}}
                                    <div class="flex-1 min-w-0" x-show="!editing">
                                        <span class="text-sm text-white font-medium">{{ $divisi }}</span>
                                    </div>

                                    {{-- Edit Inline --}}
                                    <form method="POST" action="{{ route('divisi.update') }}"
                                          class="flex-1 flex items-center gap-2"
                                          x-show="editing" x-cloak>
                                        @csrf
                                        <input type="hidden" name="index" value="{{ $index }}">
                                        <input type="text" name="nama_divisi"
                                            class="filter-input flex-1 py-1.5 text-sm"
                                            value="{{ $divisi }}">
                                        <button type="submit"
                                            class="flex-shrink-0 px-3 py-1.5 rounded-lg text-xs font-semibold bg-green-500/20 hover:bg-green-500/30 text-green-400 border border-green-500/30 hover:border-green-500/50 transition-all flex items-center gap-1">
                                            <i class="fas fa-check text-[10px]"></i>
                                            <span>Simpan</span>
                                        </button>
                                        <button type="button" @click="editing = false"
                                            class="flex-shrink-0 w-8 h-8 rounded-lg bg-white/5 hover:bg-white/10 text-gray-400 border border-white/10 flex items-center justify-center text-xs transition-all">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>

                                    {{-- Tombol Aksi (muncul saat hover) --}}
                                    <div class="flex items-center gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0"
                                         x-show="!editing">
                                        <button type="button" @click="editing = true"
                                            class="w-8 h-8 rounded-lg bg-[#d97757]/10 hover:bg-[#d97757]/25 text-[#e88968] border border-[#d97757]/25 hover:border-[#d97757]/50 flex items-center justify-center text-xs transition-all"
                                            title="Ubah nama divisi">
                                            <i class="fas fa-pen"></i>
                                        </button>

                                        <form method="POST" action="{{ route('divisi.destroy') }}"
                                              onsubmit="return confirm('Hapus divisi \'{{ addslashes($divisi) }}\'?')">
                                            @csrf
                                            <input type="hidden" name="index" value="{{ $index }}">
                                            <button type="submit"
                                                class="w-8 h-8 rounded-lg bg-red-500/10 hover:bg-red-500/20 text-red-400 border border-red-500/20 hover:border-red-500/40 flex items-center justify-center text-xs transition-all"
                                                title="Hapus divisi">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <div class="px-6 py-16 text-center">
                                    <div class="w-16 h-16 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center mx-auto mb-4">
                                        <i class="fas fa-sitemap text-2xl text-gray-500"></i>
                                    </div>
                                    <p class="text-white font-semibold text-sm">Belum Ada Divisi</p>
                                    <p class="text-xs text-gray-400 mt-1">
                                        Tambahkan divisi baru menggunakan form di sebelah kiri, atau tekan
                                        <strong class="text-gray-300">Reset ke Default</strong> di bagian atas.
                                    </p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-main-layout>

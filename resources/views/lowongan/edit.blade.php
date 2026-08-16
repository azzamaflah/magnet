<x-main-layout>
    <div class="claude-container">

        {{-- Header Section --}}
        <div class="border-b border-[#3a3a3a] header-section">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 py-4">
                <div class="flex items-center gap-3">
                    <a href="{{ route('lowongan.index') }}" class="text-gray-400 hover:text-white transition-colors p-2 rounded-lg hover:bg-white/5" title="Kembali">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <div>
                        <h2 class="claude-title text-xl sm:text-2xl text-white">
                            Edit Lowongan Magang
                        </h2>
                        <p class="text-xs text-gray-400 mt-0.5">
                            Perbarui informasi posisi atau ubah kuota dan status ketersediaan
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-4xl mx-auto py-8 px-4 sm:px-6">
            <div class="bg-[#2a2a2a]/60 backdrop-blur-md border border-[#3a3a3a] rounded-xl p-6 sm:p-8 shadow-lg">
                
                @if ($errors->any())
                    <div class="bg-red-500/10 border border-red-500/30 text-red-400 p-4 rounded-xl mb-6">
                        <p class="font-semibold text-sm mb-2"><i class="fas fa-exclamation-triangle mr-2"></i>Terdapat kesalahan pada input formulir:</p>
                        <ul class="list-disc list-inside text-xs space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('lowongan.update', $lowongan) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    {{-- Judul Posisi --}}
                    <div>
                        <label for="judul_posisi" class="block text-sm font-medium text-gray-300 mb-2">
                            Judul Posisi / Formasi <span class="text-red-400">*</span>
                        </label>
                        <input type="text" name="judul_posisi" id="judul_posisi" required
                            class="w-full bg-[#1a1a1a] border border-[#4a4a4a] text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-[#d97757]"
                            placeholder="Contoh: Web Developer & Sistem Informasi" value="{{ old('judul_posisi', $lowongan->judul_posisi) }}">
                    </div>

                    {{-- Divisi / Seksi --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="divisi" class="block text-sm font-medium text-gray-300 mb-2">
                                Divisi / Seksi Penempatan <span class="text-red-400">*</span>
                            </label>
                            <select name="divisi" id="divisi" required
                                class="w-full bg-[#1a1a1a] border border-[#4a4a4a] text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-[#d97757]">
                                <option value="">-- Pilih Divisi / Seksi --</option>
                                @foreach($divisiList as $divisi)
                                    <option value="{{ $divisi }}" {{ old('divisi', $lowongan->divisi) == $divisi ? 'selected' : '' }}>
                                        {{ $divisi }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Kuota & Status --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="kuota" class="block text-sm font-medium text-gray-300 mb-2">
                                    Kuota (Orang) <span class="text-red-400">*</span>
                                </label>
                                <input type="number" name="kuota" id="kuota" min="1" max="50" required
                                    class="w-full bg-[#1a1a1a] border border-[#4a4a4a] text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-[#d97757]"
                                    value="{{ old('kuota', $lowongan->kuota) }}">
                            </div>

                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-300 mb-2">
                                    Status <span class="text-red-400">*</span>
                                </label>
                                <select name="status" id="status" required
                                    class="w-full bg-[#1a1a1a] border border-[#4a4a4a] text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-[#d97757]">
                                    <option value="buka" {{ old('status', $lowongan->status) == 'buka' ? 'selected' : '' }}>Dibuka</option>
                                    <option value="tutup" {{ old('status', $lowongan->status) == 'tutup' ? 'selected' : '' }}>Ditutup</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Deskripsi Tugas --}}
                    <div>
                        <label for="deskripsi" class="block text-sm font-medium text-gray-300 mb-2">
                            Deskripsi Tugas & Pekerjaan <span class="text-red-400">*</span>
                        </label>
                        <textarea name="deskripsi" id="deskripsi" rows="5" required
                            class="w-full bg-[#1a1a1a] border border-[#4a4a4a] text-white rounded-lg p-4 text-sm focus:outline-none focus:border-[#d97757]"
                            placeholder="Jelaskan peran, tanggung jawab, dan gambaran umum pekerjaan peserta magang...">{{ old('deskripsi', $lowongan->deskripsi) }}</textarea>
                    </div>

                    {{-- Kualifikasi Persyaratan --}}
                    <div>
                        <label for="kualifikasi" class="block text-sm font-medium text-gray-300 mb-2">
                            Kualifikasi & Persyaratan Keahlian <span class="text-red-400">*</span>
                        </label>
                        <textarea name="kualifikasi" id="kualifikasi" rows="4" required
                            class="w-full bg-[#1a1a1a] border border-[#4a4a4a] text-white rounded-lg p-4 text-sm focus:outline-none focus:border-[#d97757]"
                            placeholder="Sebutkan jurusan yang dicari, keahlian teknis (tools/software), dan kriteria lainnya...">{{ old('kualifikasi', $lowongan->kualifikasi) }}</textarea>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="border-t border-[#3a3a3a] pt-6 flex items-center justify-end gap-3">
                        <a href="{{ route('lowongan.index') }}" class="bg-white/5 hover:bg-white/10 text-gray-300 px-5 py-2.5 rounded-lg text-sm transition-colors">
                            Batal
                        </a>
                        <button type="submit" class="claude-button px-6 py-2.5 text-sm font-medium inline-flex items-center gap-2">
                            <i class="fas fa-save"></i>
                            <span>Simpan Perubahan</span>
                        </button>
                    </div>
                </form>

            </div>
        </div>

    </div>
</x-main-layout>

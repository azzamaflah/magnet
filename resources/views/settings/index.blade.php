<x-main-layout>
    <div class="max-w-4xl mx-auto py-8 px-4 sm:px-6">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-white flex items-center gap-3">
                <i class="fas fa-tools text-[#e88968]"></i>
                Pengaturan Sistem
            </h2>
            <p class="text-gray-400 text-sm mt-1">Kelola batasan durasi minimal pendaftaran magang.</p>
        </div>

        @if (session('success'))
            <div
                class="mb-6 p-4 bg-green-500/10 border border-green-500/50 text-green-400 rounded-xl flex items-center gap-3">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-[#2a2a2a]/60 backdrop-blur-md border border-[#3a3a3a] rounded-2xl p-6 sm:p-8 shadow-xl">
            <form action="{{ route('settings.update') }}" method="POST" class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    {{-- Input Angka --}}
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-300">Minimal Durasi</label>
                        <input type="number" name="value" value="{{ $setting->value }}"
                            class="w-full bg-[#1a1a1a] border border-[#3a3a3a] rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-[#e88968]/50 focus:border-[#e88968] transition-all outline-none"
                            min="1">
                    </div>

                    {{-- Input Tipe --}}
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-300">Satuan Waktu</label>
                        <select name="type"
                            class="w-full bg-[#1a1a1a] border border-[#3a3a3a] rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-[#e88968]/50 focus:border-[#e88968] transition-all outline-none appearance-none">
                            <option value="bulan" {{ $setting->type == 'bulan' ? 'selected' : '' }}>Bulan</option>
                            <option value="hari" {{ $setting->type == 'hari' ? 'selected' : '' }}>Hari</option>
                        </select>
                    </div>
                </div>

                <div class="bg-[#d97757]/10 border border-[#d97757]/20 rounded-xl p-4 mt-4">
                    <p class="text-xs text-[#e88968] leading-relaxed italic">
                        * Perubahan ini akan langsung berdampak pada formulir pendaftaran mahasiswa baru. Sistem akan
                        menolak jika durasi kurang dari nilai di atas.
                    </p>
                </div>

                <div class="flex justify-end pt-4">
                    <button type="submit"
                        class="bg-gradient-to-r from-[#d97757] to-[#e88968] hover:scale-105 text-white px-8 py-3 rounded-xl font-bold transition-all shadow-lg shadow-[#d97757]/20 flex items-center gap-2">
                        <i class="fas fa-save"></i>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-main-layout>

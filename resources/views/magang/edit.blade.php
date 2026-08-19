{{-- resources/views/magang/edit.blade.php --}}
<x-main-layout>
    <div class="claude-container">

        {{-- Header Section --}}
        <div class="border-b border-[#3a3a3a] header-section">
            <div class="max-w-7xl mx-auto px-6 py-4">
                <div class="flex items-center gap-4">

                    {{-- ✅ PERUBAHAN LINK KEMBALI DI SINI --}}
                    <a href="{{ route('magang.index') }}" class="text-gray-400 hover:text-white"
                        title="Kembali ke Data Magang">
                        <i class="fas fa-arrow-left"></i>
                    </a>

                    <h2 class="claude-title text-2xl text-white">
                        Edit Data Magang
                    </h2>
                </div>
            </div>
        </div>

        {{-- Konten Utama: Form Edit --}}
        <div class="max-w-7xl mx-auto py-8 px-6">

            {{-- Menampilkan Error Validasi --}}
            @if ($errors->any())
                <div class="mb-4 bg-red-900 border border-red-700 text-red-200 px-4 py-3 rounded-lg relative" role="alert">
                    <strong class="font-bold">Oops! Terjadi kesalahan:</strong>
                    <ul class="mt-2 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Card Form Utama --}}
            <div class="bg-[#2a2a2a]/60 backdrop-blur-md border border-[#3a3a3a] rounded-xl shadow-lg overflow-hidden">
                <form action="{{ route('magang.update', $magang) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    {{-- Konten Form (Info, Foto, Medsos, Kesan) --}}
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8">

                            {{-- Kolom Kiri Form --}}
                            <div>
                                <div class="mb-6">
                                    <label for="nama" class="filter-label">Nama Lengkap</label>
                                    <input type="text" name="nama" id="nama" value="{{ old('nama', $magang->nama) }}"
                                        class="filter-input @error('nama') border-red-500 @enderror" required>
                                    @error('nama')
                                        <p class="text-red-400 text-xs mt-2">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mb-6">
                                    <label for="asal_kampus" class="filter-label">Asal Kampus</label>
                                    <input type="text" name="asal_kampus" id="asal_kampus"
                                        value="{{ old('asal_kampus', $magang->asal_kampus) }}"
                                        class="filter-input @error('asal_kampus') border-red-500 @enderror" required>
                                    @error('asal_kampus')
                                        <p class="text-red-400 text-xs mt-2">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mb-6">
                                    <label for="prodi" class="filter-label">Prodi / Jurusan</label>
                                    <input type="text" name="prodi" id="prodi" placeholder="Contoh: Informatika"
                                        value="{{ old('prodi', $magang->prodi) }}"
                                        class="filter-input @error('prodi') border-red-500 @enderror" required>
                                    @error('prodi')
                                        <p class="text-red-400 text-xs mt-2">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mb-6">
                                    <label for="tanggal_mulai" class="filter-label">Tanggal Mulai</label>
                                    <input type="date" name="tanggal_mulai" id="tanggal_mulai"
                                        value="{{ old('tanggal_mulai', $magang->tanggal_mulai->format('Y-m-d')) }}"
                                        class="filter-input @error('tanggal_mulai') border-red-500 @enderror" required>
                                    @error('tanggal_mulai')
                                        <p class="text-red-400 text-xs mt-2">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mb-6">
                                    <label for="tanggal_selesai" class="filter-label">Tanggal Selesai</label>
                                    <input type="date" name="tanggal_selesai" id="tanggal_selesai"
                                        value="{{ old('tanggal_selesai', $magang->tanggal_selesai->format('Y-m-d')) }}"
                                        class="filter-input @error('tanggal_selesai') border-red-500 @enderror"
                                        required>
                                    @error('tanggal_selesai')
                                        <p class="text-red-400 text-xs mt-2">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mb-6">
                                    <label for="link_pekerjaan" class="filter-label">Link Karya (Opsional)</label>
                                    <textarea name="link_pekerjaan" id="link_pekerjaan" rows="3"
                                        class="filter-input @error('link_pekerjaan') border-red-500 @enderror"
                                        placeholder="Contoh: https://drive.google.com/...">{{ old('link_pekerjaan', $magang->link_pekerjaan) }}</textarea>
                                    <p class="text-gray-400 text-xs mt-2">Masukkan link Google Drive, GitHub, atau
                                        portfolio online</p>
                                    @error('link_pekerjaan')
                                        <p class="text-red-400 text-xs mt-2">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Kolom Kanan Form --}}
                            <div>
                                <div class="mb-6">
                                    <label for="foto" class="filter-label">Foto Profil</label>

                                    @if($magang->foto)
                                        <div class="mb-3">
                                            <img src="{{ asset('storage/' . $magang->foto) }}" alt="Foto saat ini"
                                                class="max-w-[250px] rounded-lg mt-2 shadow-lg border border-[#3a3a3a]">
                                            <p class="text-gray-400 text-xs mt-2">Foto saat ini</p>
                                        </div>
                                    @endif

                                    <input type="file" name="foto" id="foto" accept="image/jpeg,image/png,image/jpg"
                                        class="filter-input file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-[#3a3a3a] file:text-gray-300 hover:file:bg-[#4a4a4a] @error('foto') border-red-500 @enderror"
                                        onchange="previewImage(event)">
                                    <p class="text-gray-400 text-xs mt-2">Kosongkan jika tidak ingin mengubah foto |
                                        Format: JPG, PNG | Maksimal 2MB</p>
                                    <img id="preview" class="max-w-[250px] rounded-lg mt-4 border border-[#3a3a3a]"
                                        style="display: none;" alt="Preview">
                                    @error('foto')
                                        <p class="text-red-400 text-xs mt-2">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Media Sosial --}}
                                <div class="border-t border-[#3a3a3a] md:border-t-0 pt-6 mt-8 md:pt-0 md:mt-0 mb-6">
                                    <h3 class="claude-title text-xl text-white mb-4">
                                        <i class="fas fa-share-alt mr-2"></i>Media Sosial (Opsional)
                                    </h3>

                                    <div class="mb-6">
                                        <label for="whatsapp" class="filter-label">WhatsApp</label>
                                        <div class="flex items-center gap-3">
                                            <i class="fab fa-whatsapp fa-fw fa-lg w-6 text-green-500 text-center"></i>
                                            <input type="text" name="whatsapp" id="whatsapp"
                                                value="{{ old('whatsapp', $magang->whatsapp) }}"
                                                placeholder="081234567890"
                                                class="filter-input @error('whatsapp') border-red-500 @enderror">
                                        </div>
                                        <p class="text-gray-400 text-xs mt-2 ml-9">Masukkan nomor dengan tanda + dan tanpa spasi</p>
                                        @error('whatsapp')
                                            <p class="text-red-400 text-xs mt-2 ml-9">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="mb-6">
                                        <label for="instagram" class="filter-label">Instagram</label>
                                        <div class="flex items-center gap-3">
                                            <i class="fab fa-instagram fa-fw fa-lg w-6 text-pink-500 text-center"></i>
                                            <input type="text" name="instagram" id="instagram"
                                                value="{{ old('instagram', $magang->instagram) }}"
                                                placeholder="@username"
                                                class="filter-input @error('instagram') border-red-500 @enderror">
                                        </div>
                                        @error('instagram')
                                            <p class="text-red-400 text-xs mt-2 ml-9">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="mb-6">
                                        <label for="tiktok" class="filter-label">TikTok</label>
                                        <div class="flex items-center gap-3">
                                            <i class="fab fa-tiktok fa-fw fa-lg w-6 text-blue-400 text-center"></i>
                                            <input type="text" name="tiktok" id="tiktok"
                                                value="{{ old('tiktok', $magang->tiktok) }}" placeholder="@username"
                                                class="filter-input @error('tiktok') border-red-500 @enderror">
                                        </div>
                                        @error('tiktok')
                                            <p class="text-red-400 text-xs mt-2 ml-9">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Bagian Kesan & Pesan (Full Width) --}}
                        <div class="border-t border-[#3a3a3a] pt-6 mt-8">
                            <h3 class="claude-title text-xl text-white mb-4">
                                <i class="fas fa-comment-dots mr-2"></i>Kesan & Pesan
                            </h3>

                            <div class="mb-6">
                                <label for="kesan" class="filter-label">Kesan Selama Magang</label>
                                <textarea name="kesan" id="kesan" rows="5"
                                    class="filter-input @error('kesan') border-red-500 @enderror"
                                    placeholder="Tulis kesan Anda selama mengikuti program magang di BPS Bantul...">{{ old('kesan', $magang->kesan) }}</textarea>
                                <p class="text-gray-400 text-xs mt-2">Maksimal 2000 karakter</p>
                                @error('kesan')
                                    <p class="text-red-400 text-xs mt-2">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-6">
                                <label for="pesan" class="filter-label">Pesan & Saran</label>
                                <textarea name="pesan" id="pesan" rows="5"
                                    class="filter-input @error('pesan') border-red-500 @enderror"
                                    placeholder="Tulis pesan atau saran untuk BPS Bantul...">{{ old('pesan', $magang->pesan) }}</textarea>
                                <p class="text-gray-400 text-xs mt-2">Maksimal 2000 karakter</p>
                                @error('pesan')
                                    <p class="text-red-400 text-xs mt-2">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- VISIBILITAS PUBLIK (ADMIN ONLY) --}}
                            @if(auth()->check() && auth()->user()->isAdmin())
                                <div class="border-t border-[#3a3a3a] pt-4 mt-2">
                                    <label class="filter-label flex items-center gap-2 mb-2 font-semibold text-white">
                                        <i class="fas fa-shield-alt text-[#d97757]"></i>
                                        <span>Privasi & Visibilitas Publik</span>
                                    </label>
                                    <label class="flex items-start gap-3 p-3.5 bg-black/20 border border-[#3a3a3a] rounded-xl cursor-pointer hover:border-[#d97757]/40 transition-all">
                                        <input type="checkbox" name="is_hidden" value="1" 
                                               {{ old('is_hidden', $magang->is_hidden) ? 'checked' : '' }}
                                               class="w-4 h-4 mt-0.5 rounded border-gray-600 text-[#d97757] focus:ring-[#d97757]/40 bg-[#1a1a1a]">
                                        <div>
                                            <span class="text-sm font-medium text-white block">Sembunyikan data peserta ini dari publik</span>
                                            <span class="text-xs text-gray-400 block mt-0.5">Jika dicentang, peserta ini tidak akan muncul di daftar publik dan hanya bisa dilihat oleh Administrator.</span>
                                        </div>
                                    </label>
                                </div>
                            @endif
                        </div>
                            <!-- modifikasi - start -->
                            <!-- CAPTCHA -->
                            <div class="flex justify-end mb-4">
                                <div class="g-recaptcha"
                                    data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}">
                                </div>
                            </div>
        
                            @error('g-recaptcha-response')
                                <small style="color:red">{{ $message }}</small>
                            @enderror
                            <!-- modifikasi - end -->
                    </div>

                    {{-- Tombol Aksi (Footer Card) --}}
                    <div class="bg-[#1a1a1a]/50 px-6 py-4 border-t border-[#3a3a3a] flex flex-wrap gap-3 justify-end">

                        {{-- ✅ PERUBAHAN LINK BATAL DI SINI --}}
                        <a href="{{ route('magang.index') }}" class="filter-btn filter-btn-secondary">
                            <i class="fas fa-times mr-2"></i>Batal
                        </a>
                        <button type="submit" class="filter-btn filter-btn-primary">
                            <i class="fas fa-save mr-2"></i>Update Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Script untuk preview gambar --}}
    <script>
        function previewImage(event) {
            const preview = document.getElementById('preview');
            const file = event.target.files[0];

            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            } else {
                preview.src = '';
                preview.style.display = 'none';
            }
        }
    </script>
        <!-- modifikasi - start -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <!-- modifikasi - end -->
</x-main-layout>
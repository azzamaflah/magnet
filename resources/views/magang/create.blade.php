{{-- resources/views/magang/create.blade.php --}}
<x-main-layout>
    {{-- Load Flatpickr CSS via CDN --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/dark.css">

    <style>
        /* Custom Override Flatpickr agar serasi dengan tema */
        .flatpickr-calendar {
            background: #2a2a2a !important;
            border: 1px solid #3a3a3a !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.5) !important;
        }
        .flatpickr-day.selected, .flatpickr-day.startRange, .flatpickr-day.endRange, 
        .flatpickr-day.selected.inRange, .flatpickr-day.startRange.inRange, 
        .flatpickr-day.endRange.inRange, .flatpickr-day.selected:focus, 
        .flatpickr-day.startRange:focus, .flatpickr-day.endRange:focus, 
        .flatpickr-day.selected:hover, .flatpickr-day.startRange:hover, 
        .flatpickr-day.endRange:hover, .flatpickr-day.selected.prevMonthDay, 
        .flatpickr-day.startRange.prevMonthDay, .flatpickr-day.endRange.prevMonthDay, 
        .flatpickr-day.selected.nextMonthDay, .flatpickr-day.startRange.nextMonthDay, 
        .flatpickr-day.endRange.nextMonthDay {
            background: #d97757 !important;
            border-color: #d97757 !important;
        }
        .flatpickr-day:hover {
            background: #3a3a3a !important;
            border-color: #3a3a3a !important;
        }
        .flatpickr-months .flatpickr-month {
            background: #2a2a2a !important;
            color: white !important;
            fill: white !important;
        }
        .flatpickr-current-month .flatpickr-monthDropdown-months {
            background-color: #2a2a2a !important;
        }
        .flatpickr-day.flatpickr-disabled, .flatpickr-day.flatpickr-disabled:hover {
            color: #555 !important;
        }
        .date-input-wrapper {
            position: relative;
        }
        .date-input-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            pointer-events: none;
        }
        .preview-image {
            max-width: 180px;
            height: 180px;
            object-fit: cover;
            border-radius: 12px;
            margin-top: 1rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            border: 2px solid #3a3a3a;
        }
    </style>

    <div class="claude-container">
        
        {{-- Header Section --}}
        <div class="border-b border-[#3a3a3a] header-section">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 py-4">
                <div class="flex items-center gap-4">
                    <a href="{{ route('magang.index') }}" class="text-gray-400 hover:text-white" title="Kembali ke Data Magang">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <h2 class="claude-title text-xl sm:text-2xl text-white">
                        Tambah Data Magang
                    </h2>
                </div>
            </div>
        </div>

        {{-- Konten Utama: Form --}}
        <div class="max-w-6xl mx-auto py-8 px-4 sm:px-6">

            {{-- Menampilkan Error Validasi --}}
            @if ($errors->any())
                <div class="mb-6 bg-red-900/40 border border-red-500/50 text-red-200 px-5 py-4 rounded-xl relative shadow-lg" role="alert">
                    <div class="flex items-center gap-2 font-bold mb-1">
                        <i class="fas fa-exclamation-circle text-red-400"></i>
                        <span>Mohon periksa kembali isian formulir:</span>
                    </div>
                    <ul class="mt-2 list-disc list-inside text-sm space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-[#2a2a2a]/60 backdrop-blur-md border border-[#3a3a3a] rounded-xl shadow-lg overflow-hidden">
                <form action="{{ route('magang.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="p-6 md:p-8 grid grid-cols-1 lg:grid-cols-2 gap-8">

                        {{-- Kolom Kiri: Informasi Pribadi & Pendidikan --}}
                        <div class="space-y-6">

                            {{-- Pilihan Formasi Lowongan --}}
                            <div>
                                <label for="lowongan_id" class="filter-label mb-2">
                                    <i class="fas fa-briefcase text-[#e88968] mr-1"></i> Formasi / Lowongan Magang
                                </label>
                                <select name="lowongan_id" id="lowongan_id" class="filter-select">
                                    <option value="">-- Magang Umum (Tanpa Spesifik Divisi) --</option>
                                    @foreach($lowongans as $item)
                                        <option value="{{ $item->id }}" {{ old('lowongan_id') == $item->id ? 'selected' : '' }}>
                                            {{ $item->judul_posisi }} ({{ $item->divisi }})
                                        </option>
                                    @endforeach
                                </select>
                                <span class="text-xs text-gray-400 mt-1 block">Tentukan divisi penempatan peserta magang ini.</span>
                                @error('lowongan_id') <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="nama" class="filter-label mb-2">Nama Lengkap <span class="text-red-400">*</span></label>
                                <input type="text" name="nama" id="nama" value="{{ old('nama') }}"
                                    class="filter-input @error('nama') border-red-500 @enderror" 
                                    placeholder="Masukkan nama lengkap peserta" required>
                                @error('nama')
                                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="email" class="filter-label mb-2">Alamat Email (Opsional)</label>
                                <input type="email" name="email" id="email" value="{{ old('email') }}"
                                    class="filter-input @error('email') border-red-500 @enderror" 
                                    placeholder="contoh: peserta@email.com">
                                @error('email')
                                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="asal_kampus" class="filter-label mb-2">Asal Kampus <span class="text-red-400">*</span></label>
                                <input type="text" name="asal_kampus" id="asal_kampus" value="{{ old('asal_kampus') }}"
                                    class="filter-input @error('asal_kampus') border-red-500 @enderror" 
                                    placeholder="Contoh: Universitas Gadjah Mada" required>
                                @error('asal_kampus')
                                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="prodi" class="filter-label mb-2">Prodi / Jurusan <span class="text-red-400">*</span></label>
                                <input type="text" name="prodi" id="prodi" class="filter-input @error('prodi') border-red-500 @enderror" 
                                    placeholder="Contoh: Teknologi Informasi / Statistika" value="{{ old('prodi') }}" required>
                                @error('prodi') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- RENCANA PERIODE MAGANG --}}
                            <div class="border-t border-[#3a3a3a] pt-4 mt-2">
                                <div class="flex items-center justify-between mb-3">
                                    <label class="filter-label flex items-center gap-2 font-semibold text-white">
                                        <i class="fas fa-calendar-alt text-[#e88968]"></i>
                                        <span>Periode Pelaksanaan Magang</span>
                                    </label>
                                    <span class="text-[11px] text-gray-400">
                                        Min. {{ $minDurasi ?? 3 }} {{ $tipeDurasi ?? 'bulan' }}
                                    </span>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="tanggal_mulai" class="block text-xs text-gray-300 mb-1.5 font-medium">
                                            Tanggal Mulai <span class="text-red-400">*</span>
                                        </label>
                                        <div class="date-input-wrapper">
                                            <input type="text" name="tanggal_mulai" id="tanggal_mulai" 
                                                   class="filter-input cursor-pointer" 
                                                   placeholder="Pilih Tanggal Mulai"
                                                   value="{{ old('tanggal_mulai') }}" required>
                                            <i class="fas fa-calendar-alt date-input-icon"></i>
                                        </div>
                                        @error('tanggal_mulai') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label for="tanggal_selesai" class="block text-xs text-gray-300 mb-1.5 font-medium">
                                            Tanggal Selesai <span class="text-red-400">*</span>
                                        </label>
                                        <div class="date-input-wrapper">
                                            <input type="text" name="tanggal_selesai" id="tanggal_selesai" 
                                                   class="filter-input cursor-pointer" 
                                                   placeholder="Pilih Tanggal Selesai"
                                                   value="{{ old('tanggal_selesai') }}" required>
                                            <i class="fas fa-calendar-check date-input-icon"></i>
                                        </div>
                                        @error('tanggal_selesai') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <span class="text-xs text-gray-400 mt-2 block">
                                    <i class="fas fa-info-circle text-[#e88968] mr-1"></i> Tentukan rentang waktu aktif pelaksanaan magang peserta.
                                </span>
                            </div>

                            {{-- VISIBILITAS PUBLIK --}}
                            @if(auth()->check() && auth()->user()->isAdmin())
                                <div class="border-t border-[#3a3a3a] pt-4 mt-2">
                                    <label class="filter-label flex items-center gap-2 mb-2 font-semibold text-white">
                                        <i class="fas fa-shield-alt text-[#d97757]"></i>
                                        <span>Privasi & Visibilitas Publik</span>
                                    </label>
                                    <label class="flex items-start gap-3 p-3.5 bg-black/20 border border-[#3a3a3a] rounded-xl cursor-pointer hover:border-[#d97757]/40 transition-all">
                                        <input type="checkbox" name="is_hidden" value="1" 
                                               {{ old('is_hidden') ? 'checked' : '' }}
                                               class="w-4 h-4 mt-0.5 rounded border-gray-600 text-[#d97757] focus:ring-[#d97757]/40 bg-[#1a1a1a]">
                                        <div>
                                            <span class="text-sm font-medium text-white block">Sembunyikan data peserta ini dari publik</span>
                                            <span class="text-xs text-gray-400 block mt-0.5">Jika dicentang, peserta ini tidak akan muncul di daftar direktori publik dan hanya bisa dilihat oleh Administrator.</span>
                                        </div>
                                    </label>
                                </div>
                            @endif

                        </div>

                        {{-- Kolom Kanan: Foto, Portofolio & Kontak --}}
                        <div class="space-y-6">

                            {{-- Upload Foto Profil --}}
                            <div>
                                <label for="foto" class="filter-label mb-2">Foto Profil Peserta <span class="text-red-400">*</span></label>
                                <input type="file" name="foto" id="foto" accept="image/jpeg,image/png,image/jpg"
                                    class="filter-input file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-[#d97757]/20 file:text-[#e88968] hover:file:bg-[#d97757]/40 @error('foto') border-red-500 @enderror" 
                                    onchange="previewImage(event)" required>
                                <span class="text-xs text-gray-400 mt-1 block">Format: JPG, PNG | Maksimal 2MB</span>
                                
                                <div class="mt-3 flex items-center gap-4">
                                    <img id="preview" class="preview-image" style="display: none;" alt="Preview Foto">
                                </div>
                                @error('foto')
                                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Link Karya / Portofolio --}}
                            <div>
                                <label for="link_pekerjaan" class="filter-label mb-2">Link Karya / Portofolio (Opsional)</label>
                                <textarea name="link_pekerjaan" id="link_pekerjaan" rows="2"
                                    class="filter-input @error('link_pekerjaan') border-red-500 @enderror"
                                    placeholder="Contoh: https://drive.google.com/... atau https://github.com/...">{{ old('link_pekerjaan') }}</textarea>
                                <span class="text-xs text-gray-400 mt-1 block">Tautan Google Drive, GitHub, atau situs portofolio peserta.</span>
                                @error('link_pekerjaan')
                                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Media Sosial --}}
                            <div class="border-t border-[#3a3a3a] pt-4 space-y-4">
                                <h3 class="claude-title text-lg text-white flex items-center gap-2">
                                    <i class="fas fa-share-alt text-[#e88968]"></i>
                                    <span>Kontak & Media Sosial (Opsional)</span>
                                </h3>

                                <div>
                                    <label for="whatsapp" class="filter-label mb-1.5 text-xs">WhatsApp</label>
                                    <div class="flex items-center gap-2.5">
                                        <i class="fab fa-whatsapp fa-lg text-green-500 w-5 text-center"></i>
                                        <input type="text" name="whatsapp" id="whatsapp" value="{{ old('whatsapp') }}"
                                            placeholder="+6281234567890"
                                            class="filter-input @error('whatsapp') border-red-500 @enderror">
                                    </div>
                                    <span class="text-[11px] text-gray-400 mt-1 block">Wajib format internasional dengan +62 (contoh: +6281234567890)</span>
                                    @error('whatsapp')
                                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="instagram" class="filter-label mb-1.5 text-xs">Instagram</label>
                                    <div class="flex items-center gap-2.5">
                                        <i class="fab fa-instagram fa-lg text-pink-500 w-5 text-center"></i>
                                        <input type="text" name="instagram" id="instagram" value="{{ old('instagram') }}"
                                            placeholder="@username"
                                            class="filter-input @error('instagram') border-red-500 @enderror">
                                    </div>
                                    @error('instagram')
                                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="tiktok" class="filter-label mb-1.5 text-xs">TikTok</label>
                                    <div class="flex items-center gap-2.5">
                                        <i class="fab fa-tiktok fa-lg text-blue-400 w-5 text-center"></i>
                                        <input type="text" name="tiktok" id="tiktok" value="{{ old('tiktok') }}"
                                            placeholder="@username"
                                            class="filter-input @error('tiktok') border-red-500 @enderror">
                                    </div>
                                    @error('tiktok')
                                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                        </div>
                    </div>

                    {{-- Bagian Kesan & Pesan (Full Width) --}}
                    <div class="border-t border-[#3a3a3a] p-6 md:p-8 bg-black/10">
                        <h3 class="claude-title text-lg text-white mb-4 flex items-center gap-2">
                            <i class="fas fa-comment-dots text-[#e88968]"></i>
                            <span>Testimoni Kesan & Pesan (Opsional)</span>
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="kesan" class="filter-label mb-2">Kesan Selama Magang</label>
                                <textarea name="kesan" id="kesan" rows="4"
                                    class="filter-input @error('kesan') border-red-500 @enderror"
                                    placeholder="Tulis kesan pengalaman selama mengikuti magang di BPS Bantul...">{{ old('kesan') }}</textarea>
                                @error('kesan')
                                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="pesan" class="filter-label mb-2">Pesan & Saran</label>
                                <textarea name="pesan" id="pesan" rows="4"
                                    class="filter-input @error('pesan') border-red-500 @enderror"
                                    placeholder="Tulis pesan atau saran untuk BPS Kabupaten Bantul...">{{ old('pesan') }}</textarea>
                                @error('pesan')
                                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Tombol Aksi (Footer Card) --}}
                    <div class="bg-[#1a1a1a]/70 px-6 py-4 border-t border-[#3a3a3a] flex flex-wrap gap-3 justify-end items-center">
                        <a href="{{ route('magang.index') }}" class="filter-btn filter-btn-secondary">
                            <i class="fas fa-times mr-1.5"></i>Batal
                        </a>
                        <button type="submit" class="filter-btn filter-btn-primary">
                            <i class="fas fa-save mr-1.5"></i>Simpan Data Magang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Script Flatpickr & Preview Foto --}}
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                flatpickr.localize(flatpickr.l10ns.id);

                const minDate = new Date();
                minDate.setFullYear(minDate.getFullYear() - 2); // Boleh input riwayat alumni hingga 2 tahun lalu

                const maxDate = new Date();
                maxDate.setMonth(maxDate.getMonth() + 7);

                const mulaiPicker = flatpickr("#tanggal_mulai", {
                    altInput: true,
                    altFormat: "j F Y",
                    dateFormat: "Y-m-d",
                    minDate: minDate,
                    maxDate: maxDate,
                    onChange: function (selectedDates, dateStr) {
                        selesaiPicker.set('minDate', dateStr);
                    }
                });

                const selesaiPicker = flatpickr("#tanggal_selesai", {
                    altInput: true,
                    altFormat: "j F Y",
                    dateFormat: "Y-m-d",
                    minDate: minDate,
                    maxDate: maxDate
                });
            });

            function previewImage(event) {
                const preview = document.getElementById('preview');
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.src = e.target.result;
                        preview.style.display = 'block';
                    }
                    reader.readAsDataURL(file);
                } else {
                    preview.style.display = 'none';
                }
            }
        </script>
    @endpush
</x-main-layout>
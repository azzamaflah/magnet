{{-- resources/views/daftar/edit.blade.php --}}
<x-main-layout>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/dark.css">

    <style>
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
    </style>

    <div class="claude-container">
        
        {{-- Header Section --}}
        <div class="border-b border-[#3a3a3a] header-section">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 py-4">
                <div class="flex items-center gap-4">
                    <a href="{{ route('daftar.index') }}" class="text-gray-400 hover:text-white" title="Kembali">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <h2 class="claude-title text-xl sm:text-2xl text-white">
                        Edit Berkas & Data Pendaftaran Magang
                    </h2>
                </div>
            </div>
        </div>

        <div class="max-w-5xl mx-auto py-8 px-4 sm:px-6">
            
            <div class="bg-[#2a2a2a]/60 backdrop-blur-md border border-[#3a3a3a] rounded-xl shadow-lg overflow-hidden">
                
                <form method="POST" action="{{ route('daftar.update', $pendaftaran) }}" enctype="multipart/form-data" id="pendaftaranEditForm">
                    @csrf
                    @method('PUT')

                    <div class="p-6 md:p-8 grid grid-cols-1 lg:grid-cols-2 gap-8">

                        {{-- Kolom Kiri: Data Diri & Posisi --}}
                        <div class="space-y-6">

                            {{-- Pilihan Formasi Lowongan --}}
                            <div>
                                <label for="lowongan_id" class="filter-label mb-2">
                                    <i class="fas fa-briefcase text-[#e88968] mr-1"></i> Formasi / Lowongan Magang
                                </label>
                                <select name="lowongan_id" id="lowongan_id" class="filter-select">
                                    <option value="">-- Pendaftaran Magang Umum (Tanpa Spesifik Divisi) --</option>
                                    @foreach($lowongans as $item)
                                        <option value="{{ $item->id }}" {{ (old('lowongan_id', $pendaftaran->lowongan_id) == $item->id) ? 'selected' : '' }}>
                                            {{ $item->judul_posisi }} ({{ $item->divisi }})
                                        </option>
                                    @endforeach
                                </select>
                                <span class="text-xs text-gray-400 mt-1 block">Pilih divisi yang ingin Anda tuju atau biarkan umum.</span>
                                @error('lowongan_id') <span class="text-red-400 text-sm mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="nama_pendaftar" class="filter-label mb-2">Nama Lengkap</label>
                                <input type="text" name="nama_pendaftar" id="nama_pendaftar" class="filter-input" 
                                       placeholder="Masukkan nama lengkap Anda" value="{{ old('nama_pendaftar', $pendaftaran->nama_pendaftar) }}" required>
                                @error('nama_pendaftar') <span class="text-red-400 text-sm mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="asal_kampus" class="filter-label mb-2">Asal Kampus</label>
                                <input type="text" name="asal_kampus" id="asal_kampus" class="filter-input" 
                                       placeholder="Contoh: Universitas Gadjah Mada" value="{{ old('asal_kampus', $pendaftaran->asal_kampus) }}" required>
                                @error('asal_kampus') <span class="text-red-400 text-sm mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="prodi" class="filter-label mb-2">Prodi / Jurusan</label>
                                <input type="text" name="prodi" id="prodi" class="filter-input" 
                                       placeholder="Contoh: Teknologi Informasi / Statistika" value="{{ old('prodi', $pendaftaran->prodi) }}" required>
                                @error('prodi') <span class="text-red-400 text-sm mt-1">{{ $message }}</span> @enderror
                            </div>

                            {{-- TANGGAL UI --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="tanggal_mulai" class="filter-label mb-2">Tanggal Mulai <span class="text-red-400">*</span></label>
                                    <div class="date-input-wrapper">
                                        <input type="text" name="tanggal_mulai" id="tanggal_mulai" 
                                               class="filter-input cursor-pointer" 
                                               placeholder="Pilih Tanggal Mulai"
                                               value="{{ old('tanggal_mulai', $pendaftaran->tanggal_mulai) }}">
                                        <i class="fas fa-calendar-alt date-input-icon"></i>
                                    </div>
                                    @error('tanggal_mulai') <span class="text-red-400 text-sm mt-1">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label for="tanggal_selesai" class="filter-label mb-2">Tanggal Selesai <span class="text-red-400">*</span></label>
                                    <div class="date-input-wrapper">
                                        <input type="text" name="tanggal_selesai" id="tanggal_selesai" 
                                               class="filter-input cursor-pointer" 
                                               placeholder="Pilih Tanggal Selesai"
                                               value="{{ old('tanggal_selesai', $pendaftaran->tanggal_selesai) }}">
                                        <i class="fas fa-calendar-check date-input-icon"></i>
                                    </div>
                                    @error('tanggal_selesai') <span class="text-red-400 text-sm mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>

                        </div>

                        {{-- Kolom Kanan: Upload Dokumen --}}
                        <div class="space-y-6">
                            {{-- Surat Permohonan (PDF) --}}
                            <div>
                                <label for="surat_permohonan" class="filter-label mb-2">Ganti Surat Permohonan Magang (PDF)</label>
                                @if($pendaftaran->surat_permohonan)
                                    <p class="text-xs text-green-400 mb-1 flex items-center gap-1">
                                        <i class="fas fa-check"></i> File saat ini tersimpan. Unggah hanya jika ingin mengganti.
                                    </p>
                                @endif
                                <input type="file" name="surat_permohonan" id="surat_permohonan" 
                                       class="filter-input file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-[#d97757]/20 file:text-[#e88968] hover:file:bg-[#d97757]/40 validate-file" 
                                       accept=".pdf" data-max-size="2">
                                <span class="text-xs text-gray-400 mt-1">Kosongkan jika tidak ingin mengubah berkas saat ini. Maks 2MB.</span>
                                <p class="text-red-400 text-xs mt-1 error-message hidden"></p> 
                                @error('surat_permohonan') <span class="block text-red-400 text-sm mt-1">{{ $message }}</span> @enderror
                            </div>

                            {{-- Surat Kampus (PDF) --}}
                            <div>
                                <label for="surat_kampus" class="filter-label mb-2">Ganti Surat Keterangan/Rekomendasi Kampus (PDF)</label>
                                @if($pendaftaran->surat_kampus)
                                    <p class="text-xs text-green-400 mb-1 flex items-center gap-1">
                                        <i class="fas fa-check"></i> File saat ini tersimpan. Unggah hanya jika ingin mengganti.
                                    </p>
                                @endif
                                <input type="file" name="surat_kampus" id="surat_kampus" 
                                       class="filter-input file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-[#d97757]/20 file:text-[#e88968] hover:file:bg-[#d97757]/40 validate-file" 
                                       accept=".pdf" data-max-size="2">
                                <span class="text-xs text-gray-400 mt-1">Kosongkan jika tidak ingin mengubah berkas saat ini. Maks 2MB.</span>
                                <p class="text-red-400 text-xs mt-1 error-message hidden"></p>
                                @error('surat_kampus') <span class="block text-red-400 text-sm mt-1">{{ $message }}</span> @enderror
                            </div>
                            
                            {{-- Pas Foto (JPG/PNG) --}}
                            <div>
                                <label for="pas_foto" class="filter-label mb-2">Ganti Pas Foto (JPG/PNG)</label>
                                @if($pendaftaran->pas_foto)
                                    <div class="flex items-center gap-3 mb-2">
                                        <img src="{{ asset('storage/' . $pendaftaran->pas_foto) }}" class="w-10 h-10 object-cover rounded-lg border border-[#4a4a4a]">
                                        <span class="text-xs text-green-400">Foto saat ini tersimpan. Unggah baru jika ingin mengganti.</span>
                                    </div>
                                @endif
                                <input type="file" name="pas_foto" id="pas_foto" 
                                       class="filter-input file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-[#d97757]/20 file:text-[#e88968] hover:file:bg-[#d97757]/40 validate-file" 
                                       accept="image/jpeg,image/png,image/jpg" data-max-size="1">
                                <span class="text-xs text-gray-400 mt-1">Kosongkan jika tidak ingin mengubah berkas saat ini. Maks 1MB.</span>
                                <p class="text-red-400 text-xs mt-1 error-message hidden"></p>
                                @error('pas_foto') <span class="block text-red-400 text-sm mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    
                    {{-- Tombol Submit --}}
                    <div class="bg-[#1a1a1a]/50 px-4 py-4 sm:px-6 border-t border-[#3a3a3a]">
                        <div class="flex flex-col-reverse sm:flex-row sm:justify-end sm:items-center gap-3">
                            <a href="{{ route('daftar.index') }}" class="filter-btn filter-btn-secondary w-full sm:w-auto text-center">
                                <i class="fas fa-times"></i> Batal
                            </a>
                            <button type="submit" id="submitBtn" class="filter-btn filter-btn-primary w-full sm:w-auto">
                                <i class="fas fa-save"></i> <span>Perbarui & Kirim Ulang</span>
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/id.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const configMulai = {
                locale: "id",
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "j F Y",
                minDate: "today",
                disableMobile: true,
                onChange: function(selectedDates, dateStr, instance) {
                    if (selectedDates[0]) {
                        pickerSelesai.set('minDate', selectedDates[0]);
                    }
                }
            };
            
            const configSelesai = {
                locale: "id",
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "j F Y",
                minDate: "today",
                disableMobile: true
            };

            const pickerMulai = flatpickr("#tanggal_mulai", configMulai);
            const pickerSelesai = flatpickr("#tanggal_selesai", configSelesai);

            const fileInputs = document.querySelectorAll('.validate-file');

            fileInputs.forEach(input => {
                input.addEventListener('change', function() {
                    const file = this.files[0];
                    const maxSizeMB = parseFloat(this.getAttribute('data-max-size')) || 2;
                    const maxSizeBytes = maxSizeMB * 1024 * 1024;
                    const errorMsgElement = this.parentElement.querySelector('.error-message');

                    if(errorMsgElement) {
                        errorMsgElement.classList.add('hidden');
                        errorMsgElement.textContent = '';
                    }
                    this.classList.remove('border-red-500', 'text-red-500');

                    if (file) {
                        if (file.size > maxSizeBytes) {
                            this.value = ""; 
                            Swal.fire({
                                icon: 'error',
                                title: 'File Terlalu Besar!',
                                text: `Ukuran file Anda ${(file.size / 1024 / 1024).toFixed(2)} MB.\nBatas maksimal adalah ${maxSizeMB} MB.`,
                                confirmButtonColor: '#d97757',
                                confirmButtonText: 'Mengerti',
                                background: '#1f2937', 
                                color: '#fff'
                            });
                            this.classList.add('border-red-500', 'text-red-500');
                            return; 
                        }

                        const fileType = file.type;
                        const acceptAttr = this.getAttribute('accept');
                        
                        if (acceptAttr.includes('.pdf') && fileType !== 'application/pdf') {
                            this.value = "";
                            Swal.fire({
                                icon: 'error',
                                title: 'Format Salah!',
                                text: 'Harap upload file PDF yang valid.',
                                confirmButtonColor: '#d97757',
                                background: '#1f2937', color: '#fff'
                            });
                            return;
                        }

                        if (acceptAttr.includes('image') && !fileType.startsWith('image/')) {
                            this.value = "";
                            Swal.fire({
                                icon: 'error',
                                title: 'Format Salah!',
                                text: 'Harap upload file Gambar (JPG/PNG).',
                                confirmButtonColor: '#d97757',
                                background: '#1f2937', color: '#fff'
                            });
                            return;
                        }
                    }
                });
            });

            // Form Submit Handler
            const form = document.getElementById('pendaftaranEditForm');
            const submitBtn = document.getElementById('submitBtn');

            form.addEventListener('submit', function(e) {
                const tglMulai = document.getElementById('tanggal_mulai').value;
                const tglSelesai = document.getElementById('tanggal_selesai').value;

                if (!tglMulai || !tglSelesai) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Tanggal Belum Lengkap',
                        text: 'Silakan pilih Tanggal Mulai dan Tanggal Selesai magang terlebih dahulu.',
                        confirmButtonColor: '#d97757',
                        background: '#1f2937',
                        color: '#fff'
                    });
                    return false;
                }

                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i><span>Memperbarui Berkas...</span>';
            });
        });
    </script>
    @endpush
</x-main-layout>

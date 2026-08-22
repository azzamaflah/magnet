{{-- resources/views/daftar/show.blade.php --}}
<x-main-layout>
    <div class="claude-container">
        
        {{-- Header Section --}}
        <div class="border-b border-[#3a3a3a] header-section">
            <div class="max-w-7xl mx-auto px-6 py-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('daftar.index') }}" 
                           class="w-9 h-9 rounded-xl bg-white/5 border border-white/10 hover:border-[#d97757]/60 hover:bg-[#d97757]/15 text-gray-400 hover:text-white flex items-center justify-center transition-all shadow-sm" 
                           title="Kembali ke Daftar Pendaftar">
                            <i class="fas fa-arrow-left text-sm"></i>
                        </a>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-gray-400">Pendaftaran Magang &bull;</span>
                                <span class="text-xs font-semibold text-[#e88968]">#{{ $pendaftaran->id }}</span>
                            </div>
                            <h2 class="claude-title text-xl sm:text-2xl font-bold text-white tracking-tight">
                                {{ $pendaftaran->nama_pendaftar }}
                            </h2>
                        </div>
                    </div>

                    {{-- Status Badge Header --}}
                    <div>
                        @if ($pendaftaran->status == 'pending')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-yellow-500/15 text-yellow-300 border border-yellow-500/30 shadow-sm">
                                <span class="w-2 h-2 rounded-full bg-yellow-400 animate-pulse"></span>
                                Menunggu Verifikasi
                            </span>
                        @elseif ($pendaftaran->status == 'approved')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-green-500/15 text-green-300 border border-green-500/30 shadow-sm">
                                <i class="fas fa-check-circle text-xs"></i>
                                Telah Disetujui (Approved)
                            </span>
                        @elseif ($pendaftaran->status == 'conditional')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-blue-500/15 text-blue-300 border border-blue-500/30 shadow-sm">
                                <i class="fas fa-info-circle text-xs"></i>
                                Disetujui Bersyarat
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-red-500/15 text-red-300 border border-red-500/30 shadow-sm">
                                <i class="fas fa-times-circle text-xs"></i>
                                Berkas Ditolak
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Container --}}
        <div class="max-w-7xl mx-auto py-8 px-6">
            
            {{-- Error Validation Alert --}}
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-500/15 border border-red-500/30 text-red-200 rounded-2xl flex items-start gap-3 shadow-lg" role="alert">
                    <i class="fas fa-exclamation-circle text-red-400 text-lg flex-shrink-0 mt-0.5"></i>
                    <div>
                        <strong class="font-bold text-sm block mb-1">Perhatian:</strong>
                        <ul class="list-disc list-inside text-xs space-y-0.5 text-red-300/90">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                {{-- ========================================================= --}}
                {{-- KOLOM KIRI (2 Kolom): INFO PENDAFTAR, SYARAT, & AKSI      --}}
                {{-- ========================================================= --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- 1. Kartu Informasi Pendaftar --}}
                    <div class="bg-[#2a2a2a]/60 backdrop-blur-md border border-[#3a3a3a] rounded-2xl shadow-xl overflow-hidden">
                        <div class="p-6">
                            <div class="flex items-center justify-between border-b border-[#3a3a3a] pb-4 mb-5">
                                <h3 class="claude-title text-lg text-white font-bold flex items-center gap-2">
                                    <i class="fas fa-user-circle text-[#e88968]"></i>
                                    <span>Informasi Pendaftar</span>
                                </h3>
                                <span class="text-xs text-gray-400">
                                    Diajukan pada: {{ \Carbon\Carbon::parse($pendaftaran->created_at)->format('d F Y, H:i') }} WIB
                                </span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 text-sm">
                                <div>
                                    <span class="text-xs text-gray-400 block mb-1 font-medium">Nama Lengkap</span>
                                    <p class="text-white font-bold text-base">{{ $pendaftaran->nama_pendaftar }}</p>
                                </div>

                                <div>
                                    <span class="text-xs text-gray-400 block mb-1 font-medium">Alamat Email Pendaftar</span>
                                    <p class="text-white font-medium flex items-center gap-1.5">
                                        <i class="fas fa-envelope text-xs text-gray-500"></i>
                                        <span>{{ $pendaftaran->email }}</span>
                                    </p>
                                </div>

                                <div>
                                    <span class="text-xs text-gray-400 block mb-1 font-medium">Asal Perguruan Tinggi / Kampus</span>
                                    <p class="text-white font-semibold flex items-center gap-1.5">
                                        <i class="fas fa-university text-xs text-gray-500"></i>
                                        <span>{{ $pendaftaran->asal_kampus }}</span>
                                    </p>
                                </div>

                                <div>
                                    <span class="text-xs text-gray-400 block mb-1 font-medium">Program Studi / Jurusan</span>
                                    <p class="text-white font-semibold flex items-center gap-1.5">
                                        <i class="fas fa-graduation-cap text-xs text-gray-500"></i>
                                        <span>{{ $pendaftaran->prodi ?? '-' }}</span>
                                    </p>
                                </div>

                                <div class="md:col-span-2 pt-2 border-t border-[#3a3a3a]/60">
                                    <span class="text-xs text-gray-400 block mb-1.5 font-medium">Rencana Periode Pelaksanaan Magang</span>
                                    <div class="flex flex-wrap items-center gap-3">
                                        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-white/5 border border-white/10 text-white font-semibold">
                                            <i class="fas fa-calendar-alt text-[#e88968]"></i>
                                            <span>{{ \Carbon\Carbon::parse($pendaftaran->tanggal_mulai)->format('d F Y') }} &ndash; {{ \Carbon\Carbon::parse($pendaftaran->tanggal_selesai)->format('d F Y') }}</span>
                                        </div>
                                        @php
                                            $tMulai = \Carbon\Carbon::parse($pendaftaran->tanggal_mulai);
                                            $tSelesai = \Carbon\Carbon::parse($pendaftaran->tanggal_selesai);
                                            $durasi = round($tMulai->diffInDays($tSelesai) / 30);
                                        @endphp
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-[#d97757]/15 text-[#f69d7f] border border-[#d97757]/30">
                                            <i class="fas fa-hourglass-half text-[10px]"></i>
                                            <span>Durasi: {{ max(1, (int)$durasi) }} Bulan</span>
                                        </span>
                                    </div>
                                </div>

                                {{-- Konfirmasi Kehadiran (Jika Approved) --}}
                                @if($pendaftaran->status === 'approved')
                                <div class="md:col-span-2 p-3.5 rounded-xl bg-green-500/10 border border-green-500/25">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2 text-green-300 font-semibold text-xs">
                                            <i class="fas fa-user-check text-sm"></i>
                                            <span>Konfirmasi Kehadiran Peserta</span>
                                        </div>
                                        @if($pendaftaran->konfirmasi_at)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-green-500/20 text-green-400 border border-green-500/30">
                                                <i class="fas fa-check"></i> Hadir &bull; {{ \Carbon\Carbon::parse($pendaftaran->konfirmasi_at)->format('d M Y, H:i') }} WIB
                                            </span>
                                        @else
                                            <span class="text-xs text-yellow-400 font-medium italic">
                                                <i class="fas fa-clock mr-1"></i> Menunggu konfirmasi kehadiran dari mahasiswa
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- 2. KARTU PREVIEW SYARAT & KUALIFIKASI FORMASI (FITUR BARU) --}}
                    <div class="bg-[#2a2a2a]/60 backdrop-blur-md border border-[#3a3a3a] rounded-2xl shadow-xl overflow-hidden">
                        <div class="p-6">
                            <div class="flex items-center justify-between border-b border-[#3a3a3a] pb-4 mb-4">
                                <h3 class="claude-title text-lg text-white font-bold flex items-center gap-2">
                                    <i class="fas fa-clipboard-check text-[#e88968]"></i>
                                    <span>Syarat & Kualifikasi Formasi Magang</span>
                                </h3>
                                @if($pendaftaran->lowongan)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-bold bg-[#d97757]/20 border border-[#d97757]/40 text-[#f69d7f]">
                                        <i class="fas fa-briefcase text-[10px]"></i>
                                        <span>{{ $pendaftaran->lowongan->divisi }}</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-medium bg-white/5 border border-white/10 text-gray-300">
                                        <i class="fas fa-user-graduate text-[10px]"></i>
                                        <span>Magang Umum</span>
                                    </span>
                                @endif
                            </div>

                            @if($pendaftaran->lowongan)
                                <div class="space-y-4">
                                    {{-- Info Posisi & Kuota --}}
                                    <div class="p-4 rounded-xl bg-[#1f1f1f] border border-[#3a3a3a] flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                        <div>
                                            <span class="text-xs text-gray-400 block font-medium">Posisi yang Dilamar:</span>
                                            <h4 class="text-base font-bold text-white">{{ $pendaftaran->lowongan->judul_posisi }}</h4>
                                            <p class="text-xs text-[#e88968] font-medium mt-0.5">Seksi / Divisi: {{ $pendaftaran->lowongan->divisi }}</p>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <div class="text-right">
                                                <span class="text-[11px] text-gray-400 block">Sisa Kuota:</span>
                                                <span class="text-xs font-bold text-white">
                                                    {{ $pendaftaran->lowongan->kuota_tersisa }} dari {{ $pendaftaran->lowongan->kuota }} kursi
                                                </span>
                                            </div>
                                            <div class="w-9 h-9 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-gray-300">
                                                <i class="fas fa-users text-xs"></i>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Syarat & Kualifikasi Keahlian --}}
                                    <div>
                                        <h5 class="text-xs font-bold uppercase tracking-wider text-gray-300 mb-2 flex items-center gap-1.5">
                                            <i class="fas fa-list-ul text-[#e88968]"></i>
                                            <span>Kualifikasi / Syarat Peserta Formasi:</span>
                                        </h5>
                                        <div class="p-4 rounded-xl bg-white/[0.03] border border-[#3a3a3a] text-sm text-gray-200 leading-relaxed whitespace-pre-line">
                                            {{ $pendaftaran->lowongan->kualifikasi ?: 'Tidak ada kualifikasi khusus (terbuka untuk seluruh jurusan yang relevan).' }}
                                        </div>
                                    </div>

                                    {{-- Deskripsi Tugas Lowongan --}}
                                    @if($pendaftaran->lowongan->deskripsi)
                                    <div>
                                        <h5 class="text-xs font-bold uppercase tracking-wider text-gray-300 mb-2 flex items-center gap-1.5">
                                            <i class="fas fa-tasks text-[#e88968]"></i>
                                            <span>Deskripsi Tugas & Tanggung Jawab:</span>
                                        </h5>
                                        <div class="p-4 rounded-xl bg-white/[0.03] border border-[#3a3a3a] text-sm text-gray-200 leading-relaxed whitespace-pre-line">
                                            {{ $pendaftaran->lowongan->deskripsi }}
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            @else
                                {{-- Jika Magang Umum --}}
                                <div class="p-4 rounded-xl bg-white/[0.03] border border-[#3a3a3a] space-y-3">
                                    <div class="flex items-center gap-2 text-sm font-semibold text-white">
                                        <i class="fas fa-info-circle text-[#e88968]"></i>
                                        <span>Persyaratan Standar Magang BPS Kabupaten Bantul:</span>
                                    </div>
                                    <ul class="text-xs text-gray-300 space-y-2 list-disc list-inside pl-1">
                                        <li>Mahasiswa aktif D3 / S1 / S2 dari perguruan tinggi terakreditasi.</li>
                                        <li>Melampirkan <strong>Surat Permohonan Magang</strong> resmi (PDF maksimal 1MB).</li>
                                        <li>Melampirkan <strong>Surat Keterangan/Pengantar dari Kampus</strong> (PDF maksimal 1MB).</li>
                                        <li>Mengunggah <strong>Pas Foto Formal</strong> terbaru (JPG/PNG maksimal 2MB).</li>
                                        <li>Durasi magang minimal 1 s.d. 7 bulan sesuai kesepakatan penempatan seksi BPS Bantul.</li>
                                    </ul>
                                </div>
                            @endif

                            {{-- Catatan / Syarat dari Admin Sebelumnya (Jika Ada) --}}
                            @if($pendaftaran->remarks)
                                <div class="mt-5 pt-4 border-t border-[#3a3a3a]">
                                    <h5 class="text-xs font-bold uppercase tracking-wider text-yellow-300 mb-2 flex items-center gap-1.5">
                                        <i class="fas fa-sticky-note text-yellow-400"></i>
                                        <span>Catatan Persyaratan / Alasan Keputusan Sebelumnya:</span>
                                    </h5>
                                    <div class="p-3.5 rounded-xl bg-yellow-500/10 border border-yellow-500/25 text-xs text-yellow-200 whitespace-pre-wrap leading-relaxed">
                                        {{ $pendaftaran->remarks }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- 3. Form Tindakan Keputusan Admin --}}
                    @if(auth()->user()->isAdmin())
                    <div class="bg-[#2a2a2a]/60 backdrop-blur-md border border-[#3a3a3a] rounded-2xl shadow-xl overflow-hidden">
                        <form method="POST" action="{{ route('daftar.updateStatus', $pendaftaran) }}">
                            @csrf
                            <div class="p-6">
                                <div class="flex items-center justify-between border-b border-[#3a3a3a] pb-4 mb-4">
                                    <h3 class="claude-title text-lg text-white font-bold flex items-center gap-2">
                                        <i class="fas fa-gavel text-[#e88968]"></i>
                                        <span>Keputusan & Verifikasi Admin</span>
                                    </h3>
                                    <span class="text-xs text-gray-400">
                                        Status saat ini: <strong class="text-white uppercase">{{ $pendaftaran->status }}</strong>
                                    </span>
                                </div>

                                <div>
                                    <label for="remarks" class="block text-xs font-semibold text-gray-300 mb-2">
                                        Catatan / Instruksi Syarat Tambahan
                                        <span class="text-gray-400 font-normal">(Wajib diisi jika Ditolak atau ACC Bersyarat)</span>
                                    </label>
                                    <textarea name="remarks" id="remarks" rows="4" class="filter-input text-sm leading-relaxed" 
                                              placeholder="Tuliskan catatan khusus persetujuan, instruksi persyaratan perbaikan berkas, atau alasan penolakan pendaftaran...">{{ old('remarks', $pendaftaran->remarks) }}</textarea>
                                    @error('remarks') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    
                                    {{-- Quick Template Pills --}}
                                    <div class="flex flex-wrap items-center gap-2 mt-2.5">
                                        <span class="text-[11px] text-gray-400">Saran Cepat:</span>
                                        <button type="button" onclick="document.getElementById('remarks').value='Selamat, berkas Anda telah diverifikasi dan dinyatakan diterima di program magang BPS Kabupaten Bantul.'" 
                                                class="px-2.5 py-1 text-[11px] rounded-lg bg-white/5 hover:bg-white/10 text-gray-300 border border-white/5 hover:border-white/20 transition-all">
                                            ACC Lengkap
                                        </button>
                                        <button type="button" onclick="document.getElementById('remarks').value='Disetujui bersyarat: Mohon lengkapi surat pengantar resmi berstempel basah/elektronik dari fakultas sebelum hari pertama magang.'" 
                                                class="px-2.5 py-1 text-[11px] rounded-lg bg-white/5 hover:bg-white/10 text-gray-300 border border-white/5 hover:border-white/20 transition-all">
                                            Syarat Surat Kampus
                                        </button>
                                        <button type="button" onclick="document.getElementById('remarks').value='Mohon maaf, kuota formasi pada periode yang diajukan saat ini telah penuh.'" 
                                                class="px-2.5 py-1 text-[11px] rounded-lg bg-white/5 hover:bg-white/10 text-gray-300 border border-white/5 hover:border-white/20 transition-all">
                                            Kuota Penuh
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            {{-- Action Buttons Footer --}}
                            <div class="bg-[#1a1a1a]/70 px-6 py-4 border-t border-[#3a3a3a] flex flex-wrap gap-3 justify-end items-center">
                                <button type="submit" name="status" value="rejected" 
                                        class="filter-btn bg-red-700/80 hover:bg-red-600 text-white shadow-lg shadow-red-600/20 text-xs px-4 py-2">
                                    <i class="fas fa-times-circle"></i>
                                    <span>Tolak Pendaftaran</span>
                                </button>
                                <button type="submit" name="status" value="conditional" 
                                        class="filter-btn bg-blue-700/80 hover:bg-blue-600 text-white shadow-lg shadow-blue-600/20 text-xs px-4 py-2">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <span>ACC Bersyarat</span>
                                </button>
                                <button type="submit" name="status" value="approved" 
                                        class="filter-btn filter-btn-primary text-xs px-5 py-2">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Setujui & Buat Akun Magang</span>
                                </button>
                            </div>
                        </form>
                    </div>
                    @endif

                </div>

                {{-- ========================================================= --}}
                {{-- KOLOM KANAN (1 Kolom): PREVIEW DOKUMEN & LIVE VIEWER      --}}
                {{-- ========================================================= --}}
                <div class="space-y-6">

                    {{-- 1. Pas Foto Formal --}}
                    <div class="bg-[#2a2a2a]/60 backdrop-blur-md border border-[#3a3a3a] rounded-2xl shadow-xl overflow-hidden">
                        <div class="p-6 text-center">
                            <div class="flex items-center justify-between border-b border-[#3a3a3a] pb-3 mb-4">
                                <h4 class="claude-title text-base text-white font-bold flex items-center gap-2">
                                    <i class="fas fa-camera text-[#e88968]"></i>
                                    <span>Pas Foto Formal</span>
                                </h4>
                                <span class="text-[11px] text-gray-400">Maks. 2MB</span>
                            </div>

                            @if($pendaftaran->pas_foto)
                                <div class="relative group mx-auto max-w-[180px] rounded-2xl overflow-hidden border-2 border-[#4a4a4a] shadow-lg mb-4">
                                    <img src="{{ asset('storage/' . $pendaftaran->pas_foto) }}" 
                                         alt="Pas Foto {{ $pendaftaran->nama_pendaftar }}" 
                                         class="w-full h-52 object-cover transition-transform duration-300 group-hover:scale-105">
                                    <a href="{{ asset('storage/' . $pendaftaran->pas_foto) }}" target="_blank" 
                                       class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 flex items-center justify-center text-white text-xs font-semibold gap-1.5 transition-opacity">
                                        <i class="fas fa-expand"></i> Buka Penuh
                                    </a>
                                </div>
                                <a href="{{ route('daftar.downloadFile', [$pendaftaran, 'pas_foto']) }}" 
                                   class="filter-btn filter-btn-secondary w-full justify-center text-xs py-2">
                                    <i class="fas fa-download"></i>
                                    <span>Unduh Foto</span>
                                </a>
                            @else
                                <div class="py-8 text-center text-gray-400">
                                    <i class="fas fa-image text-3xl mb-2 text-gray-500"></i>
                                    <p class="text-xs">Foto tidak tersedia</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- 2. Surat Permohonan Magang (PDF Preview) --}}
                    <div class="bg-[#2a2a2a]/60 backdrop-blur-md border border-[#3a3a3a] rounded-2xl shadow-xl overflow-hidden">
                        <div class="p-6">
                            <div class="flex items-center justify-between border-b border-[#3a3a3a] pb-3 mb-4">
                                <h4 class="claude-title text-base text-white font-bold flex items-center gap-2">
                                    <i class="fas fa-file-pdf text-red-400"></i>
                                    <span>Surat Permohonan</span>
                                </h4>
                                <span class="text-[11px] text-gray-400">Format PDF</span>
                            </div>

                            @if($pendaftaran->surat_permohonan)
                                <div class="p-4 rounded-xl bg-red-500/10 border border-red-500/25 mb-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-red-500/20 text-red-400 flex items-center justify-center text-lg flex-shrink-0">
                                            <i class="fas fa-file-pdf"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-xs font-bold text-white truncate">Surat_Permohonan_Magang.pdf</p>
                                            <span class="text-[11px] text-red-300">Dokumen permohonan calon peserta</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-2">
                                    <button type="button" onclick="openPdfModal('{{ asset('storage/' . $pendaftaran->surat_permohonan) }}', 'Surat Permohonan Magang')"
                                            class="filter-btn bg-white/5 hover:bg-red-500/20 text-red-300 border border-red-500/30 text-xs py-2 justify-center">
                                        <i class="fas fa-eye"></i>
                                        <span>Lihat PDF</span>
                                    </button>
                                    <a href="{{ route('daftar.downloadFile', [$pendaftaran, 'surat_permohonan']) }}" 
                                       class="filter-btn filter-btn-secondary text-xs py-2 justify-center">
                                        <i class="fas fa-download"></i>
                                        <span>Unduh</span>
                                    </a>
                                </div>
                            @else
                                <div class="py-6 text-center text-gray-400">
                                    <i class="fas fa-file-excel text-3xl mb-2 text-gray-500"></i>
                                    <p class="text-xs">Surat permohonan belum diunggah</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- 3. Surat Rekomendasi Kampus (PDF Preview) --}}
                    <div class="bg-[#2a2a2a]/60 backdrop-blur-md border border-[#3a3a3a] rounded-2xl shadow-xl overflow-hidden">
                        <div class="p-6">
                            <div class="flex items-center justify-between border-b border-[#3a3a3a] pb-3 mb-4">
                                <h4 class="claude-title text-base text-white font-bold flex items-center gap-2">
                                    <i class="fas fa-university text-blue-400"></i>
                                    <span>Surat Pengantar Kampus</span>
                                </h4>
                                <span class="text-[11px] text-gray-400">Format PDF</span>
                            </div>

                            @if($pendaftaran->surat_kampus)
                                <div class="p-4 rounded-xl bg-blue-500/10 border border-blue-500/25 mb-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-blue-500/20 text-blue-400 flex items-center justify-center text-lg flex-shrink-0">
                                            <i class="fas fa-university"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-xs font-bold text-white truncate">Surat_Keterangan_Kampus.pdf</p>
                                            <span class="text-[11px] text-blue-300">Rekomendasi dari universitas/fakultas</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-2">
                                    <button type="button" onclick="openPdfModal('{{ asset('storage/' . $pendaftaran->surat_kampus) }}', 'Surat Keterangan Kampus')"
                                            class="filter-btn bg-white/5 hover:bg-blue-500/20 text-blue-300 border border-blue-500/30 text-xs py-2 justify-center">
                                        <i class="fas fa-eye"></i>
                                        <span>Lihat PDF</span>
                                    </button>
                                    <a href="{{ route('daftar.downloadFile', [$pendaftaran, 'surat_kampus']) }}" 
                                       class="filter-btn filter-btn-secondary text-xs py-2 justify-center">
                                        <i class="fas fa-download"></i>
                                        <span>Unduh</span>
                                    </a>
                                </div>
                            @else
                                <div class="py-6 text-center text-gray-400">
                                    <i class="fas fa-file-excel text-3xl mb-2 text-gray-500"></i>
                                    <p class="text-xs">Surat kampus belum diunggah</p>
                                </div>
                            @endif
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>

    {{-- ============================================================= --}}
    {{-- MODAL INTERAKTIF LIVE PREVIEW DOKUMEN PDF (LANGSUNG DI BROWSER) --}}
    {{-- ============================================================= --}}
    <div id="pdfModal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-md hidden items-center justify-center p-4 sm:p-6 transition-opacity">
        <div class="bg-[#222222] border border-[#3a3a3a] rounded-3xl w-full max-w-4xl max-h-[90vh] flex flex-col shadow-2xl overflow-hidden animate-fadeIn">
            
            {{-- Modal Header --}}
            <div class="px-6 py-4 bg-[#1a1a1a] border-b border-[#3a3a3a] flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-[#d97757]/20 text-[#e88968] flex items-center justify-center text-sm">
                        <i class="fas fa-file-pdf"></i>
                    </div>
                    <div>
                        <h4 id="pdfModalTitle" class="text-sm font-bold text-white">Pratinjau Dokumen PDF</h4>
                        <span class="text-[11px] text-gray-400">MagNet PDF Viewer</span>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <a id="pdfModalOpenTab" href="#" target="_blank" 
                       class="px-3 py-1.5 rounded-xl bg-white/5 hover:bg-white/10 text-gray-300 hover:text-white border border-white/10 text-xs font-semibold flex items-center gap-1.5 transition-colors" title="Buka di Tab Baru">
                        <i class="fas fa-external-link-alt text-[10px]"></i>
                        <span>Tab Baru</span>
                    </a>
                    <button type="button" onclick="closePdfModal()" 
                            class="w-8 h-8 rounded-xl bg-white/5 hover:bg-white/10 text-gray-400 hover:text-white flex items-center justify-center transition-colors">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>
            </div>

            {{-- Modal Body (Iframe PDF Viewer) --}}
            <div class="p-4 flex-1 overflow-hidden bg-[#141414] min-h-[500px]">
                <iframe id="pdfIframe" src="" class="w-full h-full min-h-[500px] rounded-xl border border-[#3a3a3a]" frameborder="0"></iframe>
            </div>
            
            {{-- Modal Footer --}}
            <div class="px-6 py-3 bg-[#1a1a1a] border-t border-[#3a3a3a] flex items-center justify-between text-xs text-gray-400">
                <span>Gunakan kontrol PDF browser untuk memperbesar atau mencetak berkas.</span>
                <button type="button" onclick="closePdfModal()" class="filter-btn filter-btn-secondary text-xs px-3 py-1">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function openPdfModal(url, title) {
            const modal = document.getElementById('pdfModal');
            const iframe = document.getElementById('pdfIframe');
            const modalTitle = document.getElementById('pdfModalTitle');
            const openTabLink = document.getElementById('pdfModalOpenTab');

            modalTitle.textContent = title;
            iframe.src = url;
            openTabLink.href = url;

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function closePdfModal() {
            const modal = document.getElementById('pdfModal');
            const iframe = document.getElementById('pdfIframe');

            modal.classList.add('hidden');
            modal.classList.remove('flex');
            iframe.src = '';
            document.body.style.overflow = 'auto';
        }

        // Close on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closePdfModal();
            }
        });
    </script>
    @endpush
</x-main-layout>
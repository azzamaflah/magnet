{{-- resources/views/daftar/show.blade.php --}}
<x-main-layout>
    <div class="claude-container">
        
        {{-- Header Section --}}
        <div class="border-b border-[#3a3a3a] header-section">
            <div class="max-w-7xl mx-auto px-6 py-4">
                <div class="flex items-center gap-4">
                    <a href="{{ route('daftar.index') }}" class="text-gray-400 hover:text-white" title="Kembali">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <h2 class="claude-title text-2xl text-white">
                        Detail Pendaftaran: {{ $pendaftaran->nama_pendaftar }}
                    </h2>
                </div>
            </div>
        </div>

        {{-- Grid Utama (Info di kiri, Dokumen di kanan) --}}
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

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Kolom Kiri: Info & Form Tindakan --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- Box Info Detail --}}
                    <div class="bg-[#2a2a2a]/60 backdrop-blur-md border border-[#3a3a3a] rounded-xl shadow-lg overflow-hidden">
                        <div class="p-6">
                            <h3 class="claude-title text-xl text-white mb-4">Informasi Pendaftar</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                <div>
                                    <dt class="text-gray-400">Nama Lengkap</dt>
                                    <dd class="text-white font-medium">{{ $pendaftaran->nama_pendaftar }}</dd>
                                </div>
                                <div>
                                    <dt class="text-gray-400">Email</dt>
                                    <dd class="text-white font-medium">{{ $pendaftaran->email }}</dd>
                                </div>
                                <div>
                                    <dt class="text-gray-400">Asal Kampus</dt>
                                    <dd class="text-white font-medium">{{ $pendaftaran->asal_kampus }}</dd>
                                </div>

                                {{-- ✅ TAMBAHAN BARU: PRODI/JURUSAN --}}
                                <div>
                                    <dt class="text-gray-400">Prodi / Jurusan</dt>
                                    <dd class="text-white font-medium">{{ $pendaftaran->prodi ?? '-' }}</dd>
                                </div>

                                <div class="md:col-span-2">
                                    <dt class="text-gray-400">Periode Magang</dt>
                                    <dd class="text-white font-medium">
                                        {{ \Carbon\Carbon::parse($pendaftaran->tanggal_mulai)->format('d M Y') }} - 
                                        {{ \Carbon\Carbon::parse($pendaftaran->tanggal_selesai)->format('d M Y') }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-gray-400">Status Saat Ini</dt>
                                    <dd>
                                        @if ($pendaftaran->status == 'pending')
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-900 text-yellow-300">Menunggu</span>
                                        @elseif ($pendaftaran->status == 'approved')
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-900 text-green-300">Disetujui</span>
                                        @elseif ($pendaftaran->status == 'conditional')
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-900 text-blue-300">Bersyarat</span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-900 text-red-300">Ditolak</span>
                                        @endif
                                    </dd>
                                </div>
                                @if($pendaftaran->remarks)
                                <div class="md:col-span-2">
                                    <dt class="text-gray-400">Catatan Terakhir</dt>
                                    <dd class="text-white font-medium whitespace-pre-wrap">{{ $pendaftaran->remarks }}</dd>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Box Form Tindakan Admin --}}
                    @if(auth()->user()->isAdmin() && in_array($pendaftaran->status, ['pending', 'conditional']))
                    <div class="bg-[#2a2a2a]/60 backdrop-blur-md border border-[#3a3a3a] rounded-xl shadow-lg overflow-hidden">
                        <form method="POST" action="{{ route('daftar.updateStatus', $pendaftaran) }}">
                            @csrf
                            <div class="p-6">
                                <h3 class="claude-title text-xl text-white mb-4">Form Tindakan Admin</h3>
                                <div>
                                    <label for="remarks" class="filter-label mb-2">Catatan (Wajib jika Ditolak/Bersyarat, Opsional jika Disetujui)</label>
                                    <textarea name="remarks" id="remarks" rows="6" class="filter-input" 
                                              placeholder="Tuliskan catatan persetujuan, syarat, atau alasan penolakan...">{{ old('remarks', $pendaftaran->remarks) }}</textarea>
                                    @error('remarks') <span class="text-red-400 text-sm mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            
                            {{-- Tombol Aksi --}}
                            <div class="bg-[#1a1a1a]/50 px-6 py-4 border-t border-[#3a3a3a] flex flex-wrap gap-3 justify-end">
                                <button type="submit" name="status" value="rejected" 
                                        class="filter-btn bg-red-700 hover:bg-red-600 text-white shadow-lg shadow-red-600/30">
                                    <i class="fas fa-times-circle"></i> Tolak
                                </button>
                                <button type="submit" name="status" value="conditional" 
                                        class="filter-btn bg-blue-700 hover:bg-blue-600 text-white shadow-lg shadow-blue-600/30">
                                    <i class="fas fa-exclamation-triangle"></i> ACC Bersyarat
                                </button>
                                <button type="submit" name="status" value="approved" 
                                        class="filter-btn filter-btn-primary">
                                    <i class="fas fa-check-circle"></i> Setujui & Pindahkan
                                </button>
                            </div>
                        </form>
                    </div>
                    @endif
                </div>

                {{-- Kolom Kanan: Dokumen --}}
                <div class="space-y-6">
                    {{-- Box Pas Foto --}}
                    <div class="bg-[#2a2a2a]/60 backdrop-blur-md border border-[#3a3a3a] rounded-xl shadow-lg overflow-hidden">
                        <div class="p-6">
                            <h3 class="claude-title text-xl text-white mb-4">Pas Foto</h3>
                            <a href="{{ asset('storage/' . $pendaftaran->pas_foto) }}" target="_blank">
                                <img src="{{ asset('storage/' . $pendaftaran->pas_foto) }}" alt="Pas Foto {{ $pendaftaran->nama_pendaftar }}" 
                                     class="max-w-xs mx-auto w-full rounded-lg object-cover">
                            </a>
                            <a href="{{ route('daftar.downloadFile', [$pendaftaran, 'pas_foto']) }}" 
                               class="filter-btn filter-btn-secondary mt-4 w-full justify-center">
                                <i class="fas fa-download"></i> Download Foto
                            </a>
                        </div>
                    </div>

                    {{-- Box Surat Permohonan --}}
                    <div class="bg-[#2a2a2a]/60 backdrop-blur-md border border-[#3a3a3a] rounded-xl shadow-lg overflow-hidden">
                        <div class="p-6">
                            <h3 class="claude-title text-xl text-white mb-4">Surat Permohonan</h3>
                            <a href="{{ route('daftar.downloadFile', [$pendaftaran, 'surat_permohonan']) }}" 
                               class="filter-btn filter-btn-secondary w-full justify-center">
                                <i class="fas fa-download"></i> Download Surat Permohonan
                            </a>
                        </div>
                    </div>

                    {{-- Box Surat Kampus --}}
                    <div class="bg-[#2a2a2a]/60 backdrop-blur-md border border-[#3a3a3a] rounded-xl shadow-lg overflow-hidden">
                        <div class="p-6">
                            <h3 class="claude-title text-xl text-white mb-4">Surat Keterangan Kampus</h3>
                            <a href="{{ route('daftar.downloadFile', [$pendaftaran, 'surat_kampus']) }}" 
                               class="filter-btn filter-btn-secondary w-full justify-center">
                                <i class="fas fa-download"></i> Download Surat Kampus
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-main-layout>
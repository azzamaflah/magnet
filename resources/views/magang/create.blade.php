<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Data Magang - BPS Bantul</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Fraunces:wght@400;500;600&display=swap');

        html {
            background-color: #1a1a1a;
            overscroll-behavior: none; 
        }

        * {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }

        .claude-title {
            font-family: 'Fraunces', 'Georgia', 'Times New Roman', serif;
            font-weight: 400;
            letter-spacing: -0.02em;
        }

        body {
            background-color: #1a1a1a;
            color: #e8e8e8;
        }

        /* Menggunakan style 'filter-input' dari layout utama agar konsisten */
        .claude-input {
            background-color: rgba(45, 45, 45, 0.6);
            border: 1px solid rgba(58, 58, 58, 0.8);
            color: #e8e8e8;
            padding: 0.75rem 1rem;
            border-radius: 10px;
            width: 100%;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-size: 0.875rem;
        }

        .claude-input:focus {
            outline: none;
            border-color: #d97757;
            background-color: rgba(45, 45, 45, 0.9);
            box-shadow: 0 0 0 3px rgba(217, 119, 87, 0.2);
        }
        
        /* Menggunakan style 'filter-label' dari layout utama */
        .claude-label {
            display: block;
            color: #c4c4c4;
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        /* Menggunakan style 'filter-btn' dari layout utama */
        .claude-button {
            padding: 0.75rem 1.25rem;
            border-radius: 10px;
            font-weight: 500;
            font-size: 0.875rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            white-space: nowrap;
            background: linear-gradient(135deg, #d97757 0%, #e88968 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(217, 119, 87, 0.3);
        }

        .claude-button:hover {
            background: linear-gradient(135deg, #e88968 0%, #f09a7a 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(217, 119, 87, 0.4);
        }

        .claude-button-secondary {
            padding: 0.75rem 1.25rem;
            border-radius: 10px;
            font-weight: 500;
            font-size: 0.875rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            white-space: nowrap;
            background-color: rgba(156, 163, 175, 0.2);
            color: #9ca3af;
            border: 1px solid rgba(156, 163, 175, 0.3);
        }

        .claude-button-secondary:hover {
            background-color: rgba(156, 163, 175, 0.3);
            transform: translateY(-2px);
        }

        .preview-image {
            max-width: 200px;
            border-radius: 12px;
            margin-top: 1rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            border: 1px solid #3a3a3a;
        }

        .social-input-wrapper {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .social-icon {
            font-size: 1.5rem;
            min-width: 24px;
            text-align: center;
        }
    </style>
</head>

{{-- Menggunakan <x-main-layout> --}}
<x-main-layout>
    <div class="claude-container">
        
        {{-- Header Section --}}
        <div class="border-b border-[#3a3a3a] header-section">
            <div class="max-w-7xl mx-auto px-6 py-4">
                <div class="flex items-center gap-4">
                    <a href="{{ route('magang.index') }}" class="text-gray-400 hover:text-white" title="Kembali">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <h2 class="claude-title text-2xl text-white">
                        Tambah Data Magang
                    </h2>
                </div>
            </div>
        </div>

        {{-- Konten Utama: Form --}}
        <div class="max-w-7xl mx-auto py-8 px-6">

            <div class="bg-[#2a2a2a]/60 backdrop-blur-md border border-[#3a3a3a] rounded-xl shadow-lg overflow-hidden">
                <form action="{{ route('magang.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="p-6 md:p-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">

                            {{-- Kolom Kiri Form --}}
                            <div class="space-y-6">
                                <div>
                                    <label for="nama" class="claude-label">Nama Lengkap</label>
                                    <input type="text" name="nama" id="nama" value="{{ old('nama') }}"
                                        class="claude-input @error('nama') border-red-500 @enderror" required>
                                    @error('nama')
                                        <p class="text-red-400 text-xs mt-2">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="asal_kampus" class="claude-label">Asal Kampus</label>
                                    <input type="text" name="asal_kampus" id="asal_kampus" value="{{ old('asal_kampus') }}"
                                        class="claude-input @error('asal_kampus') border-red-500 @enderror" required>
                                    @error('asal_kampus')
                                        <p class="text-red-400 text-xs mt-2">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- ✅ INPUT BARU: Prodi/Jurusan --}}
                                <div>
                                    <label for="prodi" class="claude-label">Prodi / Jurusan</label>
                                    <input type="text" name="prodi" id="prodi" class="claude-input" 
                                        placeholder="Contoh: Informatika" value="{{ old('prodi') }}" required>
                                    @error('prodi') <span class="text-red-400 text-sm mt-1">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label for="tanggal_mulai" class="claude-label">Tanggal Mulai</label>
                                    <input type="date" name="tanggal_mulai" id="tanggal_mulai" value="{{ old('tanggal_mulai') }}"
                                        class="claude-input @error('tanggal_mulai') border-red-500 @enderror" required style="color-scheme: dark;">
                                    @error('tanggal_mulai')
                                        <p class="text-red-400 text-xs mt-2">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="tanggal_selesai" class="claude-label">Tanggal Selesai</label>
                                    <input type="date" name="tanggal_selesai" id="tanggal_selesai"
                                        value="{{ old('tanggal_selesai') }}"
                                        class="claude-input @error('tanggal_selesai') border-red-500 @enderror" required style="color-scheme: dark;">
                                    @error('tanggal_selesai')
                                        <p class="text-red-400 text-xs mt-2">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="link_pekerjaan" class="claude-label">Link Karya (Opsional)</label>
                                    <textarea name="link_pekerjaan" id="link_pekerjaan" rows="3"
                                        class="claude-input @error('link_pekerjaan') border-red-500 @enderror"
                                        placeholder="Contoh: https://drive.google.com/...">{{ old('link_pekerjaan') }}</textarea>
                                    <p class="text-gray-400 text-xs mt-2">Masukkan link Google Drive, GitHub, atau portfolio online</p>
                                    @error('link_pekerjaan')
                                        <p class="text-red-400 text-xs mt-2">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Kolom Kanan Form --}}
                            <div class="space-y-6">
                                <div>
                                    <label for="foto" class="claude-label">Foto Profil</label>
                                    <input type="file" name="foto" id="foto" accept="image/jpeg,image/png,image/jpg"
                                        class="claude-input file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-[#3a3a3a] file:text-gray-300 hover:file:bg-[#4a4a4a] @error('foto') border-red-500 @enderror" onchange="previewImage(event)"
                                        required>
                                    <p class="text-gray-400 text-xs mt-2">Format: JPG, PNG | Maksimal 2MB</p>
                                    <img id="preview"
                                        class="preview-image"
                                        style="display: none;"
                                        alt="Preview">
                                    @error('foto')
                                        <p class="text-red-400 text-xs mt-2">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Media Sosial --}}
                                <div class="border-t border-[#3a3a3a] pt-6 space-y-6">
                                    <h3 class="claude-title text-xl text-white">
                                        <i class="fas fa-share-alt mr-2"></i>Media Sosial (Opsional)
                                    </h3>
                                    <div>
                                        <label for="whatsapp" class="claude-label">WhatsApp</label>
                                        <div class="social-input-wrapper">
                                            <i class="fab fa-whatsapp social-icon text-green-500"></i>
                                            <input type="text" name="whatsapp" id="whatsapp" value="{{ old('whatsapp') }}"
                                                placeholder="081234567890"
                                                class="claude-input @error('whatsapp') border-red-500 @enderror">
                                        </div>
                                        <p class="text-gray-400 text-xs mt-2 ml-9">Masukkan nomor dengan tanda + dan tanpa spasi</p>
                                        @error('whatsapp')
                                            <p class="text-red-400 text-xs mt-2 ml-9">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="instagram" class="claude-label">Instagram</label>
                                        <div class="social-input-wrapper">
                                            <i class="fab fa-instagram social-icon text-pink-500"></i>
                                            <input type="text" name="instagram" id="instagram" value="{{ old('instagram') }}"
                                                placeholder="@username"
                                                class="claude-input @error('instagram') border-red-500 @enderror">
                                        </div>
                                        @error('instagram')
                                            <p class="text-red-400 text-xs mt-2 ml-9">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="tiktok" class="claude-label">TikTok</label>
                                        <div class="social-input-wrapper">
                                            <i class="fab fa-tiktok social-icon text-blue-400"></i>
                                            <input type="text" name="tiktok" id="tiktok" value="{{ old('tiktok') }}"
                                                placeholder="@username"
                                                class="claude-input @error('tiktok') border-red-500 @enderror">
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
                                <label for="kesan" class="claude-label">Kesan Selama Magang</label>
                                <textarea name="kesan" id="kesan" rows="5"
                                    class="claude-input @error('kesan') border-red-500 @enderror"
                                    placeholder="Tulis kesan Anda selama mengikuti program magang di BPS Bantul...">{{ old('kesan') }}</textarea>
                                <p class="text-gray-400 text-xs mt-2">Maksimal 2000 karakter</p>
                                @error('kesan')
                                    <p class="text-red-400 text-xs mt-2">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="mb-6">
                                <label for="pesan" class="claude-label">Pesan & Saran</label>
                                <textarea name="pesan" id="pesan" rows="5"
                                    class="claude-input @error('pesan') border-red-500 @enderror"
                                    placeholder="Tulis pesan atau saran untuk BPS Bantul...">{{ old('pesan') }}</textarea>
                                <p class="text-gray-400 text-xs mt-2">Maksimal 2000 karakter</p>
                                @error('pesan')
                                    <p class="text-red-400 text-xs mt-2">{{ $message }}</p>
                                @enderror
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
                        <div class="flex gap-3 justify-end pt-4 border-t border-[#3a3a3a]">
                            <a href="{{ route('magang.index') }}" class="claude-button-secondary">
                                <i class="fas fa-times mr-2"></i>Batal
                            </a>
                            <button type="submit" class="claude-button">
                                <i class="fas fa-save mr-2"></i>Simpan Data
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    @push('scripts')
    <script>
        // Saya memindahkan script ke @push('scripts') agar dimuat di akhir <body>
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
    @endpush
</x-main-layout>
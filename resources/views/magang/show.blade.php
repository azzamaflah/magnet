{{-- resources/views/magang/show.blade.php --}}
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Data Magang - BPS Bantul</title>
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

        .detail-card {
            background-color: #2a2a2a;
            border: 1px solid #3a3a3a;
            border-radius: 16px;
            padding: 2rem;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .status-badge.aktif {
            background-color: rgba(34, 197, 94, 0.2);
            color: #4ade80;
        }

        .status-badge.selesai {
            background-color: rgba(156, 163, 175, 0.2);
            color: #9ca3af;
        }

        .status-badge.belum {
            background-color: rgba(251, 191, 36, 0.2);
            color: #fbbf24;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }

        .status-badge.aktif .status-dot {
            background-color: #4ade80;
            box-shadow: 0 0 8px #4ade80;
        }

        .status-badge.selesai .status-dot {
            background-color: #9ca3af;
        }

        .status-badge.belum .status-dot {
            background-color: #fbbf24;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(1, 1fr);
            gap: 1.5rem;
            margin: 2rem 0;
        }

        @media (min-width: 768px) {
            .info-grid {
                /* ✅ DIUBAH AGAR BISA 3 KOLOM */
                grid-template-columns: repeat(3, 1fr);
            }
        }
        
        /* ✅ Style agar item bisa span 2 kolom */
        @media (min-width: 1024px) {
            .lg\:col-span-2 {
                grid-column: span 2 / span 2;
            }
        }


        .info-item {
            background-color: #242424;
            padding: 1.25rem;
            border-radius: 12px;
            border: 1px solid #333;
        }

        .info-label {
            color: #9ca3af;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .info-value {
            color: white;
            font-size: 1.125rem;
            font-weight: 500;
        }

        .claude-button {
            background-color: #d97757;
            color: #ffffff;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .claude-button:hover {
            background-color: #e88968;
            transform: translateY(-1px);
        }

        .claude-button-secondary {
            background-color: #3a3a3a;
            color: #e8e8e8;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .claude-button-secondary:hover {
            background-color: #4a4a4a;
        }

        .claude-button-danger {
            background-color: #dc2626;
            color: #ffffff;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .claude-button-danger:hover {
            background-color: #b91c1c;
        }

        .initial-avatar {
            width: 250px;
            height: 250px;
            border-radius: 50%;
            background: linear-gradient(135deg, #d97757 0%, #e88968 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 6rem;
            font-weight: 700;
            color: white;
            margin: 0 auto;
            border: 4px solid #3a3a3a;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
        }

        .social-media-section {
            background-color: #242424;
            padding: 1.5rem;
            border-radius: 12px;
            border: 1px solid #333;
            margin-top: 2rem;
        }

        .social-links {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-top: 1rem;
        }

        .social-link {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.875rem 1.25rem;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s;
            font-size: 0.95rem;
        }

        .social-link:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        .social-link i {
            font-size: 1.5rem;
        }

        .social-whatsapp {
            background: rgba(37, 211, 102, 0.15);
            color: #25d366;
            border: 1px solid rgba(37, 211, 102, 0.3);
        }

        .social-whatsapp:hover {
            background: rgba(37, 211, 102, 0.25);
        }

        .social-instagram {
            background: rgba(225, 48, 108, 0.15);
            color: #e1306c;
            border: 1px solid rgba(225, 48, 108, 0.3);
        }

        .social-instagram:hover {
            background: rgba(225, 48, 108, 0.25);
        }

        .social-tiktok {
            background: rgba(0, 242, 234, 0.15);
            color: #00f2ea;
            border: 1px solid rgba(0, 242, 234, 0.3);
        }

        .social-tiktok:hover {
            background: rgba(0, 242, 234, 0.25);
        }

        .karya-section {
            background-color: #242424;
            padding: 1.5rem;
            border-radius: 12px;
            border: 1px solid #333;
            margin-top: 2rem;
        }

        .karya-link {
            color: #60a5fa;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 0.5rem;
            font-weight: 500;
        }

        .karya-link:hover {
            color: #93c5fd;
        }

        .success-alert {
            background-color: rgba(34, 197, 94, 0.1);
            border: 1px solid rgba(34, 197, 94, 0.3);
            color: #4ade80;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }

        .kesan-pesan-section {
            background-color: #242424;
            padding: 1.5rem;
            border-radius: 12px;
            border: 1px solid #333;
            margin-top: 2rem;
        }

        .kesan-pesan-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
            margin-top: 1rem;
        }

        @media (min-width: 768px) {
            .kesan-pesan-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        .kesan-pesan-item {
            background-color: #1f1f1f;
            border: 1px solid #333;
            border-radius: 10px;
            padding: 1.25rem;
        }

        .kesan-pesan-label {
            color: #9ca3af;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 600;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .kesan-pesan-content {
            color: #e8e8e8;
            line-height: 1.7;
            font-size: 0.95rem;
        }

        .kesan-pesan-empty {
            color: #7a7a7a;
            font-style: italic;
        }
    </style>
</head>

{{-- Menggunakan <x-main-layout> --}}
<x-main-layout>
    <div class="claude-container">
        <div class="min-h-screen py-8 px-4">
            <div class="max-w-7xl mx-auto">
                <div class="mb-8">
                    <a href="{{ route('magang.index') }}"
                        class="text-[#d97757] hover:text-[#e88968] inline-flex items-center gap-2 mb-4">
                        <i class="fas fa-arrow-left"></i>
                        <span>Kembali ke Data Magang</span>
                    </a>
                    <h1 class="claude-title text-3xl text-white">Detail Data Magang</h1>
                </div>

                @if (session('success'))
                    <div class="success-alert">
                        <i class="fas fa-check-circle mr-2"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                <div class="detail-card">
                    <div class="text-center mb-8">
                        @if($magang->foto)
                            <img src="{{ asset('storage/' . $magang->foto) }}" alt="{{ $magang->nama }}"
                                style="width: 250px; height: 250px; object-fit: cover; border-radius: 50%; margin: 0 auto; border: 4px solid #3a3a3a; box-shadow: 0 8px 24px rgba(0,0,0,0.3);">
                        @else
                            <div class="initial-avatar"
                                style="background: linear-gradient(135deg, #{{ substr(md5($magang->nama), 0, 6) }} 0%, #{{ substr(md5($magang->nama), 6, 6) }} 100%);">
                                {{ $magang->initials }}
                            </div>
                        @endif
                    </div>

                    <div class="text-center mb-6">
                        <h2 class="text-3xl font-bold text-white mb-3">{{ $magang->nama }}</h2>
                        @php
                            $now = \Carbon\Carbon::now();
                            $mulai = \Carbon\Carbon::parse($magang->tanggal_mulai);
                            $selesai = \Carbon\Carbon::parse($magang->tanggal_selesai);

                            if ($now->lt($mulai)) {
                                $statusClass = 'belum';
                                $statusText = 'Belum Mulai';
                            } elseif ($now->between($mulai, $selesai)) {
                                $statusClass = 'aktif';
                                $statusText = 'Sedang Magang';
                            } else {
                                $statusClass = 'selesai';
                                $statusText = 'Selesai';
                            }
                        @endphp
                        <div class="status-badge {{ $statusClass }}">
                            <span class="status-dot"></span>
                            {{ $statusText }}
                        </div>
                    </div>

                    {{-- ✅ GRID INFO DIPERBARUI --}}
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">
                                <i class="fas fa-university mr-1"></i> Asal Kampus
                            </div>
                            <div class="info-value">{{ $magang->asal_kampus }}</div>
                        </div>

                        {{-- ✅ PRODI DITAMBAHKAN DI SINI --}}
                        <div class="info-item">
                            <div class="info-label">
                                <i class="fas fa-book-open mr-1"></i> Prodi / Jurusan
                            </div>
                            <div class="info-value">{{ $magang->prodi ?? '-' }}</div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">
                                <i class="fas fa-calendar-alt mr-1"></i> Periode Magang
                            </div>
                            <div class="info-value">
                                {{ $magang->periode_bulan }} Bulan
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">
                                <i class="fas fa-play-circle mr-1"></i> Tanggal Mulai
                            </div>
                            <div class="info-value">{{ $mulai->isoFormat('D MMMM YYYY') }}</div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">
                                <i class="fas fa-stop-circle mr-1"></i> Tanggal Selesai
                            </div>
                            <div class="info-value">{{ $selesai->isoFormat('D MMMM YYYY') }}</div>
                        </div>
                    </div>
                    {{-- AKHIR GRID INFO --}}


                    @if($magang->whatsapp || $magang->instagram || $magang->tiktok)
                        <div class="social-media-section">
                            <h3 class="text-white font-semibold text-lg mb-3">
                                <i class="fas fa-share-alt mr-2"></i>Media Sosial
                            </h3>
                            <div class="social-links">
                                @if($magang->whatsapp && auth()->check() && auth()->user()->role == 'admin')
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $magang->whatsapp) }}" target="_blank"
                                        class="social-link social-whatsapp">
                                        <i class="fab fa-whatsapp"></i>
                                        <span>WhatsApp</span>
                                    </a>
                                @endif
                                @if($magang->instagram)
                                    <a href="https://instagram.com/{{ ltrim($magang->instagram, '@') }}" target="_blank"
                                        class="social-link social-instagram">
                                        <i class="fab fa-instagram"></i>
                                        <span>{{ $magang->instagram }}</span>
                                    </a>
                                @endif
                                @if($magang->tiktok)
                                    <a href="https://tiktok.com/@{{ ltrim($magang->tiktok, '@') }}" target="_blank"
                                        class="social-link social-tiktok">
                                        <i class="fab fa-tiktok"></i>
                                        <span>{{ $magang->tiktok }}</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif

                    <div class="kesan-pesan-section">
                        <h3 class="text-white font-semibold text-lg mb-1">
                            <i class="fas fa-comment-dots mr-2"></i>Kesan & Pesan
                        </h3>
                        <div class="kesan-pesan-grid">
                            <div class="kesan-pesan-item">
                                <div class="kesan-pesan-label">
                                    <i class="fas fa-quote-left"></i>
                                    <span>Kesan</span>
                                </div>
                                <div class="kesan-pesan-content">
                                    @if($magang->kesan)
                                        {!! nl2br(e($magang->kesan)) !!}
                                    @else
                                        <span class="kesan-pesan-empty">Belum ada kesan</span>
                                    @endif
                                </div>
                            </div>

                            <div class="kesan-pesan-item">
                                <div class="kesan-pesan-label">
                                    <i class="fas fa-paper-plane"></i>
                                    <span>Pesan</span>
                                </div>
                                <div class="kesan-pesan-content">
                                    @if($magang->pesan)
                                        {!! nl2br(e($magang->pesan)) !!}
                                    @else
                                        <span class="kesan-pesan-empty">Belum ada pesan</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($magang->link_pekerjaan)
                        <div class="karya-section">
                            <h3 class="text-white font-semibold text-lg mb-2">
                                <i class="fas fa-briefcase mr-2"></i>Karya/Pekerjaan
                            </h3>
                            <a href="{{ $magang->link_pekerjaan }}" target="_blank" class="karya-link">
                                <i class="fas fa-external-link-alt"></i>
                                <span>Lihat Karya</span>
                            </a>
                        </div>
                    @endif

                    <div class="mt-6 pt-6 border-t border-[#3a3a3a]">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-[#7a7a7a]">
                            <div>
                                <i class="fas fa-clock mr-2"></i>Dibuat:
                                {{ $magang->created_at->isoFormat('D MMM YYYY, HH:mm') }}
                            </div>
                            <div>
                                <i class="fas fa-sync-alt mr-2"></i>Diupdate:
                                {{ $magang->updated_at->isoFormat('D MMM YYYY, HH:mm') }}
                            </div>
                        </div>
                    </div>

                    <div
                        class="flex flex-col md:flex-row gap-3 justify-between items-center mt-8 pt-6 border-t border-[#3a3a3a]">
                        <a href="{{ route('magang.index') }}" class="claude-button-secondary w-full md:w-auto">
                            <i class="fas fa-arrow-left"></i>
                            <span>Kembali</span>
                        </a>

                        @if(auth()->user()->isAdmin() || (isset($magang->user_id) && $magang->user_id == auth()->id()))
                            <div class="flex flex-col md:flex-row gap-3 w-full md:w-auto">
                                <a href="{{ route('magang.edit', $magang) }}"
                                    class="claude-button w-full md:w-auto justify-center">
                                    <i class="fas fa-edit"></i>
                                    <span>Edit Data</span>
                                </a>
                                
                                @if(auth()->user()->isAdmin())
                                <form action="{{ route('magang.destroy', $magang) }}" method="POST"
                                    class="inline-block w-full md:w-auto"
                                    onsubmit="return confirm('Yakin ingin menghapus data {{ $magang->nama }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="claude-button-danger w-full justify-center">
                                        <i class="fas fa-trash"></i>
                                        <span>Hapus</span>
                                    </button>
                                </form>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-main-layout>
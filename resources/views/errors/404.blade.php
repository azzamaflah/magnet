<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 - Tidak Ditemukan | MagNet</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Fraunces:wght@400;500;600&display=swap');

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #1a1a1a 0%, #2d1810 100%);
            color: #e8e8e8;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            position: relative;
        }

        .claude-title {
            font-family: 'Fraunces', serif;
        }

        /* Background Orbs Animation */
        .bg-orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(100px);
            opacity: 0.15;
            animation: float 20s ease-in-out infinite;
            z-index: 0;
        }
        .orb-1 { width: 400px; height: 400px; background: #d97757; top: -10%; left: -10%; }
        .orb-2 { width: 300px; height: 300px; background: #d97757; bottom: -10%; right: -10%; animation-delay: 5s; }

        @keyframes float {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(30px, -30px); }
        }

        /* Card Style */
        .error-card {
            background-color: rgba(42, 42, 42, 0.6);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(217, 119, 87, 0.2);
            border-radius: 24px;
            padding: 3rem;
            text-align: center;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            animation: fadeInUp 0.8s ease-out;
            z-index: 10;
            max-width: 500px;
            width: 90%;
            margin: auto;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Button Style */
        .claude-button {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, #d97757 0%, #e88968 100%);
            color: white;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            box-shadow: 0 4px 15px rgba(217, 119, 87, 0.3);
        }
        .claude-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(217, 119, 87, 0.5);
        }
        .btn-secondary {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: none;
        }
        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.2);
        }

        /* Icon Animation */
        .ghost-icon {
            animation: bounce 3s infinite ease-in-out;
            filter: drop-shadow(0 0 20px rgba(217, 119, 87, 0.4));
        }
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-15px); }
        }
    </style>
</head>
<body>

    {{-- Background Elements --}}
    <div class="bg-orb orb-1"></div>
    <div class="bg-orb orb-2"></div>

    {{-- Main Content --}}
    <div class="flex-1 flex items-center justify-center p-4">
        <div class="error-card">
            
            <div class="mb-6">
                <i class="fas fa-ghost text-8xl text-[#d97757] ghost-icon"></i>
            </div>

            <h1 class="text-6xl font-bold text-white mb-2 tracking-wider claude-title">
                4<span class="text-[#d97757]">0</span>4
            </h1>

            <h2 class="text-xl font-medium text-gray-200 mb-4">
                Halaman Tidak Ditemukan
            </h2>

            <p class="text-gray-400 mb-8 text-sm leading-relaxed">
                Oops! Halaman yang Anda cari sepertinya tersesat di antah berantah. Mungkin link-nya salah atau sudah dihapus.
            </p>

            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="javascript:history.back()" class="claude-button btn-secondary justify-center">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali</span>
                </a>
                
                <a href="{{ url('/') }}" class="claude-button justify-center">
                    <i class="fas fa-home"></i>
                    <span>Ke Beranda</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Footer Simple --}}
    <div class="text-center py-6 text-gray-600 text-xs relative z-10">
        &copy; {{ date('Y') }} MagNet - BPS Bantul
    </div>

</body>
</html>
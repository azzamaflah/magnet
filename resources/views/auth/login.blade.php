<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - BPS Bantul</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Fraunces:wght@400;500;600&display=swap');

        * {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }

        .claude-title {
            font-family: 'Fraunces', 'Georgia', 'Times New Roman', serif;
            font-weight: 400;
            letter-spacing: -0.02em;
        }

        body {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d1810 100%);
            color: #e8e8e8;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 1rem;
            position: relative;
            overflow-y: auto;
            flex-direction: column;
        }

        /* Animated Background Orbs */
        .bg-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.15;
            animation: float 20s ease-in-out infinite;
        }

        .orb-1 {
            width: 400px;
            height: 400px;
            background: #d97757;
            top: -10%;
            left: -10%;
            animation-delay: 0s;
        }

        .orb-2 {
            width: 350px;
            height: 350px;
            background: #d97757;
            bottom: -10%;
            right: -10%;
            animation-delay: 7s;
        }

        .orb-3 {
            width: 300px;
            height: 300px;
            background: #d97757;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            animation-delay: 14s;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(30px, -30px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
        }

        /* Form Card with Animation */
        .form-card {
            background-color: rgba(42, 42, 42, 0.8);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(217, 119, 87, 0.2);
            border-radius: 24px;
            padding: 2.5rem;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.6);
            position: relative;
            z-index: 10;
            animation: slideUp 0.8s ease-out;
            margin: 0 auto;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Logo Animation */
        .logo-container {
            animation: fadeIn 1s ease-out 0.3s both;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }

        /* Input Styles */
        .claude-input {
            background-color: rgba(45, 45, 45, 0.6);
            border: 1px solid rgba(58, 58, 58, 0.8);
            color: #e8e8e8;
            padding: 0.875rem 1rem;
            border-radius: 12px;
            width: 100%;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .claude-input:focus {
            outline: none;
            border-color: #d97757;
            background-color: rgba(45, 45, 45, 0.9);
            box-shadow: 0 0 0 3px rgba(217, 119, 87, 0.3);
            transform: translateY(-2px);
        }

        .claude-input::placeholder { color: #6a6a6a; }

        .input-group { animation: slideInLeft 0.6s ease-out both; }
        .input-group:nth-child(1) { animation-delay: 0.4s; }
        .input-group:nth-child(2) { animation-delay: 0.5s; }

        @keyframes slideInLeft {
            from { opacity: 0; transform: translateX(-20px); }
            to { opacity: 1; transform: translateX(0); }
        }

        .claude-label {
            display: block;
            color: #c4c4c4;
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        /* Button Styles */
        .claude-button {
            background: linear-gradient(135deg, #d97757 0%, #e88968 100%);
            color: #ffffff;
            padding: 0.875rem 1.75rem;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
            cursor: pointer;
            display: flex; /* Menggunakan Flexbox untuk centering */
            justify-content: center; /* Center Horizontal */
            align-items: center;    /* Center Vertikal */
            gap: 0.5rem;
            box-shadow: 0 4px 15px rgba(217, 119, 87, 0.3);
            width: 100%; /* Tombol Login Full Width agar rapi */
            font-size: 1rem; /* Ukuran font sedikit diperbesar */
        }

        .claude-button:hover {
            background: linear-gradient(135deg, #e88968 0%, #f09a7a 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(217, 119, 87, 0.4);
        }

        .claude-button:active {
            transform: translateY(0);
        }

        .button-container {
            animation: slideInRight 0.6s ease-out 0.6s both;
            margin-top: 2rem; /* Tambah jarak karena checkbox hilang */
        }

        @keyframes slideInRight {
            from { opacity: 0; transform: translateX(20px); }
            to { opacity: 1; transform: translateX(0); }
        }

        /* Error Message */
        .error-message {
            color: #f87171;
            font-size: 0.875rem;
            margin-top: 0.5rem;
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }

        /* Responsive Design */
        @media (max-width: 640px) {
            .form-card {
                padding: 2rem 1.5rem;
                max-width: 100%;
                margin: 1rem;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            }
            .claude-title { font-size: 1.5rem; }
        }

        @media (max-width: 400px) {
            .form-card { padding: 1.5rem 1rem; }
            .claude-input { padding: 0.75rem 0.875rem; font-size: 0.875rem; }
            .bg-orb { display: none; }
        }

        /* Icon Animation */
        .claude-button i { transition: transform 0.3s; }
        .claude-button:hover i { transform: translateX(3px); }

        /* Logo Styling */
        .logo-image {
            max-width: 80px;
            height: auto;
            margin: 0 auto 1rem;
            display: block;
        }
    </style>
</head>

<body>
    <div class="bg-orb orb-1"></div>
    <div class="bg-orb orb-2"></div>
    <div class="bg-orb orb-3"></div>

    <div class="form-card">
        <div class="text-center mb-8 logo-container">
            <img src="/images/Magnet.png" alt="Logo BPS Bantul" class="logo-image">
            <h1 class="claude-title text-2xl text-white mb-2">MagNet</h1>
            <p class="text-sm text-[#9ca3af]">Magang Network</p>
            <p class="text-sm text-[#9ca3af]">Aplikasi Monitoring dan Pendaftaran Magang</p>
        </div>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-5 input-group">
                <label for="email" class="claude-label">Email</label>
                <input id="email" class="claude-input" type="email" name="email" value="{{ old('email') }}" required
                    autofocus autocomplete="username" placeholder="admin@bps.go.id" />
                @error('email')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-5 input-group">
                <label for="password" class="claude-label">Password</label>
                <input id="password" class="claude-input" type="password" name="password" required
                    autocomplete="current-password" placeholder="••••••••" />
                @error('password')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            {{-- BAGIAN HILANG: CHECKBOX INGAT SAYA --}}

            {{-- BUTTON LOG IN FULL WIDTH --}}
            <div class="button-container">
                <button type="submit" class="claude-button">
                    Log in
                    <i class="fas fa-arrow-right ml-2"></i>
                </button>
            </div>
        </form>

        <div class="relative my-6" style="animation: fadeIn 0.6s ease-out 0.8s both;">
            <div class="absolute inset-0 flex items-center" aria-hidden="true">
                <div class="w-full border-t border-gray-700"></div>
            </div>
            <div class="relative flex justify-center text-sm">
                <span class="px-2 bg-[#2a2a2a] text-gray-400">Pendaftar Harus Login Dengan Google</span>
            </div>
        </div>

        <div style="animation: slideInRight 0.6s ease-out 0.9s both;">
            <a href="{{ route('google.redirect') }}" 
                class="claude-button w-full flex justify-center items-center" 
                style="background: #ffffff; color: #1f2937; text-decoration: none; box-shadow: 0 4px 15px rgba(255, 255, 255, 0.1);">
                <i class="fab fa-google mr-2"></i> 
                <span>Login dengan Google</span>
            </a>
        </div>
        
    </div>
</body>

</html>
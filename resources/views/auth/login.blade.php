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

        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }

        html, body {
            width: 100%;
            min-height: 100vh;
            min-height: 100dvh;
            overflow-x: hidden;
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
            min-height: 100dvh;
            padding: 2rem 1rem;
            position: relative;
        }

        /* Background Orbs Container & Animation */
        .orb-wrapper {
            position: fixed;
            inset: 0;
            overflow: hidden;
            pointer-events: none;
            z-index: 0;
        }

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
            max-width: 440px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.6);
            position: relative;
            z-index: 10;
            animation: slideUp 0.8s ease-out;
            margin: auto;
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
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
            box-shadow: 0 4px 15px rgba(217, 119, 87, 0.3);
            width: 100%;
            font-size: 1rem;
        }

        .claude-button:hover {
            background: linear-gradient(135deg, #e88968 0%, #f09a7a 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(217, 119, 87, 0.4);
        }

        .claude-button:active {
            transform: translateY(0);
        }

        /* Google Button Style */
        .google-button {
            background: #ffffff;
            color: #1f2937;
            padding: 0.875rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.95rem;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            width: 100%;
        }

        .google-button:hover {
            background: #f8fafc;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 255, 255, 0.15);
            color: #111827;
        }

        .google-button:active {
            transform: translateY(0);
        }

        .button-container {
            animation: slideInRight 0.6s ease-out 0.6s both;
            margin-top: 2rem;
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
                margin: auto;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            }
            .claude-title { font-size: 1.5rem; }
        }

        @media (max-width: 400px) {
            .form-card { padding: 1.5rem 1rem; }
            .claude-input { padding: 0.75rem 0.875rem; font-size: 0.875rem; }
        }

        /* Button Arrow Transition */
        .claude-button svg, .claude-button i {
            transition: transform 0.3s ease;
        }
        .claude-button:hover svg, .claude-button:hover i {
            transform: translateX(4px);
        }

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
    <div class="orb-wrapper">
        <div class="bg-orb orb-1"></div>
        <div class="bg-orb orb-2"></div>
        <div class="bg-orb orb-3"></div>
    </div>

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
                    <span>Log in</span>
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
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
            <a href="{{ route('google.redirect') }}" class="google-button">
                <svg class="google-icon" viewBox="0 0 24 24" width="20" height="20">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" />
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" />
                </svg>
                <span>Login dengan Google</span>
            </a>
        </div>
        
    </div>
</body>

</html>
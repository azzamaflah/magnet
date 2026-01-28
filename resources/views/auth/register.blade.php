<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - BPS Bantul</title>
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
            overflow: hidden;
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

            0%,
            100% {
                transform: translate(0, 0) scale(1);
            }

            33% {
                transform: translate(30px, -30px) scale(1.1);
            }

            66% {
                transform: translate(-20px, 20px) scale(0.9);
            }
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
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Logo Animation */
        .logo-container {
            animation: fadeIn 1s ease-out 0.3s both;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
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

        .claude-input::placeholder {
            color: #6a6a6a;
        }

        .input-group {
            animation: slideInLeft 0.6s ease-out both;
        }

        .input-group:nth-child(1) {
            animation-delay: 0.4s;
        }

        .input-group:nth-child(2) {
            animation-delay: 0.5s;
        }
        
        .input-group:nth-child(3) {
            animation-delay: 0.6s;
        }

        .input-group:nth-child(4) {
            animation-delay: 0.7s;
        }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
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
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: 0 4px 15px rgba(217, 119, 87, 0.3);
            width: 100%; /* Tombol Penuh */
            justify-content: center; /* Teks di tengah */
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
            animation: slideInRight 0.6s ease-out 0.8s both;
            width: 100%;
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(20px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Error Message */
        .error-message {
            color: #f87171;
            font-size: 0.875rem;
            margin-top: 0.5rem;
            /* Animasi shake bisa ditambahkan jika diperlukan */
        }

        /* Link Styles */
        .link-login {
            color: #9ca3af;
            text-decoration: none;
            transition: all 0.3s;
            font-size: 0.875rem;
            text-align: center;
            display: block;
            margin-top: 1.5rem;
            animation: fadeIn 0.6s ease-out 0.9s both;
        }

        .link-login:hover {
            color: #d97757;
        }

        .link-login span {
            color: #d97757;
            font-weight: 600;
        }

        /* Responsive Design */
        @media (max-width: 640px) {
            .form-card {
                padding: 2rem 1.5rem;
                max-width: 100%;
                margin: 1rem;
            }

            .claude-title {
                font-size: 1.5rem;
            }
        }

        @media (max-width: 400px) {
            .form-card {
                padding: 1.5rem 1rem;
            }

            .claude-input {
                padding: 0.75rem 0.875rem;
                font-size: 0.875rem;
            }

            .bg-orb {
                display: none;
            }
        }

        /* Icon Animation */
        .claude-button i {
            transition: transform 0.3s;
        }

        .claude-button:hover i {
            transform: translateX(3px);
        }
    </style>
</head>

<body>
    <!-- Animated Background Orbs -->
    <div class="bg-orb orb-1"></div>
    <div class="bg-orb orb-2"></div>
    <div class="bg-orb orb-3"></div>

    <div class="form-card">
        <div class="text-center mb-8 logo-container">
            <h1 class="claude-title text-2xl text-white mb-2">Buat Akun Baru</h1>
            <p class="text-sm text-[#9ca3af]">Data Magang BPS Bantul</p>
        </div>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Name -->
            <div class="mb-5 input-group">
                <label for="name" class="claude-label">Nama Lengkap</label>
                <input id="name" class="claude-input" type="text" name="name" value="{{ old('name') }}" required
                    autofocus autocomplete="name" placeholder="Nama Anda" />
                @error('name')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email Address -->
            <div class="mb-5 input-group">
                <label for="email" class="claude-label">Email</label>
                <input id="email" class="claude-input" type="email" name="email" value="{{ old('email') }}" required
                    autocomplete="username" placeholder="email@anda.com" />
                @error('email')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div class="mb-5 input-group">
                <label for="password" class="claude-label">Password</label>
                <input id="password" class="claude-input" type="password" name="password" required
                    autocomplete="new-password" placeholder="••••••••" />
                @error('password')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div class="mb-5 input-group">
                <label for="password_confirmation" class="claude-label">Konfirmasi Password</label>
                <input id="password_confirmation" class="claude-input" type="password" name="password_confirmation"
                    required autocomplete="new-password" placeholder="••••••••" />
                @error('password_confirmation')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>


            <div class="mt-8 button-container">
                <button type="submit" class="claude-button">
                    Daftar
                    <i class="fas fa-arrow-right"></i>
                </button>
            </div>

            <a href="{{ route('login') }}" class="link-login">
                Sudah punya akun? <span>Login di sini</span>
            </a>
        </form>
    </div>
</body>

</html>
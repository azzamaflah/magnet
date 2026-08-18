{{-- resources/views/layouts/main.blade.php --}}
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MAGNET</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/png" href="{{ asset('images/Magnet.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script>
        if (localStorage.getItem('_x_expanded') === 'false') {
            document.documentElement.classList.add('sidebar-collapsed-on-load');
        }
    </script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Fraunces:wght@400;500;600&display=swap');

        @media (min-width: 1024px) {
            html.sidebar-collapsed-on-load aside {
                width: 5rem !important; /* w-20 */
            }
            html.sidebar-collapsed-on-load main {
                padding-left: 5rem !important; /* pl-20 */
            }
        }

        [x-cloak] {
            display: none !important;
        }

        html {
            background-color: #1a1a1a;
            overscroll-behavior: none;
            /* <-- Tambahkan baris ini */
        }

        html.sidebar-collapsed-on-load aside {
            width: 5rem !important; /* w-20 */
        }

        html.sidebar-collapsed-on-load main {
            padding-left: 5rem !important; /* pl-20 */
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
            background: linear-gradient(135deg, #1a1a1a 0%, #2d1810 100%);
            color: #e8e8e8;
            position: relative;
            overflow-x: hidden;
        }

        /* Animated Background Orbs */
        .bg-orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(100px);
            opacity: 0.1;
            animation: float 25s ease-in-out infinite;
            pointer-events: none;
            z-index: 0;
        }

        .orb-1 {
            width: 500px;
            height: 500px;
            background: #d97757;
            top: -15%;
            left: -10%;
            animation-delay: 0s;
        }

        .orb-2 {
            width: 400px;
            height: 400px;
            background: #d97757;
            bottom: -10%;
            right: -10%;
            animation-delay: 8s;
        }

        .orb-3 {
            width: 350px;
            height: 350px;
            background: #d97757;
            top: 40%;
            right: 20%;
            animation-delay: 16s;
        }

        @keyframes float {

            0%,
            100% {
                transform: translate(0, 0) scale(1);
            }

            33% {
                transform: translate(50px, -50px) scale(1.1);
            }

            66% {
                transform: translate(-30px, 30px) scale(0.9);
            }
        }

        .claude-container {
            background-color: transparent;
            min-height: 100vh;
            position: relative;
            z-index: 1;
        }

        /* Header Animation */
        .header-section {
            animation: slideDown 0.6s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .magang-card {
            transition: transform 0.4s cubic-bezier(0.25, 0.8, 0.25, 1),
                box-shadow 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
            border-radius: 24px;
            overflow: hidden;
            position: relative;
            height: 480px;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            opacity: 0;
            animation: fadeInUp 0.6s ease-out forwards;
            z-index: 10;
        }


        .card-wrapper::before {
            content: "";
            position: absolute;
            /* Posisikan 2px di luar card (ini adalah ketebalan border) */
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;

            /* Bulatkan 2px lebih besar dari radius card (24px + 2px) */
            border-radius: 26px;

            /* Gradient yang akan berputar */
            background: conic-gradient(from var(--gradient-angle, 0deg),
                    #d97757,
                    #f09a7a,
                    #e88968,
                    #f09a7a,
                    #d97757);

            /* Sembunyikan by default */
            opacity: 0;

            /* Animasi berputar */
            animation: rotate-gradient 4s linear infinite;

            /* Transisi untuk opacity */
            transition: opacity 0.4s ease-out;

            /* Tempatkan di belakang card */
            z-index: 1;
        }

        .card-wrapper {
            position: relative;
        }

        @keyframes rotate-gradient {
            0% {
                --gradient-angle: 0deg;
            }

            100% {
                --gradient-angle: 360deg;
            }
        }

        ` .card-wrapper:hover .magang-card {
            /* 1. Efek floating (mengambang) */
            transform: translateY(-12px) scale(1.02);

            /* 2. Beri bayangan yang lebih jelas */
            box-shadow: 0 10px 30px rgba(217, 119, 87, 0.4);
        }

        .card-wrapper:hover::before {
            /* 3. Tampilkan border animasi */
            opacity: 1;
        }

        /* Staggered animation for cards */
        .card-wrapper:nth-child(1) .magang-card {
            animation-delay: 0.1s;
        }

        .card-wrapper:nth-child(2) .magang-card {
            animation-delay: 0.2s;
        }

        .card-wrapper:nth-child(3) .magang-card {
            animation-delay: 0.3s;
        }

        .card-wrapper:nth-child(4) .magang-card {
            animation-delay: 0.4s;
        }

        .card-wrapper:nth-child(5) .magang-card {
            animation-delay: 0.5s;
        }

        .card-wrapper:nth-child(6) .magang-card {
            animation-delay: 0.6s;
        }

        .card-wrapper:nth-child(n+7) .magang-card {
            animation-delay: 0.7s;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* .magang-card:hover {
                   transition: transform 0.3s ease-out, box-shadow 0.3s ease-out;
                } */

        .card-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to bottom,
                    rgba(0, 0, 0, 0) 0%,
                    rgba(0, 0, 0, 0.1) 30%,
                    rgba(0, 0, 0, 0.5) 70%,
                    rgba(0, 0, 0, 0.85) 100%);
        }

        .hide-scrollbar {
            scrollbar-width: none;
            /* Untuk Firefox */
            -ms-overflow-style: none;
            /* Untuk Internet Explorer 10+ */
        }

        .hide-scrollbar::-webkit-scrollbar {
            display: none;
            /* Untuk Chrome, Safari, dan Edge */
        }

        .card-overlay::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 50%;
            background: linear-gradient(to bottom,
                    rgba(255, 255, 255, 0) 0%,
                    rgba(255, 255, 255, 0.02) 50%,
                    rgba(255, 255, 255, 0.05) 100%);
        }

        .card-blur-bottom {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 50%;
            backdrop-filter: blur(0px);
            -webkit-backdrop-filter: blur(0px);
            mask-image: linear-gradient(to bottom,
                    transparent 0%,
                    rgba(0, 0, 0, 0.3) 30%,
                    rgba(0, 0, 0, 0.6) 50%,
                    rgba(0, 0, 0, 0.8) 70%,
                    black 100%);
            -webkit-mask-image: linear-gradient(to bottom,
                    transparent 0%,
                    rgba(0, 0, 0, 0.3) 30%,
                    rgba(0, 0, 0, 0.6) 50%,
                    rgba(0, 0, 0, 0.8) 70%,
                    black 100%);
        }



        .card-content {
            position: relative;
            z-index: 10;
            height: 100%;
            display: flex;
            flex-direction: column;
            padding: 1.5rem;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .card-name {
            color: white;
            font-size: 1.5rem;
            font-weight: 600;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
            line-height: 1.2;
        }

        .card-socials {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            position: absolute;
            bottom: 1.5rem;
            right: 1.5rem;
            z-index: 20;
        }

        .card-socials .social-icon-link {
            width: 36px;
            height: 36px;
        }

        .card-socials .social-icon-link i {
            font-size: 1rem;
        }

        .status-badge {
            background-color: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(10px);
            color: white;
            padding: 0.4rem 0.875rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.375rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }
        }

        .status-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background-color: white;
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

        .card-spacer {
            flex: 1;
        }

        .card-footer {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .card-info {
            flex: 1;
        }

        .info-item {
            margin-bottom: 0.5rem;
        }

        .info-label {
            color: rgba(255, 255, 255, 0.75);
            font-size: 0.7rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
        }

        .info-value {
            color: white;
            font-size: 0.95rem;
            font-weight: 500;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.4);
        }

        /* ========================================
           FILTER STYLES
           ======================================== */

        .filter-container {
            background-color: rgba(42, 42, 42, 0.6);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(217, 119, 87, 0.2);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            animation: slideDown 0.5s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .filter-form {
            width: 100%;
        }

        .filter-grid {
            display: grid;
            grid-template-columns: 1fr 1fr auto;
            gap: 1rem;
            align-items: end;
        }

        .filter-item {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .filter-label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #c4c4c4;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .filter-label i {
            color: #d97757;
            font-size: 0.875rem;
        }

        .filter-input,
        .filter-select {
            background-color: rgba(45, 45, 45, 0.6);
            border: 1px solid rgba(58, 58, 58, 0.8);
            color: #e8e8e8;
            padding: 0.75rem 1rem;
            border-radius: 10px;
            width: 100%;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-size: 0.875rem;
        }

        .filter-input:focus,
        .filter-select:focus {
            outline: none;
            border-color: #d97757;
            background-color: rgba(45, 45, 45, 0.9);
            box-shadow: 0 0 0 3px rgba(217, 119, 87, 0.2);
        }

        .filter-input::placeholder {
            color: #6a6a6a;
        }

        .filter-select {
            cursor: pointer;
        }

        .filter-select option {
            background-color: #2a2a2a;
            color: #e8e8e8;
            padding: 0.5rem;
        }

        .filter-actions {
            display: flex;
            gap: 0.5rem;
            flex-direction: row;
        }

        .filter-btn {
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
        }

        .filter-btn-primary {
            background: linear-gradient(135deg, #d97757 0%, #e88968 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(217, 119, 87, 0.3);
        }

        .filter-btn-primary:hover {
            background: linear-gradient(135deg, #e88968 0%, #f09a7a 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(217, 119, 87, 0.4);
        }

        .filter-btn-secondary {
            background-color: rgba(156, 163, 175, 0.2);
            color: #9ca3af;
            border: 1px solid rgba(156, 163, 175, 0.3);
        }

        .filter-btn-secondary:hover {
            background-color: rgba(156, 163, 175, 0.3);
            transform: translateY(-2px);
        }

        .filter-info {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 1rem;
            padding: 0.75rem 1rem;
            background-color: rgba(59, 130, 246, 0.1);
            border: 1px solid rgba(59, 130, 246, 0.3);
            border-radius: 10px;
            color: #60a5fa;
            font-size: 0.875rem;
        }

        .filter-info i {
            font-size: 1rem;
        }

        .filter-info strong {
            color: #93c5fd;
            font-weight: 600;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background-color: rgba(42, 42, 42, 0.4);
            border: 2px dashed rgba(58, 58, 58, 0.6);
            border-radius: 16px;
        }

        /* Responsive Filter */
        @media (max-width: 1024px) {
            .filter-grid {
                grid-template-columns: 1fr 1fr;
            }

            .filter-actions {
                grid-column: 1 / -1;
                justify-content: flex-end;
            }
        }

        @media (max-width: 640px) {
            .filter-grid {
                grid-template-columns: 1fr;
            }

            .filter-actions {
                flex-direction: column;
            }

            .filter-btn {
                width: 100%;
                justify-content: center;
            }

            .filter-container {
                padding: 1rem;
            }
        }

        /* Animation for filter items */
        .filter-item {
            animation: fadeInUp 0.5s ease-out both;
        }

        .filter-item:nth-child(1) {
            animation-delay: 0.1s;
        }

        .filter-item:nth-child(2) {
            animation-delay: 0.2s;
        }

        .filter-item:nth-child(3) {
            animation-delay: 0.3s;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ========================================
           LOADING SPINNER STYLES
           ======================================== */

        .search-loader,
        .select-loader {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #d97757;
            font-size: 1rem;
            pointer-events: none;
        }

        .search-loader i,
        .select-loader i {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Filter Input dengan Loader */
        .filter-input:focus~.search-loader {
            color: #e88968;
        }

        /* Adjust padding untuk input dengan loader */
        .filter-input {
            padding-right: 2.5rem;
        }

        .filter-select {
            padding-right: 2.5rem;
        }

        /* Loading Overlay (Optional) */
        .filter-loading {
            position: relative;
            pointer-events: none;
            opacity: 0.6;
        }

        .filter-loading::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(42, 42, 42, 0.5);
            backdrop-filter: blur(2px);
            border-radius: 16px;
        }

        /* Disable button saat loading */
        .filter-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* Smooth transition untuk filter container */
        .filter-container {
            transition: opacity 0.3s ease;
        }

        /* Remove button animation update */
        .filter-btn {
            position: relative;
        }

        .filter-btn-secondary {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .filter-btn-secondary:hover {
            background-color: rgba(239, 68, 68, 0.2);
            color: #f87171;
            border-color: rgba(239, 68, 68, 0.4);
        }

        .filter-btn-secondary:active {
            transform: scale(0.95);
        }

        /* Keyboard indicator */
        .filter-input:focus {
            background-color: rgba(45, 45, 45, 0.95);
        }

        /* Success state setelah filter */
        @keyframes filterSuccess {
            0% {
                border-color: rgba(217, 119, 87, 0.2);
            }

            50% {
                border-color: rgba(34, 197, 94, 0.5);
            }

            100% {
                border-color: rgba(217, 119, 87, 0.2);
            }
        }

        .filter-container.success {
            animation: filterSuccess 0.6s ease;
        }

        /* Social Media Section */
        .social-icon-link {
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            text-decoration: none;
            position: relative;
        }

        .social-icon-link:hover {
            transform: translateY(-3px) scale(1.15);
        }

        .social-icon-link i {
            font-size: 1.5rem;
            filter: drop-shadow(0 2px 8px rgba(0, 0, 0, 0.6));
            transition: all 0.3s ease;
        }

        .social-icon-link:hover i {
            filter: drop-shadow(0 4px 12px rgba(217, 119, 87, 0.8));
        }

        .social-icon-link i {
            color: white;
        }

        /* Action Buttons */
        .card-actions-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-actions {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }

        .action-btn {
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: transparent;
            color: white;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            border: none;
            text-decoration: none;
        }

        .action-btn:hover {
            transform: scale(1.25) rotate(5deg);
        }

        .action-btn:active {
            transform: scale(1.1);
        }

        .action-btn i {
            font-size: 1.25rem;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.4));
        }

        .action-btn.detail i {
            color: #60a5fa;
        }

        .action-btn.edit i {
            color: #d97757;
        }

        .action-btn.delete i {
            color: #f87171;
        }

        .claude-button {
            display: inline-flex !important;
            align-items: center !important;
            padding: 0.6rem 1.5rem !important;
            font-size: 0.8rem !important;
            line-height: 1.25 !important;
            background: linear-gradient(135deg, #d97757 0%, #e88968 100%);
            color: #ffffff;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 12px;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(217, 119, 87, 0.3);
        }

        .claude-button:hover {
            background: linear-gradient(135deg, #e88968 0%, #f09a7a 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(217, 119, 87, 0.5);
        }

        .claude-button:active {
            transform: translateY(0);
        }

        .success-alert {
            background-color: rgba(34, 197, 94, 0.1);
            border: 1px solid rgba(34, 197, 94, 0.3);
            color: #4ade80;
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            animation: slideInDown 0.5s ease-out;
            position: relative;
            overflow: hidden;
        }

        .success-alert::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            height: 3px;
            background: #4ade80;
            animation: progressBar 3s linear forwards;
        }

        @keyframes progressBar {
            from {
                width: 100%;
            }

            to {
                width: 0%;
            }
        }

        .success-alert.fade-out {
            animation: fadeOutUp 0.5s ease-out forwards;
        }

        @keyframes fadeOutUp {
            from {
                opacity: 1;
                transform: translateY(0);
            }

            to {
                opacity: 0;
                transform: translateY(-20px);
            }
        }

        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .empty-state {
            background-color: rgba(42, 42, 42, 0.6);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(217, 119, 87, 0.2);
            border-radius: 16px;
            padding: 4rem 2rem;
            text-align: center;
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.95);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .empty-state svg {
            animation: bounce 2s ease-in-out infinite;
        }

        @keyframes bounce {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        .initial-avatar {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 5;
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4rem;
            font-weight: 700;
            color: white;
            text-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            border: 3px solid rgba(255, 255, 255, 0.3);
            animation: scaleIn 0.5s ease-out;
        }

        @keyframes scaleIn {
            from {
                opacity: 0;
                transform: translate(-50%, -50%) scale(0.5);
            }

            to {
                opacity: 1;
                transform: translate(-50%, -50%) scale(1);
            }
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .magang-card {
                height: 460px;
            }
        }

        @media (max-width: 768px) {
            .magang-card {
                height: 440px;
            }

            .card-name {
                font-size: 1.35rem;
            }

            .initial-avatar {
                width: 120px;
                height: 120px;
                font-size: 3rem;
            }

            .card-socials {
                bottom: 1rem;
                right: 1rem;
            }

            .card-socials .social-icon-link {
                width: 32px;
                height: 32px;
            }

            .card-socials .social-icon-link i {
                font-size: 0.9rem;
            }
        }

        @media (max-width: 640px) {
            .header-section .flex {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start !important;
            }

            .claude-button {
                width: 100%;
                text-align: center;
                justify-content: center;
            }

            .magang-card {
                height: 420px;
            }

            .card-content {
                padding: 1.25rem;
            }

            .card-name {
                font-size: 1.25rem;
            }

            .status-badge {
                font-size: 0.7rem;
                padding: 0.35rem 0.75rem;
            }

            .info-value {
                font-size: 0.875rem;
            }

            .action-btn {
                width: 40px;
                height: 40px;
            }

            .action-btn i {
                font-size: 1.1rem;
            }

            .initial-avatar {
                width: 100px;
                height: 100px;
                font-size: 2.5rem;
            }

            .card-socials {
                gap: 0.4rem;
            }

            /* Show social icons by default on mobile */
            .card-socials {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @media (max-width: 400px) {
            .magang-card {
                height: 400px;
            }

            .card-content {
                padding: 1rem;
            }

            .card-name {
                font-size: 1.15rem;
            }

            .card-socials .social-icon-link {
                width: 28px;
                height: 28px;
            }

            .card-socials .social-icon-link i {
                font-size: 0.85rem;
            }

            .bg-orb {
                display: none;
            }
        }

        /* Smooth scrolling */
        html {
            scroll-behavior: smooth;
        }

        /* Floating particles */
        .particle {
            position: fixed;
            width: 4px;
            height: 4px;
            background: rgba(217, 119, 87, 0.6);
            border-radius: 50%;
            pointer-events: none;
            animation: particleFloat 3s ease-in-out infinite;
            z-index: 0;
        }

        @keyframes particleFloat {

            0%,
            100% {
                transform: translateY(0) translateX(0);
                opacity: 0;
            }

            10% {
                opacity: 1;
            }

            90% {
                opacity: 1;
            }

            100% {
                transform: translateY(-100px) translateX(50px);
                opacity: 0;
            }
        }

        /* Loading state animation */
        @keyframes shimmer {
            0% {
                background-position: -1000px 0;
            }

            100% {
                background-position: 1000px 0;
            }
        }

        .view-toggle-btn {
            padding: 0.375rem 0.75rem;
            /* p-1.5 px-3 */
            color: #9ca3af;
            /* text-gray-400 */
            border-radius: 6px;
            /* rounded-md */
            transition: all 0.2s ease-in-out;
            font-size: 0.875rem;
            /* text-sm */
            line-height: 1;
        }

        .view-toggle-btn:hover {
            color: #ffffff;
            /* text-white */
        }

        .view-toggle-btn.active {
            background-color: rgba(217, 119, 87, 0.4);
            /* Brand color semi-transparent */
            color: #e88968;
            /* Brand color */
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        body.view-grid #magang-container {
            display: grid;
            grid-template-columns: repeat(1, minmax(0, 1fr));
            /* grid-cols-1 */
            gap: 1.5rem;
            /* gap-6 */
            padding-left: 1.5rem;
            /* px-6 */
            padding-right: 1.5rem;
            /* px-6 */
        }

        @media (min-width: 768px) {
            body.view-grid #magang-container {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                /* md:grid-cols-2 */
            }
        }

        @media (min-width: 1024px) {
            body.view-grid #magang-container {
                grid-template-columns: repeat(3, minmax(0, 1fr));
                /* lg:grid-cols-3 */
            }
        }

        body.view-grid .card-wrapper {
            width: auto;
        }

        body.view-row #magang-container {
            display: flex;
            overflow-x: auto;
            gap: 1.5rem;
            /* gap-6 */
            padding-top: 1rem;
            /* py-4 */
            padding-bottom: 1rem;
            /* py-4 */
            padding-left: 1.5rem;
            /* px-6 */
            padding-right: 1.5rem;
            /* px-6 */
            /* Sembunyikan scrollbar di sini */
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        body.view-row #magang-container::-webkit-scrollbar {
            display: none;
        }

        /* Kartu di mode row lebarnya tetap */
        body.view-row .card-wrapper {
            flex-shrink: 0;
            width: 24rem;
            /* w-96 */
        }
        
        .claude-calendar { color: #d1d5db; }
.fc .fc-toolbar-title { color: #ffffffff; }
.fc .fc-button-primary { background-color: #22c55e; border-color: #22c55e; color: #ffffff; }
.fc .fc-button-primary:hover,
.fc .fc-button-primary:active { background-color: #16a34a; border-color: #16a34a; }
.fc .fc-button { background-color: #374151; border-color: #4b5563; color: #d1d5db; }
.fc .fc-daygrid-day.fc-day-today { background-color: rgba(34, 197, 94, 0.1); }
.fc .fc-daygrid-day-number { color: #ffffffff !important; }
.fc .fc-daygrid-day.fc-day-today .fc-daygrid-day-number { color: #e88968 !important; font-weight: bold; }
.fc .fc-col-header-cell-cushion { color: #f3b49f !important; }

.fc-event { cursor: help; }
.fc-daygrid-event {
    background-color: transparent !important;
    border: none !important;
    padding: 0 !important;
    margin-top: 4px;
    margin-left: 5px;
    display: inline-flex;
    width: auto;
    height: auto;
}

.fc-daygrid-event-dot { display: none !important; }
.fc-event-title, .fc-event-time { display: none !important; }


/* --- PERBAIKAN RESPONSIVENESS KALENDER MOBILE (Gunakan ID #calendar untuk spesifisitas tinggi) --- */
@media (max-width: 640px) {
    /* Memaksa Toolbar menjadi kolom dan rata kiri */
    #calendar .fc-toolbar.fc-header-toolbar {
        display: flex !important;
        flex-direction: column !important;
        align-items: flex-start !important;
        gap: 8px !important;
    }
    
    /* Pindahkan Title ke baris paling atas (Order: -1) */
    #calendar .fc-toolbar-chunk:nth-child(2) { 
        order: -1 !important; 
        width: 100% !important;
        text-align: left !important;
    }

    /* Atur tata letak grup tombol agar rata kanan */
    #calendar .fc-toolbar-chunk {
        width: 100% !important;
        display: flex !important;
        justify-content: space-between !important;
        align-items: center !important;
    }
    
    /* Kecilkan teks judul dan tombol di mobile */
    #calendar .fc-toolbar-title {
        font-size: 1.125rem !important; /* text-lg */
        margin: 0 !important;
    }
    #calendar .fc-button {
        padding: 0.3rem 0.6rem !important;
        font-size: 0.75rem !important; /* text-xs */
        line-height: 1 !important;
    }
    
    /* Teks hari di kalender (Min, Sen, dst) */
    #calendar .fc-col-header-cell-cushion {
        padding-top: 0 !important;
        padding-bottom: 0 !important;
        font-size: 0.65rem !important; /* Kecilkan teks hari */
    }
}
    </style>
</head>

<body class="font-sans antialiased view-grid"
    x-data="{ expanded: JSON.parse(localStorage.getItem('_x_expanded') ?? 'true'), mobileOpen: false }"
    x-init="document.documentElement.classList.remove('sidebar-collapsed-on-load')"
    :class="{ 'overflow-hidden': mobileOpen }">

    {{-- ✅ HEADER KHUSUS MOBILE (LG:HIDDEN) --}}
    {{-- Urutan diubah: Tombol dulu, baru Judul. justify-between -> justify-start gap-4 --}}
    <header x-cloak
        class="lg:hidden fixed top-0 left-0 right-0 z-30 flex items-center justify-start gap-4 h-16 px-6 bg-[#1a1a1a]/80 backdrop-blur-sm border-b border-[#3a3a3a]">
        <button @click="mobileOpen = true" class="text-gray-300 p-2 -ml-2 rounded-lg hover:bg-white/5 hover:text-white">
            <i class="fas fa-bars w-5 text-center"></i>
        </button>
        <h1 class="claude-title text-xl text-white font-semibold">
            MagNet
        </h1>
    </header>

    <div class="flex">
        @include('layouts.sidebar')

        <main class="flex-1 transition-all duration-500 ease-in-out pt-16 lg:pt-0"
            :class="expanded ? 'lg:pl-64' : 'lg:pl-20'">

            {{ $slot }}
        </main>
    </div>

    {{-- OVERLAY MENU MOBILE --}}
    <div x-cloak x-show="mobileOpen" @click="mobileOpen = false"
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/50 z-40 lg:hidden">
    </div>

    {{-- Script Alpine sudah ada di sidebar.blade.php --}}
    @stack('scripts')

    {{-- MagBot: AI Chatbot Widget --}}
    <x-chatbot-widget />

</body>

</html>
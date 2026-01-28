<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Kehadiran</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Fraunces:wght@400;500;600&display=swap');
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #1a1a1a 0%, #2d1810 100%);
            color: #e8e8e8;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .claude-title {
            font-family: 'Fraunces', 'Georgia', 'Times New Roman', serif;
        }
        .card {
            background-color: rgba(42, 42, 42, 0.6);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(217, 119, 87, 0.2);
            border-radius: 16px;
            padding: 2.5rem;
            max-width: 600px;
            width: 100%;
            margin: 2rem;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        .icon-success {
            font-size: 4rem;
            color: #4ade80;
        }
        .icon-error {
            font-size: 4rem;
            color: #f87171;
        }
        .claude-button {
            background: linear-gradient(135deg, #d97757 0%, #e88968 100%);
            color: #ffffff;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 12px;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(217, 119, 87, 0.3);
            text-decoration: none;
            padding: 0.75rem 1.5rem;
            display: inline-block;
            margin-top: 1.5rem;
        }
        .claude-button:hover {
            background: linear-gradient(135deg, #e88968 0%, #f09a7a 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(217, 119, 87, 0.5);
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <div class="card">
        @if ($isError)
            <i class="fas fa-times-circle icon-error mb-4"></i>
            <h1 class="claude-title text-3xl font-bold mb-4">Konfirmasi Gagal</h1>
        @else
            <i class="fas fa-check-circle icon-success mb-4"></i>
            <h1 class="claude-title text-3xl font-bold mb-4">Konfirmasi Berhasil!</h1>
        @endif

        <p class="text-gray-300 text-lg mb-2">
            Halo, **{{ $pendaftaran->nama_pendaftar }}**!
        </p>
        <p class="text-gray-400">
            {{ $message }}
        </p>

         <a href="{{ config('app.url') }}" class="claude-button">
            Kembali ke Halaman Utama
        </a> 
    </div>

</body>
</html>
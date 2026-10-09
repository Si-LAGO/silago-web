<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - SILAGO</title>
    <!-- Google Fonts Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: '#15803d',
                        deep: '#1b4d3e',
                        soft: '#ebf5f0',
                        line: '#e8ebed',
                        ink: '#111827',
                        muted: '#4b5563',
                        faint: '#9ca3af',
                        page: '#f9fafb'
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-page antialiased flex h-screen">
    <!-- Left Side -->
    <div class="hidden lg:flex w-1/2 bg-deep text-white flex-col justify-center px-16">
        <h1 class="text-4xl font-bold mb-4 tracking-wide text-brand">SILAGO</h1>
        <h2 class="text-3xl font-semibold mb-6">Kelola Layanan SILAGO dengan Aman</h2>
        <p class="text-lg text-gray-300 max-w-md">Masuk ke panel admin untuk mengelola pengguna, barang, transaksi, dan laporan pada platform SILAGO.</p>
    </div>

    <!-- Right Side -->
    <div class="w-full lg:w-1/2 flex items-center justify-center bg-white">
        <div class="w-full max-w-md px-8">
            <h2 class="text-2xl font-bold text-ink mb-8 text-center">Masuk ke Akun Admin</h2>
            
            @if($errors->any())
                <div class="mb-4 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg text-sm">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.authenticate') ?? '#' }}">
                @csrf
                <div class="mb-5">
                    <label class="block text-sm font-medium text-ink mb-2">Email atau Username</label>
                    <input type="text" name="login" class="w-full px-4 py-2 border border-line rounded-lg focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand" placeholder="superadmin@silago.id" value="{{ old('login') }}" required autofocus>
                </div>
                
                <div class="mb-6">
                    <label class="block text-sm font-medium text-ink mb-2">Kata Sandi</label>
                    <input type="password" name="password" class="w-full px-4 py-2 border border-line rounded-lg focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand" required>
                </div>
                
                <button type="submit" class="w-full bg-deep hover:bg-brand text-white font-medium py-2.5 px-4 rounded-lg transition-colors">
                    Masuk
                </button>
            </form>
        </div>
    </div>
</body>
</html>

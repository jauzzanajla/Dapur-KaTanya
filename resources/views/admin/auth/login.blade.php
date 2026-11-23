<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Administrator - Dapur KaTanya</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-gradient-to-br from-slate-50 via-white to-red-50/30 text-slate-800 font-sans min-h-screen flex items-center justify-center p-4">

    <div class="max-w-md w-full space-y-6">

        <!-- Logo & Branding Header -->
        <div class="text-center space-y-2">
            <div class="w-16 h-16 rounded-3xl bg-gradient-to-tr from-red-600 to-orange-500 flex items-center justify-center text-white font-black text-3xl mx-auto shadow-xl shadow-red-500/25">
                DK
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">
                Dapur <span class="text-red-600">KaTanya</span>
            </h1>
            <p class="text-xs text-slate-500 font-medium">
                Panel Manajemen & Laporan Keuangan (Role Admin)
            </p>
        </div>

        <!-- Login Card -->
        <div class="bg-white rounded-3xl p-8 border border-slate-200/80 shadow-xl space-y-5">
            @if($errors->any())
                <div class="p-3 bg-red-50 border border-red-200 text-red-700 text-xs rounded-xl">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('admin.login.submit') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Email -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Email Administrator</label>
                    <div class="relative">
                        <input type="email"
                               name="email"
                               value="{{ old('email') }}"
                               required
                               class="w-full pl-10 pr-4 py-2.5 text-sm rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent font-medium">
                        <i data-lucide="mail" class="w-4 h-4 text-slate-400 absolute left-3.5 top-3"></i>
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-bold text-slate-700">Password</label>
                    </div>
                    <div class="relative">
                        <input type="password"
                               name="password"
                               required
                               class="w-full pl-10 pr-4 py-2.5 text-sm rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent font-medium">
                        <i data-lucide="lock" class="w-4 h-4 text-slate-400 absolute left-3.5 top-3"></i>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center gap-2 cursor-pointer text-slate-600">
                        <input type="checkbox" name="remember" class="rounded text-red-600 focus:ring-red-500">
                        <span>Ingat saya</span>
                    </label>
                
                </div>

                <!-- Submit Button (Red Primary) -->
                <button type="submit"
                        class="w-full py-3 px-4 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold text-sm shadow-lg shadow-red-600/30 transition active:scale-95 flex items-center justify-center gap-2">
                    <i data-lucide="log-in" class="w-4 h-4"></i>
                    <span>Masuk ke Dashboard Admin</span>
                </button>
            </form>

        </div>

        <div class="text-center">
            <a href="{{ route('catalog') }}" class="text-xs font-bold text-slate-500 hover:text-red-600 transition">
                &larr; Kembali ke Katalog Toko
            </a>
        </div>

    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>

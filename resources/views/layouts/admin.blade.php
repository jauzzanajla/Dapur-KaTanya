<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - Dapur KaTanya</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#fef2f2',
                            100: '#fee2e2',
                            200: '#fecaca',
                            300: '#fca5a5',
                            400: '#f87171',
                            500: '#ef4444',
                            600: '#dc2626', // Main Red
                            700: '#b91c1c',
                            800: '#991b1b',
                            900: '#7f1d1d',
                        },
                        secondary: {
                            50: '#fff7ed',
                            100: '#ffedd5',
                            200: '#fed7aa',
                            300: '#fdba74',
                            400: '#fb923c',
                            500: '#f97316', // Main Orange
                            600: '#ea580c',
                            700: '#c2410c',
                        }
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased min-h-screen flex"
      x-data="{ sidebarOpen: false }">

    <!-- Mobile Sidebar Backdrop -->
    <div x-cloak
         x-show="sidebarOpen"
         @click="sidebarOpen = false"
         class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-sm lg:hidden transition-opacity">
    </div>

    <!-- Sidebar Navigation -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
           class="fixed inset-y-0 left-0 z-50 w-72 bg-white border-r border-slate-200/80 shadow-lg lg:shadow-none lg:static lg:translate-x-0 transition-transform duration-300 flex flex-col justify-between">

        <div>
            <!-- Sidebar Header / Brand -->
            <div class="h-20 px-6 border-b border-slate-100 flex items-center justify-between">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                    <div class="w-16 h-12 shrink-0 flex items-center justify-center">
                        <img src="{{ asset('images/dapur-katanya-logo.png') }}" alt="Logo Dapur KaTanya" class="w-full h-full object-contain">
                    </div>
                    <div>
                        <span class="text-lg font-black tracking-tight text-slate-900">
                            Dapur <span class="text-red-600">KaTanya</span>
                        </span>
                        <p class="text-[10px] font-bold text-orange-600 uppercase tracking-widest">
                            Admin Portal
                        </p>
                    </div>
                </a>
                <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-slate-600 p-1">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Navigation Links -->
            <div class="px-4 py-6 space-y-1">
                <div class="px-3 pb-2 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                    Menu Utama
                </div>

                <a href="{{ route('admin.dashboard') }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-red-50 text-red-600 border border-red-200 shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    <i data-lucide="layout-dashboard" class="w-4 h-4 {{ request()->routeIs('admin.dashboard') ? 'text-red-600' : 'text-slate-400' }}"></i>
                    <span>Dashboard Overview</span>
                </a>

                <a href="{{ route('admin.products.index') }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.products.*') ? 'bg-red-50 text-red-600 border border-red-200 shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    <i data-lucide="utensils" class="w-4 h-4 {{ request()->routeIs('admin.products.*') ? 'text-red-600' : 'text-slate-400' }}"></i>
                    <span>Menu & Multi-Gambar</span>
                </a>

                <a href="{{ route('admin.qris.index') }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.qris.*') ? 'bg-red-50 text-red-600 border border-red-200 shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    <i data-lucide="qr-code" class="w-4 h-4 {{ request()->routeIs('admin.qris.*') ? 'text-red-600' : 'text-slate-400' }}"></i>
                    <span>Kelola QRIS</span>
                </a>

                <a href="{{ route('admin.orders.index') }}"
                   class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.orders.*') ? 'bg-red-50 text-red-600 border border-red-200 shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    <div class="flex items-center gap-3">
                        <i data-lucide="shopping-cart" class="w-4 h-4 {{ request()->routeIs('admin.orders.*') ? 'text-red-600' : 'text-slate-400' }}"></i>
                        <span>Pesanan & Verifikasi</span>
                    </div>
                    @php
                        $pendingVerification = \App\Models\Order::where('status', 'menunggu_verifikasi')->count();
                    @endphp
                    @if($pendingVerification > 0)
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-orange-500 text-white animate-pulse">
                            {{ $pendingVerification }}
                        </span>
                    @endif
                </a>

                <a href="{{ route('admin.pickup-schedules.index') }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.pickup-schedules.*') ? 'bg-red-50 text-red-600 border border-red-200 shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    <i data-lucide="calendar-days" class="w-4 h-4 {{ request()->routeIs('admin.pickup-schedules.*') ? 'text-red-600' : 'text-slate-400' }}"></i>
                    <span>Jadwal Pickup / Open PO</span>
                </a>

                <div class="px-3 pt-6 pb-2 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                    Laporan & Finansial
                </div>

                <a href="{{ route('admin.sales.index') }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.sales.*') ? 'bg-red-50 text-red-600 border border-red-200 shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    <i data-lucide="bar-chart-3" class="w-4 h-4 {{ request()->routeIs('admin.sales.*') ? 'text-red-600' : 'text-slate-400' }}"></i>
                    <span>Rekap Penjualan & Grafik</span>
                </a>

                <a href="{{ route('admin.raw-materials.index') }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.raw-materials.*') ? 'bg-red-50 text-red-600 border border-red-200 shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    <i data-lucide="file-spreadsheet" class="w-4 h-4 {{ request()->routeIs('admin.raw-materials.*') ? 'text-red-600' : 'text-slate-400' }}"></i>
                    <span>Bahan Baku (Impor CSV)</span>
                </a>
            </div>
        </div>

        <!-- Sidebar Footer -->
        <div class="p-4 border-t border-slate-100 space-y-3">
            <a href="{{ route('catalog') }}" target="_blank"
               class="flex items-center justify-center gap-2 w-full py-2 px-3 text-xs font-bold text-slate-600 hover:text-red-600 bg-slate-50 hover:bg-red-50 rounded-xl border border-slate-200 transition">
                <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                <span>Buka Katalog Toko</span>
            </a>

            <div class="flex items-center justify-between pt-2">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-red-100 text-red-600 font-black text-xs flex items-center justify-center">
                        AD
                    </div>
                    <div class="text-left">
                        <p class="text-xs font-bold text-slate-800 leading-tight">{{ Auth::user()->name ?? 'Admin' }}</p>
                        <p class="text-[10px] text-slate-400">Role: Administrator</p>
                    </div>
                </div>

                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" title="Logout"
                            class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition">
                        <i data-lucide="log-out" class="w-4 h-4"></i>
                    </button>
                </form>
            </div>
        </div>

    </aside>

    <!-- Main Admin Workspace Area -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

        <!-- Top Header Bar -->
        <header class="h-20 bg-white border-b border-slate-200/80 px-6 sm:px-8 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <button @click="sidebarOpen = true" class="lg:hidden text-slate-500 hover:text-slate-700 p-2 rounded-lg">
                    <i data-lucide="menu" class="w-6 h-6"></i>
                </button>
                <h1 class="text-lg sm:text-xl font-black text-slate-900 tracking-tight">
                    @yield('page_title', 'Dashboard')
                </h1>
            </div>

            <div class="flex items-center gap-3">
                <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 bg-orange-50 border border-orange-200 text-orange-700 rounded-xl text-xs font-semibold">
                    <i data-lucide="database" class="w-3.5 h-3.5 text-orange-500"></i>
                    <span>PostgreSQL Online</span>
                </div>
                <div class="flex items-center gap-2 px-3 py-1.5 bg-slate-100 text-slate-700 rounded-xl text-xs font-semibold">
                    <i data-lucide="calendar" class="w-3.5 h-3.5 text-slate-500"></i>
                    <span>{{ date('d M Y') }}</span>
                </div>
            </div>
        </header>

        <!-- Flash Messages -->
        <div class="px-6 sm:px-8 pt-6">
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl flex items-center gap-3 shadow-sm mb-4">
                    <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-600 shrink-0"></i>
                    <div class="text-xs sm:text-sm font-semibold">{{ session('success') }}</div>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-2xl flex items-center gap-3 shadow-sm mb-4">
                    <i data-lucide="alert-circle" class="w-5 h-5 text-red-600 shrink-0"></i>
                    <div class="text-xs sm:text-sm font-semibold">{{ session('error') }}</div>
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-2xl shadow-sm mb-4">
                    <div class="flex items-center gap-2 font-bold text-xs sm:text-sm mb-1">
                        <i data-lucide="alert-triangle" class="w-4 h-4 text-red-600"></i>
                        Periksa Kesalahan Form:
                    </div>
                    <ul class="list-disc list-inside text-xs space-y-0.5 ml-4">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <!-- Scrollable Main Page Body -->
        <main class="flex-1 overflow-y-auto px-6 sm:px-8 pb-12 pt-2">
            @yield('content')
        </main>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
        });
    </script>
</body>
</html>

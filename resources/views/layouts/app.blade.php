<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dapur KaTanya') - Dimsum & Cireng Kuah Keju Spesial</title>
    <meta name="description" content="Dapur KaTanya - Aneka Dimsum & Cireng Kuah Keju Lumer, Es.T Segar, S.Kom Es Coklat, dan Paket Bundling Cepat Lulus.">
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
    <style>
        [x-cloak] { display: none !important; }
        html { scroll-behavior: smooth; }
    </style>
</head>
<body class="bg-white text-slate-800 font-sans antialiased min-h-screen flex flex-col selection:bg-red-500 selection:text-white"
      x-data="dapurCart()"
      x-init="initCart()">

    <!-- Announcement Bar -->
    <div class="bg-gradient-to-r from-red-600 via-orange-500 to-red-600 text-white text-xs font-semibold py-2 px-4 text-center tracking-wide">
        🧀 Buka Setiap Hari! Nikmati Sensasi Gurih Lumer Dimsum & Cireng Kuah Keju Dapur KaTanya. Pembayaran Mudah via QRIS! 🧀
    </div>

    <!-- Header Navbar -->
    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-slate-100 shadow-sm transition-all">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Brand Logo -->
                <a href="{{ route('catalog') }}" class="flex items-center gap-3 group">
                    <div class="w-16 h-12 shrink-0 flex items-center justify-center group-hover:scale-105 transition-transform">
                        <img src="{{ asset('images/dapur-katanya-logo.png') }}" alt="Logo Dapur KaTanya" class="w-full h-full object-contain">
                    </div>
                    <div>
                        <span class="text-2xl font-black tracking-tight text-slate-900 group-hover:text-red-600 transition-colors">
                            Dapur <span class="text-red-600">KaTanya</span>
                        </span>
                        <p class="text-xs font-medium text-orange-600 flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-orange-500 animate-pulse"></span>
                            Dimsum & Cireng Kuah Keju
                        </p>
                    </div>
                </a>

                <!-- Navigation Links -->
                <nav class="hidden md:flex items-center gap-8 text-sm font-semibold text-slate-600">
                    <a href="{{ route('catalog') }}" class="hover:text-red-600 transition-colors {{ request()->routeIs('catalog') ? 'text-red-600 font-bold' : '' }}">
                        Katalog Menu
                    </a>
                    <a href="{{ route('order.track') }}" class="hover:text-red-600 transition-colors {{ request()->routeIs('order.track') ? 'text-red-600 font-bold' : '' }}">
                        Lacak Pesanan
                    </a>
                    <a href="#outlet-info" class="hover:text-red-600 transition-colors">
                        Info Pickup & Jam Buka
                    </a>
                </nav>

                <!-- Actions: Cart Button & Admin Shortcut -->
                <div class="flex items-center gap-3">
                    <a href="{{ route('order.track') }}" class="md:hidden p-2 text-slate-600 hover:text-red-600">
                        <i data-lucide="search" class="w-5 h-5"></i>
                    </a>

                    <!-- Cart Trigger Button -->
                    <button @click="openCartDrawer = true"
                            class="relative flex items-center gap-2.5 bg-red-600 hover:bg-red-700 text-white font-bold px-4 py-2.5 rounded-xl shadow-md shadow-red-600/20 hover:shadow-lg transition-all active:scale-95">
                        <i data-lucide="shopping-bag" class="w-5 h-5"></i>
                        <span class="hidden sm:inline text-sm font-bold">Keranjang</span>
                        <span x-show="totalCount > 0"
                              x-text="totalCount"
                              x-transition
                              class="bg-orange-500 text-white text-xs font-black px-2 py-0.5 rounded-full min-w-[20px] text-center border-2 border-white shadow">
                        </span>
                    </button>

                    <!-- Admin Portal Button -->
                   
                </div>
            </div>
        </div>
    </header>

    <!-- Flash Messages -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center gap-3 shadow-sm mb-4">
                <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-600 shrink-0"></i>
                <div class="text-sm font-medium">{{ session('success') }}</div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl flex items-center gap-3 shadow-sm mb-4">
                <i data-lucide="alert-circle" class="w-5 h-5 text-red-600 shrink-0"></i>
                <div class="text-sm font-medium">{{ session('error') }}</div>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl shadow-sm mb-4">
                <div class="flex items-center gap-2 font-bold text-sm mb-1">
                    <i data-lucide="alert-triangle" class="w-5 h-5 text-red-600"></i>
                    Mohon periksa kembali form Anda:
                </div>
                <ul class="list-disc list-inside text-xs space-y-1 ml-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <!-- Main Content -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer id="outlet-info" class="bg-slate-900 text-white mt-20 pt-16 pb-12 border-t-4 border-red-600">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-10">
                <!-- Col 1: Brand -->
                <div class="md:col-span-2 space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-16 h-12 shrink-0 flex items-center justify-center">
                            <img src="{{ asset('images/dapur-katanya-logo.png') }}" alt="Logo Dapur KaTanya" class="w-full h-full object-contain">
                        </div>
                        <span class="text-2xl font-black tracking-tight">Dapur <span class="text-red-500">KaTanya</span></span>
                    </div>
                    <p class="text-slate-400 text-sm leading-relaxed max-w-md">
                        Pelopor jajanan kekinian dengan kuah keju spesial yang kental, gurih, dan nagih. Menyajikan Dimsum Kuah Keju, Cireng Kuah Keju, Es.T, S.Kom Es Coklat, dan Paket Bundling Hemat "Paket Cepat Lulus".
                    </p>
                    <div class="flex items-center gap-2 text-xs text-orange-400 font-medium">
                        <i data-lucide="shield-check" class="w-4 h-4 text-orange-400"></i>
                        100% Halal, Bersih, & Higienis
                    </div>
                </div>

                <!-- Col 2: Outlet & Pickup Info -->
                <div class="space-y-3">
                    <h4 class="text-sm font-bold uppercase tracking-wider text-orange-400">Lokasi & Pengambilan</h4>
                    <p class="text-slate-300 text-sm">
                        <strong>Outlet Dapur KaTanya</strong><br>
                        Jl. Kampus Raya No. 12 (Dekat Gerbang Utama Kampus)<br>
                        Kota Pelajar, Indonesia
                    </p>
                    <p class="text-xs text-slate-400">
                        <i data-lucide="clock" class="w-3.5 h-3.5 inline mr-1 text-orange-400"></i>
                        Buka: Setiap Hari (10:00 - 21:00 WIB)
                    </p>
                    <p class="text-xs text-red-400 font-semibold">
                        Sistem Pemesanan: Ambil Sendiri (Pickup)
                    </p>
                </div>

                <!-- Col 3: Pembayaran & Kontak -->
                <div class="space-y-3">
                    <h4 class="text-sm font-bold uppercase tracking-wider text-orange-400">Metode Pembayaran</h4>
                    <div class="bg-slate-800/80 p-3 rounded-xl border border-slate-700">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="px-2 py-0.5 bg-red-600 text-white text-[11px] font-black rounded">QRIS</span>
                            <span class="text-xs text-slate-300 font-medium">Semua Bank & E-Wallet</span>
                        </div>
                        <p class="text-xs text-slate-400 leading-snug">
                            BCA, Mandiri, BRI, BNI, GoPay, OVO, DANA, ShopeePay & LinkAja.
                        </p>
                    </div>
                    <div class="pt-2">
                        <a href="https://wa.me/6281382099669" target="_blank" rel="noopener noreferrer"
                           class="inline-flex items-center gap-2 text-sm text-emerald-400 hover:text-emerald-300 transition-colors">
                            <i data-lucide="phone" class="w-4 h-4"></i>
                            WhatsApp CS: +62 813-8209-9669
                        </a>
                    </div>
                </div>
            </div>

            <div class="border-t border-slate-800 mt-12 pt-6 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-4">
                <p>&copy; {{ date('Y') }} Dapur KaTanya. Hak Cipta Dilindungi.</p>
                <div class="flex items-center gap-4">
                    <a href="{{ route('admin.login') }}" class="hover:text-slate-300 transition-colors">Admin Login</a>
                    <span>&bull;</span>
                    <a href="{{ route('order.track') }}" class="hover:text-slate-300 transition-colors">Lacak Pesanan</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- ========================================== -->
    <!-- SLIDE-OVER SHOPPING CART & CHECKOUT DRAWER -->
    <!-- ========================================== -->
    <div x-cloak
         x-show="openCartDrawer"
         class="fixed inset-0 z-50 overflow-hidden"
         aria-labelledby="slide-over-title"
         role="dialog"
         aria-modal="true">

        <!-- Backdrop -->
        <div x-show="openCartDrawer"
             x-transition:enter="ease-in-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in-out duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="openCartDrawer = false"
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity">
        </div>

        <div class="fixed inset-y-0 right-0 max-w-full flex pl-10">
            <div x-show="openCartDrawer"
                 x-transition:enter="transform transition ease-in-out duration-300 sm:duration-400"
                 x-transition:enter-start="translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transform transition ease-in-out duration-300 sm:duration-400"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="translate-x-full"
                 class="w-screen max-w-md bg-white shadow-2xl flex flex-col">

                <!-- Drawer Header -->
                <div class="px-6 py-5 bg-gradient-to-r from-red-600 to-orange-500 text-white flex items-center justify-between shadow-md">
                    <div class="flex items-center gap-3">
                        <i data-lucide="shopping-bag" class="w-6 h-6"></i>
                        <div>
                            <h2 class="text-lg font-black tracking-tight" id="slide-over-title">Keranjang Pesanan</h2>
                            <p class="text-xs text-orange-100" x-text="totalCount + ' item dipilih'"></p>
                        </div>
                    </div>
                    <button @click="openCartDrawer = false" class="p-2 text-white/80 hover:text-white rounded-lg hover:bg-white/10 transition">
                        <i data-lucide="x" class="w-6 h-6"></i>
                    </button>
                </div>

                <!-- Step Indicator -->
                <div class="bg-slate-50 px-6 py-3 border-b border-slate-200 flex items-center justify-between text-xs font-bold">
                    <button @click="checkoutStep = 1"
                            :class="checkoutStep === 1 ? 'text-red-600 border-b-2 border-red-600 pb-1' : 'text-slate-400'"
                            class="flex items-center gap-1.5 transition">
                        <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-black"
                              :class="checkoutStep === 1 ? 'bg-red-600 text-white' : 'bg-slate-200 text-slate-600'">1</span>
                        <span>Item & Pengambilan</span>
                    </button>
                    <i data-lucide="chevron-right" class="w-4 h-4 text-slate-300"></i>
                    <button @click="if(canProceedToPayment()) checkoutStep = 2"
                            :class="checkoutStep === 2 ? 'text-red-600 border-b-2 border-red-600 pb-1' : 'text-slate-400'"
                            class="flex items-center gap-1.5 transition">
                        <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-black"
                              :class="checkoutStep === 2 ? 'bg-red-600 text-white' : 'bg-slate-200 text-slate-600'">2</span>
                        <span>QRIS & Bukti Bayar</span>
                    </button>
                </div>

                <!-- Form container -->
                <form action="{{ route('order.store') }}" method="POST" enctype="multipart/form-data" class="flex-1 flex flex-col justify-between overflow-hidden">
                    @csrf
                    <!-- Hidden input containing JSON cart string -->
                    <input type="hidden" name="cart_items" :value="JSON.stringify(cart)">

                    <!-- Scrollable Content Body -->
                    <div class="flex-1 overflow-y-auto px-6 py-4 space-y-6">

                        <!-- ========================= -->
                        <!-- STEP 1: CART ITEMS & FORM -->
                        <!-- ========================= -->
                        <div x-show="checkoutStep === 1" class="space-y-6">
                            <!-- Empty Cart State -->
                            <template x-if="cart.length === 0">
                                <div class="py-16 text-center space-y-3">
                                    <div class="w-16 h-16 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto">
                                        <i data-lucide="shopping-cart" class="w-8 h-8"></i>
                                    </div>
                                    <h3 class="text-base font-bold text-slate-800">Keranjang Belanja Masih Kosong</h3>
                                    <p class="text-xs text-slate-500 max-w-xs mx-auto">
                                        Yuk pilih menu Dimsum, Cireng Kuah Keju atau Minuman favoritmu dari katalog!
                                    </p>
                                    <button type="button" @click="openCartDrawer = false"
                                            class="mt-2 text-xs font-bold text-red-600 hover:text-red-700 bg-red-50 px-4 py-2 rounded-lg">
                                        Lihat Menu Sekarang &rarr;
                                    </button>
                                </div>
                            </template>

                            <!-- Items List -->
                            <template x-if="cart.length > 0">
                                <div class="space-y-3">
                                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Daftar Menu Dipilih</h3>
                                    <div class="divide-y divide-slate-100 border border-slate-100 rounded-2xl overflow-hidden shadow-sm">
                                        <template x-for="item in cart" :key="item.id">
                                            <div class="p-3 bg-white flex items-center gap-3">
                                                <img :src="item.image" :alt="item.name" class="w-14 h-14 object-cover rounded-xl border border-slate-100 shrink-0">
                                                <div class="flex-1 min-w-0">
                                                    <h4 class="text-sm font-bold text-slate-800 truncate" x-text="item.name"></h4>
                                                    <p class="text-xs font-semibold text-red-600" x-text="formatRupiah(item.price)"></p>
                                                    <div class="flex items-center gap-2 mt-1.5">
                                                        <!-- Counter -->
                                                        <div class="flex items-center border border-slate-200 rounded-lg bg-slate-50">
                                                            <button type="button" @click="decreaseQty(item.id)" class="px-2 py-0.5 text-slate-600 hover:bg-slate-200 rounded-l-lg font-bold text-xs">-</button>
                                                            <span class="px-2 text-xs font-bold text-slate-800" x-text="item.quantity"></span>
                                                            <button type="button" @click="increaseQty(item.id)" class="px-2 py-0.5 text-slate-600 hover:bg-slate-200 rounded-r-lg font-bold text-xs">+</button>
                                                        </div>
                                                        <span class="text-xs font-bold text-slate-500 ml-auto" x-text="formatRupiah(item.price * item.quantity)"></span>
                                                        <button type="button" @click="removeFromCart(item.id)" class="p-1 text-slate-400 hover:text-red-600 rounded">
                                                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>

                            <!-- Mandatory Checkout Form Inputs -->
                            <template x-if="cart.length > 0">
                                <div class="space-y-4 pt-2 border-t border-slate-100">
                                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Data Pengambilan (Pickup)</h3>

                                    <!-- Customer Name -->
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1">
                                            Nama Pemesan <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text"
                                               x-model="customerName"
                                               name="customer_name"
                                               required
                                               placeholder="Contoh: Budi Santoso"
                                               class="w-full text-sm px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                                    </div>

                                    <!-- Mandatory Phone Number Input (FEATURE 3) -->
                                    <div class="p-3.5 bg-red-50/80 rounded-2xl border-2 border-red-200 space-y-1.5">
                                        <div class="flex items-center justify-between">
                                            <label class="block text-xs font-black text-red-900">
                                                Nomor HP Asli (WhatsApp) <span class="text-red-600">*</span>
                                            </label>
                                            <span class="text-[10px] font-bold px-2 py-0.5 bg-red-600 text-white rounded-full uppercase tracking-wider">WAJIB</span>
                                        </div>
                                        <input type="tel"
                                               x-model="customerPhone"
                                               name="customer_phone"
                                               required
                                               placeholder="Contoh: 081234567890"
                                               class="w-full text-sm px-3.5 py-2.5 rounded-xl border border-red-300 bg-white focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent font-medium">
                                        <p class="text-[11px] text-red-700 font-medium leading-tight">
                                            ⚠️ Kolom ini <strong>WAJIB</strong> diisi dengan nomor HP aktif untuk keperluan konfirmasi pesanan & kendala di luar sistem.
                                        </p>
                                    </div>

                                    <!-- Pickup Date (Date Picker) (FEATURE 5) -->
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1">
                                            Tanggal Pengambilan Pesanan (Date Picker) <span class="text-red-500">*</span>
                                        </label>
                                        <input type="date"
                                               x-model="pickupDate"
                                               name="pickup_date"
                                               min="{{ date('Y-m-d') }}"
                                               required
                                               class="w-full text-sm px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent bg-white">
                                    </div>

                                    <!-- Pickup Location (Lokasi Pengambilan) -->
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1">
                                            Lokasi Pengambilan (Pickup Point) <span class="text-red-500">*</span>
                                        </label>
                                        <select x-model="pickupLocation" name="pickup_location" required
                                                class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent bg-white font-medium">
                                            <option value="Kampus B">📍 Kampus B</option>
                                            <option value="Kampus C">📍 Kampus C</option>
                                            <option value="Lainnya">📍 Lainnya</option>
                                        </select>
                                        <div x-show="pickupLocation === 'Lainnya'" x-cloak class="mt-2">
                                            <input type="text" name="pickup_location_other" x-model="otherPickupLocation"
                                                   :required="pickupLocation === 'Lainnya'" maxlength="150"
                                                   placeholder="Tuliskan titik pickup yang diinginkan"
                                                   class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                                        </div>
                                        <p class="text-[11px] text-slate-400 mt-1">Pilih Kampus B, Kampus C, atau tuliskan lokasi lain.</p>
                                    </div>

                                    <!-- Pickup Time -->
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1">
                                            Jam Perkiraan Pengambilan
                                        </label>
                                        <select x-model="pickupTime" name="pickup_time"
                                                class="w-full text-sm px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent bg-white">
                                            <option value="11:00">11:00 WIB (Siang)</option>
                                            <option value="12:30">12:30 WIB (Makan Siang)</option>
                                            <option value="14:00">14:00 WIB</option>
                                            <option value="15:30">15:30 WIB (Sore)</option>
                                            <option value="17:00">17:00 WIB</option>
                                            <option value="18:30">18:30 WIB (Malam)</option>
                                            <option value="20:00">20:00 WIB</option>
                                        </select>
                                    </div>

                                    <!-- Notes -->
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Tambahan (Opsional)</label>
                                        <textarea x-model="orderNotes" name="notes" rows="2"
                                                  placeholder="Contoh: Kuah keju dipisah, minta sendok 2..."
                                                  class="w-full text-sm px-3.5 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"></textarea>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- ============================================== -->
                        <!-- STEP 2: QRIS PAYMENT & UPLOAD BUKTI BAYAR     -->
                        <!-- ============================================== -->
                        <div x-show="checkoutStep === 2" class="space-y-5">
                            <!-- Notice Badge -->
                            <div class="bg-orange-50 border border-orange-200 p-3.5 rounded-2xl flex items-center gap-3">
                                <i data-lucide="qr-code" class="w-6 h-6 text-orange-600 shrink-0"></i>
                                <div>
                                    <h4 class="text-xs font-bold text-orange-950">Metode Pembayaran: HANYA QRIS</h4>
                                    <p class="text-[11px] text-orange-800">Scan QRIS di bawah lalu upload bukti transfer untuk menyelesaikan pesanan.</p>
                                </div>
                            </div>

                            <!-- Total to Pay Card -->
                            <div class="bg-slate-900 text-white p-4 rounded-2xl text-center shadow-lg space-y-1">
                                <p class="text-xs uppercase tracking-wider text-slate-400 font-bold">Total Pembayaran</p>
                                <p class="text-2xl font-black text-orange-400" x-text="formatRupiah(totalPrice)"></p>
                                <p class="text-[11px] text-slate-300">Merchant: <strong>Dapur KaTanya</strong></p>
                            </div>

                            <!-- QRIS Image from Admin Settings -->
                            <div class="bg-white border-2 border-orange-200 rounded-2xl p-4 text-center shadow-sm space-y-3">
                                @if(isset($qrisPath) && $qrisPath)
                                    <img src="{{ \App\Support\Media::url($qrisPath) }}"
                                         alt="QRIS Dapur KaTanya"
                                         class="max-w-[260px] max-h-72 object-contain mx-auto rounded-xl border border-slate-100 shadow">
                                    <p class="text-[11px] text-slate-500">
                                        Dapat discan menggunakan aplikasi:<br>
                                        <span class="font-bold text-slate-700">BCA Mobile, Mandiri Livin, BRImo, BNI, GoPay, OVO, DANA, ShopeePay</span>
                                    </p>
                                @else
                                    <div class="max-w-md mx-auto rounded-xl border border-orange-200 bg-orange-50 p-5 text-orange-800">
                                        <i data-lucide="triangle-alert" class="w-8 h-8 mx-auto mb-2 text-orange-600"></i>
                                        <p class="text-sm font-black">QRIS belum disetel</p>
                                        <p class="text-xs mt-1">Silakan hubungi admin untuk informasi pembayaran.</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Upload Bukti Bayar (FEATURE 6) -->
                            <div class="p-4 bg-red-50/70 border-2 border-dashed border-red-300 rounded-2xl space-y-2">
                                <div class="flex items-center justify-between">
                                    <label class="block text-xs font-black text-red-900">
                                        Upload Bukti Bayar / Transfer <span class="text-red-600">*</span>
                                    </label>
                                    <span class="text-[10px] font-bold px-2 py-0.5 bg-red-600 text-white rounded-full">WAJIB</span>
                                </div>
                                <p class="text-[11px] text-slate-600">
                                    Unggah screenshot struk pembayaran QRIS Anda (format: JPG, PNG, WEBP, PDF, maks 5MB).
                                </p>

                                <input type="file"
                                       name="payment_proof"
                                       id="payment_proof"
                                       accept="image/*,.pdf"
                                       @change="handleProofUpload($event)"
                                       required
                                       class="block w-full text-xs text-slate-500
                                              file:mr-4 file:py-2 file:px-4
                                              file:rounded-xl file:border-0
                                              file:text-xs file:font-bold
                                              file:bg-red-600 file:text-white
                                              hover:file:bg-red-700 cursor-pointer">

                                <!-- Image Preview Container -->
                                <template x-if="proofPreviewUrl">
                                    <div class="mt-3 p-2 bg-white rounded-xl border border-red-200 text-center">
                                        <p class="text-[11px] font-bold text-slate-700 mb-1">Preview Bukti Transfer:</p>
                                        <img :src="proofPreviewUrl" alt="Preview Bukti" class="max-h-48 mx-auto rounded-lg shadow-sm border border-slate-100">
                                    </div>
                                </template>
                            </div>
                        </div>

                    </div>

                    <!-- Drawer Footer / Action Buttons -->
                    <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-500">Subtotal</span>
                            <span class="text-base font-black text-slate-900" x-text="formatRupiah(totalPrice)"></span>
                        </div>

                        <!-- Step 1 Button: Go to QRIS -->
                        <div x-show="checkoutStep === 1">
                            <button type="button"
                                    @click="goToPaymentStep()"
                                    :disabled="cart.length === 0 || !customerPhone || customerPhone.trim().length < 10"
                                    class="w-full py-3.5 px-4 rounded-xl font-bold text-sm text-white flex items-center justify-center gap-2 shadow-lg transition-all active:scale-95"
                                    :class="(cart.length > 0 && customerPhone && customerPhone.trim().length >= 10)
                                            ? 'bg-red-600 hover:bg-red-700 shadow-red-600/30'
                                            : 'bg-slate-300 cursor-not-allowed'">
                                <span>Lanjut ke Pembayaran QRIS</span>
                                <i data-lucide="arrow-right" class="w-4 h-4"></i>
                            </button>
                            <p x-show="!customerPhone || customerPhone.trim().length < 10"
                               class="text-[11px] text-red-600 text-center mt-1.5 font-semibold">
                                * Isi Nomor HP Asli terlebih dahulu untuk mengaktifkan tombol lanjut.
                            </p>
                        </div>

                        <!-- Step 2 Buttons: Back & Confirm Order -->
                        <div x-show="checkoutStep === 2" class="flex gap-2">
                            <button type="button" @click="checkoutStep = 1"
                                    class="px-4 py-3 bg-white border border-slate-200 text-slate-700 hover:bg-slate-100 font-bold text-xs rounded-xl transition">
                                Kembali
                            </button>
                            <button type="submit"
                                    :disabled="!hasUploadedProof"
                                    class="flex-1 py-3 px-4 rounded-xl font-black text-sm text-white flex items-center justify-center gap-2 shadow-lg transition-all active:scale-95"
                                    :class="hasUploadedProof ? 'bg-red-600 hover:bg-red-700 shadow-red-600/30' : 'bg-slate-300 cursor-not-allowed'">
                                <i data-lucide="check" class="w-4 h-4"></i>
                                <span>Konfirmasi Pesanan Sekarang</span>
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- GLOBAL PRODUCT MULTI-IMAGE VIEWER MODAL   -->
    <!-- ========================================== -->
    <div x-cloak
         x-show="galleryModal.open"
         @keydown.escape.window="galleryModal.open = false"
         class="fixed inset-0 z-50 overflow-y-auto"
         role="dialog"
         aria-modal="true">

        <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-md transition-opacity"
             @click="galleryModal.open = false"></div>

        <div class="flex items-center justify-center min-h-screen p-4 text-center">
            <div class="relative bg-white rounded-3xl max-w-2xl w-full p-6 text-left shadow-2xl overflow-hidden border border-slate-100">
                <!-- Close Button -->
                <button @click="galleryModal.open = false"
                        class="absolute top-4 right-4 z-10 w-9 h-9 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center transition">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>

                <div class="space-y-4">
                    <!-- Title & Badge -->
                    <div class="pr-10">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-xs font-black uppercase px-2.5 py-0.5 rounded-full bg-orange-100 text-orange-700" x-text="galleryModal.badge || 'Katalog Pilihan'"></span>
                            <span class="text-xs text-slate-400 font-semibold" x-text="galleryModal.category"></span>
                        </div>
                        <h3 class="text-xl font-black text-slate-900" x-text="galleryModal.title"></h3>
                        <p class="text-xs text-slate-500 mt-1" x-text="galleryModal.description"></p>
                    </div>

                    <!-- Main Active Image -->
                    <div class="relative rounded-2xl overflow-hidden bg-slate-50 border border-slate-100 aspect-[4/3] flex items-center justify-center">
                        <img :src="galleryModal.activeImage" :alt="galleryModal.title" class="w-full h-full object-contain">
                    </div>

                    <!-- Thumbnail List (Multi-image preview) -->
                    <div class="flex items-center gap-3 overflow-x-auto py-2">
                        <template x-for="(img, idx) in galleryModal.images" :key="idx">
                            <button type="button"
                                    @click="galleryModal.activeImage = img"
                                    class="w-16 h-16 rounded-xl overflow-hidden border-2 transition shrink-0"
                                    :class="galleryModal.activeImage === img ? 'border-red-600 scale-105 shadow-md' : 'border-slate-200 opacity-60 hover:opacity-100'">
                                <img :src="img" class="w-full h-full object-cover">
                            </button>
                        </template>
                    </div>

                    <!-- Footer Action -->
                    <div class="flex items-center justify-between pt-3 border-t border-slate-100">
                        <div>
                            <span class="text-xs text-slate-400 font-medium">Harga Menu</span>
                            <p class="text-xl font-black text-red-600" x-text="formatRupiah(galleryModal.price)"></p>
                        </div>
                        <button type="button"
                                @click="addToCart({id: galleryModal.id, name: galleryModal.title, price: galleryModal.price, image: galleryModal.activeImage}); galleryModal.open = false"
                                class="bg-red-600 hover:bg-red-700 text-white font-bold px-6 py-2.5 rounded-xl shadow-md shadow-red-600/30 transition active:scale-95 flex items-center gap-2">
                            <i data-lucide="plus" class="w-4 h-4"></i>
                            <span>Tambah ke Keranjang</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts: Alpine Cart Store -->
    <script>
        function dapurCart() {
            return {
                openCartDrawer: false,
                checkoutStep: 1, // 1: Items & details, 2: QRIS & Proof
                cart: [],
                customerName: '',
                customerPhone: '',
                pickupDate: '{{ date("Y-m-d") }}',
                pickupTime: '12:30',
                pickupLocation: 'Kampus B',
                otherPickupLocation: '',
                orderNotes: '',
                hasUploadedProof: false,
                proofPreviewUrl: null,

                galleryModal: {
                    open: false,
                    id: null,
                    title: '',
                    badge: '',
                    category: '',
                    price: 0,
                    description: '',
                    activeImage: '',
                    images: []
                },

                initCart() {
                    const saved = localStorage.getItem('dapur_katanya_cart');
                    if (saved) {
                        try {
                            this.cart = JSON.parse(saved);
                        } catch (e) {
                            this.cart = [];
                        }
                    }
                    this.$watch('cart', () => {
                        localStorage.setItem('dapur_katanya_cart', JSON.stringify(this.cart));
                    });
                    setTimeout(() => lucide.createIcons(), 100);
                },

                get totalCount() {
                    return this.cart.reduce((sum, item) => sum + item.quantity, 0);
                },

                get totalPrice() {
                    return this.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
                },

                addToCart(product, qty = 1) {
                    const existing = this.cart.find(i => i.id === product.id);
                    if (existing) {
                        existing.quantity += qty;
                    } else {
                        this.cart.push({
                            id: product.id,
                            name: product.name,
                            price: product.price,
                            image: product.image,
                            quantity: qty
                        });
                    }
                    this.openCartDrawer = true;
                    this.checkoutStep = 1;
                    setTimeout(() => lucide.createIcons(), 50);
                },

                increaseQty(id) {
                    const item = this.cart.find(i => i.id === id);
                    if (item) item.quantity++;
                },

                decreaseQty(id) {
                    const item = this.cart.find(i => i.id === id);
                    if (item) {
                        if (item.quantity > 1) {
                            item.quantity--;
                        } else {
                            this.removeFromCart(id);
                        }
                    }
                },

                removeFromCart(id) {
                    this.cart = this.cart.filter(i => i.id !== id);
                },

                canProceedToPayment() {
                    return this.cart.length > 0 &&
                           this.customerPhone &&
                           this.customerPhone.trim().length >= 10 &&
                           this.customerName.trim().length > 0;
                },

                goToPaymentStep() {
                    if (!this.customerName.trim()) {
                        alert('Silakan masukkan Nama Pemesan terlebih dahulu.');
                        return;
                    }
                    if (!this.customerPhone || this.customerPhone.trim().length < 10) {
                        alert('Nomor HP Asli WAJIB diisi minimal 10 digit angka!');
                        return;
                    }
                    this.checkoutStep = 2;
                    setTimeout(() => lucide.createIcons(), 50);
                },

                handleProofUpload(event) {
                    const file = event.target.files[0];
                    if (file) {
                        this.hasUploadedProof = true;
                        if (file.type.startsWith('image/')) {
                            this.proofPreviewUrl = URL.createObjectURL(file);
                        } else {
                            this.proofPreviewUrl = null;
                        }
                    } else {
                        this.hasUploadedProof = false;
                        this.proofPreviewUrl = null;
                    }
                },

                openGallery(product) {
                    this.galleryModal = {
                        open: true,
                        id: product.id,
                        title: product.name,
                        badge: product.badge,
                        category: product.category,
                        price: product.price,
                        description: product.description,
                        activeImage: product.images[0] || product.image,
                        images: product.images && product.images.length ? product.images : [product.image]
                    };
                    setTimeout(() => lucide.createIcons(), 50);
                },

                formatRupiah(num) {
                    return 'Rp ' + Number(num || 0).toLocaleString('id-ID');
                }
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
        });
    </script>
</body>
</html>

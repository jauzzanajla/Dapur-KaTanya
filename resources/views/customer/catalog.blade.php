@extends('layouts.app')

@section('title', 'Katalog Menu Lezat')

@section('content')
<!-- Hero Section -->
<section class="relative bg-gradient-to-b from-orange-50/50 via-white to-white py-12 md:py-16 border-b border-slate-100 overflow-hidden">
    <!-- Decorative background elements -->
    <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full bg-red-100/50 blur-3xl -z-10"></div>
    <div class="absolute top-1/2 -left-20 w-80 h-80 rounded-full bg-orange-100/50 blur-3xl -z-10"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
            <!-- Left Headline -->
            <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-orange-100 border border-orange-200 text-orange-700 text-xs font-black uppercase tracking-wider">
                    <span class="w-2 h-2 rounded-full bg-orange-500 animate-ping"></span>
                    Sensasi Baru Jajanan Kampus
                </div>

                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-slate-900 tracking-tight leading-[1.15]">
                    Lumer Kejunya, <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-red-600 to-orange-500">
                        Bikin Nagih Setiap Gigitan!
                    </span>
                </h1>

                <p class="text-base sm:text-lg text-slate-600 max-w-xl mx-auto lg:mx-0 leading-relaxed font-normal">
                    Selamat datang di <strong>Dapur KaTanya</strong>! Nikmati racikan istimewa Dimsum & Cireng bertabur Kuah Keju kental gurih, didampingi Es.T segar dan S.Kom es coklat pekat.
                </p>

                <!-- Key Value Badges -->
                <div class="flex flex-wrap items-center justify-center lg:justify-start gap-4 pt-2 text-xs font-bold text-slate-700">
                    <div class="flex items-center gap-2 bg-white px-3.5 py-2 rounded-xl shadow-sm border border-slate-100">
                        <i data-lucide="check-circle" class="w-4 h-4 text-red-600"></i>
                        <span>100% Homemade & Fresh</span>
                    </div>
                    <div class="flex items-center gap-2 bg-white px-3.5 py-2 rounded-xl shadow-sm border border-slate-100">
                        <i data-lucide="qr-code" class="w-4 h-4 text-orange-500"></i>
                        <span>Bayar Instan QRIS</span>
                    </div>
                    <div class="flex items-center gap-2 bg-white px-3.5 py-2 rounded-xl shadow-sm border border-slate-100">
                        <i data-lucide="map-pin" class="w-4 h-4 text-red-600"></i>
                        <span>Pickup Mudah Tanpa Antre</span>
                    </div>
                </div>
            </div>

            <!-- Right Featured Highlight Card -->
            @if($featuredProduct)
            <div class="lg:col-span-5">
                <div class="relative bg-white rounded-3xl p-6 shadow-xl border border-slate-100 text-center transform hover:-translate-y-1 transition duration-300">
                    <span class="absolute -top-3 left-6 px-3.5 py-1 bg-gradient-to-r from-red-600 to-orange-500 text-white font-black text-xs uppercase tracking-wider rounded-full shadow-md">
                        {{ $featuredProduct->badge ?: 'Menu Unggulan' }}
                    </span>
                    <img src="{{ $featuredProduct->primary_image_url }}"
                         alt="{{ $featuredProduct->name }}"
                         class="w-full h-52 object-contain mx-auto my-2">
                    <h3 class="text-xl font-black text-slate-900">{{ $featuredProduct->name }}</h3>
                    <p class="text-xs text-slate-500 mt-1 line-clamp-2">{{ $featuredProduct->description }}</p>
                    <div class="mt-4 flex items-center justify-between pt-3 border-t border-slate-100">
                        <div class="text-left">
                            <span class="text-[11px] text-slate-400 font-bold uppercase">Harga Menu</span>
                            <p class="text-xl font-black text-red-600">Rp {{ number_format($featuredProduct->harga_jual, 0, ',', '.') }}</p>
                        </div>
                        <button type="button"
                                @click="addToCart({id: {{ $featuredProduct->id }}, name: @js($featuredProduct->name), price: {{ $featuredProduct->harga_jual }}, image: @js($featuredProduct->primary_image_url)})"
                                class="bg-red-600 hover:bg-red-700 text-white font-bold text-xs px-5 py-3 rounded-xl shadow-md shadow-red-600/30 transition active:scale-95 flex items-center gap-1.5">
                            <i data-lucide="plus" class="w-4 h-4"></i>
                            Pesan Menu
                        </button>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</section>

<!-- Catalog Section -->
<section id="menu-section" class="py-12 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Filters & Search Toolbar -->
        <div class="flex flex-col md:flex-row items-center justify-between gap-4 pb-8 border-b border-slate-100">
            <!-- Category Pills -->
            <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
                <a href="{{ route('catalog') }}"
                   class="px-4 py-2 rounded-xl text-xs font-bold transition {{ !$category ? 'bg-red-600 text-white shadow-md shadow-red-600/20' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    Semua Menu ({{ $products->count() }})
                </a>
                <a href="{{ route('catalog', ['category' => 'makanan', 'search' => $search]) }}"
                   class="px-4 py-2 rounded-xl text-xs font-bold transition {{ $category === 'makanan' ? 'bg-red-600 text-white shadow-md shadow-red-600/20' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    🧀 Makanan (Dimsum & Cireng)
                </a>
                <a href="{{ route('catalog', ['category' => 'minuman', 'search' => $search]) }}"
                   class="px-4 py-2 rounded-xl text-xs font-bold transition {{ $category === 'minuman' ? 'bg-red-600 text-white shadow-md shadow-red-600/20' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    🥤 Minuman (Es.T & S.Kom)
                </a>
                <a href="{{ route('catalog', ['category' => 'paket', 'search' => $search]) }}"
                   class="px-4 py-2 rounded-xl text-xs font-bold transition {{ $category === 'paket' ? 'bg-red-600 text-white shadow-md shadow-red-600/20' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    🎓 Paket Bundling
                </a>
            </div>

            <!-- Search Form -->
            <form action="{{ route('catalog') }}" method="GET" class="w-full md:w-72">
                @if($category)
                    <input type="hidden" name="category" value="{{ $category }}">
                @endif
                <div class="relative">
                    <input type="text"
                           name="search"
                           value="{{ $search }}"
                           placeholder="Cari menu favorit..."
                           class="w-full pl-9 pr-4 py-2.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-3"></i>
                </div>
            </form>
        </div>

        <!-- Products Grid -->
        <div class="mt-8">
            @if($products->isEmpty())
                <div class="text-center py-16 bg-slate-50 rounded-3xl border border-slate-100">
                    <div class="w-16 h-16 bg-orange-100 text-orange-600 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i data-lucide="utensils" class="w-8 h-8"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800">Menu Tidak Ditemukan</h3>
                    <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                        Maaf, tidak ada produk yang cocok dengan pencarian Anda. Silakan coba kata kunci lain.
                    </p>
                    <a href="{{ route('catalog') }}" class="mt-4 inline-block text-xs font-bold text-red-600 hover:underline">
                        Lihat Semua Menu
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                    @foreach($products as $product)
                        @php
                            $imagesList = $product->images->map(fn($img) => $img->url)->toArray();
                            if (empty($imagesList)) {
                                $imagesList = [$product->primary_image_url];
                            }
                        @endphp
                        <div class="group bg-white rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between overflow-hidden relative">

                            <!-- Image Card Container -->
                            <div class="relative bg-slate-50 aspect-[4/3] flex items-center justify-center p-4 overflow-hidden">
                                <!-- Badge (Supporting Accent: Orange) -->
                                @if($product->badge)
                                    <span class="absolute top-4 left-4 z-10 px-3 py-1 bg-orange-500 text-white font-extrabold text-[11px] uppercase tracking-wider rounded-full shadow-sm">
                                        {{ $product->badge }}
                                    </span>
                                @endif

                                <!-- Multi-image gallery trigger badge -->
                                <button type="button"
                                        @click="openGallery({
                                            id: {{ $product->id }},
                                            name: '{{ addslashes($product->name) }}',
                                            category: '{{ ucfirst($product->category) }}',
                                            badge: '{{ addslashes($product->badge ?? '') }}',
                                            price: {{ $product->harga_jual }},
                                            description: '{{ addslashes($product->description) }}',
                                            image: '{{ $product->primary_image_url }}',
                                            images: {{ json_encode($imagesList) }}
                                        })"
                                        class="absolute bottom-3 right-3 z-10 bg-white/90 backdrop-blur-sm hover:bg-white text-slate-700 font-bold text-[11px] px-2.5 py-1 rounded-lg border border-slate-200/80 shadow-sm flex items-center gap-1.5 transition">
                                    <i data-lucide="camera" class="w-3.5 h-3.5 text-red-600"></i>
                                    <span>{{ count($imagesList) }} Foto</span>
                                </button>

                                <!-- Product Image with zoom on hover -->
                                <img src="{{ $product->primary_image_url }}"
                                     alt="{{ $product->name }}"
                                     class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300">
                            </div>

                            <!-- Content Info -->
                            <div class="p-6 flex-1 flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center justify-between text-xs text-slate-400 font-semibold mb-1">
                                        <span class="capitalize">{{ $product->category }}</span>
                                        <span class="flex items-center gap-1 {{ $product->is_available ? 'text-emerald-600' : 'text-red-600' }}">
                                            <span class="h-1.5 w-1.5 rounded-full {{ $product->is_available ? 'bg-emerald-500' : 'bg-red-500' }}"></span>
                                            {{ $product->is_available ? 'Tersedia' : 'Habis' }}
                                        </span>
                                    </div>
                                    <h3 class="text-lg font-black text-slate-900 group-hover:text-red-600 transition-colors">
                                        {{ $product->name }}
                                    </h3>
                                    <p class="text-xs text-slate-500 mt-2 line-clamp-2 leading-relaxed">
                                        {{ $product->description }}
                                    </p>
                                </div>

                                <!-- Price & Action Button (Red Primary) -->
                                <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between">
                                    <div>
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Harga Jual</span>
                                        <p class="text-xl font-black text-red-600 tracking-tight">
                                            Rp {{ number_format($product->harga_jual, 0, ',', '.') }}
                                        </p>
                                    </div>

                                    <button type="button"
                                            @click="addToCart({
                                                id: {{ $product->id }},
                                                name: '{{ addslashes($product->name) }}',
                                                price: {{ $product->harga_jual }},
                                                image: '{{ $product->primary_image_url }}'
                                            })"
                                            @disabled(! $product->is_available) class="flex items-center gap-1.5 rounded-xl px-4 py-2.5 text-xs font-bold text-white shadow-md transition {{ $product->is_available ? 'bg-red-600 shadow-red-600/20 hover:bg-red-700 hover:shadow-lg active:scale-95' : 'cursor-not-allowed bg-slate-400' }}">
                                        <i data-lucide="{{ $product->is_available ? 'plus' : 'ban' }}" class="w-4 h-4"></i>
                                        <span>{{ $product->is_available ? 'Pesan' : 'Habis' }}</span>
                                    </button>
                                </div>
                            </div>

                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>
</section>

<!-- Pickup & How to Order Guide Section -->
<section class="py-14 bg-slate-50 border-t border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-10">
            <h2 class="text-2xl sm:text-3xl font-black text-slate-900">Cara Mudah Pesan & Ambil (Pickup)</h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-2">Pesan sekarang, tentukan tanggal pengambilan, bayar via QRIS, dan ambil pesananmu tanpa antre!</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <!-- Step 1 -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm relative space-y-3">
                <span class="w-8 h-8 rounded-full bg-red-100 text-red-600 font-black text-sm flex items-center justify-center">1</span>
                <h4 class="font-bold text-sm text-slate-900">Pilih Menu</h4>
                <p class="text-xs text-slate-500">Pilih menu favoritmu dari katalog dan tentukan jumlah porsi di keranjang belanja.</p>
            </div>

            <!-- Step 2 -->
            <div class="bg-white p-6 rounded-2xl border-2 border-red-200 shadow-sm relative space-y-3 bg-red-50/20">
                <span class="w-8 h-8 rounded-full bg-red-600 text-white font-black text-sm flex items-center justify-center">2</span>
                <h4 class="font-bold text-sm text-red-900">Wajib Nomor HP & Tanggal</h4>
                <p class="text-xs text-slate-600">Isi Nomor HP asli untuk konfirmasi kendala dan pilih tanggal pickup yang kamu inginkan.</p>
            </div>

            <!-- Step 3 -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm relative space-y-3">
                <span class="w-8 h-8 rounded-full bg-orange-100 text-orange-600 font-black text-sm flex items-center justify-center">3</span>
                <h4 class="font-bold text-sm text-slate-900">Scan QRIS & Bukti</h4>
                <p class="text-xs text-slate-500">Lakukan pembayaran dengan scan QRIS resmi dan unggah foto/screenshot bukti transfer.</p>
            </div>

            <!-- Step 4 -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm relative space-y-3">
                <span class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 font-black text-sm flex items-center justify-center">4</span>
                <h4 class="font-bold text-sm text-slate-900">Ambil di Outlet</h4>
                <p class="text-xs text-slate-500">Admin akan memverifikasi dan menyiapkan pesanan hangat untuk kamu ambil tepat waktu!</p>
            </div>
        </div>
    </div>
</section>
@endsection

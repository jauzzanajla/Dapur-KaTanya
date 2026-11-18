@extends('layouts.admin')

@section('title', 'Kelola Menu & HPP')
@section('page_title', 'Manajemen Menu, Multi-Gambar & HPP (Modal)')

@section('content')
<div class="space-y-6">

    <!-- Header & Action Row -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-black text-slate-900 tracking-tight">Katalog Menu Dapur KaTanya</h2>
            <p class="text-xs text-slate-500">
                Atur informasi menu, multi-gambar per produk, serta modal (HPP) dan harga jual untuk penghitungan laba otomatis.
            </p>
        </div>

        <a href="{{ route('admin.products.create') }}"
           class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white font-bold text-xs rounded-xl shadow-md shadow-red-600/20 transition active:scale-95 flex items-center justify-center gap-2">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Tambah Menu Baru</span>
        </a>
    </div>

    <!-- Admin Notice: Modal & Margin -->
    <div class="p-4 bg-orange-50 border border-orange-200 rounded-2xl flex items-center gap-3">
        <div class="w-9 h-9 rounded-xl bg-orange-500 text-white flex items-center justify-center shrink-0">
            <i data-lucide="shield-check" class="w-5 h-5"></i>
        </div>
        <div class="text-xs">
            <span class="font-black text-orange-900">FITUR KHUSUS ADMIN (MODAL & KEUNTUNGAN):</span>
            <p class="text-orange-800">
                Kolom <strong>Modal (HPP)</strong> dan <strong>Margin</strong> hanya dapat diakses oleh Administrator. Sistem secara otomatis menghitung omset, modal pokok, dan laba bersih dari setiap pesanan yang masuk.
            </p>
        </div>
    </div>

    <!-- Filters & Search Toolbar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
        <!-- Category Filter -->
        <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
            <a href="{{ route('admin.products.index') }}"
               class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ !$category ? 'bg-red-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Semua Menu
            </a>
            <a href="{{ route('admin.products.index', ['category' => 'makanan', 'search' => $search]) }}"
               class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $category === 'makanan' ? 'bg-red-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Makanan
            </a>
            <a href="{{ route('admin.products.index', ['category' => 'minuman', 'search' => $search]) }}"
               class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $category === 'minuman' ? 'bg-red-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Minuman
            </a>
            <a href="{{ route('admin.products.index', ['category' => 'paket', 'search' => $search]) }}"
               class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $category === 'paket' ? 'bg-red-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Paket Bundling
            </a>
        </div>

        <!-- Search Bar -->
        <form action="{{ route('admin.products.index') }}" method="GET" class="w-full md:w-72">
            @if($category)
                <input type="hidden" name="category" value="{{ $category }}">
            @endif
            <div class="relative">
                <input type="text"
                       name="search"
                       value="{{ $search }}"
                       placeholder="Cari nama menu..."
                       class="w-full pl-9 pr-4 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                <i data-lucide="search" class="w-3.5 h-3.5 text-slate-400 absolute left-3 top-2.5"></i>
            </div>
        </form>
    </div>

    <!-- Products Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-400 uppercase tracking-wider font-bold border-b border-slate-200">
                        <th class="py-3.5 px-4">Menu & Multi-Gambar</th>
                        <th class="py-3.5 px-4">Kategori & Badge</th>
                        <th class="py-3.5 px-4">Modal (HPP)</th>
                        <th class="py-3.5 px-4">Harga Jual</th>
                        <th class="py-3.5 px-4">Margin Laba</th>
                        <th class="py-3.5 px-4">Katalog & Ketersediaan</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($products as $product)
                        <tr class="hover:bg-slate-50/70 transition">
                            <!-- Image & Name -->
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="relative w-14 h-14 rounded-2xl bg-slate-100 overflow-hidden border border-slate-200 shrink-0">
                                        <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                        <!-- Photo count badge -->
                                        <span class="absolute bottom-0 right-0 bg-slate-900/80 text-white text-[9px] font-black px-1.5 py-0.5 rounded-tl-lg">
                                            {{ $product->images->count() }} 📷
                                        </span>
                                    </div>
                                    <div>
                                        <h4 class="font-extrabold text-slate-900 text-sm leading-tight">{{ $product->name }}</h4>
                                        <p class="text-[11px] text-slate-400 line-clamp-1 max-w-xs mt-0.5">
                                            {{ $product->description }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            <!-- Category & Badge -->
                            <td class="py-4 px-4">
                                <div class="space-y-1">
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-700">
                                        {{ $product->category }}
                                    </span>
                                    @if($product->badge)
                                        <div>
                                            <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-orange-100 text-orange-700 border border-orange-200">
                                                {{ $product->badge }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </td>

                            <!-- Modal / HPP -->
                            <td class="py-4 px-4">
                                <span class="font-bold text-slate-700">
                                    Rp {{ number_format($product->modal, 0, ',', '.') }}
                                </span>
                            </td>

                            <!-- Harga Jual -->
                            <td class="py-4 px-4">
                                <span class="font-black text-red-600 text-sm">
                                    Rp {{ number_format($product->harga_jual, 0, ',', '.') }}
                                </span>
                            </td>

                            <!-- Margin -->
                            <td class="py-4 px-4">
                                <span class="font-extrabold text-emerald-600">
                                    +Rp {{ number_format($product->margin, 0, ',', '.') }}
                                </span>
                                <div class="text-[10px] font-bold text-emerald-700">
                                    ({{ $product->margin_percentage }}% margin)
                                </div>
                            </td>

                            <!-- Active Status Toggle -->
                            <td class="py-4 px-4">
                                <form action="{{ route('admin.products.toggle', $product) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                            title="Klik untuk mengubah visibilitas katalog"
                                            class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider border transition {{ $product->is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' : 'bg-slate-100 text-slate-500 border-slate-200 hover:bg-slate-200' }}">
                                        {{ $product->is_active ? 'Ditampilkan' : 'Disembunyikan' }}
                                    </button>
                                </form>
                                <form action="{{ route('admin.products.availability.toggle', $product) }}" method="POST" class="mt-2">
                                    @csrf
                                    <button type="submit" title="Klik untuk mengubah ketersediaan"
                                            class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider border transition {{ $product->is_available ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' : 'bg-red-50 text-red-700 border-red-200 hover:bg-red-100' }}">
                                        {{ $product->is_available ? 'Tersedia' : 'Habis' }}
                                    </button>
                                </form>
                            </td>

                            <!-- Actions -->
                            <td class="py-4 px-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.products.edit', $product) }}"
                                       class="p-2 text-slate-500 hover:text-red-600 hover:bg-red-50 rounded-xl transition"
                                       title="Edit Menu & Multi-Gambar">
                                        <i data-lucide="edit-3" class="w-4 h-4"></i>
                                    </a>

                                    <form action="{{ route('admin.products.destroy', $product) }}"
                                          method="POST"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus menu \'{{ addslashes($product->name) }}\'?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition"
                                                title="Hapus Menu">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                Belum ada menu yang terdaftar. Silakan klik tombol "Tambah Menu Baru".
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($products->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $products->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

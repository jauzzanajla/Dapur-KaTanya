@extends('layouts.admin')

@section('title', 'Edit Menu: ' . $product->name)
@section('page_title', 'Edit Menu & Galeri Multi-Gambar')

@section('content')
<div class="max-w-4xl space-y-6">

    <!-- Breadcrumb & Back -->
    <div class="flex items-center gap-2 text-xs font-bold text-slate-500">
        <a href="{{ route('admin.products.index') }}" class="hover:text-red-600 transition flex items-center gap-1">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Kembali ke Daftar Menu</span>
        </a>
    </div>

    <!-- Main Edit Card -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm space-y-6">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div>
                <h2 class="text-xl font-black text-slate-900">Edit Menu: {{ $product->name }}</h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    Perbarui data produk, modal HPP, harga jual, dan kelola foto katalog.
                </p>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
                Total {{ $product->images->count() }} Gambar
            </span>
        </div>

        <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Row 1: Nama & Kategori -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        Nama Menu <span class="text-red-600">*</span>
                    </label>
                    <input type="text"
                           name="name"
                           value="{{ old('name', $product->name) }}"
                           required
                           class="w-full text-sm px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent font-medium">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        Kategori Menu <span class="text-red-600">*</span>
                    </label>
                    <select name="category" required
                            class="w-full text-sm px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent bg-white font-medium">
                        <option value="makanan" {{ old('category', $product->category) === 'makanan' ? 'selected' : '' }}>🧀 Makanan (Dimsum / Cireng)</option>
                        <option value="minuman" {{ old('category', $product->category) === 'minuman' ? 'selected' : '' }}>🥤 Minuman (Es.T / S.Kom Es Coklat)</option>
                        <option value="paket" {{ old('category', $product->category) === 'paket' ? 'selected' : '' }}>🎓 Paket Bundling Hemat</option>
                    </select>
                </div>
            </div>

            <label class="flex items-start gap-3 p-3 rounded-xl border border-orange-200 bg-orange-50/70 cursor-pointer">
                <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}
                       class="mt-0.5 rounded text-red-600 focus:ring-red-500 w-4 h-4">
                <span>
                    <span class="block text-xs font-black text-slate-800">Tampilkan sebagai Menu Unggulan di Beranda</span>
                    <span class="block text-[11px] text-slate-500 mt-0.5">Hanya satu menu unggulan yang ditampilkan pada katalog pelanggan.</span>
                </span>
            </label>

            <!-- Row 2: Badge & Active -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        Badge Promosi (Aksen Orange)
                    </label>
                    <input type="text"
                           name="badge"
                           value="{{ old('badge', $product->badge) }}"
                           placeholder="Contoh: Best Seller / Signature / Hemat Bundling"
                           class="w-full text-sm px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent font-medium">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        Visibilitas Katalog
                    </label>
                    <label class="flex items-center gap-3 p-2.5 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-50 transition">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}
                               class="rounded text-red-600 focus:ring-red-500 w-4 h-4">
                        <span class="text-xs font-bold text-slate-700">Tampilkan menu di katalog pelanggan</span>
                    </label>
                    <div class="mt-3">
                        <label for="is_available" class="mb-1.5 block text-xs font-bold text-slate-700">Ketersediaan Menu</label>
                        <select id="is_available" name="is_available" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs font-semibold focus:border-transparent focus:outline-none focus:ring-2 focus:ring-orange-500">
                            <option value="1" @selected(old('is_available', $product->is_available))>Tersedia</option>
                            <option value="0" @selected(! old('is_available', $product->is_available))>Habis</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Row 3: Modal (HPP) & Harga Jual -->
            <div class="p-5 bg-orange-50/60 rounded-2xl border-2 border-orange-200 space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i data-lucide="calculator" class="w-5 h-5 text-orange-600"></i>
                        <h3 class="text-xs font-black uppercase tracking-wider text-orange-950">
                            Manajemen Modal & Keuntungan (Role Admin)
                        </h3>
                    </div>
                    <span class="text-xs font-black text-emerald-700 bg-white px-2.5 py-1 rounded-lg border border-orange-200">
                        Margin: Rp {{ number_format($product->margin, 0, ',', '.') }} ({{ $product->margin_percentage }}%)
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-800 mb-1.5">
                            Modal (Harga Bersih / HPP) <span class="text-red-600">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-2.5 text-xs font-bold text-slate-400">Rp</span>
                            <input type="number"
                                   name="modal"
                                   value="{{ old('modal', $product->modal) }}"
                                   step="500"
                                   min="0"
                                   required
                                   class="w-full pl-10 pr-4 py-2.5 text-sm rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent font-bold text-slate-800 bg-white">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-800 mb-1.5">
                            Harga Jual ke Pembeli <span class="text-red-600">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-2.5 text-xs font-bold text-slate-400">Rp</span>
                            <input type="number"
                                   name="harga_jual"
                                   value="{{ old('harga_jual', $product->harga_jual) }}"
                                   step="500"
                                   min="0"
                                   required
                                   class="w-full pl-10 pr-4 py-2.5 text-sm rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent font-bold text-red-600 bg-white">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Row 4: Deskripsi -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                    Deskripsi Menu <span class="text-red-600">*</span>
                </label>
                <textarea name="description"
                          rows="3"
                          required
                          class="w-full text-sm px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent font-normal">{{ old('description', $product->description) }}</textarea>
            </div>

            <!-- Row 5: Upload Additional Images -->
            <div class="space-y-3 pt-2 border-t border-slate-100">
                <label class="block text-xs font-black uppercase tracking-wider text-slate-900">
                    Tambah Foto Baru ke Galeri Produk
                </label>
                <input type="file"
                       name="new_images[]"
                       multiple
                       accept="image/*"
                       class="block w-full text-xs text-slate-500
                              file:mr-4 file:py-2 file:px-4
                              file:rounded-xl file:border-0
                              file:text-xs file:font-bold
                              file:bg-orange-500 file:text-white
                              hover:file:bg-orange-600 cursor-pointer">
            </div>

            <!-- Submit Button (Red Primary) -->
            <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.products.index') }}"
                   class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 font-bold text-xs transition">
                    Batal
                </a>
                <button type="submit"
                        class="px-6 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-black text-xs shadow-md shadow-red-600/30 transition active:scale-95 flex items-center gap-2">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    <span>Simpan Perubahan</span>
                </button>
            </div>

        </form>

        <!-- Existing Images Management Section -->
        <div class="pt-8 border-t-2 border-slate-100 space-y-4">
            <h3 class="text-sm font-black uppercase tracking-wider text-slate-900">
                Foto Galeri Produk Saat Ini (Multi-Gambar)
            </h3>
            <p class="text-xs text-slate-500">
                Atur foto utama dan kelola gambar katalog produk:
            </p>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                @foreach($product->images as $img)
                    <div class="bg-white rounded-2xl border-2 {{ $img->is_primary ? 'border-red-600 shadow-md' : 'border-slate-200' }} p-3 space-y-2 relative group">
                        <div class="relative aspect-square rounded-xl overflow-hidden bg-slate-50">
                            <img src="{{ $img->url }}" class="w-full h-full object-cover">
                            @if($img->is_primary)
                                <span class="absolute top-2 left-2 bg-red-600 text-white text-[9px] font-black px-2 py-0.5 rounded shadow">
                                    Foto Utama
                                </span>
                            @endif
                        </div>

                        <div class="flex items-center justify-between gap-1 pt-1">
                            @if(!$img->is_primary)
                                <form action="{{ route('admin.products.image.primary', $img) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                            class="text-[10px] font-bold text-slate-700 hover:text-red-600 bg-slate-100 hover:bg-red-50 px-2 py-1 rounded transition">
                                        Jadikan Utama
                                    </button>
                                </form>
                            @else
                                <span class="text-[10px] font-bold text-red-600">Utama ★</span>
                            @endif

                            @if($product->images->count() > 1)
                                <form action="{{ route('admin.products.image.delete', $img) }}"
                                      method="POST"
                                      onsubmit="return confirm('Hapus gambar ini dari galeri produk?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="p-1 text-slate-400 hover:text-red-600 rounded transition"
                                            title="Hapus Gambar">
                                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

</div>
@endsection

@extends('layouts.admin')

@section('title', 'Tambah Menu Baru')
@section('page_title', 'Tambah Menu & Unggah Multi-Gambar')

@section('content')
<div class="max-w-4xl space-y-6"
     x-data="{
        selectedFiles: [],
        previews: [],
        handleFiles(event) {
            const files = Array.from(event.target.files);
            this.selectedFiles = files;
            this.previews = [];
            files.forEach(file => {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.previews.push({ name: file.name, url: e.target.result });
                    };
                    reader.readAsDataURL(file);
                }
            });
        }
     }">

    <!-- Breadcrumb & Back -->
    <div class="flex items-center gap-2 text-xs font-bold text-slate-500">
        <a href="{{ route('admin.products.index') }}" class="hover:text-red-600 transition flex items-center gap-1">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Kembali ke Daftar Menu</span>
        </a>
    </div>

    <!-- Main Form Card -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm space-y-6">
        <div class="pb-4 border-b border-slate-100">
            <h2 class="text-xl font-black text-slate-900">Form Menu Dapur KaTanya</h2>
            <p class="text-xs text-slate-500 mt-0.5">
                Isi rincian produk dan unggah lebih dari 1 gambar untuk katalog.
            </p>
        </div>

        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Row 1: Nama & Kategori -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        Nama Menu <span class="text-red-600">*</span>
                    </label>
                    <input type="text"
                           name="name"
                           value="{{ old('name') }}"
                           required
                           placeholder="Contoh: Dimsum Mozzarella Keju Lumer"
                           class="w-full text-sm px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent font-medium">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        Kategori Menu <span class="text-red-600">*</span>
                    </label>
                    <select name="category" required
                            class="w-full text-sm px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent bg-white font-medium">
                        <option value="makanan" {{ old('category') === 'makanan' ? 'selected' : '' }}>🧀 Makanan (Dimsum / Cireng)</option>
                        <option value="minuman" {{ old('category') === 'minuman' ? 'selected' : '' }}>🥤 Minuman (Es.T / S.Kom Es Coklat)</option>
                        <option value="paket" {{ old('category') === 'paket' ? 'selected' : '' }}>🎓 Paket Bundling Hemat</option>
                    </select>
                </div>
            </div>

            <label class="flex items-start gap-3 p-3 rounded-xl border border-orange-200 bg-orange-50/70 cursor-pointer">
                <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}
                       class="mt-0.5 rounded text-red-600 focus:ring-red-500 w-4 h-4">
                <span>
                    <span class="block text-xs font-black text-slate-800">Tampilkan sebagai Menu Unggulan di Beranda</span>
                    <span class="block text-[11px] text-slate-500 mt-0.5">Menu ini akan mengisi kartu utama katalog pelanggan.</span>
                </span>
            </label>

            <!-- Row 2: Badge (Orange Accent) & Active Status -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        Badge Promosi (Aksen Orange)
                    </label>
                    <input type="text"
                           name="badge"
                           value="{{ old('badge') }}"
                           placeholder="Contoh: Best Seller / Signature / Hemat Bundling"
                           class="w-full text-sm px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent font-medium">
                    <p class="text-[11px] text-slate-400 mt-1">Ditampilkan sebagai label badge orange di katalog menu.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        Visibilitas Katalog
                    </label>
                    <label class="flex items-center gap-3 p-2.5 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-50 transition">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                               class="rounded text-red-600 focus:ring-red-500 w-4 h-4">
                        <span class="text-xs font-bold text-slate-700">Tampilkan menu di katalog pelanggan</span>
                    </label>
                    <div class="mt-3">
                        <label for="is_available" class="mb-1.5 block text-xs font-bold text-slate-700">Ketersediaan Menu</label>
                        <select id="is_available" name="is_available" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs font-semibold focus:border-transparent focus:outline-none focus:ring-2 focus:ring-orange-500">
                            <option value="1" @selected(old('is_available', true))>Tersedia</option>
                            <option value="0" @selected(! old('is_available', true))>Habis</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Row 3: Modal (HPP) & Harga Jual (Admin Finance Management) -->
            <div class="p-5 bg-orange-50/60 rounded-2xl border-2 border-orange-200 space-y-4">
                <div class="flex items-center gap-2">
                    <i data-lucide="calculator" class="w-5 h-5 text-orange-600"></i>
                    <h3 class="text-xs font-black uppercase tracking-wider text-orange-950">
                        Manajemen Modal & Keuntungan (Khusus Role Admin)
                    </h3>
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
                                   value="{{ old('modal') }}"
                                   step="500"
                                   min="0"
                                   required
                                   placeholder="Contoh: 14000"
                                   class="w-full pl-10 pr-4 py-2.5 text-sm rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent font-bold text-slate-800 bg-white">
                        </div>
                        <p class="text-[11px] text-slate-500 mt-1">Biaya pokok produksi/pembelian bahan per porsi.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-800 mb-1.5">
                            Harga Jual ke Pembeli <span class="text-red-600">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-2.5 text-xs font-bold text-slate-400">Rp</span>
                            <input type="number"
                                   name="harga_jual"
                                   value="{{ old('harga_jual') }}"
                                   step="500"
                                   min="0"
                                   required
                                   placeholder="Contoh: 22000"
                                   class="w-full pl-10 pr-4 py-2.5 text-sm rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent font-bold text-red-600 bg-white">
                        </div>
                        <p class="text-[11px] text-slate-500 mt-1">Harga resmi yang dibayar oleh customer.</p>
                    </div>
                </div>
            </div>

            <!-- Row 4: Deskripsi -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                    Deskripsi Singkat Menu <span class="text-red-600">*</span>
                </label>
                <textarea name="description"
                          rows="3"
                          required
                          placeholder="Jelaskan kelezatan rasa, isian bahan, dan sensasi kuah keju..."
                          class="w-full text-sm px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent font-normal">{{ old('description') }}</textarea>
            </div>

            <!-- Row 5: Multi-Gambar Upload (FEATURE ADMIN 1) -->
            <div class="space-y-3 pt-2 border-t border-slate-100">
                <div class="flex items-center justify-between">
                    <div>
                        <label class="block text-xs font-black uppercase tracking-wider text-slate-900">
                            Unggah Multi-Gambar Katalog <span class="text-red-600">*</span>
                        </label>
                        <p class="text-[11px] text-slate-500">
                            Pilih lebih dari 1 file gambar sekaligus untuk galeri produk (format: JPG, PNG, WEBP, SVG).
                        </p>
                    </div>
                    <span class="text-xs font-bold text-red-600" x-text="selectedFiles.length + ' Foto Dipilih'"></span>
                </div>

                <div class="border-2 border-dashed border-slate-200 hover:border-red-400 rounded-3xl p-6 text-center bg-slate-50 transition cursor-pointer relative">
                    <input type="file"
                           name="images[]"
                           multiple
                           accept="image/*"
                           required
                           @change="handleFiles($event)"
                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                    <div class="space-y-2 pointer-events-none">
                        <div class="w-12 h-12 bg-white rounded-2xl shadow-sm text-red-600 flex items-center justify-center mx-auto border border-slate-100">
                            <i data-lucide="upload-cloud" class="w-6 h-6"></i>
                        </div>
                        <p class="text-xs font-bold text-slate-800">
                            Klik atau Seret foto-foto menu ke sini
                        </p>
                        <p class="text-[11px] text-slate-400">
                            Mendukung upload multiple image sekaligus. Foto pertama otomatis menjadi foto utama.
                        </p>
                    </div>
                </div>

                <!-- Live Preview Grid of Selected Multi-Images -->
                <template x-if="previews.length > 0">
                    <div class="space-y-2 pt-2">
                        <span class="text-xs font-bold text-slate-700">Preview Foto yang Akan Diunggah:</span>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            <template x-for="(prev, index) in previews" :key="index">
                                <div class="relative rounded-2xl overflow-hidden border border-slate-200 bg-white aspect-square shadow-sm group">
                                    <img :src="prev.url" class="w-full h-full object-cover">
                                    <span x-show="index === 0" class="absolute top-2 left-2 bg-red-600 text-white text-[9px] font-black px-2 py-0.5 rounded shadow">
                                        Foto Utama
                                    </span>
                                    <span class="absolute bottom-1 right-2 bg-slate-900/70 text-white text-[9px] font-bold px-1.5 py-0.5 rounded" x-text="'#' + (index + 1)"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Submit Button (Red Primary) -->
            <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.products.index') }}"
                   class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 font-bold text-xs transition">
                    Batal
                </a>
                <button type="submit"
                        class="px-6 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-black text-xs shadow-md shadow-red-600/30 transition active:scale-95 flex items-center gap-2">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    <span>Simpan & Publikasikan Menu</span>
                </button>
            </div>

        </form>
    </div>

</div>
@endsection

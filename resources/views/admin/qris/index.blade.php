@extends('layouts.admin')

@section('title', 'Kelola QRIS Pembayaran')
@section('page_title', 'Kelola QRIS Pembayaran')

@section('content')
<div class="max-w-4xl space-y-6">
    <div class="bg-gradient-to-r from-red-600 to-orange-500 rounded-3xl p-6 sm:p-8 text-white shadow-xl shadow-red-600/15">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center shrink-0">
                <i data-lucide="qr-code" class="w-6 h-6"></i>
            </div>
            <div>
                <h2 class="text-xl sm:text-2xl font-black">QRIS Pembayaran Toko</h2>
                <p class="mt-1 text-xs sm:text-sm text-orange-50">Gambar ini akan ditampilkan kepada pembeli pada langkah pembayaran checkout.</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <section class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-5">
            <div>
                <h3 class="text-sm font-black text-slate-900">Unggah Gambar QRIS</h3>
                <p class="text-xs text-slate-500 mt-1">Format PNG atau JPG, ukuran maksimal 5MB.</p>
            </div>

            <form action="{{ route('admin.qris.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <label for="qris_image" class="block p-5 rounded-2xl border-2 border-dashed border-orange-200 bg-orange-50/50 text-center">
                    <i data-lucide="upload-cloud" class="w-8 h-8 mx-auto text-orange-500"></i>
                    <span class="block mt-2 text-xs font-bold text-slate-700">Pilih file gambar QRIS</span>
                    <input id="qris_image" name="qris_image" type="file" accept="image/png,image/jpeg,.jpg,.jpeg,.png" required
                           class="block w-full mt-3 text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-red-600 file:text-white hover:file:bg-red-700 cursor-pointer">
                </label>

                <button type="submit" class="w-full py-3 px-4 rounded-xl bg-red-600 hover:bg-red-700 text-white font-black text-xs shadow-md shadow-red-600/20 transition flex items-center justify-center gap-2">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    Simpan QRIS
                </button>
            </form>
        </section>

        <section class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-4">
            <div>
                <h3 class="text-sm font-black text-slate-900">QRIS Saat Ini</h3>
                <p class="text-xs text-slate-500 mt-1">Tersimpan di storage public dan digunakan pada checkout.</p>
            </div>

            @if($qrisPath)
                <div class="min-h-64 rounded-2xl border border-slate-200 bg-slate-50 p-4 flex items-center justify-center">
                    <img src="{{ asset('storage/' . $qrisPath) }}" alt="QRIS Pembayaran Dapur KaTanya" class="max-h-72 max-w-full object-contain rounded-xl">
                </div>
                <p class="text-[11px] text-emerald-700 font-bold flex items-center gap-2">
                    <i data-lucide="circle-check" class="w-4 h-4"></i>
                    QRIS aktif: {{ $qrisPath }}
                </p>
            @else
                <div class="min-h-64 rounded-2xl border border-orange-200 bg-orange-50 p-5 flex flex-col items-center justify-center text-center">
                    <i data-lucide="triangle-alert" class="w-9 h-9 text-orange-500"></i>
                    <p class="mt-3 text-sm font-black text-orange-900">QRIS belum disetel</p>
                    <p class="mt-1 text-xs text-orange-800">Unggah gambar agar pembeli dapat melihat QRIS di halaman checkout.</p>
                </div>
            @endif
        </section>
    </div>
</div>
@endsection

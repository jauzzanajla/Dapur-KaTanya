@extends('layouts.admin')

@section('title', 'Rekap Produksi Harian')
@section('page_title', 'Rekap Produksi / Kebutuhan Bahan')

@section('content')
<div class="space-y-6">
    <section class="bg-gradient-to-r from-red-600 to-orange-500 rounded-3xl p-6 sm:p-8 text-white shadow-xl shadow-red-600/15">
        <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-5">
            <div>
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/20 text-[10px] font-black uppercase tracking-wider">
                    <i data-lucide="chef-hat" class="w-3.5 h-3.5"></i>
                    Persiapan Dapur
                </span>
                <h2 class="text-2xl sm:text-3xl font-black tracking-tight mt-3">Rekap Kebutuhan Produksi Harian</h2>
                <p class="text-xs sm:text-sm text-orange-50 mt-2">Jumlah otomatis dihitung dari seluruh pesanan yang masuk pada tanggal dipilih. Pesanan dibatalkan tidak dihitung.</p>
            </div>

            <form action="{{ route('admin.production.index') }}" method="GET" class="flex flex-wrap items-end gap-2 bg-white/10 p-3 rounded-2xl border border-white/20">
                <label class="text-[10px] font-bold uppercase tracking-wider w-full">Tanggal Pesanan Masuk</label>
                <input type="date" name="date" value="{{ $date }}" required
                       class="text-xs px-3 py-2.5 rounded-xl border border-white/30 bg-white text-slate-800 focus:outline-none focus:ring-2 focus:ring-orange-300">
                <button type="submit" class="px-4 py-2.5 rounded-xl bg-white text-red-600 hover:bg-red-50 text-xs font-black transition">
                    Tampilkan
                </button>
                <a href="{{ route('admin.production.index') }}" class="px-3 py-2.5 rounded-xl bg-red-900/30 hover:bg-red-900/50 text-white text-xs font-bold transition">
                    Hari Ini
                </a>
            </form>
        </div>
    </section>

    <div class="flex flex-wrap items-center gap-2 text-xs">
        <span class="px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-600">Tanggal pesanan masuk: <strong class="text-slate-900">{{ \Carbon\Carbon::parse($date)->format('d M Y') }}</strong></span>
        <span class="px-3 py-2 rounded-xl bg-orange-50 border border-orange-200 text-orange-800"><strong>{{ $prepOrders->count() }}</strong> pesanan masuk</span>
        <span class="px-3 py-2 rounded-xl bg-slate-100 text-slate-600">{{ $prepTotals['paket'] }} Paket Cepat Lulus</span>
    </div>

    <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <article class="bg-white p-5 rounded-3xl border border-red-200 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-[10px] font-black text-red-700 uppercase tracking-wider">Dimsum Mentah / Siap Saji</p>
                <span class="w-9 h-9 rounded-xl bg-red-100 text-red-600 flex items-center justify-center"><i data-lucide="package" class="w-5 h-5"></i></span>
            </div>
            <p class="text-3xl font-black text-slate-900 mt-3">{{ $prepTotals['dimsum'] }} <span class="text-sm text-slate-500">pcs</span></p>
            <p class="text-[11px] text-slate-500 mt-2">{{ $portionTotals['dimsum'] }} porsi × 2 pcs + {{ $prepTotals['paket'] }} paket × 1 pcs</p>
        </article>

        <article class="bg-white p-5 rounded-3xl border border-orange-200 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-[10px] font-black text-orange-700 uppercase tracking-wider">Cireng Mentah / Siap Saji</p>
                <span class="w-9 h-9 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center"><i data-lucide="package" class="w-5 h-5"></i></span>
            </div>
            <p class="text-3xl font-black text-slate-900 mt-3">{{ $prepTotals['cireng'] }} <span class="text-sm text-slate-500">pcs</span></p>
            <p class="text-[11px] text-slate-500 mt-2">{{ $portionTotals['cireng'] }} porsi × 5 pcs + {{ $prepTotals['paket'] }} paket × 4 pcs</p>
        </article>

        <article class="bg-white p-5 rounded-3xl border border-amber-200 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-[10px] font-black text-amber-700 uppercase tracking-wider">Es.T (Esteh)</p>
                <span class="w-9 h-9 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center"><i data-lucide="cup-soda" class="w-5 h-5"></i></span>
            </div>
            <p class="text-3xl font-black text-slate-900 mt-3">{{ $prepTotals['es_teh'] }} <span class="text-sm text-slate-500">cup</span></p>
            <p class="text-[11px] text-slate-500 mt-2">{{ $portionTotals['es_teh'] }} cup pesanan + {{ $prepTotals['paket'] }} cup dari paket</p>
        </article>

        <article class="bg-white p-5 rounded-3xl border border-yellow-200 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-[10px] font-black text-yellow-800 uppercase tracking-wider">S.Kom (Es Coklat)</p>
                <span class="w-9 h-9 rounded-xl bg-yellow-100 text-yellow-800 flex items-center justify-center"><i data-lucide="cup-soda" class="w-5 h-5"></i></span>
            </div>
            <p class="text-3xl font-black text-slate-900 mt-3">{{ $prepTotals['es_coklat'] }} <span class="text-sm text-slate-500">cup</span></p>
            <p class="text-[11px] text-slate-500 mt-2">{{ $portionTotals['es_coklat'] }} porsi × 1 cup</p>
        </article>
    </section>

    <section class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between gap-3">
            <div>
                <h3 class="text-sm font-black text-slate-900">Rincian Menu yang Dipesan</h3>
                <p class="text-xs text-slate-500 mt-1">Jumlah porsi menu sebelum dikonversi menjadi kebutuhan fisik.</p>
            </div>
            <i data-lucide="list-checks" class="w-5 h-5 text-orange-500"></i>
        </div>
        @if(count($menuTotals))
            <div class="divide-y divide-slate-100">
                @foreach($menuTotals as $menuName => $quantity)
                    <div class="px-5 sm:px-6 py-3 flex items-center justify-between gap-4 text-xs">
                        <span class="font-semibold text-slate-700">{{ $menuName }}</span>
                        <span class="shrink-0 px-3 py-1.5 rounded-lg bg-red-50 text-red-700 font-black">{{ $quantity }} porsi</span>
                    </div>
                @endforeach
            </div>
        @else
            <div class="p-10 text-center">
                <i data-lucide="clipboard-x" class="w-8 h-8 mx-auto text-slate-300"></i>
                <p class="text-sm font-bold text-slate-500 mt-2">Belum ada pesanan pickup pada tanggal ini.</p>
            </div>
        @endif
    </section>
</div>
@endsection

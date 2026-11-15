@extends('layouts.app')

@section('title', 'Lacak Pesanan Saya')

@section('content')
<div class="py-12 bg-slate-50 min-h-[80vh]">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        <!-- Header & Search Box -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm text-center space-y-4">
            <div class="w-12 h-12 bg-red-100 text-red-600 rounded-2xl flex items-center justify-center mx-auto">
                <i data-lucide="search" class="w-6 h-6"></i>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900">Lacak Status Pesanan</h1>
            <p class="text-xs sm:text-sm text-slate-500 max-w-md mx-auto">
                Masukkan <strong>Nomor Pesanan</strong> (contoh: DKT-...) atau <strong>Nomor HP Asli</strong> yang Anda gunakan saat checkout.
            </p>

            <form action="{{ route('order.track') }}" method="GET" class="max-w-md mx-auto flex gap-2">
                <input type="text"
                       name="q"
                       value="{{ $query }}"
                       required
                       placeholder="Nomor Pesanan atau Nomor HP..."
                       class="flex-1 text-sm px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent font-medium">
                <button type="submit"
                        class="bg-red-600 hover:bg-red-700 text-white font-bold text-xs px-5 py-3 rounded-xl shadow-md shadow-red-600/20 transition active:scale-95 flex items-center gap-2">
                    <i data-lucide="search" class="w-4 h-4"></i>
                    <span>Cek</span>
                </button>
            </form>
        </div>

        <!-- Search Results -->
        @if($query)
            <div class="space-y-4">
                <div class="flex items-center justify-between text-xs font-bold text-slate-500 px-2">
                    <span>Hasil Pencarian untuk: "<strong class="text-slate-800">{{ $query }}</strong>"</span>
                    <span>Ditemukan: {{ $orders->count() }} pesanan</span>
                </div>

                @if($orders->isEmpty())
                    <div class="bg-white rounded-3xl p-8 border border-slate-200 text-center space-y-3">
                        <i data-lucide="inbox" class="w-10 h-10 text-slate-300 mx-auto"></i>
                        <h3 class="text-base font-bold text-slate-800">Pesanan Tidak Ditemukan</h3>
                        <p class="text-xs text-slate-500 max-w-sm mx-auto">
                            Pastikan nomor pesanan atau nomor telepon yang dimasukkan sudah benar.
                        </p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($orders as $order)
                            <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm hover:shadow-md transition flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div class="space-y-1.5">
                                    <div class="flex items-center gap-2">
                                        <span class="font-mono font-black text-slate-900 text-base">{{ $order->order_number }}</span>
                                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold border {{ $order->status_badge['bg'] }}">
                                            {{ $order->status_badge['label'] }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-500">
                                        Pemesan: <strong>{{ $order->customer_name }}</strong> ({{ $order->customer_phone }}) &bull;
                                        Pickup: <strong>{{ $order->pickup_date->format('d M Y') }}</strong> Jam {{ $order->pickup_time ?: '12:00' }} WIB
                                    </p>
                                    <p class="text-[11px] text-orange-700 font-medium">
                                        📍 Lokasi: {{ $order->pickup_location ?? 'Outlet Utama - Kampus Pusat (Jl. Kampus Raya No. 12)' }}
                                    </p>
                                    <p class="text-xs text-slate-600">
                                        Menu:
                                        @foreach($order->items as $item)
                                            <span class="inline-block bg-slate-100 px-2 py-0.5 rounded text-[11px] mr-1">
                                                {{ $item->product_name }} ({{ $item->quantity }}x)
                                            </span>
                                        @endforeach
                                    </p>
                                </div>

                                <div class="flex sm:flex-col items-center sm:items-end justify-between sm:justify-center gap-2 pt-3 sm:pt-0 border-t sm:border-0 border-slate-100">
                                    <div class="text-left sm:text-right">
                                        <span class="text-[10px] text-slate-400 font-bold uppercase">Total Bayar</span>
                                        <p class="text-base font-black text-red-600">
                                            Rp {{ number_format($order->total_omset, 0, ',', '.') }}
                                        </p>
                                    </div>
                                    <a href="{{ route('order.success', $order->order_number) }}"
                                       class="text-xs font-bold text-red-600 hover:text-red-700 bg-red-50 px-3.5 py-1.5 rounded-xl border border-red-200 hover:bg-red-100 transition">
                                        Lihat Detail &rarr;
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif

    </div>
</div>
@endsection

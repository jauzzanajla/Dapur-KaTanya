@extends('layouts.app')

@section('title', 'Detail Pesanan ' . $order->order_number)

@section('content')
@php
    $orderItemsMessage = $order->items
        ->map(fn ($item) => "- {$item->product_name} x{$item->quantity}")
        ->implode("\n");
    $totalMenuQuantity = $order->items->sum('quantity');
    $whatsappMessage = implode("\n", [
        'Halo Dapur KaTanya, saya ingin konfirmasi pesanan:',
        'Kode Pesanan: '.$order->order_number,
        'Nama Pembeli: '.$order->customer_name,
        'Nama Menu:',
        $orderItemsMessage,
        'Total Menu: '.$totalMenuQuantity,
        'Jumlah Harga: Rp '.number_format($order->total_omset, 0, ',', '.'),
    ]);
    $whatsappUrl = 'https://wa.me/6281337649923?text='.urlencode($whatsappMessage);
@endphp
<div class="py-12 bg-slate-50 min-h-[80vh]"
     x-data="{
        init() {
            // If just placed, clear localStorage cart
            @if(session('success'))
                localStorage.removeItem('dapur_katanya_cart');
                if (typeof this.cart !== 'undefined') {
                    this.cart = [];
                }
            @endif
        }
     }">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Top Status Card -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-md space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-100">
                <div class="space-y-1">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full {{ $order->status_badge['bg'] }} text-xs font-black uppercase tracking-wider border">
                        <span class="w-2 h-2 rounded-full {{ $order->status_badge['dot'] }} animate-ping"></span>
                        <span>{{ $order->status_badge['label'] }}</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                        Pesanan <span class="text-red-600 font-mono">{{ $order->order_number }}</span>
                    </h1>
                    <p class="text-xs text-slate-500">
                        Dibuat pada {{ $order->created_at->format('d M Y, H:i') }} WIB
                    </p>
                </div>
            </div>

            <!-- Details Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 bg-slate-50 p-4 rounded-2xl border border-slate-100 text-xs">
                <div>
                    <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">Nama Pemesan</span>
                    <p class="font-bold text-slate-800 text-sm mt-0.5">{{ $order->customer_name }}</p>
                </div>
                <div>
                    <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">Nomor HP Asli (WA)</span>
                    <p class="font-bold text-red-600 text-sm mt-0.5">{{ $order->customer_phone }}</p>
                </div>
                <div>
                    <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">Tanggal Pengambilan</span>
                    <p class="font-bold text-slate-800 text-sm mt-0.5">{{ $order->pickup_date->format('d M Y') }}</p>
                </div>
                <div>
                    <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">Jam Pickup & Metode</span>
                    <p class="font-bold text-slate-800 text-sm mt-0.5">{{ $order->pickup_time ?: '12:00' }} WIB (Pickup)</p>
                </div>
            </div>

            <!-- Pickup Location Alert -->
            <div class="p-4 bg-orange-50/80 rounded-2xl border border-orange-200 flex items-start gap-3">
                <i data-lucide="map-pin" class="w-5 h-5 text-orange-600 shrink-0 mt-0.5"></i>
                <div class="text-xs">
                    <span class="font-bold text-orange-950 uppercase tracking-wider text-[10px]">Titik Lokasi Pengambilan (Pickup Point):</span>
                    <p class="font-black text-slate-900 text-sm mt-0.5">{{ $order->pickup_location ?? 'Kampus B' }}</p>
                    <p class="text-slate-500 text-[11px] mt-0.5">Silakan tunjukkan nomor pesanan Anda kepada kasir/petugas saat mengambil pesanan.</p>
                </div>
            </div>

            @if($order->notes)
                <div class="p-3.5 bg-amber-50 rounded-xl border border-amber-200 text-xs text-amber-900">
                    <strong>Catatan Pembeli:</strong> {{ $order->notes }}
                </div>
            @endif

            <!-- Order Items Table -->
            <div class="space-y-3">
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-700">Rincian Menu Pesanan</h3>
                <div class="border border-slate-200 rounded-2xl overflow-hidden divide-y divide-slate-100">
                    @foreach($order->items as $item)
                        <div class="p-4 flex items-center justify-between text-xs sm:text-sm">
                            <div class="space-y-0.5">
                                <h4 class="font-bold text-slate-900">{{ $item->product_name }}</h4>
                                <p class="text-xs text-slate-400">
                                    {{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}
                                </p>
                            </div>
                            <span class="font-extrabold text-slate-800">
                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                            </span>
                        </div>
                    @endforeach

                    <!-- Total Row -->
                    <div class="p-4 bg-slate-50 flex items-center justify-between font-black text-base text-slate-900">
                        <span>Total Pembayaran (QRIS)</span>
                        <span class="text-xl text-red-600">Rp {{ number_format($order->total_omset, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Payment Proof Section -->
            <div class="pt-4 border-t border-slate-100 space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-slate-700">Bukti Pembayaran QRIS</h3>
                    <span class="text-xs text-emerald-600 font-semibold flex items-center gap-1">
                        <i data-lucide="check-check" class="w-4 h-4"></i> Telah Diunggah
                    </span>
                </div>

                @if($order->payment_proof)
                    <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/80 flex flex-col sm:flex-row items-center gap-4">
                        <img src="{{ $order->payment_proof_url }}"
                             alt="Bukti Transfer {{ $order->order_number }}"
                             class="w-32 h-32 object-cover rounded-xl border border-slate-200 shadow-sm shrink-0">
                        <div class="space-y-1 text-center sm:text-left text-xs">
                            <p class="font-bold text-slate-800">File Bukti Transfer QRIS</p>
                            <p class="text-slate-500">Tersimpan aman di sistem Dapur KaTanya untuk verifikasi tim kasir.</p>
                            <a href="{{ $order->payment_proof_url }}" target="_blank"
                               class="inline-block mt-2 text-xs font-bold text-red-600 hover:underline">
                                Lihat Bukti Ukuran Penuh &rarr;
                            </a>
                        </div>
                    </div>
                @else
                    <p class="text-xs text-slate-400">Tidak ada file bukti pembayaran.</p>
                @endif
            </div>

            <!-- Action Buttons -->
            <div class="pt-6 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                <a href="{{ route('catalog') }}"
                   class="text-xs font-bold text-slate-600 hover:text-red-600 flex items-center gap-1.5 transition">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    <span>Kembali ke Katalog Menu</span>
                </a>
                <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer"
                   class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 py-3 text-xs font-bold text-white transition hover:bg-emerald-700">
                    <i data-lucide="message-circle" class="h-4 w-4"></i>
                    Konfirmasi via WhatsApp
                </a>
                <a href="{{ route('order.track', ['q' => $order->customer_phone]) }}"
                   class="text-xs font-bold text-orange-600 hover:underline">
                    Lihat Semua Riwayat Pesanan Saya &rarr;
                </a>
            </div>

        </div>

    </div>
</div>
@endsection

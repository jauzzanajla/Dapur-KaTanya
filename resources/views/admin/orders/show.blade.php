@extends('layouts.admin')

@section('title', 'Pesanan ' . $order->order_number)
@section('page_title', 'Rincian Pesanan & Laba: ' . $order->order_number)

@section('content')
<div class="max-w-5xl space-y-6">

    <!-- Breadcrumb & Back -->
    <div class="flex items-center gap-2 text-xs font-bold text-slate-500">
        <a href="{{ route('admin.orders.index') }}" class="hover:text-red-600 transition flex items-center gap-1">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Kembali ke Daftar Pesanan</span>
        </a>
    </div>

    <!-- Top Card: Header & Status Updater -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-100">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider border {{ $order->status_badge['bg'] }}">
                        {{ $order->status_badge['label'] }}
                    </span>
                    <span class="text-xs text-slate-400">QRIS Payment</span>
                </div>
                <h2 class="text-2xl font-black text-slate-900 font-mono tracking-tight">{{ $order->order_number }}</h2>
                <p class="text-xs text-slate-500">Dipesan pada {{ $order->created_at->format('d F Y, H:i') }} WIB</p>
            </div>

            <!-- Status Changer Dropdown -->
            <form action="{{ route('admin.orders.status', $order) }}" method="POST" class="flex items-center gap-2">
                @csrf
                <label class="text-xs font-bold text-slate-600 shrink-0">Ubah Status:</label>
                <select name="status" onchange="this.form.submit()"
                        class="text-xs font-bold rounded-xl border border-slate-300 px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-red-500 font-medium">
                    <option value="menunggu_verifikasi" {{ $order->status === 'menunggu_verifikasi' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                    <option value="diproses" {{ $order->status === 'diproses' ? 'selected' : '' }}>Diproses (Masak)</option>
                    <option value="siap_diambil" {{ $order->status === 'siap_diambil' ? 'selected' : '' }}>Siap Diambil (Pickup)</option>
                    <option value="selesai" {{ $order->status === 'selesai' ? 'selected' : '' }}>Pesanan Selesai</option>
                    <option value="dibatalkan" {{ $order->status === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </form>
        </div>

        <!-- Customer & Pickup Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 bg-slate-50 p-5 rounded-2xl border border-slate-100 text-xs">
            <div class="space-y-1">
                <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">Nama Pembeli</span>
                <p class="font-bold text-slate-900 text-sm">{{ $order->customer_name }}</p>
            </div>

            <div class="space-y-1">
                <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">Nomor HP Asli (WhatsApp)</span>
                <div class="flex items-center gap-2">
                    <p class="font-bold text-red-600 text-sm font-mono">{{ $order->customer_phone }}</p>
                    @php
                        $cleanPhone = preg_replace('/[^0-9]/', '', $order->customer_phone);
                        if (str_starts_with($cleanPhone, '0')) {
                            $cleanPhone = '62' . substr($cleanPhone, 1);
                        }
                    @endphp
                    <a href="https://wa.me/{{ $cleanPhone }}" target="_blank"
                       class="text-[10px] font-bold px-2 py-0.5 rounded bg-emerald-600 text-white flex items-center gap-1 shadow-sm">
                        <i data-lucide="message-circle" class="w-3 h-3"></i> Chat WA
                    </a>
                </div>
            </div>

            <div class="space-y-1">
                <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">Jadwal Pengambilan (Pickup)</span>
                <p class="font-bold text-slate-900 text-sm">
                    {{ $order->pickup_date->format('d M Y') }} &bull; Jam {{ $order->pickup_time ?: '-' }} WIB
                </p>
            </div>
        </div>

        @if($order->notes)
            <div class="p-3 bg-amber-50 rounded-xl border border-amber-200 text-xs text-amber-900">
                <strong>Catatan Pembeli:</strong> {{ $order->notes }}
            </div>
        @endif

        <!-- Profit Analysis Table (Role Admin Only) -->
        <div class="space-y-3 pt-2">
            <div class="flex items-center justify-between">
                <h3 class="text-xs font-black uppercase tracking-wider text-slate-900 flex items-center gap-2">
                    <i data-lucide="pie-chart" class="w-4 h-4 text-orange-500"></i>
                    <span>Rincian Menu & Kalkulasi Laba (Role Admin)</span>
                </h3>
                <span class="text-[11px] text-emerald-600 font-bold">
                    Laba Bersih: Rp {{ number_format($order->net_profit, 0, ',', '.') }}
                </span>
            </div>

            <div class="border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-50 text-slate-400 uppercase tracking-wider font-bold border-b border-slate-200">
                            <th class="py-3 px-4">Menu</th>
                            <th class="py-3 px-4 text-center">Qty</th>
                            <th class="py-3 px-4">Harga Jual</th>
                            <th class="py-3 px-4">Modal (HPP)</th>
                            <th class="py-3 px-4">Subtotal Omset</th>
                            <th class="py-3 px-4 text-right">Keuntungan Bersih</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($order->items as $item)
                            <tr class="hover:bg-slate-50/50">
                                <td class="py-3.5 px-4 font-bold text-slate-900">
                                    {{ $item->product_name }}
                                </td>
                                <td class="py-3.5 px-4 text-center font-bold text-slate-700">
                                    {{ $item->quantity }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-600">
                                    Rp {{ number_format($item->price, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-500 font-medium">
                                    Rp {{ number_format($item->modal, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 font-bold text-slate-900">
                                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-black text-emerald-600">
                                    +Rp {{ number_format($item->subtotal_profit, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-slate-50 border-t-2 border-slate-200 font-bold">
                        <tr>
                            <td colspan="4" class="py-3.5 px-4 text-slate-700 uppercase tracking-wider">
                                Total Akumulasi Transaksi
                            </td>
                            <td class="py-3.5 px-4 font-black text-red-600 text-sm">
                                Rp {{ number_format($order->total_omset, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-black text-emerald-600 text-sm">
                                Rp {{ number_format($order->net_profit, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Payment Proof Viewer -->
        <div class="pt-6 border-t border-slate-100 space-y-3">
            <h3 class="text-xs font-black uppercase tracking-wider text-slate-900">
                Bukti Transfer QRIS Terverifikasi
            </h3>

            @if($order->payment_proof)
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 flex flex-col sm:flex-row items-center gap-6">
                    <img src="{{ $order->payment_proof_url }}"
                         alt="Bukti Transfer"
                         class="w-48 h-48 object-cover rounded-xl border border-slate-200 shadow-sm shrink-0">
                    <div class="space-y-2 text-xs">
                        <p class="font-bold text-slate-800 text-sm">File Struk QRIS Pembeli</p>
                        <p class="text-slate-500">
                            Pastikan nominal yang tertera pada struk sesuai dengan Total Pembayaran <strong>Rp {{ number_format($order->total_omset, 0, ',', '.') }}</strong>.
                        </p>
                        <a href="{{ $order->payment_proof_url }}" target="_blank"
                           class="inline-flex items-center gap-1.5 px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-bold text-xs rounded-xl shadow-sm transition">
                            <i data-lucide="zoom-in" class="w-4 h-4"></i>
                            <span>Buka Ukuran Penuh</span>
                        </a>
                    </div>
                </div>
            @else
                <p class="text-xs text-slate-400">Tidak ada bukti transfer yang diunggah.</p>
            @endif
        </div>

    </div>

</div>
@endsection

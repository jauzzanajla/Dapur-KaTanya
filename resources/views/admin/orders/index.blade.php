@extends('layouts.admin')

@section('title', 'Kelola Pesanan & Verifikasi QRIS')
@section('page_title', 'Kelola Pesanan Masuk & Verifikasi Pembayaran')

@section('content')
<div class="space-y-6" x-data="{ proofModal: { open: false, url: '', orderNo: '' } }">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-black text-slate-900 tracking-tight">Daftar Transaksi Pesanan</h2>
            <p class="text-xs text-slate-500">
                Verifikasi bukti transfer QRIS, hubungi nomor HP pemesan, dan perbarui status pesanan pickup.
            </p>
        </div>
    </div>

    <!-- Status Tabs -->
    <div class="flex flex-wrap items-center gap-2 border-b border-slate-200 pb-3">
        <a href="{{ route('admin.orders.index') }}"
           class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ !$status ? 'bg-red-600 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            <span>Semua</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ !$status ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-700' }}">{{ $counts['all'] }}</span>
        </a>

        <a href="{{ route('admin.orders.index', ['status' => 'menunggu_verifikasi', 'search' => $search]) }}"
           class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ $status === 'menunggu_verifikasi' ? 'bg-orange-500 text-white shadow-sm' : 'bg-white text-orange-700 hover:bg-orange-50 border border-orange-200' }}">
            <span>Menunggu Verifikasi</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-orange-600 text-white">{{ $counts['menunggu_verifikasi'] }}</span>
        </a>

        <a href="{{ route('admin.orders.index', ['status' => 'diproses', 'search' => $search]) }}"
           class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ $status === 'diproses' ? 'bg-blue-600 text-white shadow-sm' : 'bg-white text-blue-700 hover:bg-blue-50 border border-blue-200' }}">
            <span>Diproses</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-blue-100 text-blue-800">{{ $counts['diproses'] }}</span>
        </a>

        <a href="{{ route('admin.orders.index', ['status' => 'siap_diambil', 'search' => $search]) }}"
           class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ $status === 'siap_diambil' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white text-emerald-700 hover:bg-emerald-50 border border-emerald-200' }}">
            <span>Siap Diambil</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-emerald-100 text-emerald-800">{{ $counts['siap_diambil'] }}</span>
        </a>

        <a href="{{ route('admin.orders.index', ['status' => 'selesai', 'search' => $search]) }}"
           class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ $status === 'selesai' ? 'bg-slate-800 text-white shadow-sm' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200' }}">
            <span>Selesai</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-slate-100 text-slate-700">{{ $counts['selesai'] }}</span>
        </a>

        <a href="{{ route('admin.orders.index', ['status' => 'dibatalkan', 'search' => $search]) }}"
           class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ $status === 'dibatalkan' ? 'bg-red-600 text-white shadow-sm' : 'bg-white text-red-700 hover:bg-red-50 border border-red-200' }}">
            <span>Dibatalkan</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-red-100 text-red-800">{{ $counts['dibatalkan'] }}</span>
        </a>
    </div>

    <!-- Search & Filter Form -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
        <form action="{{ route('admin.orders.index') }}" method="GET" class="flex flex-wrap items-center gap-3 w-full md:w-auto">
            @if($status)
                <input type="hidden" name="status" value="{{ $status }}">
            @endif

            <div class="relative w-full sm:w-64">
                <input type="text"
                       name="search"
                       value="{{ $search }}"
                       placeholder="No. pesanan, nama, no HP..."
                       class="w-full pl-9 pr-4 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-red-500 font-medium">
                <i data-lucide="search" class="w-3.5 h-3.5 text-slate-400 absolute left-3 top-2.5"></i>
            </div>

            <div class="flex items-center gap-2">
                <input type="date"
                       name="date"
                       value="{{ $date }}"
                       title="Filter Tanggal Pickup"
                       class="text-xs px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-red-500 bg-white">
                <button type="submit" class="px-3.5 py-2 bg-slate-800 text-white rounded-xl text-xs font-bold hover:bg-slate-700 transition">
                    Filter
                </button>
                @if($search || $date)
                    <a href="{{ route('admin.orders.index', ['status' => $status]) }}" class="text-xs text-red-600 hover:underline font-bold">
                        Reset
                    </a>
                @endif
            </div>
        </form>

        <a href="{{ route('admin.sales.export') }}"
           class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-sm transition active:scale-95 flex items-center gap-2">
            <i data-lucide="download" class="w-4 h-4"></i>
            <span>Ekspor CSV Laporan</span>
        </a>
    </div>

    <!-- Kitchen Preparation Summary -->
    <section class="bg-white rounded-3xl border border-orange-200 shadow-sm overflow-hidden">
        <div class="p-5 sm:p-6 bg-gradient-to-r from-orange-50 to-red-50 border-b border-orange-100">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <h2 class="text-sm sm:text-base font-black text-slate-900 flex items-center gap-2">
                        <i data-lucide="clipboard-list" class="w-5 h-5 text-red-600"></i>
                        Ringkasan Persiapan Menu
                    </h2>
                    <p class="text-xs text-slate-600 mt-1">
                        Total pesanan masuk {{ \Carbon\Carbon::parse($prepDate)->format('d M Y') }} dari {{ $prepOrders->count() }} pesanan, tidak termasuk yang dibatalkan.
                    </p>
                </div>
                <span class="text-[11px] font-bold text-orange-800 bg-orange-100 px-3 py-1.5 rounded-full">
                    Paket Cepat Lulus: {{ $prepTotals['paket'] }} porsi
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3 p-5 sm:p-6">
            <div class="p-4 rounded-2xl bg-red-50 border border-red-100">
                <p class="text-[10px] font-black text-red-700 uppercase tracking-wider">Dimsum yang disiapkan</p>
                <p class="text-2xl font-black text-slate-900 mt-1">{{ $prepTotals['dimsum'] }} <span class="text-sm text-slate-500">pcs</span></p>
                <p class="text-[11px] text-slate-500 mt-1">{{ $portionTotals['dimsum'] }} porsi &times; 2 pcs + {{ $prepTotals['paket'] }} paket &times; 1 pcs</p>
            </div>
            <div class="p-4 rounded-2xl bg-orange-50 border border-orange-100">
                <p class="text-[10px] font-black text-orange-700 uppercase tracking-wider">Cireng yang disiapkan</p>
                <p class="text-2xl font-black text-slate-900 mt-1">{{ $prepTotals['cireng'] }} <span class="text-sm text-slate-500">pcs</span></p>
                <p class="text-[11px] text-slate-500 mt-1">{{ $portionTotals['cireng'] }} porsi &times; 5 pcs + {{ $prepTotals['paket'] }} paket &times; 4 pcs</p>
            </div>
            <div class="p-4 rounded-2xl bg-amber-50 border border-amber-100">
                <p class="text-[10px] font-black text-amber-700 uppercase tracking-wider">Es Teh (Es.T) disiapkan</p>
                <p class="text-2xl font-black text-slate-900 mt-1">{{ $prepTotals['es_teh'] }} <span class="text-sm text-slate-500">gelas</span></p>
                <p class="text-[11px] text-slate-500 mt-1">{{ $portionTotals['es_teh'] }} gelas pesanan + {{ $prepTotals['paket'] }} Es Teh dari paket</p>
            </div>
            <div class="p-4 rounded-2xl bg-yellow-50 border border-yellow-100">
                <p class="text-[10px] font-black text-yellow-800 uppercase tracking-wider">Es Coklat (S.Kom) disiapkan</p>
                <p class="text-2xl font-black text-slate-900 mt-1">{{ $prepTotals['es_coklat'] }} <span class="text-sm text-slate-500">gelas</span></p>
                <p class="text-[11px] text-slate-500 mt-1">{{ $portionTotals['es_coklat'] }} porsi S.Kom &times; 1 gelas</p>
            </div>
        </div>

        <div class="px-5 sm:px-6 pb-5 sm:pb-6">
            <h3 class="text-[10px] font-black uppercase tracking-wider text-slate-500 mb-2">Jumlah porsi menu dari pesanan yang masuk tanggal tersebut</h3>
            @if(count($menuTotals))
                <div class="flex flex-wrap gap-2">
                    @foreach($menuTotals as $menuName => $quantity)
                        <span class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs">
                            <span class="font-semibold text-slate-700">{{ $menuName }}</span>
                            <strong class="text-red-600">{{ $quantity }} porsi</strong>
                        </span>
                    @endforeach
                </div>
            @else
                <p class="text-xs text-slate-400">Belum ada pesanan untuk tanggal ini.</p>
            @endif
        </div>
    </section>

    <!-- Orders Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-400 uppercase tracking-wider font-bold border-b border-slate-200">
                        <th class="py-3.5 px-4">No. Pesanan</th>
                        <th class="py-3.5 px-4">Nama Pembeli & HP (WA)</th>
                        <th class="py-3.5 px-4">Jadwal Pickup</th>
                        <th class="py-3.5 px-4">Menu Dipesan</th>
                        <th class="py-3.5 px-4">Total Omset</th>
                        <th class="py-3.5 px-4">Bukti QRIS</th>
                        <th class="py-3.5 px-4">Ubah Status</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($orders as $order)
                        <tr class="hover:bg-slate-50/70 transition">
                            <!-- Order Number -->
                            <td class="py-4 px-4 font-mono font-bold text-slate-900">
                                <a href="{{ route('admin.orders.show', $order) }}" class="text-red-600 hover:underline">
                                    {{ $order->order_number }}
                                </a>
                                <div class="text-[10px] text-slate-400 font-sans">
                                    {{ $order->created_at->format('d/m/Y H:i') }}
                                </div>
                            </td>

                            <!-- Customer & Mandatory Phone Number -->
                            <td class="py-4 px-4">
                                <span class="font-extrabold text-slate-800 text-sm block">{{ $order->customer_name }}</span>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="font-mono text-red-600 font-bold">{{ $order->customer_phone }}</span>
                                    @php
                                        $cleanPhone = preg_replace('/[^0-9]/', '', $order->customer_phone);
                                        if (str_starts_with($cleanPhone, '0')) {
                                            $cleanPhone = '62' . substr($cleanPhone, 1);
                                        }
                                        $waMsg = urlencode("Halo {$order->customer_name}, kami dari Dapur KaTanya mengonfirmasi pesanan Anda {$order->order_number}.");
                                    @endphp
                                    <a href="https://wa.me/{{ $cleanPhone }}?text={{ $waMsg }}" target="_blank"
                                       title="Chat WhatsApp Pembeli"
                                       class="text-emerald-600 hover:text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded text-[10px] font-bold flex items-center gap-1 border border-emerald-200">
                                        <i data-lucide="message-circle" class="w-3 h-3"></i> WA
                                    </a>
                                </div>
                            </td>

                            <!-- Pickup Date & Time -->
                            <td class="py-4 px-4">
                                <div class="font-bold text-slate-800">{{ $order->pickup_date->format('d/m/Y') }}</div>
                                <div class="text-[11px] text-slate-500 font-medium">Jam {{ $order->pickup_time ?: '-' }} WIB</div>
                                <div class="text-[10px] text-orange-600 font-semibold mt-0.5 line-clamp-1 max-w-[180px]" title="{{ $order->pickup_location }}">
                                    📍 {{ $order->pickup_location ?? 'Outlet Utama - Kampus Pusat' }}
                                </div>
                            </td>

                            <!-- Order Items -->
                            <td class="py-4 px-4">
                                <div class="space-y-0.5 max-w-xs">
                                    @foreach($order->items as $it)
                                        <div class="text-[11px] text-slate-700 font-medium">
                                            &bull; {{ $it->product_name }} <span class="text-slate-400 font-bold">({{ $it->quantity }}x)</span>
                                        </div>
                                    @endforeach
                                </div>
                            </td>

                            <!-- Total Omset -->
                            <td class="py-4 px-4 font-black text-slate-900 text-sm">
                                Rp {{ number_format($order->total_omset, 0, ',', '.') }}
                            </td>

                            <!-- Payment Proof Preview -->
                            <td class="py-4 px-4">
                                @if($order->payment_proof)
                                    <button type="button"
                                            @click="proofModal.open = true; proofModal.url = '{{ $order->payment_proof_url }}'; proofModal.orderNo = '{{ $order->order_number }}'"
                                            class="flex items-center gap-1.5 px-2.5 py-1 bg-red-50 hover:bg-red-100 text-red-700 rounded-lg text-[11px] font-bold border border-red-200 transition">
                                        <i data-lucide="file-text" class="w-3.5 h-3.5"></i>
                                        <span>Cek Bukti</span>
                                    </button>
                                @else
                                    <span class="text-slate-400 text-[11px]">-</span>
                                @endif
                            </td>

                            <!-- Status Dropdown Form -->
                            <td class="py-4 px-4">
                                <form action="{{ route('admin.orders.status', $order) }}" method="POST">
                                    @csrf
                                    <select name="status"
                                            onchange="this.form.submit()"
                                            class="text-[11px] font-bold rounded-lg border px-2 py-1 focus:outline-none transition
                                                   {{ $order->status === 'menunggu_verifikasi' ? 'bg-orange-50 border-orange-300 text-orange-800' : '' }}
                                                   {{ $order->status === 'diproses' ? 'bg-blue-50 border-blue-300 text-blue-800' : '' }}
                                                   {{ $order->status === 'siap_diambil' ? 'bg-emerald-50 border-emerald-300 text-emerald-800' : '' }}
                                                   {{ $order->status === 'selesai' ? 'bg-slate-100 border-slate-300 text-slate-700' : '' }}
                                                   {{ $order->status === 'dibatalkan' ? 'bg-red-50 border-red-300 text-red-800' : '' }}">
                                        <option value="menunggu_verifikasi" {{ $order->status === 'menunggu_verifikasi' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                                        <option value="diproses" {{ $order->status === 'diproses' ? 'selected' : '' }}>Diproses (Masak)</option>
                                        <option value="siap_diambil" {{ $order->status === 'siap_diambil' ? 'selected' : '' }}>Siap Diambil (Pickup)</option>
                                        <option value="selesai" {{ $order->status === 'selesai' ? 'selected' : '' }}>Pesanan Selesai</option>
                                        <option value="dibatalkan" {{ $order->status === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                                    </select>
                                </form>
                            </td>

                            <!-- Detail Action -->
                            <td class="py-4 px-4 text-right">
                                <a href="{{ route('admin.orders.show', $order) }}"
                                   class="text-xs font-bold text-slate-600 hover:text-red-600 bg-slate-50 hover:bg-red-50 px-2.5 py-1.5 rounded-xl border border-slate-200 transition">
                                    Rincian
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400">
                                Tidak ada transaksi pesanan yang sesuai filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Bukti Transfer QRIS -->
    <div x-cloak
         x-show="proofModal.open"
         @keydown.escape.window="proofModal.open = false"
         class="fixed inset-0 z-50 overflow-y-auto"
         role="dialog"
         aria-modal="true">

        <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-md" @click="proofModal.open = false"></div>

        <div class="flex items-center justify-center min-h-screen p-4 text-center">
            <div class="relative bg-white rounded-3xl max-w-lg w-full p-6 text-left shadow-2xl overflow-hidden border border-slate-100 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <h3 class="text-base font-black text-slate-900">Bukti Pembayaran QRIS</h3>
                        <p class="text-xs text-slate-500 font-mono" x-text="'Pesanan: ' + proofModal.orderNo"></p>
                    </div>
                    <button @click="proofModal.open = false" class="text-slate-400 hover:text-slate-600 p-1">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <div class="bg-slate-50 rounded-2xl p-2 border border-slate-200 flex items-center justify-center max-h-[70vh] overflow-auto">
                    <img :src="proofModal.url" alt="Bukti Transfer" class="max-w-full max-h-[65vh] object-contain rounded-xl">
                </div>

                <div class="flex items-center justify-between pt-2">
                    <a :href="proofModal.url" target="_blank"
                       class="text-xs font-bold text-red-600 hover:underline flex items-center gap-1">
                        <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                        <span>Buka Tab Baru</span>
                    </a>
                    <button type="button" @click="proofModal.open = false"
                            class="px-4 py-2 bg-slate-800 text-white font-bold text-xs rounded-xl hover:bg-slate-700">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

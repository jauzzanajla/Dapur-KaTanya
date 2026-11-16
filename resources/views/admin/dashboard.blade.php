@extends('layouts.admin')

@section('title', 'Dashboard Overview')
@section('page_title', 'Dashboard Ringkasan Operasional')

@section('content')
<div class="space-y-8">

    <!-- Top Action Banner -->
    <div class="bg-gradient-to-r from-red-600 via-red-500 to-orange-500 rounded-3xl p-6 sm:p-8 text-white shadow-xl shadow-red-600/15 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="space-y-2">
            <span class="inline-block px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-xs font-black uppercase tracking-wider text-white">
                F&B Management System
            </span>
            <h2 class="text-2xl sm:text-3xl font-black tracking-tight">
                Selamat Datang di Dapur KaTanya!
            </h2>
            <p class="text-xs sm:text-sm text-red-100 max-w-xl">
                Kelola menu & multi-gambar, pantau modal vs omset, periksa bukti transfer QRIS, dan rekap keuntungan bersih secara otomatis.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('admin.products.create') }}"
               class="px-4 py-2.5 bg-white text-red-600 hover:bg-red-50 font-black text-xs rounded-xl shadow-md transition active:scale-95 flex items-center gap-2">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Tambah Menu Baru</span>
            </a>
            <a href="{{ route('admin.qris.index') }}"
               class="px-4 py-2.5 bg-white text-orange-700 hover:bg-orange-50 font-black text-xs rounded-xl shadow-md transition active:scale-95 flex items-center gap-2">
                <i data-lucide="qr-code" class="w-4 h-4"></i>
                <span>Kelola QRIS</span>
            </a>
            <a href="{{ route('admin.sales.index') }}"
               class="px-4 py-2.5 bg-red-900/40 hover:bg-red-900/60 text-white font-bold text-xs rounded-xl border border-white/20 transition active:scale-95 flex items-center gap-2">
                <i data-lucide="bar-chart-3" class="w-4 h-4"></i>
                <span>Rekap Penjualan & Grafik</span>
            </a>
            <a href="{{ route('admin.production.index') }}"
               class="px-4 py-2.5 bg-orange-100 hover:bg-orange-200 text-orange-900 font-bold text-xs rounded-xl border border-orange-200 transition active:scale-95 flex items-center gap-2">
                <i data-lucide="clipboard-list" class="w-4 h-4"></i>
                <span>Rekap Produksi / Kebutuhan Bahan</span>
            </a>
            <a href="{{ route('admin.pickup-schedules.index') }}"
               class="px-4 py-2.5 bg-white text-red-700 hover:bg-red-50 font-bold text-xs rounded-xl border border-red-100 transition active:scale-95 flex items-center gap-2">
                <i data-lucide="calendar-days" class="w-4 h-4"></i>
                <span>Jadwal Pickup / Open PO</span>
            </a>
        </div>
    </div>

    <!-- Financial KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Card 1: Omset Hari Ini -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Omset Hari Ini</span>
                <div class="w-9 h-9 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center">
                    <i data-lucide="coins" class="w-5 h-5"></i>
                </div>
            </div>
            <div>
                <p class="text-2xl font-black text-slate-900">
                    Rp {{ number_format($todayOmset, 0, ',', '.') }}
                </p>
                <p class="text-[11px] text-emerald-600 font-semibold mt-1">
                    Laba kotor hari ini: Rp {{ number_format($todayProfit, 0, ',', '.') }}
                </p>
            </div>
        </div>

        <!-- Card 2: Total Omset Keseluruhan -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Omset</span>
                <div class="w-9 h-9 rounded-xl bg-red-100 text-red-600 flex items-center justify-center">
                    <i data-lucide="trending-up" class="w-5 h-5"></i>
                </div>
            </div>
            <div>
                <p class="text-2xl font-black text-red-600">
                    Rp {{ number_format($totalOmset, 0, ',', '.') }}
                </p>
                <p class="text-[11px] text-slate-500 font-medium mt-1">
                    Akumulasi seluruh pesanan aktif
                </p>
            </div>
        </div>

        <!-- Card 3: Total Pengeluaran Bahan Baku -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Bahan Baku (CSV)</span>
                <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center">
                    <i data-lucide="shopping-basket" class="w-5 h-5"></i>
                </div>
            </div>
            <div>
                <p class="text-2xl font-black text-slate-900">
                    Rp {{ number_format($totalBahanBaku, 0, ',', '.') }}
                </p>
                <a href="{{ route('admin.raw-materials.index') }}" class="text-[11px] text-orange-600 hover:underline font-semibold mt-1 inline-block">
                    Kelola / Impor CSV Stok &rarr;
                </a>
            </div>
        </div>

        <!-- Card 4: Keuntungan Bersih (Net Profit) -->
        <div class="bg-white p-5 rounded-3xl border-2 border-emerald-200 shadow-sm space-y-3 bg-emerald-50/20">
            <div class="flex items-center justify-between">
                <span class="text-xs font-black text-emerald-900 uppercase tracking-wider">Keuntungan Bersih</span>
                <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center">
                    <i data-lucide="wallet" class="w-5 h-5"></i>
                </div>
            </div>
            <div>
                <p class="text-2xl font-black text-emerald-600">
                    Rp {{ number_format($netProfit, 0, ',', '.') }}
                </p>
                <p class="text-[11px] text-emerald-700 font-semibold mt-1">
                    Omset - Modal HPP - Bahan Baku
                </p>
            </div>
        </div>
    </div>

    <!-- Quick Status Alerts -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <a href="{{ route('admin.orders.index', ['status' => 'menunggu_verifikasi']) }}"
           class="p-4 bg-white rounded-2xl border border-slate-200 hover:border-orange-300 transition shadow-sm flex items-center justify-between group">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center font-bold">
                    <i data-lucide="alert-circle" class="w-5 h-5"></i>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-slate-800">Menunggu Verifikasi QRIS</h4>
                    <p class="text-[11px] text-slate-500">Periksa bukti transfer pembeli</p>
                </div>
            </div>
            <span class="text-lg font-black text-orange-600 group-hover:scale-110 transition-transform">
                {{ $pendingCount }}
            </span>
        </a>

        <a href="{{ route('admin.orders.index', ['status' => 'diproses']) }}"
           class="p-4 bg-white rounded-2xl border border-slate-200 hover:border-blue-300 transition shadow-sm flex items-center justify-between group">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center font-bold">
                    <i data-lucide="flame" class="w-5 h-5"></i>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-slate-800">Sedang Dimasak / Diproses</h4>
                    <p class="text-[11px] text-slate-500">Persiapan menu hangat</p>
                </div>
            </div>
            <span class="text-lg font-black text-blue-600 group-hover:scale-110 transition-transform">
                {{ $processingCount }}
            </span>
        </a>

        <a href="{{ route('admin.orders.index', ['status' => 'siap_diambil']) }}"
           class="p-4 bg-white rounded-2xl border border-slate-200 hover:border-emerald-300 transition shadow-sm flex items-center justify-between group">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold">
                    <i data-lucide="check-circle" class="w-5 h-5"></i>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-slate-800">Siap Diambil (Pickup)</h4>
                    <p class="text-[11px] text-slate-500">Tersedia di counter outlet</p>
                </div>
            </div>
            <span class="text-lg font-black text-emerald-600 group-hover:scale-110 transition-transform">
                {{ $readyPickupCount }}
            </span>
        </a>
    </div>

    <!-- Two Columns: Recent Orders & Recent Raw Material Purchases -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

        <!-- Left 8 Cols: Recent Orders -->
        <div class="lg:col-span-8 bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <i data-lucide="clock" class="w-5 h-5 text-red-600"></i>
                    <h3 class="text-sm font-black uppercase tracking-wider text-slate-900">Pesanan Masuk Terbaru</h3>
                </div>
                <a href="{{ route('admin.orders.index') }}" class="text-xs font-bold text-red-600 hover:underline">
                    Lihat Semua &rarr;
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-slate-400 uppercase tracking-wider font-bold border-b border-slate-100">
                            <th class="py-3 px-2">No. Pesanan</th>
                            <th class="py-3 px-2">Pembeli & No HP</th>
                            <th class="py-3 px-2">Jadwal Pickup</th>
                            <th class="py-3 px-2">Total Omset</th>
                            <th class="py-3 px-2">Status</th>
                            <th class="py-3 px-2 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentOrders as $order)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3 px-2 font-mono font-bold text-slate-900">
                                    {{ $order->order_number }}
                                </td>
                                <td class="py-3 px-2">
                                    <span class="font-bold text-slate-800">{{ $order->customer_name }}</span>
                                    <div class="text-[11px] text-red-600 font-mono">{{ $order->customer_phone }}</div>
                                </td>
                                <td class="py-3 px-2 text-slate-600">
                                    {{ $order->pickup_date->format('d/m/Y') }} ({{ $order->pickup_time ?: '-' }})
                                </td>
                                <td class="py-3 px-2 font-extrabold text-slate-900">
                                    Rp {{ number_format($order->total_omset, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-2">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $order->status_badge['bg'] }}">
                                        {{ $order->status_badge['label'] }}
                                    </span>
                                </td>
                                <td class="py-3 px-2 text-right">
                                    <a href="{{ route('admin.orders.show', $order) }}"
                                       class="text-xs font-bold text-red-600 hover:bg-red-50 px-2.5 py-1 rounded-lg border border-red-200 transition">
                                        Periksa
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-400">Belum ada pesanan masuk.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right 4 Cols: Quick Actions & Materials -->
        <div class="lg:col-span-4 space-y-6">

            <!-- Products Summary Card -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-sm font-black uppercase tracking-wider text-slate-900">Menu Dapur KaTanya</h3>
                    <span class="text-xs font-bold text-orange-600">{{ $activeProducts }} Aktif</span>
                </div>
                <p class="text-xs text-slate-500">
                    Total {{ $totalProducts }} menu terdaftar dengan multi-gambar dan pengaturan HPP/Modal.
                </p>
                <div class="space-y-2 pt-1">
                    <a href="{{ route('admin.products.index') }}"
                       class="w-full flex items-center justify-between px-3.5 py-2.5 bg-slate-50 hover:bg-slate-100 rounded-xl text-xs font-bold text-slate-700 transition">
                        <span>Kelola Menu & HPP</span>
                        <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400"></i>
                    </a>
                    <a href="{{ route('admin.products.create') }}"
                       class="w-full flex items-center justify-between px-3.5 py-2.5 bg-red-50 hover:bg-red-100 rounded-xl text-xs font-bold text-red-700 transition">
                        <span>+ Tambah Menu Baru</span>
                        <i data-lucide="plus" class="w-4 h-4 text-red-600"></i>
                    </a>
                </div>
            </div>

            <!-- Recent Expenses from Bahan Baku -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-sm font-black uppercase tracking-wider text-slate-900">Pengeluaran Bahan Baku</h3>
                    <a href="{{ route('admin.raw-materials.index') }}" class="text-xs font-bold text-orange-600 hover:underline">
                        Impor CSV &rarr;
                    </a>
                </div>

                <div class="space-y-3">
                    @forelse($recentExpenses as $exp)
                        <div class="flex items-center justify-between text-xs">
                            <div class="space-y-0.5">
                                <p class="font-bold text-slate-800">{{ $exp->material_name }}</p>
                                <p class="text-[11px] text-slate-400">{{ $exp->purchase_date->format('d/m/Y') }} &bull; {{ $exp->quantity }} {{ $exp->unit }}</p>
                            </div>
                            <span class="font-bold text-red-600">
                                Rp {{ number_format($exp->total_cost, 0, ',', '.') }}
                            </span>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 py-3 text-center">Belum ada catatan pengeluaran bahan.</p>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

</div>
@endsection

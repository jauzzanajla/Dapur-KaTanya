@extends('layouts.admin')

@section('title', 'Rekap Penjualan & Grafik Keuntungan')
@section('page_title', 'Rekapitulasi Penjualan & Analisis Finansial')

@section('content')
<div class="space-y-8">

    <!-- Top Action Row -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-black text-slate-900 tracking-tight">Statistik Omset & Laba Bersih</h2>
            <p class="text-xs text-slate-500">
                Pantau tren pendapatan, margin keuntungan bersih, dan download laporan penjualan format CSV.
            </p>
        </div>

        <!-- Ekspor CSV Button (FEATURE ADMIN 4) -->
        <a href="{{ route('admin.sales.export', ['period' => $period, 'start_date' => $startDate, 'end_date' => $endDate]) }}"
           class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-md shadow-emerald-600/20 transition active:scale-95 flex items-center justify-center gap-2">
            <i data-lucide="file-down" class="w-4 h-4"></i>
            <span>Ekspor Laporan Penjualan (.CSV)</span>
        </a>
    </div>

    <!-- Period Filter Toolbar -->
    <div class="bg-white p-4 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
        <!-- Quick Period Pills -->
        <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
            <a href="{{ route('admin.sales.index', ['period' => 'today']) }}"
               class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ $period === 'today' ? 'bg-red-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Hari Ini
            </a>
            <a href="{{ route('admin.sales.index', ['period' => '7days']) }}"
               class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ $period === '7days' ? 'bg-red-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                7 Hari Terakhir
            </a>
            <a href="{{ route('admin.sales.index', ['period' => '30days']) }}"
               class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ $period === '30days' ? 'bg-red-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                30 Hari Terakhir
            </a>
            <a href="{{ route('admin.sales.index', ['period' => 'this_month']) }}"
               class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ $period === 'this_month' ? 'bg-red-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Bulan Ini
            </a>
            <a href="{{ route('admin.sales.index', ['period' => 'all']) }}"
               class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ $period === 'all' ? 'bg-red-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Semua Riwayat
            </a>
        </div>

        <!-- Custom Date Range Form -->
        <form action="{{ route('admin.sales.index') }}" method="GET" class="flex items-center gap-2 w-full md:w-auto">
            <input type="date" name="start_date" value="{{ $startDate }}"
                   class="text-xs px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-red-500 bg-white">
            <span class="text-xs text-slate-400 font-bold">s/d</span>
            <input type="date" name="end_date" value="{{ $endDate }}"
                   class="text-xs px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-red-500 bg-white">
            <button type="submit" class="px-3.5 py-2 bg-slate-900 text-white text-xs font-bold rounded-xl hover:bg-slate-800 transition">
                Terapkan
            </button>
        </form>
    </div>

    <!-- Financial KPI Summary Cards (FEATURE ADMIN 2 & 3) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- 1. Total Omset -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Omset (Penjualan)</span>
                <div class="w-8 h-8 rounded-xl bg-red-100 text-red-600 flex items-center justify-center">
                    <i data-lucide="trending-up" class="w-4 h-4"></i>
                </div>
            </div>
            <p class="text-2xl font-black text-slate-900">
                Rp {{ number_format($totalOmset, 0, ',', '.') }}
            </p>
            <p class="text-[11px] text-slate-500">
                Dari {{ $totalTransaksi }} transaksi pesanan
            </p>
        </div>

        <!-- 2. Total Modal Pokok (HPP) -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Modal HPP</span>
                <div class="w-8 h-8 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center">
                    <i data-lucide="calculator" class="w-4 h-4"></i>
                </div>
            </div>
            <p class="text-2xl font-black text-slate-900">
                Rp {{ number_format($totalModalProduk, 0, ',', '.') }}
            </p>
            <p class="text-[11px] text-slate-500">
                Laba Kotor: Rp {{ number_format($labaKotor, 0, ',', '.') }}
            </p>
        </div>

        <!-- 3. Pengeluaran Bahan Baku (CSV) -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Biaya Bahan Baku</span>
                <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center">
                    <i data-lucide="shopping-basket" class="w-4 h-4"></i>
                </div>
            </div>
            <p class="text-2xl font-black text-amber-700">
                Rp {{ number_format($totalPengeluaranBahanBaku, 0, ',', '.') }}
            </p>
            <a href="{{ route('admin.raw-materials.index') }}" class="text-[11px] text-orange-600 hover:underline font-semibold block">
                Catatan dari Impor CSV &rarr;
            </a>
        </div>

        <!-- 4. Keuntungan Bersih (Net Profit) -->
        <div class="bg-white p-5 rounded-3xl border-2 border-emerald-300 shadow-sm space-y-2 bg-emerald-50/20">
            <div class="flex items-center justify-between">
                <span class="text-xs font-black text-emerald-900 uppercase tracking-wider">Keuntungan Bersih</span>
                <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center">
                    <i data-lucide="badge-percent" class="w-4 h-4"></i>
                </div>
            </div>
            <p class="text-2xl font-black text-emerald-600">
                Rp {{ number_format($keuntunganBersih, 0, ',', '.') }}
            </p>
            <p class="text-[11px] text-emerald-700 font-bold">
                Margin Bersih: {{ $profitMargin }}%
            </p>
        </div>
    </div>

    <!-- Charts Section (FEATURE ADMIN 3) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

        <!-- Chart 1: Omset vs Keuntungan Bersih (8 Cols) -->
        <div class="lg:col-span-8 bg-white p-6 sm:p-7 rounded-3xl border border-slate-200/80 shadow-sm space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-slate-100">
                <div>
                    <h3 class="text-base font-black text-slate-900">Grafik Omset vs Keuntungan Bersih</h3>
                    <p class="text-xs text-slate-500">Statistik harian penjualan dan laba bersih</p>
                </div>
                <div class="flex items-center gap-4 text-xs font-bold">
                    <span class="flex items-center gap-1.5 text-red-600">
                        <span class="w-3 h-3 rounded-full bg-red-600"></span> Omset
                    </span>
                    <span class="flex items-center gap-1.5 text-emerald-600">
                        <span class="w-3 h-3 rounded-full bg-emerald-500"></span> Laba Bersih
                    </span>
                </div>
            </div>

            <div class="h-72 w-full relative">
                <canvas id="salesTrendChart"></canvas>
            </div>
        </div>

        <!-- Chart 2: Top Selling Menu (4 Cols) -->
        <div class="lg:col-span-4 bg-white p-6 sm:p-7 rounded-3xl border border-slate-200/80 shadow-sm space-y-4">
            <div class="pb-3 border-b border-slate-100">
                <h3 class="text-base font-black text-slate-900">Menu Terlaris</h3>
                <p class="text-xs text-slate-500">Distribusi jumlah porsi terjual</p>
            </div>

            <div class="h-64 w-full relative flex items-center justify-center">
                <canvas id="topProductsChart"></canvas>
            </div>

            <div class="space-y-2 pt-2 border-t border-slate-100 text-xs">
                @foreach(array_slice($topProductLabels, 0, 4) as $idx => $label)
                    <div class="flex items-center justify-between">
                        <span class="text-slate-700 font-medium truncate max-w-[180px]">{{ $label }}</span>
                        <span class="font-bold text-slate-900">{{ $topProductQty[$idx] ?? 0 }} porsi</span>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

    <!-- Sales Transaction Detailed Table -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
            <div>
                <h3 class="text-base font-black text-slate-900">Rincian Transaksi Penjualan</h3>
                <p class="text-xs text-slate-500">Rekap transaksi yang masuk dalam periode terpilih</p>
            </div>
            <a href="{{ route('admin.sales.export', ['period' => $period, 'start_date' => $startDate, 'end_date' => $endDate]) }}"
               class="text-xs font-bold text-emerald-700 hover:text-emerald-800 bg-emerald-50 border border-emerald-200 px-3.5 py-2 rounded-xl transition flex items-center gap-1.5 self-start sm:self-auto">
                <i data-lucide="download" class="w-3.5 h-3.5"></i>
                <span>Download File .CSV</span>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-400 uppercase tracking-wider font-bold border-b border-slate-200">
                        <th class="py-3 px-3">No. Pesanan</th>
                        <th class="py-3 px-3">Pembeli & No HP</th>
                        <th class="py-3 px-3">Tanggal Pickup</th>
                        <th class="py-3 px-3">Total Omset</th>
                        <th class="py-3 px-3">Total Modal HPP</th>
                        <th class="py-3 px-3">Keuntungan Bersih</th>
                        <th class="py-3 px-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($ordersList as $ord)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="py-3.5 px-3 font-mono font-bold text-slate-900">
                                <a href="{{ route('admin.orders.show', $ord) }}" class="text-red-600 hover:underline">
                                    {{ $ord->order_number }}
                                </a>
                            </td>
                            <td class="py-3.5 px-3">
                                <span class="font-bold text-slate-800">{{ $ord->customer_name }}</span>
                                <div class="text-[11px] text-slate-500 font-mono">{{ $ord->customer_phone }}</div>
                            </td>
                            <td class="py-3.5 px-3 text-slate-600">
                                {{ $ord->pickup_date->format('d/m/Y') }}
                            </td>
                            <td class="py-3.5 px-3 font-bold text-slate-900">
                                Rp {{ number_format($ord->total_omset, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-3 text-slate-500 font-medium">
                                Rp {{ number_format($ord->total_modal, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-3 font-black text-emerald-600">
                                +Rp {{ number_format($ord->net_profit, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-3">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $ord->status_badge['bg'] }}">
                                    {{ $ord->status_badge['label'] }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">
                                Belum ada data penjualan pada periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Render Interactive Chart.js Scripts -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Chart 1: Omset vs Keuntungan Bersih
        const ctxSales = document.getElementById('salesTrendChart').getContext('2d');
        new Chart(ctxSales, {
            type: 'bar',
            data: {
                labels: {!! json_encode($dailyLabels) !!},
                datasets: [
                    {
                        label: 'Total Omset (Rp)',
                        data: {!! json_encode($dailyOmset) !!},
                        backgroundColor: 'rgba(220, 38, 38, 0.85)', // Red Primary
                        borderColor: '#DC2626',
                        borderWidth: 1.5,
                        borderRadius: 8,
                    },
                    {
                        label: 'Keuntungan Bersih (Rp)',
                        data: {!! json_encode($dailyProfit) !!},
                        backgroundColor: 'rgba(16, 185, 129, 0.85)', // Emerald Profit
                        borderColor: '#10B981',
                        borderWidth: 1.5,
                        borderRadius: 8,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': Rp ' + Number(context.raw || 0).toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + (value / 1000) + 'k';
                            },
                            font: { size: 10 }
                        },
                        grid: { color: 'rgba(0,0,0,0.05)' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10 } }
                    }
                }
            }
        });

        // Chart 2: Top Products Doughnut
        const ctxTop = document.getElementById('topProductsChart').getContext('2d');
        new Chart(ctxTop, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($topProductLabels) !!},
                datasets: [{
                    data: {!! json_encode($topProductQty) !!},
                    backgroundColor: [
                        '#DC2626', // Red
                        '#EA580C', // Orange
                        '#F59E0B', // Amber
                        '#10B981', // Emerald
                        '#6366F1', // Indigo
                        '#EC4899', // Pink
                        '#8B5CF6'  // Purple
                    ],
                    borderWidth: 2,
                    borderColor: '#FFFFFF'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 10 } } },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ' + context.raw + ' porsi';
                            }
                        }
                    }
                },
                cutout: '65%'
            }
        });
    });
</script>
@endsection

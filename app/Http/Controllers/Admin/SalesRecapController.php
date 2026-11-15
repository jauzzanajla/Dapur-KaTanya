<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\RawMaterialPurchase;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SalesRecapController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->query('period', '30days'); // today, 7days, 30days, this_month, all
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $query = Order::with('items')->where('status', '!=', 'dibatalkan');

        // Apply period filters
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ]);
            $rawMatQuery = RawMaterialPurchase::whereBetween('purchase_date', [$startDate, $endDate]);
        } elseif ($period === 'today') {
            $query->whereDate('created_at', Carbon::today());
            $rawMatQuery = RawMaterialPurchase::whereDate('purchase_date', Carbon::today());
        } elseif ($period === '7days') {
            $query->where('created_at', '>=', Carbon::today()->subDays(6)->startOfDay());
            $rawMatQuery = RawMaterialPurchase::where('purchase_date', '>=', Carbon::today()->subDays(6));
        } elseif ($period === 'this_month') {
            $query->whereMonth('created_at', Carbon::now()->month)->whereYear('created_at', Carbon::now()->year);
            $rawMatQuery = RawMaterialPurchase::whereMonth('purchase_date', Carbon::now()->month)->whereYear('purchase_date', Carbon::now()->year);
        } elseif ($period === 'all') {
            $rawMatQuery = RawMaterialPurchase::query();
        } else { // default: 30 days
            $query->where('created_at', '>=', Carbon::today()->subDays(29)->startOfDay());
            $rawMatQuery = RawMaterialPurchase::where('purchase_date', '>=', Carbon::today()->subDays(29));
        }

        $filteredOrders = (clone $query)->orderBy('created_at', 'desc')->get();

        // Financial KPI calculations
        $totalOmset = $filteredOrders->sum('total_omset');
        $totalModalProduk = $filteredOrders->sum('total_modal');
        $totalPengeluaranBahanBaku = $rawMatQuery->sum('total_cost');

        // Laba Kotor Penjualan = Total Omset - Total Modal HPP
        $labaKotor = $totalOmset - $totalModalProduk;

        // Keuntungan Bersih = Laba Kotor - Pengeluaran Bahan Baku
        $keuntunganBersih = $labaKotor - $totalPengeluaranBahanBaku;

        $totalTransaksi = $filteredOrders->count();
        $averageOrderValue = $totalTransaksi > 0 ? ($totalOmset / $totalTransaksi) : 0;
        $profitMargin = $totalOmset > 0 ? round(($keuntunganBersih / $totalOmset) * 100, 1) : 0;

        // Chart 1: Daily Omset & Keuntungan Bersih (last 14 days or filtered period)
        $chartDays = 14;
        $dailyLabels = [];
        $dailyOmset = [];
        $dailyProfit = [];

        for ($i = $chartDays - 1; $i >= 0; $i--) {
            $d = Carbon::today()->subDays($i);
            $dayStr = $d->format('Y-m-d');
            $dailyLabels[] = $d->format('d M');

            $dayOrders = Order::where('status', '!=', 'dibatalkan')
                ->whereDate('created_at', $dayStr)
                ->get();

            $dayOmset = $dayOrders->sum('total_omset');
            $dayModal = $dayOrders->sum('total_modal');
            $dayBahan = RawMaterialPurchase::whereDate('purchase_date', $dayStr)->sum('total_cost');
            $dayNet = ($dayOmset - $dayModal) - $dayBahan;

            $dailyOmset[] = (float) $dayOmset;
            $dailyProfit[] = (float) $dayNet;
        }

        // Chart 2: Top Selling Products
        $topProducts = OrderItem::selectRaw('product_name, sum(quantity) as total_qty, sum(subtotal) as total_omset, sum(subtotal_profit) as total_profit')
            ->whereHas('order', function($q) {
                $q->where('status', '!=', 'dibatalkan');
            })
            ->groupBy('product_name')
            ->orderBy('total_qty', 'desc')
            ->limit(7)
            ->get();

        $topProductLabels = $topProducts->pluck('product_name')->toArray();
        $topProductQty = $topProducts->pluck('total_qty')->map(fn($v) => (int)$v)->toArray();
        $topProductOmset = $topProducts->pluck('total_omset')->map(fn($v) => (float)$v)->toArray();

        // List of orders for detailed table
        $ordersList = $filteredOrders->take(50);

        return view('admin.sales.index', compact(
            'period',
            'startDate',
            'endDate',
            'totalOmset',
            'totalModalProduk',
            'totalPengeluaranBahanBaku',
            'labaKotor',
            'keuntunganBersih',
            'totalTransaksi',
            'averageOrderValue',
            'profitMargin',
            'dailyLabels',
            'dailyOmset',
            'dailyProfit',
            'topProductLabels',
            'topProductQty',
            'topProductOmset',
            'ordersList'
        ));
    }

    /**
     * Export Sales Report to CSV.
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $period = $request->query('period', 'all');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $query = Order::with('items')->orderBy('created_at', 'desc');

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ]);
        } elseif ($period === 'today') {
            $query->whereDate('created_at', Carbon::today());
        } elseif ($period === '7days') {
            $query->where('created_at', '>=', Carbon::today()->subDays(6)->startOfDay());
        } elseif ($period === 'this_month') {
            $query->whereMonth('created_at', Carbon::now()->month)->whereYear('created_at', Carbon::now()->year);
        }

        $orders = $query->get();

        $filename = 'Laporan-Penjualan-Dapur-KaTanya-' . Carbon::now()->format('Ymd-His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($orders) {
            $file = fopen('php://output', 'w');
            // Write UTF-8 BOM so Excel opens it correctly with Indonesian accents/symbols
            fputs($file, "\xEF\xBB\xBF");

            // CSV Header Row
            fputcsv($file, [
                'No',
                'Nomor Pesanan',
                'Nama Pembeli',
                'Nomor HP Asli',
                'Tanggal Pickup',
                'Jam Pickup',
                'Item Pesanan & Qty',
                'Total Omset (Rp)',
                'Total Modal HPP (Rp)',
                'Keuntungan Bersih (Rp)',
                'Status Pesanan',
                'Metode Pembayaran',
                'Catatan Pembeli',
                'Tanggal Pesan'
            ]);

            $no = 1;
            foreach ($orders as $order) {
                $itemsStr = $order->items->map(function ($item) {
                    return "{$item->product_name} ({$item->quantity}x @ Rp " . number_format($item->price, 0, ',', '.') . ")";
                })->implode('; ');

                fputcsv($file, [
                    $no++,
                    $order->order_number,
                    $order->customer_name,
                    $order->customer_phone,
                    Carbon::parse($order->pickup_date)->format('d/m/Y'),
                    $order->pickup_time ?: '-',
                    $itemsStr,
                    number_format($order->total_omset, 2, ',', ''),
                    number_format($order->total_modal, 2, ',', ''),
                    number_format($order->net_profit, 2, ',', ''),
                    $order->status_badge['label'] ?? $order->status,
                    $order->payment_method,
                    $order->notes ?: '-',
                    $order->created_at->format('d/m/Y H:i'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

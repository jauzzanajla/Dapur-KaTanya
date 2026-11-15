<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\RawMaterialPurchase;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // Today metrics
        $todayOrders = Order::whereDate('created_at', $today)->where('status', '!=', 'dibatalkan')->get();
        $todayOmset = $todayOrders->sum('total_omset');
        $todayModal = $todayOrders->sum('total_modal');
        $todayProfit = $todayOrders->sum('net_profit');

        // All-time metrics
        $allOrders = Order::where('status', '!=', 'dibatalkan')->get();
        $totalOmset = $allOrders->sum('total_omset');
        $totalModal = $allOrders->sum('total_modal');
        $totalBahanBaku = RawMaterialPurchase::sum('total_cost');
        $netProfit = ($totalOmset - $totalModal) - $totalBahanBaku;

        // Pending verifications
        $pendingCount = Order::where('status', 'menunggu_verifikasi')->count();
        $processingCount = Order::where('status', 'diproses')->count();
        $readyPickupCount = Order::where('status', 'siap_diambil')->count();
        $totalProducts = Product::count();
        $activeProducts = Product::where('is_active', true)->count();

        // Latest orders
        $recentOrders = Order::with('items')->latest()->take(8)->get();

        // Recent expenses
        $recentExpenses = RawMaterialPurchase::latest('purchase_date')->take(5)->get();

        return view('admin.dashboard', compact(
            'todayOmset',
            'todayProfit',
            'totalOmset',
            'netProfit',
            'totalBahanBaku',
            'pendingCount',
            'processingCount',
            'readyPickupCount',
            'totalProducts',
            'activeProducts',
            'recentOrders',
            'recentExpenses'
        ));
    }
}

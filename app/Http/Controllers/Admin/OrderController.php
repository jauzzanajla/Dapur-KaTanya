<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\ProductionRecapService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request, ProductionRecapService $productionRecap)
    {
        $status = $request->query('status');
        $search = $request->query('search');
        $date = $request->query('date');

        $query = Order::with('items')->latest();

        if ($status && in_array($status, ['menunggu_verifikasi', 'diproses', 'siap_diambil', 'selesai', 'dibatalkan'])) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'ilike', "%{$search}%")
                    ->orWhere('customer_name', 'ilike', "%{$search}%")
                    ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        if ($date) {
            $query->whereDate('pickup_date', $date);
        }

        $orders = $query->paginate(15);

        $prepDate = $date ?: now()->toDateString();
        $productionSummary = $productionRecap->summarizeForDate($prepDate);

        // Status counts for quick filter pills
        $counts = [
            'all' => Order::count(),
            'menunggu_verifikasi' => Order::where('status', 'menunggu_verifikasi')->count(),
            'diproses' => Order::where('status', 'diproses')->count(),
            'siap_diambil' => Order::where('status', 'siap_diambil')->count(),
            'selesai' => Order::where('status', 'selesai')->count(),
            'dibatalkan' => Order::where('status', 'dibatalkan')->count(),
        ];

        return view('admin.orders.index', array_merge(compact(
            'orders',
            'status',
            'search',
            'date',
            'counts',
            'prepDate',
        ), $productionSummary));
    }

    public function show(Order $order)
    {
        $order->load('items');

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:menunggu_verifikasi,diproses,siap_diambil,selesai,dibatalkan',
        ]);

        $oldStatus = $order->status;
        $order->update(['status' => $request->input('status')]);

        return back()->with('success', "Status pesanan {$order->order_number} berhasil diubah dari '{$oldStatus}' menjadi '{$order->status}'.");
    }
}

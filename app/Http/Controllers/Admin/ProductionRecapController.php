<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ProductionRecapService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductionRecapController extends Controller
{
    public function index(Request $request, ProductionRecapService $productionRecap): View
    {
        $validated = $request->validate([
            'date' => ['nullable', 'date_format:Y-m-d'],
        ]);
        $date = $validated['date'] ?? now()->toDateString();
        $summary = $productionRecap->summarizeForDate($date);

        return view('admin.production.index', [
            'date' => $date,
            ...$summary,
        ]);
    }
}

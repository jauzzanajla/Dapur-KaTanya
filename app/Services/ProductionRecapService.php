<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class ProductionRecapService
{
    /**
     * @return array{
     *     prepOrders: Collection<int, Order>,
     *     menuTotals: array<string, int>,
     *     portionTotals: array{dimsum: int, cireng: int, es_teh: int, es_coklat: int},
     *     prepTotals: array{dimsum: int, cireng: int, es_teh: int, es_coklat: int, paket: int}
     * }
     */
    public function summarizeActiveOrders(): array
    {
        $prepOrders = Order::with('items')
            ->whereIn('status', ['menunggu_verifikasi', 'diproses'])
            ->get();
        $menuTotals = [];
        $portionTotals = [
            'dimsum' => 0,
            'cireng' => 0,
            'es_teh' => 0,
            'es_coklat' => 0,
        ];
        $prepTotals = [
            'dimsum' => 0,
            'cireng' => 0,
            'es_teh' => 0,
            'es_coklat' => 0,
            'paket' => 0,
        ];

        foreach ($prepOrders as $order) {
            foreach ($order->items as $item) {
                $name = $item->product_name;
                $normalizedName = Str::lower($name);
                $quantity = (int) $item->quantity;

                $menuTotals[$name] = ($menuTotals[$name] ?? 0) + $quantity;

                if (Str::contains($normalizedName, ['cepat lulus', 'bundling'])) {
                    $prepTotals['paket'] += $quantity;
                } elseif (Str::contains($normalizedName, 'dimsum')) {
                    $portionTotals['dimsum'] += $quantity;
                } elseif (Str::contains($normalizedName, 'cireng')) {
                    $portionTotals['cireng'] += $quantity;
                } elseif (Str::contains($normalizedName, ['s.kom', 'coklat'])) {
                    $portionTotals['es_coklat'] += $quantity;
                } elseif (Str::contains($normalizedName, ['es.t', 'esteh', 'es teh'])) {
                    $portionTotals['es_teh'] += $quantity;
                }
            }
        }

        $prepTotals['dimsum'] = ($portionTotals['dimsum'] * 2) + $prepTotals['paket'];
        $prepTotals['cireng'] = ($portionTotals['cireng'] * 5) + ($prepTotals['paket'] * 4);
        $prepTotals['es_teh'] = $portionTotals['es_teh'] + $prepTotals['paket'];
        $prepTotals['es_coklat'] = $portionTotals['es_coklat'];

        ksort($menuTotals);

        return compact('prepOrders', 'menuTotals', 'portionTotals', 'prepTotals');
    }
}

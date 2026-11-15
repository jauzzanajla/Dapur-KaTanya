<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RawMaterialPurchase;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RawMaterialController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $month = $request->query('month');

        $query = RawMaterialPurchase::latest('purchase_date');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('material_name', 'ilike', "%{$search}%")
                  ->orWhere('notes', 'ilike', "%{$search}%");
            });
        }

        if ($month) {
            $carbonMonth = Carbon::parse($month);
            $query->whereMonth('purchase_date', $carbonMonth->month)
                  ->whereYear('purchase_date', $carbonMonth->year);
        }

        $purchases = $query->paginate(20);
        $totalSpending = (clone $query)->sum('total_cost');

        return view('admin.raw_materials.index', compact('purchases', 'totalSpending', 'search', 'month'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'purchase_date' => 'required|date',
            'material_name' => 'required|string|max:150',
            'quantity' => 'required|numeric|min:0.01',
            'unit' => 'required|string|max:30',
            'total_cost' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:255',
        ]);

        RawMaterialPurchase::create($request->all());

        return back()->with('success', 'Data pembelian bahan baku berhasil ditambahkan.');
    }

    /**
     * Import raw material purchases from CSV file.
     */
    public function importCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:5120',
        ], [
            'csv_file.required' => 'Pilih file CSV yang akan diunggah.',
            'csv_file.mimes' => 'Format file harus berupa CSV (.csv).',
            'csv_file.max' => 'Ukuran file CSV maksimal 5MB.',
        ]);

        $file = $request->file('csv_file');
        $path = $file->getRealPath();

        $handle = fopen($path, 'r');
        if (!$handle) {
            return back()->with('error', 'Gagal membuka file CSV.');
        }

        $rowCount = 0;
        $successCount = 0;
        $skippedCount = 0;
        $errors = [];

        // Check for UTF-8 BOM and skip it
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        // Read header
        $header = fgetcsv($handle, 1000, ',');
        if (!$header) {
            fclose($handle);
            return back()->with('error', 'File CSV kosong atau tidak valid.');
        }

        // Normalize header names
        $normalizedHeader = array_map(function($col) {
            return strtolower(trim(preg_replace('/[^a-zA-Z0-9_]/', '', str_replace(' ', '_', $col))));
        }, $header);

        // Required fields mapping
        $dateIdx = array_search('tanggal', $normalizedHeader);
        if ($dateIdx === false) $dateIdx = array_search('purchase_date', $normalizedHeader);

        $nameIdx = array_search('nama_bahan', $normalizedHeader);
        if ($nameIdx === false) $nameIdx = array_search('material_name', $normalizedHeader);
        if ($nameIdx === false) $nameIdx = array_search('nama_bahan_baku', $normalizedHeader);

        $qtyIdx = array_search('jumlah', $normalizedHeader);
        if ($qtyIdx === false) $qtyIdx = array_search('quantity', $normalizedHeader);
        if ($qtyIdx === false) $qtyIdx = array_search('qty', $normalizedHeader);

        $unitIdx = array_search('satuan', $normalizedHeader);
        if ($unitIdx === false) $unitIdx = array_search('unit', $normalizedHeader);

        $costIdx = array_search('harga_total', $normalizedHeader);
        if ($costIdx === false) $costIdx = array_search('total_cost', $normalizedHeader);
        if ($costIdx === false) $costIdx = array_search('total_harga', $normalizedHeader);
        if ($costIdx === false) $costIdx = array_search('biaya', $normalizedHeader);

        $notesIdx = array_search('keterangan', $normalizedHeader);
        if ($notesIdx === false) $notesIdx = array_search('notes', $normalizedHeader);

        if ($dateIdx === false || $nameIdx === false || $costIdx === false) {
            fclose($handle);
            return back()->with('error', 'Format kolom CSV tidak sesuai template. Pastikan terdapat kolom: tanggal, nama_bahan, jumlah, satuan, harga_total.');
        }

        while (($row = fgetcsv($handle, 1000, ',')) !== false) {
            $rowCount++;

            // Ignore empty rows
            if (empty(array_filter($row))) {
                continue;
            }

            try {
                $rawDate = trim($row[$dateIdx] ?? '');
                $materialName = trim($row[$nameIdx] ?? '');
                $rawQty = trim($row[$qtyIdx] ?? '1');
                $unit = trim($row[$unitIdx] ?? 'pcs');
                $rawCost = trim($row[$costIdx] ?? '0');
                $notes = $notesIdx !== false ? trim($row[$notesIdx] ?? '') : null;

                if (empty($materialName)) {
                    $skippedCount++;
                    continue;
                }

                // Clean numbers (remove currency symbol, dot/comma separators)
                $cleanCost = (float) preg_replace('/[^0-9.]/', '', str_replace(',', '.', str_replace(['Rp', 'rp', ' ', '.'], ['', '', '', ''], $rawCost)));
                $cleanQty = (float) str_replace(',', '.', preg_replace('/[^0-9.,]/', '', $rawQty));
                if ($cleanQty <= 0) $cleanQty = 1;

                // Parse date (supports Y-m-d, d/m/Y, d-m-Y)
                $purchaseDate = null;
                try {
                    if (str_contains($rawDate, '/')) {
                        $purchaseDate = Carbon::createFromFormat('d/m/Y', $rawDate)->toDateString();
                    } elseif (str_contains($rawDate, '-') && strlen($rawDate) === 10 && substr($rawDate, 4, 1) === '-') {
                        $purchaseDate = Carbon::parse($rawDate)->toDateString();
                    } else {
                        $purchaseDate = Carbon::parse($rawDate)->toDateString();
                    }
                } catch (\Exception $e) {
                    $purchaseDate = Carbon::today()->toDateString();
                }

                RawMaterialPurchase::create([
                    'purchase_date' => $purchaseDate,
                    'material_name' => $materialName,
                    'quantity' => $cleanQty,
                    'unit' => $unit ?: 'pcs',
                    'total_cost' => $cleanCost,
                    'notes' => $notes,
                ]);

                $successCount++;
            } catch (\Exception $e) {
                $skippedCount++;
                $errors[] = "Baris #{$rowCount}: " . $e->getMessage();
            }
        }

        fclose($handle);

        $msg = "Impor CSV berhasil! {$successCount} data pembelian bahan baku telah dicatat ke database.";
        if ($skippedCount > 0) {
            $msg .= " ({$skippedCount} baris dilewati karena format tidak sesuai).";
        }

        return back()->with('success', $msg);
    }

    /**
     * Download template CSV for Raw Materials.
     */
    public function downloadTemplate(): StreamedResponse
    {
        $filename = 'template-impor-bahan-baku-dapur-katanya.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF"); // UTF-8 BOM

            // Header
            fputcsv($file, ['tanggal', 'nama_bahan', 'jumlah', 'satuan', 'harga_total', 'keterangan']);

            // Example rows
            fputcsv($file, [Carbon::today()->format('Y-m-d'), 'Daging Ayam Fillet', '5', 'kg', '190000', 'Supplier Pasar Baru']);
            fputcsv($file, [Carbon::today()->format('Y-m-d'), 'Keju Mozzarella Melt', '3', 'box', '105000', 'Toko Bahan Keju Prima']);
            fputcsv($file, [Carbon::today()->format('Y-m-d'), 'Tepung Tapioka Premium', '10', 'kg', '110000', 'Agen Sembako']);
            fputcsv($file, [Carbon::today()->format('Y-m-d'), 'Cup Minuman 16oz & Seal', '200', 'pcs', '130000', 'Pabrik Plastik']);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function destroy(RawMaterialPurchase $rawMaterial)
    {
        $rawMaterial->delete();
        return back()->with('success', 'Catatan pembelian bahan baku berhasil dihapus.');
    }
}

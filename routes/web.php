<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\SalesRecapController;
use App\Http\Controllers\Admin\RawMaterialController;
use App\Http\Controllers\Admin\QrisController;
use App\Http\Controllers\Admin\ProductionRecapController;

/*
|--------------------------------------------------------------------------
| Web Routes - Dapur KaTanya
|--------------------------------------------------------------------------
*/

// Customer Routes
Route::get('/', [CustomerController::class, 'index'])->name('catalog');
Route::post('/order', [CustomerController::class, 'storeOrder'])->name('order.store');
Route::get('/order/{order_number}', [CustomerController::class, 'showOrder'])->name('order.success');
Route::get('/lacak-pesanan', [CustomerController::class, 'trackOrder'])->name('order.track');

// Admin Auth
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Admin Protected Area
    Route::middleware(['admin'])->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('qris', [QrisController::class, 'index'])->name('qris.index');
        Route::post('qris', [QrisController::class, 'store'])->name('qris.store');
        Route::get('production-recap', [ProductionRecapController::class, 'index'])->name('production.index');

        // SEMENTARA: cek konfigurasi storage di server. Hapus setelah masalah upload selesai.
        Route::get('cek-storage', function () {
            $disk = config('filesystems.media_disk', 'public');

            try {
                \Illuminate\Support\Facades\Storage::disk($disk)->put('tes-vercel.txt', 'halo '.now());
                $tesTulis = 'BERHASIL';
            } catch (\Throwable $e) {
                $prev = $e->getPrevious();
                $tesTulis = 'GAGAL: '.$e->getMessage()
                    .($prev ? ' — '.get_class($prev).': '.$prev->getMessage() : '');
            }

            return [
                'driver_public' => config('filesystems.disks.public.driver'),
                'media_disk' => $disk,
                'driver_media' => config("filesystems.disks.{$disk}.driver"),
                'bucket_ada' => filled(config('filesystems.disks.s3.bucket')),
                'key_ada' => filled(config('filesystems.disks.s3.key')),
                'secret_ada' => filled(config('filesystems.disks.s3.secret')),
                'endpoint' => config('filesystems.disks.s3.endpoint'),
                'region' => config('filesystems.disks.s3.region'),
                'url' => config('filesystems.disks.s3.url'),
                'tes_tulis' => $tesTulis,
            ];
        })->name('cek-storage');

        // CRUD Products & Multi-images
        Route::resource('products', ProductController::class);
        Route::post('products/{product}/toggle-status', [ProductController::class, 'toggleStatus'])->name('products.toggle');
        Route::delete('product-images/{image}', [ProductController::class, 'deleteImage'])->name('products.image.delete');
        Route::post('product-images/{image}/set-primary', [ProductController::class, 'setPrimaryImage'])->name('products.image.primary');

        // Orders Management
        Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::post('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');

        // Sales Recap, Charts, & CSV Export
        Route::get('sales-recap', [SalesRecapController::class, 'index'])->name('sales.index');
        Route::get('sales-recap/export-csv', [SalesRecapController::class, 'exportCsv'])->name('sales.export');

        // Raw Material Purchases & CSV Import
        Route::get('raw-materials', [RawMaterialController::class, 'index'])->name('raw-materials.index');
        Route::post('raw-materials', [RawMaterialController::class, 'store'])->name('raw-materials.store');
        Route::post('raw-materials/import-csv', [RawMaterialController::class, 'importCsv'])->name('raw-materials.import');
        Route::get('raw-materials/template', [RawMaterialController::class, 'downloadTemplate'])->name('raw-materials.template');
        Route::delete('raw-materials/{rawMaterial}', [RawMaterialController::class, 'destroy'])->name('raw-materials.destroy');
    });
});
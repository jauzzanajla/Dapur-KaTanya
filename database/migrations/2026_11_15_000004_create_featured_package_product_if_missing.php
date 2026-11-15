<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $package = DB::table('products')->where('slug', 'paket-cepat-lulus')->first();

        if (! $package) {
            $now = now();
            $productId = DB::table('products')->insertGetId([
                'name' => 'Paket Bundling: "Paket Cepat Lulus"',
                'slug' => 'paket-cepat-lulus',
                'category' => 'paket',
                'description' => 'Paket bundling super hemat! Mendapatkan 1 porsi Dimsum Kuah Keju, 1 porsi Cireng Kuah Keju, plus 1 Es.T.',
                'badge' => 'Paket Favorit Mahasiswa',
                'modal' => 21000,
                'harga_jual' => 35000,
                'is_active' => true,
                'is_featured' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        } else {
            $productId = $package->id;
        }

        DB::table('products')->where('id', '!=', $productId)->update(['is_featured' => false]);
        DB::table('products')->where('id', $productId)->update(['is_featured' => true]);

        if (! DB::table('product_images')->where('product_id', $productId)->exists()) {
            DB::table('product_images')->insert([
                'product_id' => $productId,
                'image_path' => 'products/bundling-1.svg',
                'is_primary' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('products')
            ->where('slug', 'paket-cepat-lulus')
            ->update(['is_featured' => false]);
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('category')->default('makanan'); // makanan, minuman, paket
            $table->text('description');
            $table->string('badge')->nullable(); // e.g., 'Best Seller', 'Signature', 'Hemat'
            $table->decimal('modal', 14, 2)->default(0); // HPP / Modal khusus Admin
            $table->decimal('harga_jual', 14, 2); // Harga Jual ke Customer
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('image_path');
            $table->boolean('is_primary')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->string('customer_name');
            $table->string('customer_phone'); // Wajib nomor HP asli
            $table->date('pickup_date'); // Date picker pengambilan
            $table->string('pickup_time')->nullable(); // Jam perkiraan pickup
            $table->text('notes')->nullable();
            $table->string('payment_method')->default('QRIS');
            $table->string('payment_proof')->nullable(); // File path bukti bayar
            $table->decimal('total_omset', 14, 2); // Total yang dibayar pembeli
            $table->decimal('total_modal', 14, 2)->default(0); // Total HPP dari item pesanan
            $table->decimal('net_profit', 14, 2)->default(0); // total_omset - total_modal
            $table->string('status')->default('menunggu_verifikasi'); // menunggu_verifikasi, diproses, siap_diambil, selesai, dibatalkan
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->string('product_name');
            $table->integer('quantity');
            $table->decimal('price', 14, 2); // Harga jual per unit saat dipesan
            $table->decimal('modal', 14, 2); // Modal per unit saat dipesan
            $table->decimal('subtotal', 14, 2); // price * quantity
            $table->decimal('subtotal_modal', 14, 2); // modal * quantity
            $table->decimal('subtotal_profit', 14, 2); // subtotal - subtotal_modal
            $table->timestamps();
        });

        Schema::create('raw_material_purchases', function (Blueprint $table) {
            $table->id();
            $table->date('purchase_date'); // Tanggal pembelian
            $table->string('material_name'); // Nama bahan baku
            $table->decimal('quantity', 12, 2); // Jumlah
            $table->string('unit'); // Satuan (kg, gram, pcs, liter, ikat, dll)
            $table->decimal('total_cost', 14, 2); // Total biaya pembelian
            $table->text('notes')->nullable(); // Keterangan / supplier
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('raw_material_purchases');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('product_images');
        Schema::dropIfExists('products');
    }
};

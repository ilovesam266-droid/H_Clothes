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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
                ->constrained('orders')
                ->cascadeOnDelete();

            $table->foreignId('variant_id')
                ->constrained('variants')
                ->restrictOnDelete();

            $table->string('product_name', 255);
            $table->string('variant_name', 255);

            $table->unsignedbigInteger('unit_price');
            $table->unsignedInteger('quantity')->default(1);

            $table->timestamps();

            /* ================= INDEXES ================= */

            $table->index('order_id', 'idx_order_items_order');
            $table->index('variant_id', 'idx_order_items_variant');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};

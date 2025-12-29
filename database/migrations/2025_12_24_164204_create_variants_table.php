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
        Schema::create('variants', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnDelete();

            $table->string('size', 20);
            $table->string('color', 30);

            $table->string('sku', 100)->nullable();

            $table->bigInteger('price');
            $table->unsignedInteger('stock');
            $table->unsignedInteger('sold')->default(0);

            $table->timestamps();
            $table->softDeletes();

            /* ================= INDEXES ================= */

            $table->index('deleted_at', 'idx_variants_deleted');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variants');
    }
};

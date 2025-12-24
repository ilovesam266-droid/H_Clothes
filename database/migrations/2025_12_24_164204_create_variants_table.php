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

            $table->tinyInteger('type');
            $table->string('name', 50);

            $table->unsignedBigInteger('stock');
            $table->unsignedBigInteger('sold')->default(0);

            $table->timestamps();
            $table->softDeletes();

            /* ================= INDEXES ================= */

            $table->index('deleted_at', 'idx_variants_deleted');
            $table->index(['product_id', 'type'], 'idx_variants_product_type');
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

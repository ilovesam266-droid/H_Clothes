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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnDelete();

            $table->tinyInteger('rating')->default(5);
            $table->string('body', 255)->nullable();

            $table->timestamps();
            $table->softDeletes();

            /* ================= INDEXES ================= */

            $table->index('rating', 'idx_reviews_rating');
            $table->index('deleted_at', 'idx_reviews_deleted');

            $table->index(
                ['product_id', 'deleted_at'],
                'idx_reviews_product_active'
            );

            $table->index(
                ['product_id', 'rating'],
                'idx_reviews_product_rating'
            );

            $table->unique(
                ['user_id', 'product_id'],
                'uk_reviews_user_product'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};

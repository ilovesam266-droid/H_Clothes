<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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

            $table->foreignId('created_by')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('name', 125);
            $table->string('slug', 125);
            $table->tinyInteger('status')->default(1);

            $table->text('description');
            $table->json('detail')->nullable();

            $table->timestamps();
            $table->softDeletes();

            /* ================= INDEXES ================= */

            $table->unique('slug', 'idx_products_slug');
            $table->index('status', 'idx_products_status');
            $table->index('deleted_at', 'idx_products_deleted');
            $table->fullText(['name', 'description']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

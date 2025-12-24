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
        Schema::create('categoryables', function (Blueprint $table) {
            $table->id();

            $table->foreignId('category_id')
                ->constrained('categories')
                ->cascadeOnDelete();
            // xóa category → tự xóa mapping

            $table->unsignedBigInteger('categoryable_id');
            $table->string('categoryable_type', 100);

            $table->timestamps();

            /* ================= INDEXES ================= */

            // render product / post / entity
            $table->index(
                ['categoryable_id', 'categoryable_type'],
                'idx_categoryables_target'
            );

            // filter category
            $table->index(
                ['category_id', 'categoryable_type'],
                'idx_categoryables_category_type'
            );

            // avoid remapping
            $table->unique(
                ['category_id', 'categoryable_id', 'categoryable_type'],
                'uk_categoryables_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categoryables');
    }
};

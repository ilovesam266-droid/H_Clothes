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
        Schema::create('imageables', function (Blueprint $table) {
            $table->id();

            $table->foreignId('image_id')
                ->constrained('images')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('imageable_id');
            $table->string('imageable_type', 100);

            $table->timestamps();
            $table->softDeletes();

            /* ================= INDEXES ================= */

            $table->index(
                ['imageable_id', 'imageable_type'],
                'idx_imageables_target'
            );

            $table->index('deleted_at', 'idx_imageables_deleted');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('imageables');
    }
};

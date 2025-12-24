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
        Schema::create('mails', function (Blueprint $table) {
            $table->id();

            $table->tinyInteger('type');

            $table->string('subject', 255);
            $table->text('body');

            $table->json('variables')->nullable();

            $table->timestamps();

            /* ================= INDEXES ================= */

            $table->index('type', 'idx_mails_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mails');
    }
};

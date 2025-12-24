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
        Schema::create('mail_batches', function (Blueprint $table) {
            $table->id();

            $table->foreignId('template_id')
                ->constrained('mails')
                ->restrictOnDelete();

            $table->string('trigger_type', 20);

            $table->string('trigger_source', 100);

            $table->json('variables')->nullable();

            $table->unsignedInteger('total_recipients')->default(0);
            $table->unsignedInteger('sent_count')->default(0);
            $table->unsignedInteger('failed_count')->default(0);

            $table->timestamps();

            /* ================= INDEXES ================= */

            $table->index(
                ['trigger_type', 'trigger_source'],
                'idx_mail_batches_trigger'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mail_batches');
    }
};

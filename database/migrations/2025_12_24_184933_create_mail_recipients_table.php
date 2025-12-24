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
        Schema::create('mail_recipients', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('mail_batch_id')
                ->constrained('mail_batches')
                ->cascadeOnDelete();

            $table->string('target_type', 20)->nullable();

            $table->unsignedBigInteger('target_id')->nullable();

            $table->string('email', 255);

            $table->tinyInteger('status')->default(0);

            $table->timestamp('sent_at')->nullable();

            $table->timestamps();

            /* ================= INDEXES ================= */

            $table->index('status', 'idx_mail_recipients_status');
            $table->index('sent_at', 'idx_mail_recipients_sent');
            $table->index(
                ['mail_batch_id', 'status'],
                'idx_mail_batch_status'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mail_recipients');
    }
};

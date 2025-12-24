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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
                ->constrained('orders')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->tinyInteger('payment_method')->default(0);

            $table->string('payment_transaction', 255)->nullable();

            $table->bigInteger('total_amount');

            $table->tinyInteger('status')->default(0);

            $table->json('meta_data')->nullable();

            $table->timestamps();
            $table->softDeletes();

            /* ================= INDEXES ================= */

            $table->unique('payment_transaction', 'idx_payments_tx');
            $table->index('status', 'idx_payments_status');
            $table->index('payment_method', 'idx_payments_method');
            $table->index(['order_id', 'status'], 'idx_payments_order_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};

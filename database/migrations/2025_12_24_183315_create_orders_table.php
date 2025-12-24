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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('created_by')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->tinyInteger('status')->index();
            $table->bigInteger('total_amount');

            $table->text('admin_note')->nullable();
            $table->text('customer_note')->nullable();

            $table->string('cancel_reason')->nullable();
            $table->string('failed_reason')->nullable();

            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('delivered_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            /* ========= COMPOSITE INDEX ========= */
            $table->index(['status', 'created_at'], 'idx_orders_status_created');
            $table->index('deleted_at', 'idx_orders_deleted');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

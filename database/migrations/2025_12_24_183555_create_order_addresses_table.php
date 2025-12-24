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
        Schema::create('order_addresses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
                ->constrained('orders')
                ->cascadeOnDelete();

            $table->string('recipient_name', 150);
            $table->string('recipient_phone', 20);
            $table->string('recipient_email', 50)->nullable();

            $table->string('province', 255);
            $table->string('district', 255);
            $table->string('ward', 255)->nullable();
            $table->string('address_detail', 255);

            $table->timestamps();

            /* ================= INDEX ================= */

            $table->unique('order_id', 'uniq_order_addresses_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_addresses');
    }
};

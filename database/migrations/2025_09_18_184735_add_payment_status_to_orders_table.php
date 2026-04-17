<?php

// database/migrations/xxxx_xx_xx_xxxxxx_add_payment_status_to_orders_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_status', 20)->default('unpaid')->after('payment_method');
            // Optionally normalize status values you plan to use
            // e.g., pending, for_pickup, on_going, for_delivery, completed, canceled
        });
    }
    public function down(): void {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('payment_status');
        });
    }
};


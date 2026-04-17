<?php

// database/migrations/2025_10_10_000001_add_delivery_fields_to_orders.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
public function up(): void {
    Schema::table('orders', function (Blueprint $t) {
        // Make sure pickup_date exists (or create it)
        if (!Schema::hasColumn('orders', 'pickup_date')) {
            $t->date('pickup_date')->nullable();
        }

        // Make sure pickup_time exists (or create it)
        if (!Schema::hasColumn('orders', 'pickup_time')) {
            $t->string('pickup_time')->nullable();
        }

        // NEW delivery fields (only add if missing)
        if (!Schema::hasColumn('orders', 'delivery_date')) {
            $t->date('delivery_date')->nullable();
        }

        if (!Schema::hasColumn('orders', 'delivery_time')) {
            $t->string('delivery_time')->nullable();
        }

        // Category column – just create it if it doesn't exist
        if (!Schema::hasColumn('orders', 'category')) {
            $t->string('category')->default('pickup_delivery');
        }
    });
}

    public function down(): void {
        Schema::table('orders', function (Blueprint $t) {
            $t->dropColumn(['delivery_date','delivery_time']);
        });
    }
};


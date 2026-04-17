<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
{
    Schema::table('orders', function (Blueprint $table) {
        if (!Schema::hasColumn('orders', 'customer_id')) {
            $table->unsignedBigInteger('customer_id')->nullable()->after('user_id');
        }

        // DO NOT touch 'category' here. It already exists.

        if (!Schema::hasColumn('orders', 'pickup_name')) {
// BEFORE
// $table->string('pickup_name')->nullable()->after('category');

// AFTER
$table->string('pickup_name')->nullable();            
        }
        if (!Schema::hasColumn('orders', 'pickup_phone')) {
            $table->string('pickup_phone', 50)->nullable()->after('pickup_name');
        }
        if (!Schema::hasColumn('orders', 'pickup_address')) {
            $table->string('pickup_address')->nullable()->after('pickup_phone');
        }

        if (!Schema::hasColumn('orders', 'total_amount')) {
            $table->integer('total_amount')->nullable()->after('payment_method');
        }

        if (!Schema::hasColumn('orders', 'payment_status')) {
            $table->enum('payment_status', ['unpaid','paid'])->default('unpaid')->after('status');
        }

        if (!Schema::hasColumn('orders', 'meta')) {
            $table->json('meta')->nullable()->after('total_amount');
        }
    });
}


    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // drop only if they exist (optional guards)
            if (Schema::hasColumn('orders', 'customer_id'))  { $table->dropColumn('customer_id'); }
            if (Schema::hasColumn('orders', 'pickup_name'))  { $table->dropColumn('pickup_name'); }
            if (Schema::hasColumn('orders', 'pickup_phone')) { $table->dropColumn('pickup_phone'); }
            if (Schema::hasColumn('orders', 'pickup_address')) { $table->dropColumn('pickup_address'); }
            if (Schema::hasColumn('orders', 'total_amount')) { $table->dropColumn('total_amount'); }
            if (Schema::hasColumn('orders', 'payment_status')){ $table->dropColumn('payment_status'); }
            if (Schema::hasColumn('orders', 'meta'))         { $table->dropColumn('meta'); }
            // Do NOT drop 'category' if it's shared by older code
        });
    }
};

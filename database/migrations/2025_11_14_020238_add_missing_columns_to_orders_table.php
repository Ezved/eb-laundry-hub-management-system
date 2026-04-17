<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // service mapping (keep service_type; add service_id for FK)
            if (!Schema::hasColumn('orders', 'service_id')) {
                $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete();
            }

            // categorization
            if (!Schema::hasColumn('orders', 'category')) {
                $table->string('category', 30)->default('pickup_delivery');
            }

            // pickup/delivery person & location details
            if (!Schema::hasColumn('orders', 'pickup_name'))   $table->string('pickup_name')->nullable();
            if (!Schema::hasColumn('orders', 'pickup_phone'))  $table->string('pickup_phone', 50)->nullable();
            if (!Schema::hasColumn('orders', 'pickup_address')) $table->text('pickup_address')->nullable();
            if (!Schema::hasColumn('orders', 'pickup_location_details')) {
                $table->text('pickup_location_details')->nullable();
            }

            // delivery schedule (optional)
            if (!Schema::hasColumn('orders', 'delivery_date')) $table->date('delivery_date')->nullable();
            if (!Schema::hasColumn('orders', 'delivery_time')) $table->string('delivery_time', 20)->nullable();

            // payments
            if (!Schema::hasColumn('orders', 'payment_status')) {
                $table->string('payment_status', 20)->default('unpaid');
            }
            if (!Schema::hasColumn('orders', 'total_amount')) {
                // keep both 'total' and 'total_amount'; app uses either
                $table->unsignedInteger('total_amount')->default(0);
            }

            // flexible blob for mixed schemas (flags, form fields, etc.)
            if (!Schema::hasColumn('orders', 'meta')) {
                $table->json('meta')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Drops are optional; keep only if your DB supports it cleanly
            foreach ([
                'service_id','category','pickup_name','pickup_phone','pickup_address',
                'pickup_location_details','delivery_date','delivery_time',
                'payment_status','total_amount','meta'
            ] as $col) {
                if (Schema::hasColumn('orders', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};

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
    // Skip if table already exists (keeps your existing rows)
    if (Schema::hasTable('orders')) {
        return;
    }

    Schema::create('orders', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
        $table->date('pickup_date');
        $table->string('pickup_time', 20);
        $table->string('service_type', 100);
        $table->unsignedInteger('load_qty')->default(0);
        $table->boolean('exceeds_8kg')->default(false);
        $table->boolean('no_scale')->default(false);
        $table->text('special_instructions')->nullable();
        $table->string('payment_method', 20); // cod|gcash
        $table->unsignedInteger('pickup_delivery_charge')->default(49);
        $table->unsignedInteger('subtotal')->default(0);
        $table->unsignedInteger('surcharge')->default(0);
        $table->unsignedInteger('total')->default(0);
        $table->string('status', 30)->default('pending');
        $table->timestamps();
    });
}
};

<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_customers_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();

            // Optional link to users table (registered customer). NULL = walk-in.
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('address')->nullable();

            // same visibility flag you used on users
            $table->boolean('is_hidden')->nullable()->default(null);

            $table->timestamps();

            // Helpful indexes
            $table->index(['user_id']);
            $table->index(['name']);
            $table->index(['phone_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Add if it does not exist (works for sqlite + mysql)
            if (!Schema::hasColumn('orders', 'display_total')) {
                $table->decimal('display_total', 10, 2)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'display_total')) {
                $table->dropColumn('display_total');
            }
        });
    }
};

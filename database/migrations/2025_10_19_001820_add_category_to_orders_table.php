<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $t) {
            // SQLite-safe add (no ->change())
            if (!Schema::hasColumn('orders', 'category')) {
                $t->string('category', 30)->default('pickup_delivery'); // after() ignored by SQLite
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $t) {
            if (Schema::hasColumn('orders', 'category')) {
                $t->dropColumn('category');
            }
        });
    }
};

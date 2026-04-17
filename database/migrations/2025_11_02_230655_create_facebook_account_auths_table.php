<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // If it already exists anywhere (dev/prod), do nothing.
        if (Schema::hasTable('facebook_account_auths')) {
            return;
        }

        Schema::create('facebook_account_auths', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('provider', 32)->default('facebook');
            $table->string('provider_id', 64)->unique();   // FB numeric ID
            $table->string('provider_email')->nullable();   // FB may not return email
            $table->text('avatar')->nullable();
            $table->timestamps();

            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facebook_account_auths');
    }
};

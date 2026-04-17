<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('google_account_auths', function (Blueprint $table) {
      $table->id();
      $table->foreignId('user_id')->constrained()->cascadeOnDelete();
      $table->string('provider')->default('google');
      $table->string('provider_id');             // unique Google user id (sub)
      $table->string('provider_email')->nullable();
      $table->string('avatar')->nullable();
      $table->timestamps();

      $table->unique(['provider', 'provider_id']);
      $table->index(['provider', 'provider_email']);
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('google_account_auths');
  }
};
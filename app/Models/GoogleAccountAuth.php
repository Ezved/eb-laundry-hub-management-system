<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GoogleAccountAuth extends Model
{
  protected $table = 'google_account_auths'; // explicit, for clarity

  protected $fillable = [
    'user_id',
    'provider',        // 'google'
    'provider_id',     // Google "sub"
    'provider_email',
    'avatar',
  ];

  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }
}
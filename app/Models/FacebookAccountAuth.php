<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FacebookAccountAuth extends Model
{
  protected $table = 'facebook_account_auths';

  protected $fillable = [
    'user_id',
    'provider',        // 'facebook'
    'provider_id',     // Facebook numeric ID
    'provider_email',  // may be null if FB didn't return it
    'avatar',
  ];

  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }
}
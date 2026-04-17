<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $table = 'feedback';

    protected $fillable = [
        'user_id', 'name', 'rating', 'message', 'is_visible',
    ];

    public function user() {
        return $this->belongsTo(\App\Models\User::class);
    }
}

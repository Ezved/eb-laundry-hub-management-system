<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class UserNotification extends Model
{
    protected $fillable = ['user_id','order_id','title','body','read_at'];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function scopeUnread(Builder $q): Builder
    {
        return $q->whereNull('read_at');
    }

    public function user(){ return $this->belongsTo(User::class); }
    public function order(){ return $this->belongsTo(Order::class); }
}

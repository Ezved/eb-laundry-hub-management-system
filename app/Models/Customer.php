<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'user_id', 'name', 'email', 'phone_number', 'address', 'is_hidden',
    ];

    protected $casts = [
        'is_hidden' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Treat NULL as visible
    public function scopeVisible($q)
    {
        return $q->where(function ($qq) {
            $qq->where('is_hidden', false)->orWhereNull('is_hidden');
        });
    }

    public function scopeHidden($q)
    {
        return $q->where('is_hidden', true);
    }
    
    public function scopeExcludeAdmins($q)
{
    $q->where(function ($qq) {
        $qq->whereNull('user_id')
           ->orWhereHas('user', fn($u) => $u->where('role', '!=', 'admin'));
    });
}

// app/Models/Customer.php
public function orders(){ return $this->hasMany(\App\Models\Order::class); }
public function latestOrder(){
    return $this->hasOne(\App\Models\Order::class)->latestOfMany(); // by created_at
}

}

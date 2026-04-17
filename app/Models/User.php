<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Roles
    public const ROLE_ADMIN = 'admin';
    public const ROLE_USER  = 'user';

    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'address',
        'password',
        'role',
        'location_details',
        'status',
        'is_hidden',
        'loyalty_claims', // how many free loads already claimed
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'is_blocked'        => 'boolean', # added 11/17/2025
        'is_hidden'         => 'boolean',
        'loyalty_claims'    => 'integer',
    ];


    /* =========================
     * Relationships
     * ========================= */
    public function customer(): HasOne
    {
        return $this->hasOne(Customer::class);
    }

    public function orders(): HasMany
    {
        // uses user_id on orders table
        return $this->hasMany(Order::class, 'user_id');
    }

    public function latestOrder(): HasOne
    {
        return $this->hasOne(Order::class, 'user_id')->latestOfMany();
    }

    // Social account links
    public function googleAccounts(): HasMany
    {
        return $this->hasMany(\App\Models\GoogleAccountAuth::class);
    }

    public function facebookAccounts(): HasMany
    {
        return $this->hasMany(\App\Models\FacebookAccountAuth::class);
    }

    /* =========================
     * Scopes
     * ========================= */
    public function scopeVisible($q)
    {
        // Treat NULL as visible (legacy)
        return $q->where(function ($qq) {
            $qq->where('is_hidden', false)
                ->orWhereNull('is_hidden');
        });
    }

    public function scopeHidden($q)
    {
        return $q->where('is_hidden', true);
    }

    /* =========================
     * Convenience helpers
     * ========================= */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isBlocked(): bool
    {
        return ($this->status ?? 'active') === 'blocked';
    }

    /* =========================
     * Keep Customer in sync
     * ========================= */
    protected static function booted()
    {
        static::created(function (User $user) {
            $user->customer()->firstOrCreate(
                ['user_id' => $user->id],
                [
                    'name'         => $user->name,
                    'email'        => $user->email,
                    'phone_number' => $user->phone_number,
                    'address'      => $user->address,
                    'is_hidden'    => $user->is_hidden,
                ]
            );
        });

        static::updated(function (User $user) {
            if ($user->customer) {
                $user->customer->update([
                    'name'         => $user->name,
                    'email'        => $user->email,
                    'phone_number' => $user->phone_number,
                    'address'      => $user->address,
                    'is_hidden'    => $user->is_hidden,
                ]);
            }
        });
    }
}

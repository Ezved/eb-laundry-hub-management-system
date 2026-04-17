<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'customer_id',
        'user_id',
        'category',

        // both schemas supported
        'service_id',
        'service_type',

        'pickup_name',
        'pickup_phone',
        'pickup_address',
        'pickup_date',
        'pickup_time',
        'delivery_date',
        'delivery_time',
        'load_qty',
        'exceeds_8kg',
        'no_scale',
        'special_instructions',
        'payment_method',
        'status',
        'payment_status',
        'pickup_delivery_charge',
        'subtotal',
        'surcharge',
        'total',
        'total_amount',
        'meta',
    ];

    protected $casts = [
        'pickup_date'            => 'date',
        'delivery_date'          => 'date',
        'created_at'             => 'datetime',
        'updated_at'             => 'datetime',
        'exceeds_8kg'            => 'boolean',
        'no_scale'               => 'boolean',
        'special_instructions'   => 'string',
        'meta'                   => 'array',

        // money fields
        'total'                  => 'decimal:2',
        'total_amount'           => 'decimal:2',
        'subtotal'               => 'decimal:2',
        'surcharge'              => 'decimal:2',
        'pickup_delivery_charge' => 'decimal:2',
    ];

    // Append helpers when casting to array/json (optional; safe)
    protected $appends = [
        'display_total',
        'service_title',
    ];

    /* ----------------- Relationships ----------------- */

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class); // inverse of User::orders()
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // Present if your schema has services table + service_id
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /* ----------------- Accessors / Helpers ----------------- */

    /**
     * Prefer explicit totals (> 0) then fall back to summing known components.
     * This covers both schemas: walk-ins (total_amount) and pickup/delivery (total),
     * or computes from parts if neither explicit total is set.
     */
    public function getDisplayTotalAttribute()
    {
        foreach (['total_amount', 'total'] as $c) {
            if (array_key_exists($c, $this->attributes) && (float)($this->$c ?? 0) > 0) {
                return (float)$this->$c;
            }
        }

        $sum = 0.0;
        foreach ([
            'subtotal',
            'surcharge',
            'pickup_delivery_charge',
            'total_estimated',
            'pud_charge',
            'pickup_only_charge',
        ] as $c) {
            if (array_key_exists($c, $this->attributes) && $this->$c !== null) {
                $sum += (float)$this->$c;
            }
        }
        return $sum;
    }

    /**
     * Human-friendly service label regardless of schema.
     */
    public function getServiceTitleAttribute(): ?string
    {
        if (!empty($this->service_type)) {
            return $this->service_type;
        }
        return optional($this->service)->title;
    }
}

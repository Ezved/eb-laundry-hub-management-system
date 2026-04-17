<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id','kind','code','description','qty','unit_price','line_total'
    ];

    public function order() { return $this->belongsTo(Order::class); }
}

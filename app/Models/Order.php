<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id', 'status', 'total_price'
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}

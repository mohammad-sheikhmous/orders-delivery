<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Order extends Model
{
    use HasTranslations;

    protected $fillable = [
        'user_id', 'status', 'total_price'
    ];

    public $translatable = ['status'];

    protected $appends = ['translatedStatus'];

    protected $hidden = ['status'];

    public function getTranslatedStatusAttribute($val)
    {
        return $this->status;
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}

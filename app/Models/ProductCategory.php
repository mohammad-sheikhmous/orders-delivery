<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    protected $fillable = [
        'id', 'name', 'slug', 'photo', 'active', 'vendor_id'
    ];

    public function getActiveAttribute($val)
    {
        return $val == 1 ? 'active' : 'inactive';
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'product_category_id');
    }
}

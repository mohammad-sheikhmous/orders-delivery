<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    protected $fillable = [
        'mobile', 'name', 'password', 'address', 'longitude', 'latitude', 'email', 'active', 'logo', 'main_category_id'
    ];

    public function scopeSelection($query)
    {
        return $query->select('mobile', 'name', 'address', 'longitude', 'latitude', 'email', 'active', 'logo', 'main_category_id');
    }

    public function mainCategory()
    {
        return $this->belongsTo(MainCategory::class, 'main_category_id');
    }

    public function productCategories()
    {
        return $this->hasMany(ProductCategory::class, 'vendor_id');
    }

    protected $casts = [
        'password' => 'hashed',
    ];
}

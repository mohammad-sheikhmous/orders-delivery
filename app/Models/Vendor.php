<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    protected $fillable = [
        'mobile', 'name', 'password', 'address', 'longitude', 'latitude', 'email', 'active', 'logo', 'main_category_id'
    ];

    public function scopeSelectionForShowing($query)
    {
        return $query->select('id', 'mobile', 'name', 'address', 'longitude', 'latitude', 'email', 'active', 'logo', 'main_category_id');
    }

    public function scopeSelectionForIndexing($query)
    {
        return $query->select('id', 'name', 'logo', 'main_category_id');
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

    public function getActiveAttribute($val)
    {
        return $val == 1 ? 'active' : 'inactive';
    }

    public function setActiveAttribute($val)
    {
        if ($val == 'active')
            $this->attributes['active'] = 1;
        elseif ($val == 'inactive')
            $this->attributes['active'] = 0;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Vendor extends Model
{
    use HasTranslations;

    protected $fillable = [
        'mobile', 'name', 'password', 'address', 'longitude', 'latitude', 'email', 'active', 'photo', 'main_category_id'
    ];

    public $translatable = ['name'];

    protected $appends = ['translated_name'];

    protected $hidden = ['name', 'password'];

    protected $casts = [
        'password' => 'hashed',
    ];

    public function getTranslatedNameAttribute($val)
    {
        return $this->name;
    }

    public function scopeSelectionForShowing($query)
    {
        return $query->select('id', 'mobile', 'name', 'address', 'longitude', 'latitude', 'email', 'active', 'photo', 'main_category_id');
    }

    public function scopeSelectionForIndexing($query)
    {
        return $query->select('id', 'name', 'photo', 'main_category_id','active');
    }

    public function mainCategory()
    {
        return $this->belongsTo(MainCategory::class, 'main_category_id');
    }

    public function productCategories()
    {
        return $this->hasMany(ProductCategory::class, 'vendor_id');
    }

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

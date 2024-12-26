<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Product extends Model
{
    use HasTranslations;

    protected $fillable = [
        'name', 'photo', 'description', 'amount', 'price', 'active', 'product_category_id'
    ];

    public $translatable = ['name', 'description'];

    protected $appends = ['translated_name', 'translated_description'];

    protected $hidden = ['name', 'description'];

    public function getTranslatedNameAttribute($val)
    {
        return $this->name;
    }

    public function getTranslatedDescriptionAttribute($val)
    {
        return $this->description;
    }

    public function productCategory()
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function favoredByUsers()
    {
        return $this->belongsToMany(User::class, 'favorites');
    }

    public function scopeSelectionForShowing($query)
    {
        return $query->select('id', 'name', 'photo', 'description', 'amount', 'price', 'active', 'product_category_id');
    }

    public function scopeSelectionForIndexing($query)
    {
        return $query->select('id', 'name', 'photo', 'product_category_id');
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

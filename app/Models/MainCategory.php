<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MainCategory extends Model
{
    protected $fillable = [
        'id','name', 'slug', 'photo', 'active'
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

    public function scopeSelection($query)
    {
        return $query->select('id','name', 'slug', 'photo', 'active');
    }

    public function vendors()
    {
        return $this->hasMany(Vendor::class,'main_category_id');
    }
}

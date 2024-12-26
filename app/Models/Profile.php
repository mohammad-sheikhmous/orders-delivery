<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'firstName', 'lastName', 'address', 'email', 'photo', 'longitude', 'latitude', 'user_id'
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
}

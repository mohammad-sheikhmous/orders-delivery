<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'photo',
        'password'
    ];
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function casts()
    {
        return [
            'password' => 'hashed',
        ];
    }
}

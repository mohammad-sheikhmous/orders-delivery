<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Observers\UserObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'mobile',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function generateCode()
    {
        $this->timestamps = false;
        $this->code = rand(100, 999) . '-' . rand(100, 999);
        $this->code_expire_at = now()->addMinutes(15);
        $this->verified = 0;
        $this->save();
    }

    public function resetCode()
    {
        $this->timestamps = true;
        $this->code = null;
        $this->code_expire_at = null;
        $this->verified = 1;
        $this->save();
    }

    // to register the observer
    protected static function booted(): void
    {
        self::observe(UserObserver::class);
    }

    public function profile()
    {
        return $this->hasOne(Profile::class, 'user_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id');
    }

    public function cart()
    {
        return $this->hasOne(Cart::class, 'user_id');
    }

    public function favorites()
    {
        return $this->belongsToMany(Product::class, 'favorites');
    }

    public function scopeSelection($query)
    {
        return $query->select('id', 'mobile')->with(['profile' => function ($q) {
            $q->select('id', 'firstName', 'lastName', 'address', 'email', 'longitude', 'latitude', 'photo', 'user_id');
        }]);
    }
}

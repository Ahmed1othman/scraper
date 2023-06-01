<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'phone',
        'email',
        'status',
        'password',
        'subscription_status',
        'subscription_expiration_date',
        'number_of_products',
        'fcm_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];


    public static function usersCount(){
        return $userCount = User::role('normal user')->count();
    }



    // attributes
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }
//scope
    public function scopeNormalUsers($query)
    {
        return $query->whereHas('roles', function ($query) {
            $query->where('name', 'normal user');
        });
    }

    public function scopeNormalUserCount($query)
    {
        return $query->whereHas('roles', function ($query) {
            $query->where('name', 'normal user');
        })->count();
    }

    //rel
    public function products()
    {
        return $this->belongsToMany(Product::class, 'user_products')->withPivot('price','status');
    }

    public function notifications()
    {
        return $this->hasMany(PriceNotification::class, 'user_id');
    }

    public function remainingProducts()
    {
        $productsCount = $this->products()->count();
        return $this->number_of_products - $productsCount;
    }
}

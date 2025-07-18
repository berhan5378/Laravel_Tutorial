<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;


class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'uuid',
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
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

     // Automatically generate UUID when creating a new user
     protected static function boot()
     {
         parent::boot();
         static::creating(function ($model) {
             $model->uuid = Str::uuid(); // Generate UUID
         });
     }

     public function carts()
     {
         return $this->hasMany(Cart::class, 'user_id', 'uuid'); // foreign key, local key
     }

     public function wishlist()
     {
         return $this->hasMany(Wishlist::class, 'user_id', 'uuid'); // foreign key, local key
     }
     
     
}

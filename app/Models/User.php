<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use App\Enums\Image\ImagePurposeType; // Import the enum

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
        'username',
        'name',
        'email',
        'password',
        'referral_code',                
        'parent_referral_code',         
        'registration_ip_address',      
        'registration_user_agent',      
        'registration_device_cookie_id',
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

    public function accounts()
    {
        return $this->hasMany(Account::class, 'user_id');
    }

    public function referrer()
    {
        return $this->belongsTo(User::class, 'parent_referral_code', 'referral_code');
    }
    
    public function referredUsers()
    {
        return $this->hasMany(User::class, 'parent_referral_code', 'referral_code');
    }

    public function avatar(): \Illuminate\Database\Eloquent\Relations\MorphOne
    {
        return $this->morphOne(Image::class, 'imageable')
                    ->where('purpose', ImagePurposeType::USER_AVATAR->value); // Use ->value
    }

}

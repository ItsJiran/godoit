<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache; // Import the Cache facade

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
        'whatsapp',
        'kota',
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

    /**
     * Get all acquisitions for this user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function acquisitions()
    {
        return $this->hasMany(UserAcquisition::class);
    }

    public function avatar(): \Illuminate\Database\Eloquent\Relations\MorphOne
    {
        return $this->morphOne(Image::class, 'imageable')
                    ->where('purpose', ImagePurposeType::USER_AVATAR->value); // Use ->value
    }

    public function generateReferralParam()
    {
        return '?reg=' . $this->username;
    }

    public function generateReferralUrl()
    {
        return env('APP_URL') . '/' . $this->generateReferralParam();
    }

    /**
     * Get a list of all active membership acquisitions for this user.
     * This leverages the 'acquisitions' relationship and filters by product type 'membership'.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function activeMemberships()
    {
        return $this->acquisitions()
                    ->whereHas('product', function ($query) {
                        $query->where('type', ProductType::MEMBERSHIP->value); // Filter products by 'membership' type
                    })
                    ->active(); // Apply the 'active' scope from the UserAcquisition model
    }

    /**
     * Get the active premium membership acquisition for this user, with caching.
     *
     * @return UserAcquisition|null
     */
    public function activeMembershipPremium(): ?UserAcquisition
    {
        $cacheKey = 'user_' . $this->id . '_active_premium_membership';
        $cacheDuration = now()->addMinutes(60); // Cache for 60 minutes

        return Cache::remember($cacheKey, $cacheDuration, function () {
            return $this->acquisitions()
                ->whereHas('product', function ($query) {
                    $query->where('productable_type', Membership::class)
                          ->where('productable_id', 1); // Assuming '1' is the ID for the premium membership
                })
                ->active()
                ->first();
        });
    }

}

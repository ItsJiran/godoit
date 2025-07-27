<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class ReferralRegistration extends Model
{
    
    /**
     * Nama tabel yang terkait dengan model.
     *
     * @var string
     */
    protected $table = 'referral_registrations';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'referrer_user_id',   // ID pengguna yang merujuk
        'referred_user_id',   // ID pengguna yang dirujuk
        'referral_code_used', // Kode referral yang digunakan
        'registered_at',      // Timestamp pendaftaran referral
    ];

    /**
     * Atribut yang harus di-cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'registered_at' => 'datetime',
    ];

    /**
     * Dapatkan pengguna yang merujuk.
     */
    public function referrer()
    {
        return $this->belongsTo(User::class, 'referrer_user_id');
    }

    /**
     * Dapatkan pengguna yang dirujuk.
     */
    public function referredUser()
    {
        return $this->belongsTo(User::class, 'referred_user_id');
    }

    /**
     * Membuat catatan pendaftaran referral di tabel 'referral_registrations'.
     *
     * @param  \App\Models\User  $referrer Pengguna yang merujuk.
     * @param  \App\Models\User  $referredUser Pengguna yang dirujuk.
     * @param  string  $referralCodeUsed Kode referral yang sebenarnya digunakan.
     * @return \App\Models\ReferralRegistration
     */
    public static function createReferralRegistration(User $referrer, User $referredUser, string $referralCodeUsed): ReferralRegistration
    {
        $referralRegistration = ReferralRegistration::create([
            'referrer_user_id' => $referrer->id,
            'referred_user_id' => $referredUser->id,
            'referral_code_used' => $referralCodeUsed,
            'registered_at' => now(),
        ]);

        return $referralRegistration;
    }

}

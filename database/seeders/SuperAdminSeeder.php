<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User; // Pastikan mengimpor model User
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SuperAdminSeeder extends Seeder
{
    /**
     * Jalankan database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Periksa apakah superadmin sudah ada untuk menghindari duplikasi
        if (User::where('email', 'superadmin@example.com')->doesntExist()) {
            User::create([
                'username' => 'superadmin',
                'name' => 'Super Admin',
                'email' => 'superadmin@example.com',
                'email_verified_at' => now(), // Verifikasi email secara otomatis
                'password' => Hash::make('password'), // Ganti dengan password yang lebih kuat di produksi!
                'role' => 'superadmin',
                'referral_code' => Str::random(10), // Buat kode referral unik
                'registration_ip_address' => '127.0.0.1', // IP localhost
                'registration_user_agent' => 'Seeder', // User-Agent untuk seeder
                'registration_device_cookie_id' => Str::uuid()->toString(), // UUID perangkat unik
            ]);

            $this->command->info('Akun Super Admin berhasil dibuat!');
        } else {
            $this->command->info('Akun Super Admin sudah ada. Melewati pembuatan.');
        }
    }
}


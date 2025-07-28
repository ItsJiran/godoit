<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User; // Pastikan mengimpor model User
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\App; // Untuk memeriksa lingkungan aplikasi

class DebugAdminSeeder extends Seeder
{
    /**
     * Jalankan database seeds.
     * Akun admin ini hanya akan dibuat jika aplikasi berada dalam mode 'local' (development).
     *
     * @return void
     */
    public function run()
    {
        // Periksa apakah aplikasi berjalan di lingkungan 'local' (development).
        // Ini memastikan seeder ini tidak berjalan di lingkungan produksi.
        if (App::environment('local')) {
            // Periksa apakah admin debug sudah ada untuk menghindari duplikasi.
            if (User::where('email', 'admin@debug.com')->doesntExist()) {
                User::create([
                    'username' => 'debugAdmin',
                    'name' => 'Debug Admin',
                    'email' => 'admin@debug.com',
                    'email_verified_at' => now(), // Verifikasi email secara otomatis untuk kemudahan debug
                    'password' => Hash::make('password'), // Kata sandi default untuk debug (ubah di produksi!)
                    'role' => 'admin', // Peran untuk akun debug ini adalah 'admin'
                'whatsapp' => '0',
                'kota' => '',
                'referral_code' => Str::random(10), // Buat kode referral unik
                    'registration_ip_address' => '127.0.0.1', // IP localhost
                    'registration_user_agent' => 'Debug Seeder', // User-Agent untuk seeder
                    'registration_device_cookie_id' => Str::uuid()->toString(), // UUID perangkat unik
                ]);

                $this->command->info('Akun Debug Admin berhasil dibuat (hanya di mode development)!');
            } else {
                $this->command->info('Akun Debug Admin sudah ada. Melewati pembuatan.');
            }
        } else {
            $this->command->info('Melewati pembuatan Akun Debug Admin karena bukan di mode development.');
        }
    }
}


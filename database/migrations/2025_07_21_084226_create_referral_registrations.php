<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    /**
     * Jalankan migrasi.
     * Metode ini membuat tabel baru 'referral_registrations' untuk mencatat
     * setiap pendaftaran yang terjadi melalui sistem referral.
     */
    public function up(): void
    {
        Schema::create('referral_registrations', function (Blueprint $table) {
            $table->id(); // Kolom ID utama

            // Kolom untuk ID pengguna yang merujuk (referrer).
            // Ini adalah foreign key ke tabel 'users'.
            $table->foreignId('referrer_user_id')
                  ->constrained('users') // Mengacu pada kolom 'id' di tabel 'users'
                  ->onDelete('cascade'); // Jika pengguna referrer dihapus, catatannya di sini juga dihapus

            // Kolom untuk ID pengguna yang dirujuk (referred).
            // Ini adalah foreign key ke tabel 'users'.
            // Bersifat unik karena satu pengguna hanya bisa dirujuk sekali.
            $table->foreignId('referred_user_id')
                  ->unique() // Memastikan satu pengguna hanya bisa dirujuk sekali
                  ->constrained('users') // Mengacu pada kolom 'id' di tabel 'users'
                  ->onDelete('cascade'); // Jika pengguna referred dihapus, catatannya di sini juga dihapus

            // Kolom untuk menyimpan kode referral yang sebenarnya digunakan saat pendaftaran.
            // Bisa berupa referral_code atau username yang digunakan oleh referrer.
            $table->string('referral_code_used')->nullable();

            // Kolom timestamp yang secara spesifik mencatat kapan pendaftaran referral ini terjadi.
            // Ini sangat penting untuk leaderboard (misalnya, sorting berdasarkan waktu).
            $table->timestamp('registered_at')->useCurrent(); // Menggunakan waktu saat ini sebagai default

            $table->timestamps(); // Kolom created_at dan updated_at standar Laravel
        });
    }

    /**
     * Balikkan migrasi.
     * Metode ini menghapus tabel 'referral_registrations' jika migrasi di-rollback.
     */
    public function down(): void
    {
        Schema::dropIfExists('referral_registrations');
    }
};


<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            // Kolom 'role' ditempatkan di sini sesuai urutan yang diinginkan.
            // Tidak perlu '->after()' karena ini adalah Schema::create.
            $table->enum('role', ['superadmin', 'admin', 'user'])->default('user');

            // Kolom referral system
            $table->string('referral_code')->unique()->nullable();
            $table->string('parent_referral_code')->nullable();

            // Kolom untuk deteksi perangkat dan IP saat registrasi
            $table->string('registration_ip_address')->nullable();
            $table->text('registration_user_agent')->nullable();
            $table->string('registration_device_cookie_id')->nullable();
            
            $table->rememberToken();
            $table->softDeletes(); // Menambahkan kolom deleted_at untuk soft delete
            $table->timestamps(); // Menambahkan kolom created_at dan updated_at
        });

        // Foreign key constraint ditambahkan setelah tabel 'users' dibuat.
        // Ini adalah praktik yang baik jika foreign key merujuk pada tabel yang sama
        // atau tabel lain yang dibuat dalam migrasi yang sama.
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('parent_referral_code')
                  ->references('referral_code')
                  ->on('users')
                  ->onDelete('set null'); 
        });


        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Balikkan migrasi.
     */
    public function down(): void
    {
        // Hapus foreign key constraint terlebih dahulu sebelum menghapus tabel 'users'
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['parent_referral_code']);
        });

        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};


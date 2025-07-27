<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
// use Illuminate\Support\Facades\Auth; // Tidak lagi diperlukan di sini
// use Illuminate\Validation\ValidationException; // Tidak lagi diperlukan di sini

class LoginRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna diizinkan untuk membuat permintaan ini.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Mengizinkan semua pengguna untuk mengakses form login.
        return true;
    }

    /**
     * Dapatkan aturan validasi yang berlaku untuk permintaan.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Dapatkan pesan kesalahan kustom untuk aturan validasi yang ditentukan.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            // Anda bisa menambahkan pesan kustom di sini jika diperlukan,
            // tetapi untuk login, pesan 'auth.failed' yang sudah ada di Laravel
            // biasanya cukup untuk kredensial yang salah.
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
        ];
    }

    // Metode authenticate() dihapus karena logika otentikasi sekarang ada di AuthController
}


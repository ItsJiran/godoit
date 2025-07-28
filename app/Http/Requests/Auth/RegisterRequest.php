<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna diizinkan untuk membuat permintaan ini.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Mengizinkan semua pengguna untuk mengakses form registrasi.
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
            // Nama pengguna harus diisi, berupa string, dan tidak lebih dari 255 karakter.
            'name' => ['required', 'string', 'max:255'],
            'whatsapp' => ['required', 'string', 'max:255','unique:users'],
            'kota' => ['required', 'string', 'max:255'],
            // Email harus diisi, berupa string, format email yang valid, tidak lebih dari 255 karakter,
            // dan harus unik di tabel 'users'.
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            // Kata sandi harus diisi, dikonfirmasi (ada field password_confirmation yang cocok),
            // dan memenuhi aturan default Laravel untuk kekuatan kata sandi (min. 8 karakter, dll.).
            'password' => ['required', 'confirmed', Password::defaults()],
            // Kode referral induk bersifat opsional (nullable), berupa string,
            // dan jika diisi, harus ada di kolom 'referral_code' pada tabel 'users'.
            // Ini memastikan kode referral yang dimasukkan oleh pengguna adalah valid.
            'parent_referral_code' => ['nullable', 'string', 'exists:users,referral_code'],
            // Username harus diisi, berupa string, tidak lebih dari 255 karakter,
            // dan harus unik di tabel 'users'.
            'username' => ['required', 'string', 'max:255', 'unique:users'],
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
            // Pesan khusus untuk validasi 'exists' pada parent_referral_code.
            'parent_referral_code.exists' => 'Kode referral yang Anda masukkan tidak valid.',
            // Pesan khusus untuk validasi 'unique' pada username.
            'username.unique' => 'Nama pengguna ini sudah digunakan. Silakan pilih nama pengguna lain.',
        ];
    }
}


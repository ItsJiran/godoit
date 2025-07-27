<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Models\ReferralRegistration;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB; // Impor Facade DB
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Tampilkan form registrasi.
     * Metode ini akan memeriksa apakah ada kode referral yang sudah disimpan di sesi
     * untuk pengguna yang belum terdaftar/login, dan memprioritaskannya.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm(Request $request)
    {
        // Ambil kode referral dari URL jika ada
        $parentReferralCandidate = $request->query('reg'); // Nama variabel diubah menjadi 'Candidate'

        // Ambil kode referral yang sudah disimpan di sesi untuk pengguna yang belum terdaftar/login
        $sessionReferralCode = $request->session()->get('parent_referral_code_session');

        // Tentukan parentReferralCode yang akan digunakan:
        // Prioritaskan kode referral yang sudah ada di sesi.
        $parentReferralCode = $sessionReferralCode;

        // Jika belum ada kode referral di sesi, coba deteksi dari URL
        if (!$parentReferralCode && $parentReferralCandidate) {
            $foundUser = null;

            // Coba cari pengguna berdasarkan referral_code
            $foundUser = User::where('referral_code', $parentReferralCandidate)->first();

            // Jika tidak ditemukan berdasarkan referral_code, coba cari berdasarkan name
            if (!$foundUser) {
                $foundUser = User::where('username', $parentReferralCandidate)->first();
            }

            // Jika pengguna ditemukan (baik dari referral_code atau name)
            if ($foundUser) {
                // Gunakan referral_code asli dari pengguna yang ditemukan
                $parentReferralCode = $foundUser->referral_code;
                // Simpan kode referral asli ini ke sesi
                $request->session()->put('parent_referral_code_session', $parentReferralCode);
            }
        }

        // Pastikan cookie device_id sudah terpasang. Jika belum, pasang.
        // Ini akan digunakan untuk melacak perangkat secara persisten.
        $this->getOrCreateDeviceId($request);
        
        return view('auth.register', compact('parentReferralCode'));
    }

    /**
     * Tangani permintaan registrasi pengguna baru.
     *
     * @param  \App\Http\Requests\Auth\RegisterRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(RegisterRequest $request)
    {
        // Mulai transaksi database untuk memastikan atomisitas
        DB::beginTransaction();

        try {
            $ipAddress = $request->ip();
            $userAgent = $request->header('User-Agent');
            $deviceCookieId = $this->getOrCreateDeviceId($request);

            // Tentukan kode referral induk yang akan digunakan
            $parentReferralCode = $this->determineParentReferralCode($request);

            // Lakukan pemeriksaan awal anti-penipuan
            // $this->performPreRegistrationFraudChecks($ipAddress, $deviceCookieId, $parentReferralCode);

            // Buat kode referral unik untuk pengguna baru
            $referralCode = $this->generateUniqueReferralCode();

            // Buat pengguna baru (referred user)
            $referred_user = User::create($request->validated() + [
                'referral_code' => $referralCode,
                'parent_referral_code' => $parentReferralCode,
                'registration_ip_address' => $ipAddress,
                'registration_user_agent' => $userAgent,
                'registration_device_cookie_id' => $deviceCookieId,
                'password' => Hash::make($request->password),
            ]);

            // Jika ada kode referral induk, catat pendaftaran referral
            if ($parentReferralCode) {
                // Ambil data user yang merujuk (referrer)
                // Pastikan menggunakan ->first() untuk mendapatkan model tunggal
                $referrer_user = User::where('referral_code', $parentReferralCode)->first();

                if ($referrer_user) {
                    // Panggil metode internal controller untuk membuat log referral
                    ReferralRegistration::createReferralRegistration($referrer_user, $referred_user, $parentReferralCode);
                } else {
                    Log::warning("Kode referral induk '{$parentReferralCode}' ditemukan di sesi/form tetapi tidak ada pengguna yang cocok di database saat registrasi oleh user ID: {$referred_user->id}.");
                    return redirect()->back()->withInput()->withErrors(['registration' => "Kode referral induk '{$parentReferralCode}' ditemukan di sesi/form tetapi tidak ada pengguna yang cocok di database saat registrasi oleh user ID: {$referred_user->id}."]);
                }
            }

            // Kirim event Registered untuk memicu verifikasi email
            event(new Registered($referred_user)); // Gunakan $referred_user di sini

            // Login pengguna setelah registrasi
            Auth::login($referred_user);

            // Hapus kode referral dari sesi setelah registrasi berhasil
            $request->session()->forget('parent_referral_code_session');

            // Commit transaksi jika semua operasi berhasil
            DB::commit();

            return redirect()->route('dashboard')->with('success', 'Registrasi berhasil! Silakan verifikasi email Anda.');

        } catch (ValidationException $e) {
            // Jika ada ValidationException (dari performPreRegistrationFraudChecks), rollback dan re-throw
            DB::rollBack();
            throw $e;
        } catch (Throwable $e) {
            // Tangkap exception lainnya jika terjadi kesalahan
            DB::rollBack(); // Lakukan rollback transaksi

            Log::error("Pendaftaran user gagal: " . $e->getMessage(), [
                'email' => $request->email,
                'ip_address' => $request->ip(),
                'exception' => $e
            ]);

            // Redirect kembali dengan pesan error generik
            return redirect()->back()->withInput()->withErrors(['registration' => 'Terjadi kesalahan saat pendaftaran. Silakan coba lagi.']);
        }
    }

    /**
     * Tampilkan form login.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function showLoginForm(Request $request)
    {
        $this->getOrCreateDeviceId($request); // Memastikan device_id cookie terpasang
        return view('auth.login');
    }

    /**
     * Tangani permintaan otentikasi.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        $currentIpAddress = $request->ip();
        $currentUserAgent = $request->header('User-Agent');
        $currentDeviceCookieId = $this->getOrCreateDeviceId($request); // Mengambil device_id

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            // Pemantauan Keamanan Saat Login
            if ($user->registration_ip_address !== $currentIpAddress) {
                // Log::info("Perubahan IP terdeteksi untuk pengguna ID {$user->id}: Dari {$user->registration_ip_address} ke {$currentIpAddress}.");
            }

            if ($user->registration_device_cookie_id !== $currentDeviceCookieId) {
                // Log::info("Perubahan Device ID terdeteksi untuk pengguna ID {$user->id}: Dari {$user->registration_device_cookie_id} ke {$currentDeviceCookieId}.");
            }

            return redirect()->intended('dashboard')->with('success', 'Anda berhasil login!');
        }

        throw ValidationException::withMessages([
            'email' => trans('auth.failed'),
        ]);
    }

    /**
     * Log out pengguna.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Anda telah logout.');
    }

    /**
     * Ambil device_id dari cookie atau buat yang baru jika tidak ada.
     * Pasang cookie jika baru dibuat.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    private function getOrCreateDeviceId(Request $request): string
    {
        $deviceCookieId = $request->cookie('device_id');
        if (!$deviceCookieId) {
            $deviceCookieId = Str::uuid()->toString();
            // Pasang cookie dengan masa berlaku sangat panjang (misal: 5 tahun)
            Cookie::queue('device_id', $deviceCookieId, 2628000);
        }
        return $deviceCookieId;
    }

    /**
     * Menentukan parent_referral_code yang akan digunakan untuk registrasi.
     * Memprioritaskan nilai dari sesi.
     *
     * @param  \App\Http\Requests\Auth\RegisterRequest  $request
     * @return string|null
     */
    private function determineParentReferralCode(RegisterRequest $request): ?string
    {
        $parentReferralCode = $request->session()->get('parent_referral_code_session');

        // Fallback ke input form jika tidak ada di sesi (misal: pengguna langsung POST)
        if (!$parentReferralCode) {
            $parentReferralCode = $request->parent_referral_code;
        }

        if (is_null($parentReferralCode)) return null;

        // Pastikan kode referral valid ada di database
        if ($parentReferralCode && !User::where('referral_code', $parentReferralCode)->exists()) {
            return null; // Set null jika tidak valid
        }

        return $parentReferralCode;
    }

    /**
     * Melakukan pemeriksaan anti-penipuan awal sebelum registrasi.
     * Akan melempar ValidationException jika terdeteksi aktivitas mencurigakan.
     *
     * @param  string  $ipAddress
     * @param  string  $deviceCookieId
     * @param  string|null  $parentReferralCode
     * @throws \Illuminate\Validation\ValidationException
     */
    private function performPreRegistrationFraudChecks(string $ipAddress, string $deviceCookieId, ?string $parentReferralCode): void
    {
        // 1. Pemeriksaan Kecepatan Pendaftaran dari IP/Device yang Sama
        $recentRegistrationsCount = User::where(function ($query) use ($ipAddress, $deviceCookieId) {
                $query->where('registration_ip_address', $ipAddress)
                      ->orWhere('registration_device_cookie_id', $deviceCookieId);
            })
            ->where('created_at', '>', now()->subMinutes(30)) // Pendaftaran dalam 30 menit terakhir
            ->count();

        if ($recentRegistrationsCount >= 5) {
            Log::warning("Potensi pendaftaran penipuan: Terlalu banyak pendaftaran dari IP {$ipAddress} / Device ID {$deviceCookieId}.");
            throw ValidationException::withMessages(['registration' => 'Terlalu banyak pendaftaran dari perangkat/jaringan ini. Silakan coba lagi nanti.']);
        }

        // 2. Pemeriksaan Self-Referral atau Referral dari Perangkat yang Sama
        if ($parentReferralCode) {
            $referrer = User::where('referral_code', $parentReferralCode)->first();

            if ($referrer) {
                if ($ipAddress === $referrer->registration_ip_address &&
                    $deviceCookieId === $referrer->registration_device_cookie_id) {
                    Log::warning("Self-referral/Same-device referral terdeteksi: Perujuk ID {$referrer->id} dan Pendaftar dari IP {$ipAddress} / Device ID {$deviceCookieId} yang sama.");
                    // Anda bisa melempar exception di sini atau mengembalikan flag.
                    // Untuk saat ini, kita hanya log, dan Anda bisa menambahkan kolom is_fraud_flagged.
                    // Jika ingin mencegah registrasi:
                    throw ValidationException::withMessages(['parent_referral_code' => 'Anda tidak bisa merujuk diri sendiri atau dari perangkat yang sama.']);
                }
            }
        }
    }

    /**
     * Menghasilkan kode referral unik yang belum ada di database.
     *
     * @return string
     */
    private function generateUniqueReferralCode(): string
    {
        do {
            $referralCode = Str::random(10);
        } while (User::where('referral_code', $referralCode)->exists());
        return $referralCode;
    }

    /**
     * Membuat instance pengguna baru di database.
     *
     * @param  array  $userData
     * @return \App\Models\User
     */
    private function createUser(array $userData): User
    {
        return User::create($userData);
    }

}

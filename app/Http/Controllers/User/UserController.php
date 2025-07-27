<?php

namespace App\Http\Controllers\User; // Ensure this namespace is correct

use App\Models\User;
use App\Models\Image;

use App\Enums\Image\ImagePurposeType;
use App\Http\Services\Media\ImageUploadService; // Ensure this is correct
use App\Http\Controllers\Controller; // Ensure this is correct

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule; // Untuk validasi enum role
use Illuminate\Validation\ValidationException; // Import ValidationException

class UserController extends Controller
{
    /**
     * Create a new UserController instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Ensure only authenticated users can access profile pages and related actions
        $this->middleware('auth');
        // If you have specific admin roles, you might apply middleware conditionally
        // $this->middleware('can:manage-users')->only(['createUserAdmin']);
    }

        /**
     * Display the authenticated user's profile page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function showProfile(Request $request)
    {
        $user = Auth::user()->load('avatar'); // Eager load the avatar relationship

        $avatarUrl = null;
        if ($user->avatar) {
            // Use the getTemporaryUrl method from the Image model
            // For profile pictures, 10 minutes should be sufficient for display on the page.

            $avatarConversion = $user->avatar->getConversionByTargetDimensions(96,96);
            $avatarUrl = $user->avatar->getTemporaryUrl(10, optional($avatarConversion)->path);
        }

        return view('user.profile', compact('user', 'avatarUrl'));
    }

    /**
     * Display the form for editing the authenticated user's profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function showEditProfileForm(Request $request)
    {
        $user = Auth::user()->load('avatar'); // Eager load the avatar relationship

        $avatarUrl = null;
        if ($user->avatar) {
            // Get the temporary URL for the avatar to display in the edit form.
            // A short expiration time (e.g., 10 minutes) is usually fine for display purposes.
            $avatarUrl = $user->avatar->getTemporaryUrl(10);
        }

        // Pass both the user object and the avatarUrl to the view
        return view('user.edit-profile', compact('user', 'avatarUrl'));
    }

    /**
     * Update the authenticated user's profile information.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'user_avatar' => ['nullable', 'image', 'max:5120'], // Max 5MB
            // Add other fields you want to allow updating and their validation rules here
        ]);

        // Update basic user information first
        $user->name = $request->name;
        $user->save(); // Save non-image data

        // Handle profile picture upload only if a new file is provided
        if ($request->hasFile('user_avatar')) {
            // Eager load the avatar relationship if not already loaded
            $user->loadMissing('avatar');

            // Store the OLD avatar record temporarily
            $oldAvatar = $user->avatar;

            try {
                // 1. Upload and attach the NEW avatar first
                $newAvatar = Image::createImageRecord(
                    $request->file('user_avatar'),
                    $user,
                    ImagePurposeType::USER_AVATAR->value,
                    $user->name . ' user avatar',
                    'public',
                    null,
                    ImagePurposeType::USER_AVATAR->value
                );

                // 2. If the new avatar was successfully created, then delete the old one
                if ($oldAvatar) {
                    ImageUploadService::deleteImage($oldAvatar); // Use the static method
                    $oldAvatar->forceDelete();
                }

            } catch (\Exception $e) {
                // If the new image upload/creation fails, the old avatar remains intact.
                // You might want to log the error:
                \Log::error('Failed to upload new user avatar for user ID ' . $user->id . ': ' . $e->getMessage());

                // Optionally, redirect back with an error message instead of success
                return redirect()->route('user.profile')->with('error', 'Gagal mengunggah foto profil baru. Silakan coba lagi.');
            }
        }

        return redirect()->route('user.profile')->with('status', 'Profil berhasil diperbarui!');
    }

    /**
     * Display the form to change the authenticated user's password.
     *
     * @return \Illuminate\View\View
     */
    public function showChangePasswordForm()
    {
        return view('user.change-password');
    }

    /**
     * Change the authenticated user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function changePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed', Rule::notIn([$request->current_password])], // New password must not be same as current
            // You can use Laravel 8+ password rules for stronger validation:
            // 'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
        ]);

        // Verify the current password
        if (!Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['Kata sandi saat ini tidak cocok.'],
            ]);
        }

        // Update the password
        $user->password = Hash::make($request->password);
        $user->remember_token = null; // Invalidate remember me token after password change
        $user->save();

        return redirect()->route('user.profile')->with('status', 'Kata sandi berhasil diperbarui!');
    }


    /**
     * Membuat pengguna baru dengan peran admin atau superadmin.
     * Metode ini dapat diakses oleh superadmin untuk membuat akun admin/superadmin baru.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createUserAdmin(Request $request)
    {
        // Validasi input
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            // Memastikan peran yang diberikan valid (superadmin, admin)
            'role' => ['required', 'string', Rule::in(['superadmin', 'admin'])],
        ]);

        // Buat pengguna baru
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role, // Tentukan peran (role)
            // Kolom-kolom lain seperti referral_code, ip_address, dll., bisa diisi null
            // atau sesuai kebutuhan jika ingin membuat akun admin/superadmin secara manual.
            'referral_code' => null,
            'parent_referral_code' => null,
            'registration_ip_address' => $request->ip(),
            'registration_user_agent' => $request->header('User-Agent'),
            'registration_device_cookie_id' => null, // Atau buat UUID jika diperlukan
        ]);

        return response()->json([
            'message' => 'User admin/superadmin berhasil dibuat.',
            'user' => $user->only(['id', 'name', 'email', 'role'])
        ], 201);
    }
}
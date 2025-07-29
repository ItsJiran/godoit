<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

use App\Models\Image;
use App\Enums\Image\ImagePurposeType;
use App\Services\Media\ImageUploadService; // Ensure this is correct

class ProfileController extends Controller
{
    // Edit with My Profile
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    // Update Profile
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'avatar' => ['nullable', 'image', 'max:5120'], // Max 5MB
            'whatsapp' => 'required|unique:users,whatsapp,' . $user->id,
            'kota' => 'required|string|max:255',
        ]);

        // Handle profile picture upload only if a new file is provided
        if ($request->hasFile('avatar')) {
            // Eager load the avatar relationship if not already loaded
            $user->loadMissing('avatar');

            // Store the OLD avatar record temporarily
            $oldAvatar = $user->avatar;

            try {
                // 1. Upload and attach the NEW avatar first
                $newAvatar = Image::createImageRecord(
                    $request->file('avatar'),
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

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'whatsapp' => $request->whatsapp,
            'kota' => $request->kota,
        ]);

        return redirect()->route('profile.edit')->with('success', 'Profil berhasil diperbarui.');
    }
}

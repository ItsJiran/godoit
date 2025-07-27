<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest; // Keep existing imports if they are used elsewhere
use App\Http\Requests\Auth\RegisterRequest; // Keep existing imports if they are used elsewhere
use App\Models\User; // Ensure User model is imported
use App\Models\ReferralRegistration; // Keep existing imports if they are used elsewhere
use Illuminate\Auth\Events\Registered; // Keep existing imports if they are used elsewhere
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Keep existing imports if they are used elsewhere
use Illuminate\Support\Facades\Hash; // Keep existing imports if they are used elsewhere
use Illuminate\Support\Str; // Keep existing imports if they are used elsewhere
use Illuminate\Support\Facades\Cookie; // Keep existing imports if they are used elsewhere
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password; // Import the Password facade for the broker
use Illuminate\Validation\ValidationException;

class ForgotPasswordController extends Controller
{
   /**
     * Display the form to request a password reset link.
     *
     * @return \Illuminate\View\View
     */
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Send a password reset link to the given user.
     *
     * This method validates the incoming email, uses Laravel's built-in password broker
     * to generate a new token (replacing any existing ones for that email), stores it
     * in the 'password_reset_tokens' table, and dispatches an email to the user
     * containing the reset link.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function sendResetLinkEmail(Request $request)
    {
        // 1. Validate the incoming email address
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // 2. Use Laravel's built-in Password broker to send the reset link.
        //    This method handles:
        //    - Checking if the user exists.
        //    - Deleting any old tokens for this email from 'password_reset_tokens'.
        //    - Generating a new, secure token.
        //    - Storing the new token in 'password_reset_tokens' along with 'created_at'.
        //    - Dispatching the 'Illuminate\Auth\Notifications\ResetPassword' notification
        //      to the user's email with the reset link.
        $status = Password::broker()->sendResetLink(
            $request->only('email')
        );

        // 3. Handle the response based on the status from the broker.
        //    Password::RESET_LINK_SENT indicates success.
        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', __($status)); // Redirects back to the form with success message
        }

        // 4. If an error status is returned, throw a validation exception.
        //    This typically happens if the email is not found or other issues.
        throw ValidationException::withMessages([
            'email' => [__($status)], // Returns a translatable error message (e.g., 'We can't find a user with that email address.')
        ]);
    }
}
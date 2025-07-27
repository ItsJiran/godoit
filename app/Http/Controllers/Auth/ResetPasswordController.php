<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; // Needed for hashing the new password
use Illuminate\Support\Facades\Password; // For the password broker
use Illuminate\Validation\ValidationException;
use App\Models\User; // Assuming your User model is in App\Models

class ResetPasswordController extends Controller
{
    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/login'; // Or your dashboard route, e.g., '/home'

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest'); // Only guests should access this
    }

    /**
     * Display the password reset view for the given token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string|null  $token
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showResetForm(Request $request, ?string $token = null)
    {
        // This method simply shows the form, passing the token and email to the view.
        return view('auth.reset-password')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }

    /**
     * Reset the given user's password.
     *
     * This method handles the form submission for password reset.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function reset(Request $request)
    {
        // 1. Validate the incoming request data
        $request->validate($this->rules(), $this->validationErrorMessages());

        // 2. Call the 'reset' method on the Password broker.
        //    This method handles:
        //    - Finding the user by email.
        //    - Validating the token.
        //    - Updating the password using the provided closure.
        //    - Deleting the token from 'password_reset_tokens'.
        //    - Returning a status message (e.g., Password::PASSWORD_RESET).
        $response = Password::broker()->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) { // Type-hinting User model
                // This closure defines how the password is actually updated.
                $user->forceFill([
                    'password' => Hash::make($password), // Hash the new password
                    'remember_token' => null, // Clear remember token on password change
                ])->save();

                // You might also want to log the user in here, or just redirect to login.
                // Auth::guard()->login($user); // Optional: Log user in after reset
            }
        );

        // 3. Handle the response based on the status from the broker.
        //    If successful, redirect to login with a status message.
        if ($response === Password::PASSWORD_RESET) {
            return $this->sendResetResponse($request, $response);
        }

        // 4. If failed, throw a validation exception.
        return $this->sendResetFailedResponse($request, $response);
    }

    /**
     * Get the password reset validation rules.
     *
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()], // Laravel 8+ password rules
        ];
    }

    /**
     * Get the password reset validation error messages.
     *
     * @return array<string, string>
     */
    protected function validationErrorMessages(): array
    {
        return []; // Custom messages can be defined here if needed
    }

    /**
     * Get the response for a successful password reset.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendResetResponse(Request $request, string $response)
    {
        // Redirect to login page with a success message
        return redirect($this->redirectTo)->with('status', __($response));
    }

    /**
     * Get the response for a failed password reset.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendResetFailedResponse(Request $request, string $response)
    {
        // Redirect back to the reset form with validation errors
        throw ValidationException::withMessages([
            'email' => [__($response)], // Error messages are usually tied to 'email' field for password reset failures
        ]);
    }
}
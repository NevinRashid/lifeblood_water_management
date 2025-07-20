<?php

namespace Modules\UsersAndTeams\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Modules\UsersAndTeams\Http\Requests\Auth\ForgotPasswordRequest;
use Modules\UsersAndTeams\Http\Requests\Auth\ResetPasswordRequest;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    /**
     * Send a password reset link to the user's email.
     *
     * @param ForgotPasswordRequest $request The validated request containing the user's email.
     */
    public function sendResetLinkEmail(ForgotPasswordRequest $request)
    {
        // Attempt to send the password reset link to the specified email
        $status = Password::sendResetLink($request->only('email'));

        // Check the result and return an appropriate response
        return $status === Password::RESET_LINK_SENT
            ? $this->successResponse(null, 'Password reset link sent to your email.')
            : $this->errorResponse('An error occurred while sending the reset link.', 400);
    }

    /**
     * Reset the user's password using the provided token.
     *
     * @param ResetPasswordRequest $request The validated request containing email, token, and new password.
     */
    public function reset(ResetPasswordRequest $request)
    {
        // Attempt to reset the password
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                // Update the user's password and generate a new remember token
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        // Check the result and return an appropriate response
        return $status === Password::PASSWORD_RESET
            ? $this->successResponse(null, 'Password has been successfully reset.')
            : $this->errorResponse('An error occurred while resetting the password.', 400);
    }
}

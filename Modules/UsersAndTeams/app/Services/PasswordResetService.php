<?php

namespace Modules\UsersAndTeams\Services;

use App\Facades\Logger;
use App\Services\Base\BaseService;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PasswordResetService extends BaseService
{
    /**
     * Send password reset link to the given email address
     *
     * @param string $email The email address of the user requesting a password reset
     * @return string The status of the password reset link sending process
     *
     * @throws \Illuminate\Validation\ValidationException If the email does not exist or sending fails
     */
    public function sendResetLink(string $email): string
    {
        return $this->handle(function () use ($email) {

            $status = Password::broker('users')->sendResetLink(['email' => $email]);

            if ($status !== Password::RESET_LINK_SENT) {

                Logger::auth('reset-link-failed', 'Failed to send password reset link', [
                    'email' => $email,
                    'status' => $status,
                ]);

                throw ValidationException::withMessages([
                    'email' => [__($status)],
                ]);
            }

            Logger::auth('reset-link-sent', 'Password reset link sent successfully', [
                'email' => $email,
            ]);

            return $status;
        });
    }

    /**
     * Reset the password for the given credentials
     *
     * @param array $credentials The request data including email, token, password, and confirmation
     * @return string The status of the password reset process
     *
     * @throws \Illuminate\Validation\ValidationException If the token or credentials are invalid
     */
    public function reset(array $credentials): string
    {
        return $this->handle(function () use ($credentials) {

            $status = Password::broker('users')->reset(
                $credentials,
                function ($user, $password) {
                    $user->forceFill([
                        'password' => Hash::make($password),
                    ])->setRememberToken(Str::random(60));

                    $user->save();

                    Logger::auth('password-reset', 'User password has been reset successfully', [
                        'user_id' => $user->id,
                        'email' => $user->email,
                    ]);

                    event(new PasswordReset($user));
                }
            );

            if ($status !== Password::PASSWORD_RESET) {

                Logger::auth('password-reset-failed', 'Password reset failed', [
                    'email' => $credentials['email'],
                    'status' => $status,
                ]);

                throw ValidationException::withMessages([
                    'email' => [__($status)],
                ]);
            }

            return $status;
        });
    }
}

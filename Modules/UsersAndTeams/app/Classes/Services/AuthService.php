<?php

namespace Modules\UsersAndTeams\Classes\Services;

use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Modules\UsersAndTeams\Models\User;

class AuthService
{
    /**
     * Register a new user.
     *
     * @param array $data The registration data (name, email, password)
     * @return User
     * @throws Exception
     */
    public function register(array $data)
    {
        try {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            // Attempt to send email verification
            try {
                $user->sendEmailVerificationNotification();
            } catch (Exception $e) {
                throw new Exception('Failed to send verification email. Please try again.');
            }

            return $user;
        } catch (Exception $e) {
            Log::error("Error registering user: " . $e->getMessage());
            throw new Exception('User registration failed.');
        }
    }

    /**
     * Log in a user.
     *
     * @param string $email The user's email
     * @param string $password The user's password
     * @return array
     * @throws Exception
     */
    public function login(string $email, string $password)
    {
        $user = User::where('email', $email)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            throw new Exception('Invalid credentials');
        }

        if (!$user->hasVerifiedEmail()) {
            throw new Exception('Please verify your email first.');
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'access_token' => $token,
            'token_type' => 'Bearer',
        ];
    }

    /**
     * Resend email verification notification.
     *
     * @param User $user
     * @return void
     * @throws Exception
     */
    public function resendVerificationEmail(User $user)
    {
        if ($user->hasVerifiedEmail()) {
            throw new Exception('Your email is already verified.');
        }

        try {
            $user->sendEmailVerificationNotification();
        } catch (Exception $e) {
            throw new Exception('Failed to send verification email. Please try again.');
        }
    }

    /**
     * Log out the currently authenticated user.
     *
     * @param User $user
     * @return void
     */
    public function logout(User $user)
    {
        $user->currentAccessToken()->delete();
    }
}

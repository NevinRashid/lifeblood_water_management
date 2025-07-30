<?php

namespace Modules\UsersAndTeams\Services;

use App\Facades\Logger;
use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Modules\UsersAndTeams\Models\User;

/**
 * Service class for handling authentication-related business logic.
 *
 * This service extends the BaseService to inherit common service functionalities
 * and implements AuthServiceInterface. It provides methods for user registration,
 * login, and logout, with integrated error handling and logging.
 *
 * @package App\Services
 */
class AuthService
{
    /**
     * Handles the execution of a given callback for authentication operations,
     * providing centralized exception handling specific to authentication.
     *
     * Catches AuthenticationException and general Throwable instances, re-throwing
     * them as custom AuthenticationException or generic Exception.
     *
     * @param Closure $callback The callback function to execute.
     * @return mixed The result of the executed callback.
     * @throws AuthenticationException If authentication fails (e.g., invalid credentials).
     * @throws \Exception For any other unhandled internal server errors.
     * @throws \Throwable For any other unhandled exceptions not specifically caught.
     */
    protected function handle(Closure $callback)
    {
        try {
            return $callback();
        } catch (AuthenticationException $e) {
            report($e);
            throw new AuthenticationException('Invalid credentials');
        } catch (\Throwable $e) {
            report($e);
            throw new \Exception('An internal server error occurred' . $e, 500);
        }
    }

    /**
     * Handle user registration.
     *
     * This method encrypts the user's password, creates a new user record,
     * generates a Sanctum plain text token, and logs the registration event.
     *
     * @param array $data Incoming data containing registration credentials (e.g., 'name', 'email', 'password').
     * @return array The registration success response including user's name and the generated token.
     * @throws AuthenticationException If an authentication-related issue occurs during registration.
     * @throws \Exception If an unexpected error occurs during the registration process.
     */
    public function register(array $data)
    {
        return  $this->handle(function () use ($data) {
            $data['password'] = Hash::make($data['password']);
            $user = activity()
                ->withoutLogs(function () use ($data) {
                    return  User::create($data);
                });

            if ($user->wasRecentlyCreated) {

                $success['name'] = $user->name;
                $success['token'] = $user->createToken('MyApp', ['*'], now()->addWeek())->plainTextToken;

                Logger::auth('registerd', 'the user has been registerd successfully', [
                    'user' => $user,
                ], $user);

                event(new Registered($user));

                return $success;
            }

            return false;
        });
    }

    /**
     * Handle user login.
     *
     * This method attempts to authenticate the user using provided credentials.
     * On successful authentication, it generates a Sanctum plain text token
     * and returns user details along with the token. It logs login attempts.
     *
     * @param array $data The incoming data containing email and password.
     * @return array|bool The authenticated user details and token on success, or false on authentication failure.
     * @throws AuthenticationException If authentication fails due to invalid credentials.
     * @throws \Exception If an unexpected error occurs during the login process.
     */
    public function login(array $data)
    {
        return  $this->handle(function () use ($data) {
            if (Auth::attempt(['email' => $data['email'], 'password' => $data['password']])) {

                $user = Auth::user();

                $token = $user->createToken('MyApp')->plainTextToken;

                Logger::auth('login', 'the user has been Logged in successfully', [
                    'user' => $user,
                ], $user);

                return [
                    'user' => $user,
                    'token' => $token,
                ];
            }

            Logger::auth('failed-login', 'An user could not Login!', [
                'email' => $data['email'],
            ]);

            return false;
        });
    }

    /**
     * Handle user logout.
     *
     * This method deletes the current access token for the authenticated user,
     * effectively logging them out. It logs the logout event.
     *
     * @return bool True if the current access token was successfully deleted, false otherwise.
     * @throws AuthenticationException If there is no authenticated user or token to delete.
     * @throws \Exception If an unexpected error occurs during the logout process.
     */
    public function logout()
    {
        return $this->handle(function () {

            $user = auth()->user();

            Logger::auth('logout', 'An user has been logged out successfully', [
                'user' => $user,
            ]);

            return $user->currentAccessToken()->delete();
        });
    }
}

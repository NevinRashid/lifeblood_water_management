<?php

namespace Modules\UsersAndTeams\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\UsersAndTeams\Classes\Services\AuthService;
use Modules\UsersAndTeams\Http\Requests\Auth\LoginRequest;
use Modules\UsersAndTeams\Http\Requests\Auth\RegisterRequest;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Register a new user.
     *
     * @param RegisterRequest $request
     */
    public function register(RegisterRequest $request)
    {
        try {
            $user = $this->authService->register($request->validated());

            return $this->successResponse($user, 'Registered successfully. Please verify your email.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Log in a user.
     *
     * @param LoginRequest $request
     */
    public function login(LoginRequest $request)
    {
        try {
            $data = $this->authService->login($request->validated());

            return $this->successResponse($data, 'Login successful');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 401);
        }
    }

    /**
     * Resend the email verification notification.
     *
     * @param Request $request
     */
    public function resendVerificationEmail(Request $request)
    {
        try {
            $this->authService->resendVerificationEmail($request->user());

            return $this->successResponse(null, 'Verification email resent successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Log out the currently authenticated user.
     *
     * @param Request $request
     */
    public function logout(Request $request)
    {
        try {
            $this->authService->logout($request->user());

            return $this->successResponse(null, 'Logged out successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}

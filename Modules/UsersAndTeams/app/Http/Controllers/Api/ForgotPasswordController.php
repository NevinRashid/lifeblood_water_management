<?php

namespace Modules\UsersAndTeams\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\UsersAndTeams\Http\Requests\Auth\ForgotPasswordRequest;
use Modules\UsersAndTeams\Services\PasswordResetService;

class ForgotPasswordController extends Controller
{
    /**
     * Service to handle forgetPassword-related logic 
     * and separating it from the controller
     * @var PasswordResetService
     */
    protected PasswordResetService $passwordResetService;

    /**
     * ForgotPasswordController constructor
     *
     * @param PasswordResetService $passwordResetService
     */
    public function __construct(PasswordResetService $passwordResetService)
    {
        // Inject the ForgotPasswordController to handle forgetPassword-related logic
        $this->passwordResetService = $passwordResetService;
    }

    /**
     * Handle the request to send a password reset link to the user
     *
     * @param ForgotPasswordRequest $request
     * @return JsonResponse
     */
    public function sendResetLinkEmail(ForgotPasswordRequest $request): JsonResponse
    {
        $this->passwordResetService->sendResetLink($request->input('email'));

        return $this->successResponse('Reset link sent to your email');
    }
}

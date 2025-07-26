<?php

namespace Modules\UsersAndTeams\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\UsersAndTeams\Http\Requests\Auth\LoginRequest;
use Modules\UsersAndTeams\Http\Requests\Auth\RegistrationRequest;
use Modules\UsersAndTeams\Models\User;
use Modules\UsersAndTeams\Services\AuthService;

class AuthController extends Controller
{

    /**
     * Service to handle auth-related logic 
     * and separating it from the controller
     * 
     * @var AuthService
     */
    protected $authService;

    /**
     * AuthController constructor
     *
     * @param AuthService $authService
     */
    public function __construct(AuthService $authService)
    {
        // Inject the AuthService to handle auth-related logic
        $this->authService = $authService;
    }

    /**
     * Handle user registration
     *
     * @param RegistrationRequest $request Validates the registration credentials
     * @return JsonResponse The success response including token and user details or an error message
     */
    public function register(RegistrationRequest $request)
    {
        $data = $request->validated();
        $success = $this->authService->register($data);

        if ($success)
            return $this->registerResponse($success);

        return $this->errorResponse("Something Went Wrong");
    }


    /**
     * Handle user login
     *
     * @param LoginRequest $request The incoming request containing email and password
     * @return JsonResponse success response including user details and token (error message on failure)
     */
    public function login(LoginRequest $request)
    {
        $data = $request->validated();
        $success = $this->authService->login($data);

        if ($success)
            return $this->loginResponse($success['user'], $success['token']);

        return $this->errorResponse("Your inputs do not match our credential!");
    }

    /**
     * Handle user logout
     *
     * @return JsonResponse The success message or an error message.
     */
    public function logout()
    {
        $success = $this->authService->logout();

        if ($success)
            return $this->logoutResponse();

        return $this->errorResponse('Logged out faild');
    }
}

<?php

use App\Facades\Logger;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Validation\UnauthorizedException;
use Modules\UsersAndTeams\Exceptions\EmailNotVerifiedException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'verified.api' => Modules\UsersAndTeams\Http\Middleware\EnsureEmailIsVerifiedApi::class,
            'set_locale_lang' =>  App\Http\Middleware\SetLocaleLang::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (UnauthorizedException  $e, $request) {
            Logger::security('forbidden', 'An Unauthorized User Trying To Access', ['message' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized Access, You do not have the required permission',
                'status'  => 403,
            ], 403);
        });
        $exceptions->render(function (ThrottleRequestsException $e, $request) {
            Logger::security('throttle', 'Too Many Attempts!', ['input' => $request->except(['password'])]);
            return response()->json([
                'success' => false,
                'message' => 'Too many attempt, Please try again after a few minutes',
                'status'  => 429,
            ], 429);
        });
        $exceptions->render(function (NotFoundHttpException $e, $request) {
            Logger::log('model-not-found', 'Model Not Found In The Request', ['message' => $e->getMessage()], 'model');
            return response()->json([
                'success' => false,
                'message' => 'Not Found',
            ], 404);
        });
        $exceptions->render(function (AccessDeniedHttpException $e, $request) {
            Logger::security('forbidden', 'An Unauthorized User Trying To Access', ['message' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized Access, You do not have the required permission',
                'status'  => 403,
            ], 403);
        });
        $exceptions->render(function (EmailNotVerifiedException $e, $request) {
            Logger::log('forbidden', 'User Trying To Access Without Verfying Email', [$e->getMessage()], 'unverified-email');
            return response()->json([
                'success' => false,
                'message' => 'Your email address is not verified',
                'status'  => 403,
            ], 403);
        });
    })->create();

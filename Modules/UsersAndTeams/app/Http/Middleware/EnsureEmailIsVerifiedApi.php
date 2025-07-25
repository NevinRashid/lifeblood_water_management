<?php

namespace Modules\UsersAndTeams\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\UsersAndTeams\Exceptions\EmailNotVerifiedException;

class EnsureEmailIsVerifiedApi
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {

        $user = auth()->user();

        if (! $user || ! $user->hasVerifiedEmail()) {
            throw new EmailNotVerifiedException($user);
        }

        return $next($request);
    }
}

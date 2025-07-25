<?php

namespace Modules\UsersAndTeams\Services;

use App\Facades\Logger;
use App\Services\Base\BaseService;
use Illuminate\Auth\Events\Verified;
use Modules\UsersAndTeams\Models\User;

class VerificationService extends BaseService
{
    /**
     * Send verification email if not verified yet
     *
     * @param User $user
     * @return string
     */
    public function sendVerificationEmail(User $user): string
    {

        return $this->handle(function () use ($user) {

            if ($user->hasVerifiedEmail()) {
                return 'Email already verified';
            }

            $user->sendEmailVerificationNotification();

            Logger::auth('verefication-email', 'a verification email send to user', ['user' => $user]);

            return 'Verification link sent to your email';
        });
    }

    /**
     * Verify user email if not already verified
     *
     * @param int $id
     * @return string
     */
    public function verify(int $id): string
    {
        return $this->handle(function () use ($id) {

            $user = User::findOrFail($id);

            if ($user->hasVerifiedEmail()) {
                return 'Email already verified';
            }

            $user->markEmailAsVerified();
            event(new Verified($user));

            Logger::auth('verified-email', 'an email has been verified', ['user' => $user]);

            return 'Email successfully verified';
        });
    }
}

<?php

namespace App\Listeners\Auth;

use Illuminate\Auth\Events\Failed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class UserFailedLogin
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Failed $event): void
    {
        $email= $event->credentials['email']?? 'unknown';
        $user= $event->user;
        if(!$user){
            Log::warning('Login failed: email not registered ('.$email.')');
        }
        else{
            Log::warning('Login failed: password is incorrect ('.$email.')');
        }
    }
}

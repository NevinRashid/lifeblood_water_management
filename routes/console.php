<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Auth;
use Modules\UsersAndTeams\Models\User;
use Illuminate\Support\Facades\Artisan;
use App\Jobs\SendPeriodicWaterReportJob;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('sanctum:prune-expired')->daily()->withoutOverlapping();

Schedule::command('activitylog:clean')->daily()->withoutOverlapping();

Schedule::command('auth:clear-resets')->hourly()->withoutOverlapping();

Schedule::call(function () {
    $user = User::find(Auth::user());

    if ($user) {
        SendPeriodicWaterReportJob::dispatch($user);
    }
})->dailyAt('08:00');

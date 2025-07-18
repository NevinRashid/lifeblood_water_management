<?php

use Illuminate\Support\Facades\Route;
use Modules\UsersAndTeams\Http\Controllers\UsersAndTeamsController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('usersandteams', UsersAndTeamsController::class)->names('usersandteams');
});

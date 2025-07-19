<?php

use Illuminate\Support\Facades\Route;
use Modules\UsersAndTeams\Http\Controllers\UsersAndTeamsController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('usersandteams', UsersAndTeamsController::class)->names('usersandteams');
});

<?php

use Illuminate\Support\Facades\Route;
use Modules\UsersAndTeams\Http\Controllers\Api\AuthController;
use Modules\UsersAndTeams\Http\Controllers\Api\VerificationController;

Route::post('register', [AuthController::class, 'register'])->middleware('throttle:5,5');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,5');

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    //Route::apiResource('usersandteams', UsersAndTeamsController::class)->names('usersandteams');
    Route::post('logout', [AuthController::class, 'logout']);
});

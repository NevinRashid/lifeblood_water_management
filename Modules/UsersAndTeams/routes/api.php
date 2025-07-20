<?php

use Illuminate\Support\Facades\Route;
use Modules\UsersAndTeams\Http\Controllers\Auth\AuthController;
use Modules\UsersAndTeams\Http\Controllers\UsersAndTeamsController;

Route::post('register', [AuthController::class, 'register']);
Route::post('/login',[AuthController::class,'login']);

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    //Route::apiResource('usersandteams', UsersAndTeamsController::class)->names('usersandteams');
    Route::post('logout', [AuthController::class, 'logout']);
});

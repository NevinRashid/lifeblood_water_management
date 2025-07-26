<?php

use Illuminate\Support\Facades\Route;
use Modules\UsersAndTeams\Http\Controllers\Api\Auth\AuthController;
use Modules\UsersAndTeams\Http\Controllers\TeamController;
use Modules\UsersAndTeams\Http\Controllers\UsersAndTeamsController;

Route::post('register', [AuthController::class, 'register']);
Route::post('login',[AuthController::class,'login']);

Route::middleware(['auth:sanctum'])->group(function () {
    //Route::apiResource('usersandteams', UsersAndTeamsController::class)->names('usersandteams');
    Route::post('logout', [AuthController::class, 'logout']);
    Route::apiResource('teams', TeamController::class)->names('teams');
    Route::post('teams/{team}/assign_members', [TeamController::class, 'assignMembers']);
    Route::post('teams/{team}/remove_members', [TeamController::class, 'removeMembers']);

});

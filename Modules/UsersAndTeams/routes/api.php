<?php

use Illuminate\Support\Facades\Route;
use Modules\UsersAndTeams\Http\Controllers\TeamController;
use Modules\UsersAndTeams\Http\Controllers\Api\AuthController;

use Modules\UsersAndTeams\Http\Controllers\UsersAndTeamsController;
use Modules\UsersAndTeams\Http\Controllers\Api\VerificationController;
use Modules\UsersAndTeams\Http\Controllers\Api\RoleManagementController;


Route::post('register', [AuthController::class, 'register'])->middleware(['throttle:5,5', 'set_locale_lang']);
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,5');

Route::middleware(['auth:sanctum'])->group(function () {
    //Route::apiResource('usersandteams', UsersAndTeamsController::class)->names('usersandteams');
    Route::post('logout', [AuthController::class, 'logout']);
    Route::apiResource('teams', TeamController::class)->names('teams');
    Route::post('teams/{team}/assign_members', [TeamController::class, 'assignMembers']);
    Route::post('teams/{team}/remove_members', [TeamController::class, 'removeMembers']);
});
Route::middleware(['auth:sanctum'])->prefix('admin')->group(function () {
    Route::get('/roles', [RoleManagementController::class, 'index']);
    Route::post('/users/{user}/roles/assign', [RoleManagementController::class, 'assign']);
    Route::post('/users/{user}/roles/revoke', [RoleManagementController::class, 'revoke']);
    Route::put('/users/{user}/roles/update', [RoleManagementController::class, 'update']);
});


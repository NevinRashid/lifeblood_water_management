<?php

use Illuminate\Support\Facades\Route;
use Modules\ActivityLog\Http\Controllers\Api\ActivityLogController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('activitylogs', ActivityLogController::class)->names('activitylog');
});

<?php

use Illuminate\Support\Facades\Route;
use Modules\Sensors\Http\Controllers\SensorsController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('sensors', SensorsController::class)->names('sensors');
});

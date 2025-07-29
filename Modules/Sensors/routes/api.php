<?php

use Illuminate\Support\Facades\Route;
use Modules\Sensors\Http\Controllers\SensorReadingController;
use Modules\Sensors\Http\Controllers\SensorsController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('sensors', SensorsController::class)->names('sensor');
    Route::apiResource('sensor_readings', SensorReadingController::class)->names('sensor_reading');
});

<?php

use Illuminate\Support\Facades\Route;
use Modules\WaterDistributionOperations\Http\Controllers\WaterDistributionOperationsController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('waterdistributionoperations', WaterDistributionOperationsController::class)->names('waterdistributionoperations');
});

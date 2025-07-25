<?php

use Illuminate\Support\Facades\Route;
use Modules\DistributionNetwork\Http\Controllers\DistributionNetworkController;
use Modules\DistributionNetwork\Http\Controllers\PumpingStationsController;
use Modules\DistributionNetwork\Http\Controllers\ReservoirController;
use Modules\DistributionNetwork\Http\Controllers\ValvesController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('distributionnetworks', DistributionNetworkController::class)->names('distributionnetwork');
    Route::apiResource('valves',ValvesController::class)->names('valve');
    Route::apiResource('pumping_stations',PumpingStationsController::class)->names('pumping_station');
    Route::apiResource('reservoirs',ReservoirController::class)->names('reservoirs');
});

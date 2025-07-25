<?php

use Illuminate\Support\Facades\Route;
use Modules\DistributionNetwork\Http\Controllers\DistributionNetworkController;
use Modules\DistributionNetwork\Http\Controllers\PumpingStationsController;
use Modules\DistributionNetwork\Http\Controllers\ReservoirController;
use Modules\DistributionNetwork\Http\Controllers\ValvesController;
use Modules\DistributionNetwork\Http\Controllers\DistributionPointController;
use Modules\DistributionNetwork\Http\Controllers\PipeController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('distributionnetworks', DistributionNetworkController::class)->names('distributionnetwork');
    Route::apiResource('valves',ValvesController::class)->names('valve');
    Route::apiResource('pumping_stations',PumpingStationsController::class)->names('pumping_station');
    Route::apiResource('reservoirs',ReservoirController::class)->names('reservoirs');
    Route::apiResource('pipes', PipeController::class)->names('pipe');
    Route::apiResource('distributionpoints', DistributionPointController::class)->names('distributionpoint');
});

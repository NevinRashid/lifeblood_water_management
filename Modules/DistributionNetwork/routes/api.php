<?php

use Illuminate\Support\Facades\Route;
use Modules\DistributionNetwork\Http\Controllers\DistributionNetworkController;
use Modules\DistributionNetwork\Http\Controllers\DistributionPointController;
use Modules\DistributionNetwork\Http\Controllers\PipeController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('distributionnetworks', DistributionNetworkController::class)->names('distributionnetwork');
    Route::apiResource('pipes', PipeController::class)->names('pipe');
    Route::apiResource('distributionpoints', DistributionPointController::class)->names('distributionpoint');
});

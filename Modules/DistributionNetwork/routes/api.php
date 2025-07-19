<?php

use Illuminate\Support\Facades\Route;
use Modules\DistributionNetwork\Http\Controllers\DistributionNetworkController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('distributionnetworks', DistributionNetworkController::class)->names('distributionnetwork');
});

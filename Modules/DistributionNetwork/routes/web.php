<?php

use Illuminate\Support\Facades\Route;
use Modules\DistributionNetwork\Http\Controllers\DistributionNetworkController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('distributionnetworks', DistributionNetworkController::class)->names('distributionnetwork');
});

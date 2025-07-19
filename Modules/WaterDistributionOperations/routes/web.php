<?php

use Illuminate\Support\Facades\Route;
use Modules\WaterDistributionOperations\Http\Controllers\WaterDistributionOperationsController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('waterdistributionoperations', WaterDistributionOperationsController::class)->names('waterdistributionoperations');
});

<?php

use Illuminate\Support\Facades\Route;
use Modules\WaterSources\Http\Controllers\WaterSourcesController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('watersources', WaterSourcesController::class)->names('watersources');
});

<?php

use Illuminate\Support\Facades\Route;
use Modules\WaterSources\Http\Controllers\Api\WaterExtractionController;
use Modules\WaterSources\Http\Controllers\HeatmapController;
use Modules\WaterSources\Http\Controllers\WaterSourcesController;
use Modules\WaterSources\Http\Controllers\TestingParameterController;
use Modules\WaterSources\Http\Controllers\WaterQualityTestController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {

    Route::get('/water-situation/overview', [WaterSourcesController::class, 'overview']);
    Route::apiResource('watersources', WaterSourcesController::class)->names('watersources');
    Route::post('watersources/{waterSource}/add-media', [WaterSourcesController::class, 'addMedia'])->name('water-sources.add-media');
    Route::apiResource('water-extractions', WaterExtractionController::class)->names('water-extractions');
    Route::apiResource('testing-parameters', TestingParameterController::class);
    Route::apiResource('water-quality-tests', WaterQualityTestController::class);
    Route::get('heatmap', [HeatmapController::class, 'index']);
});






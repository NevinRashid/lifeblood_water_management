<?php

use Illuminate\Support\Facades\Route;
use Modules\WaterSources\Http\Controllers\HeatmapController;
use Modules\WaterSources\Http\Controllers\WaterSourcesController;
use Modules\WaterSources\Http\Controllers\TestingParameterController;
use Modules\WaterSources\Http\Controllers\WaterQualityTestController;
use Modules\WaterSources\Http\Controllers\Api\WaterExtractionController;
use Modules\WaterSources\Http\Controllers\WaterSourceParameterController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('watersources', WaterSourcesController::class)->names('watersources');
    Route::post('watersources/{waterSource}/add-media', [WaterSourcesController::class, 'addMedia'])->name('water-sources.add-media');
    Route::apiResource('water-extractions', WaterExtractionController::class)->names('water-extractions');
    Route::apiResource('testing-parameters', TestingParameterController::class);
    Route::apiResource('water-source-parameter', WaterSourceParameterController::class);
    Route::get('water-source-parameters/{water_source}', [WaterSourceParameterController::class, 'index']);
    Route::post('water-source-parameters/{water_source}', [WaterSourceParameterController::class, 'store']);
    Route::put('water-source-parameters/{water_source}', [WaterSourceParameterController::class, 'update']);
    Route::get('/water-quality-tests/{id}/report', [WaterQualityTestController::class, 'generateReport']);
    Route::delete('water-source-parameters/{water_source}/{parameter}', [WaterSourceParameterController::class, 'destroy']);
    Route::apiResource('water-quality-tests', WaterQualityTestController::class);
    Route::get('heatmap', [HeatmapController::class, 'index']);
});






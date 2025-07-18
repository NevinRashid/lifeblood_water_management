<?php

use Illuminate\Support\Facades\Route;
use Modules\TankerLogistics\Http\Controllers\TankerLogisticsController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('tankerlogistics', TankerLogisticsController::class)->names('tankerlogistics');
});

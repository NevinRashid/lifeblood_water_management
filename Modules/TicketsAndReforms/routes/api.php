<?php

use Illuminate\Support\Facades\Route;
use Modules\TicketsAndReforms\Http\Controllers\TicketsAndReformsController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('ticketsandreforms', TicketsAndReformsController::class)->names('ticketsandreforms');
});

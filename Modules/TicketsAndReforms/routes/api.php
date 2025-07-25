<?php

use Illuminate\Support\Facades\Route;
use Modules\TicketsAndReforms\Http\Controllers\ReformController;
use Modules\TicketsAndReforms\Http\Controllers\TicketsAndReformsController;
use Modules\TicketsAndReforms\Http\Controllers\TroubleTicketController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('troubletickets', TroubleTicketController::class)->names('troubletickets');
    Route::patch('troubletickets/{troubleticket}/changeTroubleStatus', [TroubleTicketController::class, 'changeTroubleStatus']);

    Route::apiResource('reforms', ReformController::class)->names('reforms');
    Route::post('reforms/{reform}/addImage', [ReformController::class, 'addImage']);

});

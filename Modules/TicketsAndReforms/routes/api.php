<?php

use Illuminate\Support\Facades\Route;
use Modules\TicketsAndReforms\Http\Controllers\ReformController;
use Modules\TicketsAndReforms\Http\Controllers\TicketsAndReformsController;
use Modules\TicketsAndReforms\Http\Controllers\TroubleTicketController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('troubletickets', TroubleTicketController::class)->names('troubletickets');
    Route::patch('troubletickets/{troubleticket}/changeTroubleStatus', [TroubleTicketController::class, 'changeTroubleStatus']);
    Route::post('troubletickets/{troubleticket}/approve_trouble', [TroubleTicketController::class, 'approveTrouble']);
    Route::post('troubletickets/{troubleticket}/approve_complaint', [TroubleTicketController::class, 'approveComplaint']);
    Route::post('troubletickets/{troubleticket}/reject', [TroubleTicketController::class, 'reject']);

    Route::apiResource('reforms', ReformController::class)->names('reforms');
    Route::post('reforms/{reform}/addImage', [ReformController::class, 'addImage']);
    Route::get('reforms/{reform}/getImages_url', [ReformController::class, 'getImagesUrl']);

});

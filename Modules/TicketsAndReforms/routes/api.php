<?php

use Illuminate\Support\Facades\Route;
use Modules\TicketsAndReforms\Http\Controllers\ReformController;
use Modules\TicketsAndReforms\Http\Controllers\TicketsAndReformsController;
use Modules\TicketsAndReforms\Http\Controllers\TroubleTicketController;

Route::middleware(['auth:sanctum','set_locale_lang'])->group(function () {
    Route::apiResource('troubletickets', TroubleTicketController::class)->names('troubletickets');
    Route::patch('troubletickets/{troubleticket}/changeTroubleStatus', [TroubleTicketController::class, 'changeTroubleStatus']);
    Route::post('troubletickets/{troubleticket}/approve_trouble', [TroubleTicketController::class, 'approveTrouble']);
    Route::post('troubletickets/{troubleticket}/review_complaint', [TroubleTicketController::class, 'reviewComplaint']);
    Route::post('troubletickets/{troubleticket}/reject', [TroubleTicketController::class, 'reject']);
    Route::get('citizens_troubles', [TroubleTicketController::class, 'getCitizenTroubles']);
    Route::get('citizens_complaints', [TroubleTicketController::class, 'getCitizenComplaints']);


    Route::apiResource('reforms', ReformController::class)->names('reforms');
    Route::post('reforms/{reform}/addImage', [ReformController::class, 'addImage']);
    Route::get('reforms/{reform}/getImages_url', [ReformController::class, 'getImagesUrl']);

});

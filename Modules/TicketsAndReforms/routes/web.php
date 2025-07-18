<?php

use Illuminate\Support\Facades\Route;
use Modules\TicketsAndReforms\Http\Controllers\TicketsAndReformsController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('ticketsandreforms', TicketsAndReformsController::class)->names('ticketsandreforms');
});

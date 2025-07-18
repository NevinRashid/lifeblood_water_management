<?php

use Illuminate\Support\Facades\Route;
use Modules\TankerLogistics\Http\Controllers\TankerLogisticsController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('tankerlogistics', TankerLogisticsController::class)->names('tankerlogistics');
});

<?php

use Illuminate\Support\Facades\Route;
use Modules\WaterSources\Http\Controllers\WaterSourcesController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('watersources', WaterSourcesController::class)->names('watersources');
});

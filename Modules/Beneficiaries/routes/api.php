<?php

use Illuminate\Support\Facades\Route;
use Modules\Beneficiaries\Http\Controllers\BeneficiariesController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('beneficiaries', BeneficiariesController::class)->names('beneficiaries');
});

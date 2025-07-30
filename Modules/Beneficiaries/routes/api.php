<?php

use Illuminate\Support\Facades\Route;
use Modules\Beneficiaries\Http\Controllers\Api\BeneficiaryController;
use Modules\Beneficiaries\Http\Controllers\Api\WaterQuotaController;

Route::middleware(['auth:sanctum', 'set_locale_lang'])->group(function () {
    Route::apiResource('beneficiaries', BeneficiaryController::class)->names('beneficiaries');
    Route::apiResource('water-quotas', WaterQuotaController::class)->names('water-quotas');
});

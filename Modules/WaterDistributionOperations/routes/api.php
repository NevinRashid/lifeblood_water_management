<?php

use Illuminate\Support\Facades\Route;
use Modules\WaterDistributionOperations\Http\Controllers\TankerController;
use Modules\WaterDistributionOperations\Http\Controllers\TankerUserController;
use Modules\WaterDistributionOperations\Http\Controllers\DeliveryRouteController;
use Modules\WaterDistributionOperations\Http\Controllers\RouteDeliveryController;
use Modules\WaterDistributionOperations\Http\Controllers\ReservoirsActivityController;
use Modules\WaterDistributionOperations\Http\Controllers\WaterDistributionOperationsController;
use Modules\WaterDistributionOperations\Models\ReservoirActivity;

Route::middleware(['auth:sanctum','set_locale_lang'])->prefix('v1')->group(function () {
    Route::apiResource('distribution-operations', WaterDistributionOperationsController::class);
    Route::apiResource('reservoirs_activity', ReservoirsActivityController::class);
    Route::get('reservoirs_activity/{reservoir_activity}/get_current_level', [ReservoirsActivityController::class,'getCurrentLevel']);

    Route::apiResource('tankers', TankerController::class);
    Route::prefix('tankers/{tanker}')->group(function () {
        Route::get('/users', [TankerUserController::class, 'index']);
        Route::post('/users', [TankerUserController::class, 'store']);
        Route::delete('/users/{user}', [TankerUserController::class, 'destroy']);
});
        Route::apiResource('delivery-routes', DeliveryRouteController::class);
        Route::apiResource('delivery-routes.deliveries', RouteDeliveryController::class)
                ->parameters([
                    'delivery-routes' => 'deliveryRoute',
                    'deliveries' => 'routeDelivered'
                ])
                ->shallow();

}
);

Route::apiResource('reservoire-activity', ReservoirsActivityController::class);
Route::get('reservoire-activity/{reservoir}/current-level', [ReservoirsActivityController::class, 'getCurrentLevel']);

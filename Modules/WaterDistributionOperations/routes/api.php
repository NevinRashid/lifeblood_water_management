<?php

use Illuminate\Support\Facades\Route;
use Modules\WaterDistributionOperations\Http\Controllers\TankerController;
use Modules\WaterDistributionOperations\Http\Controllers\TankerUserController;
use Modules\WaterDistributionOperations\Http\Controllers\DeliveryRouteController;
use Modules\WaterDistributionOperations\Http\Controllers\RouteDeliveryController;
use Modules\WaterDistributionOperations\Http\Controllers\ReservoirActivityController;
use Modules\WaterDistributionOperations\Http\Controllers\ReservoirsActivityController;
use Modules\WaterDistributionOperations\Http\Controllers\WaterDistributionOperationsController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('distribution-operations', WaterDistributionOperationsController::class);
    
   
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

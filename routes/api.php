<?php

use App\Http\Api\PalletsController;
use App\Http\Api\ReceivingPlansController;
use App\Http\Api\ShippingRequestsController;
use App\Http\Api\WarehouseLocationsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::controller(App\Http\Api\AuthController::class)->group(function () {
    Route::post('/login', 'login');
    Route::post('/login-with-employee-code', 'loginWithEmployeeCode');
    Route::get('/check-user/{user}', 'checkEmployeeCode');
    Route::post('/logout', 'logout')->middleware('auth:sanctum');

});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Pallet routes
    Route::apiResource('pallets', PalletsController::class);
    Route::get('pallets/search', [PalletsController::class, 'searchByPalletId']);
    Route::get('pallets/{pallet}/check-crate', [PalletsController::class, 'checkPalletWithCrateCode']);
    Route::post('pallets/update-pallet-with-location', [PalletsController::class, 'updatePalletWithLocation']);
         
    // Receiving Plan routes
    Route::apiResource('receiving-plans', ReceivingPlansController::class)
         ->only(['index', 'store', 'show','update']);

    Route::apiResource('shipping-requests', ShippingRequestsController::class);

    Route::post('shipping-requests/{id}/check-out-pallet', [ShippingRequestsController::class, 'checkOutPallet']);


    Route::apiResource('warehouse-locations', WarehouseLocationsController::class);
});

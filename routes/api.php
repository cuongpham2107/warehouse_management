<?php

use App\Http\Api\PalletsController;
use App\Http\Api\ReceivingPlansController;
use App\Http\Api\WarehouseLocationsController;
use App\Http\Api\InventoryMovementsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::controller(App\Http\Api\AuthController::class)->group(function () {
    Route::post('/login', 'login');
    Route::post('/logout', 'logout')->middleware('auth:sanctum');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Pallet routes
    Route::apiResource('pallets', PalletsController::class);
    
    // Warehouse Location routes
    Route::apiResource('warehouse-locations', WarehouseLocationsController::class);
    
    // Inventory Movement routes
    Route::apiResource('inventory-movements', InventoryMovementsController::class)
         ->only(['index', 'store', 'show']);
         
    // Receiving Plan routes
    Route::apiResource('receiving-plans', ReceivingPlansController::class)
         ->only(['index', 'store', 'show']);
});

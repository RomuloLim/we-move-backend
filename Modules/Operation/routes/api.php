<?php

use Illuminate\Support\Facades\Route;
use Modules\Operation\Http\Controllers\VehicleController;
use Modules\User\Enums\Permission;

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    $viewVehiclesPermission = Permission::ViewVehicles->value;

    Route::middleware("permission:{$viewVehiclesPermission}")->group(function () {
        Route::get('vehicles', [VehicleController::class, 'index'])
            ->name('operation.vehicles.index');

        Route::get('vehicles/{vehicle}', [VehicleController::class, 'show'])
            ->name('operation.vehicles.show');
    });

    $manageVehiclesPermission = Permission::ManageVehicles->value;
    Route::middleware("permission:{$manageVehiclesPermission}")->group(function () {
        Route::post('vehicles', [VehicleController::class, 'store'])
            ->name('operation.vehicles.store');

        Route::put('vehicles/{vehicle}', [VehicleController::class, 'update'])
            ->name('operation.vehicles.update');

        Route::delete('vehicles/{vehicle}', [VehicleController::class, 'destroy'])
            ->name('operation.vehicles.destroy');
    });
});

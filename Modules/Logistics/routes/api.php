<?php

use Illuminate\Support\Facades\Route;
use Modules\Logistics\Http\Controllers\{RouteController, StopController, UserRouteController, VehicleController};
use Modules\User\Enums\Permission;

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    $viewVehiclesPermission = Permission::ViewVehicles->value;

    Route::middleware("permission:{$viewVehiclesPermission}")->group(function () {
        Route::get('vehicles', [VehicleController::class, 'index'])
            ->name('logistics.vehicles.index');

        Route::get('vehicles/{vehicle}', [VehicleController::class, 'show'])
            ->name('logistics.vehicles.show');
    });

    $manageVehiclesPermission = Permission::ManageVehicles->value;
    Route::middleware("permission:{$manageVehiclesPermission}")->group(function () {
        Route::post('vehicles', [VehicleController::class, 'store'])
            ->name('logistics.vehicles.store');

        Route::put('vehicles/{vehicle}', [VehicleController::class, 'update'])
            ->name('logistics.vehicles.update');

        Route::delete('vehicles/{vehicle}', [VehicleController::class, 'destroy'])
            ->name('logistics.vehicles.destroy');
    });

    // Route routes
    $viewRoutesPermission = Permission::ViewRoutes->value;
    Route::prefix('routes')->middleware("permission:{$viewRoutesPermission}")->group(function () {
        Route::get('/', [RouteController::class, 'index'])
            ->name('logistics.routes.index');

        Route::get('/{route}', [RouteController::class, 'show'])
            ->name('logistics.routes.show');
    });

    $manageRoutesPermission = Permission::ManageRoutes->value;
    Route::middleware("permission:{$manageRoutesPermission}")->group(function () {
        Route::post('routes', [RouteController::class, 'store'])
            ->name('logistics.routes.store');

        Route::put('routes/{route}', [RouteController::class, 'update'])
            ->name('logistics.routes.update');

        Route::delete('routes/{route}', [RouteController::class, 'destroy'])
            ->name('logistics.routes.destroy');
    });

    // Stop routes
    $viewStopsPermission = Permission::ViewStops->value;
    Route::prefix('stops')->middleware("permission:{$viewStopsPermission}")->group(function () {
        Route::get('/', [StopController::class, 'index'])
            ->name('logistics.stops.index');

        Route::get('/{stop}', [StopController::class, 'show'])
            ->name('logistics.stops.show');
    });

    $manageStopsPermission = Permission::ManageStops->value;
    Route::middleware("permission:{$manageStopsPermission}")->group(function () {
        Route::post('stops', [StopController::class, 'store'])
            ->name('logistics.stops.store');

        Route::patch('stops/update-order', [StopController::class, 'updateOrder'])
            ->name('logistics.stops.update_order');

        Route::delete('stops/{stop}', [StopController::class, 'destroy'])
            ->name('logistics.stops.destroy');
    });

    // User Routes - Gerenciamento de vínculos entre usuários e rotas
    $manageRoutesPermission = Permission::ManageRoutes->value;
    Route::prefix('user-routes')->middleware("permission:{$manageRoutesPermission}")->group(function () {
        Route::get('/user/{userId}', [UserRouteController::class, 'index'])
            ->name('logistics.user_routes.index');

        Route::get('/all-ordered-by-user/{userId}', [UserRouteController::class, 'getAllOrderedByUser'])
            ->name('logistics.user_routes.all_ordered');

        Route::post('/link', [UserRouteController::class, 'linkRoutes'])
            ->name('logistics.user_routes.link');

        Route::delete('/unlink', [UserRouteController::class, 'unlinkRoutes'])
            ->name('logistics.user_routes.unlink');
    });
});

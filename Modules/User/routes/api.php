<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| User API Routes
|--------------------------------------------------------------------------
*/


Route::prefix('v1/users')->group(function () {
    Route::post('/', [UserController::class, 'store'])->name('users.store');

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('users.index');
        Route::put('{userId}/type', [UserController::class, 'updateType'])->name('users.update-type');
    });
});

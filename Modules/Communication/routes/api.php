<?php

use Illuminate\Support\Facades\Route;
use Modules\Communication\Http\Controllers\NoticeController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::prefix('notices')->name('notices.')->group(function () {
        Route::get('/', [NoticeController::class, 'index'])->name('index');
        Route::get('/unread', [NoticeController::class, 'unread'])->name('unread');
        Route::post('/', [NoticeController::class, 'store'])->name('store');
        Route::post('/{id}/mark-as-read', [NoticeController::class, 'markAsRead'])->name('mark-as-read');
        Route::delete('/{id}', [NoticeController::class, 'destroy'])->name('destroy');
    });
});

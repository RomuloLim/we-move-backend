<?php

use Illuminate\Support\Facades\Route;
use Modules\Operation\Http\Controllers\{CourseController, InstitutionController, InstitutionCourseController, StudentRequisitionController, VehicleController};
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

    // Institution routes
    $viewInstitutionsPermission = Permission::ViewInstitutions->value;
    Route::middleware("permission:{$viewInstitutionsPermission}")->group(function () {
        Route::get('institutions', [InstitutionController::class, 'index'])
            ->name('operation.institutions.index');

        Route::get('institutions/by-course/{course}', [InstitutionCourseController::class, 'getInstitutionsByCourse'])
            ->name('operation.institutions.by-course');

        Route::get('institutions/{institution}', [InstitutionController::class, 'show'])
            ->name('operation.institutions.show');
    });

    $manageInstitutionsPermission = Permission::ManageInstitutions->value;
    Route::middleware("permission:{$manageInstitutionsPermission}")->group(function () {
        Route::post('institutions', [InstitutionController::class, 'store'])
            ->name('operation.institutions.store');

        Route::put('institutions/{institution}', [InstitutionController::class, 'update'])
            ->name('operation.institutions.update');

        Route::delete('institutions/{institution}', [InstitutionController::class, 'destroy'])
            ->name('operation.institutions.destroy');

        Route::post('institutions/{institution}/courses', [InstitutionCourseController::class, 'linkCourse'])
            ->name('operation.institutions.courses.link');

        Route::delete('institutions/{institution}/courses/unlink', [InstitutionCourseController::class, 'unlinkCourse'])
            ->name('operation.institutions.courses.unlink');
    });

    // Course routes
    $viewCoursesPermission = Permission::ViewCourses->value;
    Route::middleware("permission:{$viewCoursesPermission}")->group(function () {
        Route::get('courses', [CourseController::class, 'index'])
            ->name('operation.courses.index');

        Route::get('courses/{course}', [CourseController::class, 'show'])
            ->name('operation.courses.show');
    });

    $manageCoursesPermission = Permission::ManageCourses->value;
    Route::middleware("permission:{$manageCoursesPermission}")->group(function () {
        Route::post('courses', [CourseController::class, 'store'])
            ->name('operation.courses.store');

        Route::put('courses/{course}', [CourseController::class, 'update'])
            ->name('operation.courses.update');

        Route::delete('courses/{course}', [CourseController::class, 'destroy'])
            ->name('operation.courses.destroy');
    });

    // Student Requisition routes
    $submitRequisitionPermission = Permission::SubmitRequisition->value;
    Route::middleware("permission:{$submitRequisitionPermission}")->group(function () {
        Route::post('requisitions', [StudentRequisitionController::class, 'store'])
            ->name('operation.requisitions.store');
    });
});

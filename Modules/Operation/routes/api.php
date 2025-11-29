<?php

use Illuminate\Support\Facades\Route;
use Modules\Operation\Http\Controllers\{CourseController, InstitutionController, InstitutionCourseController, StudentController, StudentRequisitionController};
use Modules\User\Enums\Permission;

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    // Institution routes
    $viewInstitutionsPermission = Permission::ViewInstitutions->value;
    Route::prefix('institutions')->middleware("permission:{$viewInstitutionsPermission}")->group(function () {
        Route::get('/', [InstitutionController::class, 'index'])
            ->name('operation.institutions.index');
        Route::get('/ordered-by-course/{courseId}', [InstitutionCourseController::class, 'getInstitutionsOrderedByCourse'])
            ->name('operation.institutions.ordered_by_course');
        Route::get('/ordered-by-course/{courseId}', [InstitutionCourseController::class, 'getInstitutionsOrderedByCourse'])
            ->name('operation.institutions.ordered_by_course');

        Route::get('/{institution}', [InstitutionController::class, 'show'])
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
    Route::prefix('courses')->middleware("permission:{$viewCoursesPermission}")->group(function () {
        Route::get('/', [CourseController::class, 'index'])
            ->name('operation.courses.index');

        Route::get('/ordered-by-institution/{institutionId}', [InstitutionCourseController::class, 'getCoursesOrderedByInstitution'])
            ->name('operation.institutions.ordered_by_course');

        Route::get('/{course}', [CourseController::class, 'show'])
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

    $viewRequisitionsPermission = Permission::ViewRequisitions->value;
    Route::middleware("permission:{$viewRequisitionsPermission}")->group(function () {
        Route::get('requisitions', [StudentRequisitionController::class, 'index'])
            ->name('operation.requisitions.index');

        Route::get('requisitions/{id}', [StudentRequisitionController::class, 'find'])
            ->name('operation.requisitions.show');
    });

    $manageRequisitionsPermission = Permission::ManageRequisitions->value;
    Route::middleware("permission:{$manageRequisitionsPermission}")->group(function () {
        Route::patch('requisitions/{id}/approve', [StudentRequisitionController::class, 'approve'])
            ->name('operation.requisitions.approve');

        Route::patch('requisitions/{id}/reprove', [StudentRequisitionController::class, 'reprove'])
            ->name('operation.requisitions.reprove');
    });

    // Student routes
    Route::get('students/{id}', [StudentController::class, 'show'])
        ->name('operation.students.show');
});

<?php

namespace Modules\Operation\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Operation\Services\{InstitutionCourseService, InstitutionCourseServiceInterface};

class InstitutionCourseServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            InstitutionCourseServiceInterface::class,
            InstitutionCourseService::class
        );
    }
}

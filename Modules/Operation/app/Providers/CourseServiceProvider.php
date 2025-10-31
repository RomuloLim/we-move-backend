<?php

namespace Modules\Operation\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Operation\Repositories\Course\{CourseRepository, CourseRepositoryInterface};
use Modules\Operation\Services\{CourseService, CourseServiceInterface};

class CourseServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            CourseRepositoryInterface::class,
            CourseRepository::class
        );
        $this->app->bind(
            CourseServiceInterface::class,
            CourseService::class
        );
    }
}

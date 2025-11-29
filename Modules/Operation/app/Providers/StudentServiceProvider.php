<?php

namespace Modules\Operation\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Operation\Repositories\Student\{StudentRepository, StudentRepositoryInterface};
use Modules\Operation\Services\{StudentService, StudentServiceInterface};

class StudentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            StudentRepositoryInterface::class,
            StudentRepository::class
        );

        $this->app->bind(
            StudentServiceInterface::class,
            StudentService::class
        );
    }
}

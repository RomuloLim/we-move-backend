<?php

namespace Modules\Operation\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Operation\Repositories\Student\{StudentRepository, StudentRepositoryInterface};

class StudentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            StudentRepositoryInterface::class,
            StudentRepository::class
        );
    }
}

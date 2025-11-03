<?php

namespace Modules\Operation\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Operation\Repositories\StudentRequisition\{StudentRequisitionRepository, StudentRequisitionRepositoryInterface};
use Modules\Operation\Services\{StudentRequisitionService, StudentRequisitionServiceInterface};

class StudentRequisitionServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            StudentRequisitionRepositoryInterface::class,
            StudentRequisitionRepository::class
        );
        $this->app->bind(
            StudentRequisitionServiceInterface::class,
            StudentRequisitionService::class
        );
    }
}

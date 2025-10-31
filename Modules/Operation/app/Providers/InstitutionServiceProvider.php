<?php

namespace Modules\Operation\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Operation\Repositories\Institution\{InstitutionRepository, InstitutionRepositoryInterface};
use Modules\Operation\Services\{InstitutionService, InstitutionServiceInterface};

class InstitutionServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            InstitutionRepositoryInterface::class,
            InstitutionRepository::class
        );
        $this->app->bind(
            InstitutionServiceInterface::class,
            InstitutionService::class
        );
    }
}

<?php

namespace Modules\Operation\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Operation\Repositories\Vehicle\{VehicleRepository, VehicleRepositoryInterface};
use Modules\Operation\Services\{VehicleService, VehicleServiceInterface};

class VehicleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            VehicleRepositoryInterface::class,
            VehicleRepository::class
        );
        $this->app->bind(
            VehicleServiceInterface::class,
            VehicleService::class
        );
    }
}

<?php

namespace Modules\Logistics\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Logistics\Repositories\Vehicle\{VehicleRepository, VehicleRepositoryInterface};
use Modules\Logistics\Services\{VehicleService, VehicleServiceInterface};

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

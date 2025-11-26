<?php

namespace Modules\Logistics\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Logistics\Repositories\Route\{RouteRepository, RouteRepositoryInterface};
use Modules\Logistics\Services\{RouteService, RouteServiceInterface};

class RouteRegistryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            RouteRepositoryInterface::class,
            RouteRepository::class
        );
        $this->app->bind(
            RouteServiceInterface::class,
            RouteService::class
        );
    }
}

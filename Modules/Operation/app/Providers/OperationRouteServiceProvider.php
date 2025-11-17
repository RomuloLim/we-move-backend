<?php

namespace Modules\Operation\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Operation\Repositories\Route\{RouteRepository, RouteRepositoryInterface};
use Modules\Operation\Services\{RouteService, RouteServiceInterface};

class OperationRouteServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     */
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

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [];
    }
}

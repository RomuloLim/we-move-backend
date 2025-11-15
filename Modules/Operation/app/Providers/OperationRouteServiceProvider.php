<?php

namespace Modules\Operation\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Operation\Repositories\Route\StopRepository;
use Modules\Operation\Repositories\Route\RouteRepositoryInterface;

class OperationRouteServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     */
    public function register(): void {
        $this->app->bind(
            RouteRepositoryInterface::class,
            StopRepository::class
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

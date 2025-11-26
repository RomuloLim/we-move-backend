<?php

namespace Modules\Logistics\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Logistics\Repositories\Stop\{StopRepository, StopRepositoryInterface};
use Modules\Logistics\Services\{StopService, StopServiceInterface};

class StopServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->bind(
            StopRepositoryInterface::class,
            StopRepository::class
        );
        $this->app->bind(
            StopServiceInterface::class,
            StopService::class
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

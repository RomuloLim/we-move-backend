<?php

namespace Modules\Operation\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Operation\Repositories\Stop\{StopRepository, StopRepositoryInterface};
use Modules\Operation\Services\{StopService, StopServiceInterface};

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

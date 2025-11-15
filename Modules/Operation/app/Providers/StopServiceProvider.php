<?php

namespace Modules\Operation\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Operation\Repositories\Stop\StopRepository;
use Modules\Operation\Repositories\Stop\StopRepositoryInterface;

class StopServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     */
    public function register(): void {
        $this->app->bind(
            StopRepositoryInterface::class,
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

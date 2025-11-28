<?php

namespace Modules\Logistics\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Logistics\Repositories\Trip\{TripRepository, TripRepositoryInterface};
use Modules\Logistics\Services\{TripService, TripServiceInterface};

class TripServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            TripRepositoryInterface::class,
            TripRepository::class
        );
        $this->app->bind(
            TripServiceInterface::class,
            TripService::class
        );
    }
}

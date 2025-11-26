<?php

namespace Modules\Logistics\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Logistics\Repositories\UserRoute\{UserRouteRepository, UserRouteRepositoryInterface};
use Modules\Logistics\Services\{UserRouteService, UserRouteServiceInterface};

class UserRouteServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            UserRouteRepositoryInterface::class,
            UserRouteRepository::class
        );
        $this->app->bind(
            UserRouteServiceInterface::class,
            UserRouteService::class
        );
    }
}

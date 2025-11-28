<?php

namespace Modules\Logistics\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Logistics\Repositories\Boarding\{BoardingRepository, BoardingRepositoryInterface};
use Modules\Logistics\Services\{BoardingService, BoardingServiceInterface};

class BoardingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            BoardingRepositoryInterface::class,
            BoardingRepository::class
        );
        $this->app->bind(
            BoardingServiceInterface::class,
            BoardingService::class
        );
    }
}

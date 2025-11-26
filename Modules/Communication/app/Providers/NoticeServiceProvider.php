<?php

namespace Modules\Communication\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Communication\Repositories\Notice\{NoticeRepository, NoticeRepositoryInterface};
use Modules\Communication\Services\{NoticeService, NoticeServiceInterface};

class NoticeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            NoticeRepositoryInterface::class,
            NoticeRepository::class
        );
        $this->app->bind(
            NoticeServiceInterface::class,
            NoticeService::class
        );
    }
}

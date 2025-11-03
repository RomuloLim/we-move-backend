<?php

namespace Modules\Operation\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Operation\Repositories\Document\{DocumentRepository, DocumentRepositoryInterface};
use Modules\Operation\Services\{DocumentService,DocumentServiceInterface};

class DocumentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            DocumentRepositoryInterface::class,
            DocumentRepository::class
        );
        $this->app->bind(
            DocumentServiceInterface::class,
            DocumentService::class
        );
    }
}

<?php

namespace App\Providers;

use App\Services\ProjectService;
use Illuminate\Support\ServiceProvider;

class ProjectServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ProjectService::class, function ($app) {
            return new ProjectService();
        });
    }

    public function boot(): void
    {
        //
    }
}

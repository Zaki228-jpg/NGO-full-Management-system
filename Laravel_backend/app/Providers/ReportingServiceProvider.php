<?php

namespace App\Providers;

use App\Services\ReportingService;
use Illuminate\Support\ServiceProvider;

class ReportingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ReportingService::class, function ($app) {
            return new ReportingService();
        });
    }

    public function boot(): void
    {
        //
    }
}

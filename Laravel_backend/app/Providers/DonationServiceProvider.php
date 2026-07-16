<?php

namespace App\Providers;

use App\Services\DonationService;
use Illuminate\Support\ServiceProvider;

class DonationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(DonationService::class, function ($app) {
            return new DonationService();
        });
    }

    public function boot(): void
    {
        //
    }
}

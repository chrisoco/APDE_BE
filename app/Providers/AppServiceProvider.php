<?php

declare(strict_types=1);

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureCarbon();
        $this->configureModels();
        $this->configureVite();
        $this->configureUrl();
    }

    /**
     * Configure the application's models
     */
    private function configureModels(): void
    {
        Model::shouldBeStrict();
        Model::unguard();
    }

    /**
     * Configure the application's URL
     */
    private function configureUrl(): void
    {
        URL::forceHttps();
    }

    /**
     * Configure the application's Carbon
     */
    private function configureCarbon(): void
    {
        Date::use(CarbonImmutable::class);
    }

    /**
     * Configure the application's Vite
     */
    private function configureVite(): void
    {
        Vite::useAggressivePrefetching();
    }
}

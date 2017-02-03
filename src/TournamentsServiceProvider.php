<?php

namespace Xoco70\LaravelTournaments;

use Illuminate\Support\ServiceProvider;

class TournamentsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/views', 'timezones');
        $this->loadMigrationsFrom(__DIR__ . '/migrations/');
        $this->loadTranslationsFrom(__DIR__.'/translations', 'laravel-tournaments');
        $this->publishes([ __DIR__.'/translations' => resource_path('lang/vendor/laravel-tournaments'),]);
        $this->publishes([__DIR__ . '/views' => base_path('resources/views/vendor/laravel-tournaments')]);
        $this->publishes([__DIR__ . '/config/laravel-tournaments.php' => config_path('laravel-tournaments.php'),]);


    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        include __DIR__.'/web.php';
        $this->app->make(TimezonesController::class);
    }
}

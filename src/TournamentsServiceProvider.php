<?php

namespace Xoco70\KendoTournaments;

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
        $this->loadRoutesFrom(__DIR__.'/web.php');
        $this->loadViewsFrom(__DIR__ . '/views', 'kendo-tournaments');
        $this->loadMigrationsFrom(__DIR__ . '/migrations/');
        $this->loadTranslationsFrom(__DIR__.'/translations', 'kendo-tournaments');

//        $this->publishes([__DIR__ . '/views' => base_path('resources/views/vendor/kendo-tournaments')]);
        $this->publishes([__DIR__ . '/config/kendo-tournaments.php' => config_path('kendo-tournaments.php'),]);
        $this->publishes([__DIR__ . '/migrations' => $this->app->databasePath() . '/migrations'], 'migrations');
        $this->publishes([__DIR__ . '/seeds' => $this->app->databasePath() . '/seeds'], 'seeds');
        $this->publishes([__DIR__ . '/factories' => $this->app->databasePath() . '/factories'], 'factories');
        $this->publishes([__DIR__.'/assets' => public_path('vendor/kendo-tournaments'),], 'public');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // Replace HTML:: y FORM:: by native html
        include __DIR__.'/web.php';
        $this->app->make(TreeController::class);
        $this->app->make(FightController::class);
    }
}

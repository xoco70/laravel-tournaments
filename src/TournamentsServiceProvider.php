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
        $this->loadViewsFrom(__DIR__.'/views', 'timezones');
        $this->publishes([__DIR__.'/views' => base_path('resources/views/vendor/xoco70/laravel-tournaments'),
        ]);
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

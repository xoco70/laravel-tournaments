<?php

namespace Xoco70\LaravelTournaments;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class TournamentsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @param Router $router
     *
     * @return void
     */
    public function boot(Router $router)
    {
        $viewPath = __DIR__.'/../resources/views';
        $this->loadViewsFrom($viewPath, 'laravel-tournaments');

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadTranslationsFrom(__DIR__.'/../translations', 'laravel-tournaments');

        $this->publishes([__DIR__.'/../database/seeds' => $this->app->databasePath().'/seeds'], 'laravel-tournaments');
        $this->publishes([__DIR__.'/../database/factories' => $this->app->databasePath().'/factories'], 'laravel-tournaments');
        $this->publishes([__DIR__.'/../resources/assets' => public_path('vendor/laravel-tournaments')], 'laravel-tournaments');

        $router->group(['prefix' => 'laravel-tournaments', 'middleware' => ['web']], function ($router) {
            $router->get('/', 'Xoco70\LaravelTournaments\TreeController@index')->name('tree.index');
            $router->post('/championships/{championship}/trees', 'Xoco70\LaravelTournaments\TreeController@store')->name('tree.store');
            $router->put('/championships/{championship}/trees', 'Xoco70\LaravelTournaments\TreeController@update')->name('tree.update');
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->make(TreeController::class);
        $this->app->make(DBHelpers::class);
    }
}

<?php

namespace Xoco70\KendoTournaments;

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
        $this->loadViewsFrom($viewPath, 'kendo-tournaments');
//        $this->publishes([ $viewPath => base_path('resources/views/vendor/kendo-tournaments'),], 'kendo-tournaments');

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadTranslationsFrom(__DIR__.'/../translations', 'kendo-tournaments');

//        $this->publishes([__DIR__ . '/views' => base_path('resources/views/vendor/kendo-tournaments')]);
        $this->publishes([__DIR__.'/../database/migrations' => $this->app->databasePath().'/migrations'], 'migrations');
        $this->publishes([__DIR__.'/../database/seeds' => $this->app->databasePath().'/seeds'], 'seeds');
        $this->publishes([__DIR__.'/../database/factories' => $this->app->databasePath().'/factories'], 'seeds');
        $this->publishes([__DIR__.'/../resources/assets' => public_path('vendor/kendo-tournaments')], 'assets');

        $router->group(['prefix' => 'kendo-tournaments', 'middleware' => ['web']], function ($router) {
            $router->get('/', 'Xoco70\KendoTournaments\TreeController@index')->name('tree.index');
            $router->post('/championships/{championship}/trees', 'Xoco70\KendoTournaments\TreeController@store')->name('tree.store');
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // Replace HTML:: y FORM:: by native html
        $this->app->make(TreeController::class);
    }
}

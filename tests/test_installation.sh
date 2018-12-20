#!/usr/bin/env bash
composer global require laravel/installer # do it just on
~/.composer/vendor/bin/laravel new plugin-test
cd plugin-test
composer require "xoco70/laravel-tournaments:dev-master"
php artisan vendor:publish --tag=laravel-tournaments --force
touch database/database.sqlite
php artisan migrate:fresh --database=sqlite --force
composer dump-autoload
php artisan db:seed --class=LaravelTournamentSeeder --database=sqlite --force
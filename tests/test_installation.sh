#!/usr/bin/env bash
# export LARAVEL_VERSION="5.5.*"
composer create-project laravel/laravel plugin-test "$LARAVEL_VERSION"
cd plugin-test
composer require "xoco70/laravel-tournaments:dev-master"
php artisan vendor:publish --tag=laravel-tournaments --force
touch database/database.sqlite
php artisan migrate:fresh --database=sqlite --force
composer dump-autoload
php artisan db:seed --class=LaravelTournamentSeeder --database=sqlite --force
cd ..
rm -rf plugin-test
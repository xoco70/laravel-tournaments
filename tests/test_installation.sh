#!/usr/bin/env bash
rm -rf plugin-test \
&& composer create-project laravel/laravel plugin-test "$LARAVEL_VERSION" \
&& cd plugin-test \
&& composer require "xoco70/laravel-tournaments:dev-master" \
&& php artisan vendor:publish --tag=laravel-tournaments --force \
&& touch database/database.sqlite \
&& rm .env \
&& php artisan migrate:fresh --database=sqlite --force \
&& composer dump-autoload \
&& php artisan db:seed --class=LaravelTournamentSeeder --database=sqlite --force \
&& cd .. && rm -rf plugin-test
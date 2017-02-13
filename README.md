# 

Kendo- Tournaments is A Laravel 5.3+ Package that allows you to generate Tournaments tree, Direct Elimination, Round Robin, or Mixed   

## Installation

First, you'll need to install the package via Composer:

```shell
$ composer require "xoco70/kendo-tournaments":^0.8
```

Then, update `config/app.php` by adding an entry for the service provider.

```php
'providers' => [
    // ...
    Xoco70\KendoTournaments\TournamentsServiceProvider::class,
];
```

Finally, from the command line again, publish the default configuration file:

```shell
php artisan vendor:publish
```

#Run the demo

To run the demo, you need to generate Tournaments, Championships, Users, Competitors and Settings

Run Migrations:
```shell
php artisan migrate
```

Seed dummy data:
```shell
php db:seed
```

You will be able to access the demo at `http://yourdomain.com/kendo-tournaments`


## Run Functional Tests (Still not integrated)

vendor/bin/phpunit tests


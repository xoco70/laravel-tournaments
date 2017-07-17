[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/xoco70/kendo-tournaments/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/xoco70/kendo-tournaments/?branch=master)
[![License: MIT](https://img.shields.io/badge/License-MIT-brightgreen.svg?style=flat-square)](https://opensource.org/licenses/MIT)
[![Build Status](https://scrutinizer-ci.com/g/xoco70/kendo-tournaments/badges/build.png?b=master)](https://scrutinizer-ci.com/g/xoco70/kendo-tournaments/badges/build.png?b=master)

# 

Kendo- Tournaments is A Laravel 5.4 Package that allows you to generate Tournaments tree, Direct Elimination, Round Robin, or Mixed   

## Installation

First, you'll need to install the package via Composer:

```shell
$ composer require "xoco70/kendo-tournaments":^0.11
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

# Run the demo

To run the demo, you need to generate Tournaments, Championships, Users, Competitors and Settings

Run Migrations:
```shell
php artisan migrate
```

Seed dummy data:
```shell
php artisan db:seed
```

You will be able to access the demo at `http://yourdomain.com/kendo-tournaments`


## Run Functional Tests

vendor/bin/phpunit tests

## TODO

This is a work in progress, and tree creation might be very complex, so there is a bunch of things to achieve.  

- Generate a modificable ( with Select combos ) result for Preliminary Trees
- Generate Direct Elimination Tree after Preliminary
- Manage Winner and third place fight
- Manage more than 1 fighter out of preliminary round
- Manage n+1 case : When for instance, there is 17 competitors in a direct elimination tree, there will have 15 BYES.
 We can improve that making the first match with 3 competitors.
- Develop an Hybrid app with Ionic 3 to score live. If no internet, app should sync with Bluetooth :)


 

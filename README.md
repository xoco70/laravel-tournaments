<h1 align="center">
  <br>
  <img src="https://raw.githubusercontent.com/xoco70/laravel-tournaments/master/resources/assets/images/logo.png" alt="Laravel Tournaments">
  <br>
  Laravel Tournaments
  <br>
</h1>

<h4 align="center">A Laravel plugin that generate tournaments out of the box</h4>


<p align="center">
    <a href="https://scrutinizer-ci.com/g/xoco70/laravel-tournaments/?branch=master"><img src="https://camo.githubusercontent.com/d3f5c9de8adbb7fc3c18e44640c205c9d105f0ec/68747470733a2f2f7363727574696e697a65722d63692e636f6d2f672f786f636f37302f6b656e646f2d746f75726e616d656e74732f6261646765732f7175616c6974792d73636f72652e706e673f623d6d6173746572" alt="Scrutinizer Code Quality" data-canonical-src="https://scrutinizer-ci.com/g/xoco70/laravel-tournaments/badges/quality-score.png?b=master" style="max-width:100%;"></a>
    <a href="https://opensource.org/licenses/MIT"><img src="https://camo.githubusercontent.com/28ddbec0801282129302d6a51a9dd09b4c09c438/68747470733a2f2f696d672e736869656c64732e696f2f62616467652f4c6963656e73652d4d49542d627269676874677265656e2e7376673f7374796c653d666c61742d737175617265" alt="License: MIT" data-canonical-src="https://img.shields.io/badge/License-MIT-brightgreen.svg?style=flat-square" style="max-width:100%;"></a>
    <a href="https://scrutinizer-ci.com/g/xoco70/laravel-tournaments/badges/build.png?b=master"><img src="https://camo.githubusercontent.com/e1471ee47a70cb9663eb9f8b71707718451e83cc/68747470733a2f2f7363727574696e697a65722d63692e636f6d2f672f786f636f37302f6b656e646f2d746f75726e616d656e74732f6261646765732f6275696c642e706e673f623d6d6173746572" alt="Build Status" data-canonical-src="https://scrutinizer-ci.com/g/xoco70/laravel-tournaments/badges/build.png?b=master" style="max-width:100%;"></a>
    <a href="https://scrutinizer-ci.com/g/xoco70/laravel-tournaments/?branch=master"><img src="https://camo.githubusercontent.com/49b2a16d45e04d814850ed43ea432aea6d623121/68747470733a2f2f7363727574696e697a65722d63692e636f6d2f672f786f636f37302f6b656e646f2d746f75726e616d656e74732f6261646765732f636f7665726167652e706e673f623d6d6173746572" alt="Code Coverage" data-canonical-src="https://scrutinizer-ci.com/g/xoco70/laravel-tournaments/badges/coverage.png?b=master" style="max-width:100%;"></a>
</p>
<h1 align="center">
  <br>
  <img src="https://raw.githubusercontent.com/xoco70/laravel-tournaments/master/resources/assets/images/laravel-tournaments.gif" alt="Laravel Tournaments Demo">
</h1>


Laravel Tournaments is A Laravel 5.4 Package that allows you to generate Tournaments tree   
## What you can do

- Generate Direct Elimination Trees
- Generate Direct Elimination with Preliminary Round
- Change Preliminary Round Size
- Use several areas ( 1,2,4,8 )
- Modify Direct Elimination Tree generation on the fly
- Use teams instead of competitors
- Generate a list of fights

## What you can't do

This is a work in progress, and tree creation might be very complex, so there is a bunch of things to achieve.  

- Modify Preliminary Round generation on the fly
- Manage Winner and third place fight
- Manage more than 1 fighter out of preliminary round
- Manage n+1 case : When for instance, there is 17 competitors in a direct elimination tree, there will have 15 BYES.
 We can improve that making the first match with 3 competitors.
- Use any number of area ( restricted to 1,2,4,8) 

## Warning

This is still a work in progress. Things could change, things could break. Use it at your own risks in production


## Installation

First, you'll need to install the package via Composer:

```shell
$ composer require "xoco70/laravel-tournaments":^0.11
```

Then, update `config/app.php` by adding an entry for the service provider.

```php
'providers' => [
    // ...
    Xoco70\LaravelTournaments\TournamentsServiceProvider::class,
];
```

Finally, from the command line again, publish the default configuration file:

```shell
php artisan vendor:publish
```
# Usage

Coming soon

# Run the demo

To run the demo, you need to generate Tournaments, Championships, Users, Competitors and Settings

Run Migrations:
```shell
php artisan migrate
```

Seed dummy data:
```shell
php artisan db:seed --class=LaravelTournamentSeeder

```

You will be able to access the demo at `http://yourdomain.com/laravel-tournaments`


## Run Functional Tests

vendor/bin/phpunit tests

# Troubleshooting

### Specified key was too long error
For those running MariaDB or older versions of MySQL you may hit this error when trying to run migrations:
As outlined in the <a href="https://laravel.com/docs/master/migrations#creating-indexes">Migrations guide</a> to fix this all you have to do is edit your AppServiceProvider.php file and inside the boot method set a default string length:
```
use Illuminate\Support\Facades\Schema;

public function boot()
{
    Schema::defaultStringLength(191);
}
```

<h1 align="center">
<br>
<img src="https://raw.githubusercontent.com/xoco70/laravel-tournaments/master/resources/assets/images/logo.png" alt="Laravel Tournaments">
<br>
Laravel Tournaments
<br>
</h1>

<h4 align="center">A Laravel plugin that generate tournaments out of the box</h4>


<p align="center">
<a href="https://packagist.org/packages/xoco70/laravel-tournaments"><img src="https://poser.pugx.org/xoco70/laravel-tournaments/v/stable" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/xoco70/laravel-tournaments"><img src="https://poser.pugx.org/xoco70/laravel-tournaments/downloads" alt="Total Downloads"></a>
<a href="https://scrutinizer-ci.com/g/xoco70/laravel-tournaments/?branch=master"><img src="https://scrutinizer-ci.com/g/xoco70/laravel-tournaments/badges/quality-score.png?b=master" alt="Scrutinizer Code Quality" data-canonical-src="https://scrutinizer-ci.com/g/xoco70/laravel-tournaments/badges/quality-score.png?b=master" style="max-width:100%;"></a>
<a href="https://travis-ci.org/xoco70/laravel-tournaments"><img src="https://travis-ci.org/xoco70/laravel-tournaments.svg?branch=master" alt="Build Status" data-canonical-src="https://travis-ci.org/xoco70/laravel-tournaments.svg?branch=master" style="max-width:100%;"></a>
<a href="https://scrutinizer-ci.com/g/xoco70/laravel-tournaments/?branch=master"><img src="https://scrutinizer-ci.com/g/xoco70/laravel-tournaments/badges/coverage.png?b=master" alt="Code Coverage" data-canonical-src="https://scrutinizer-ci.com/g/xoco70/laravel-tournaments/badges/coverage.png?b=master" style="max-width:100%;"></a>
<a href="https://opensource.org/licenses/MIT"><img src="https://camo.githubusercontent.com/28ddbec0801282129302d6a51a9dd09b4c09c438/68747470733a2f2f696d672e736869656c64732e696f2f62616467652f4c6963656e73652d4d49542d627269676874677265656e2e7376673f7374796c653d666c61742d737175617265" alt="License: MIT" data-canonical-src="https://img.shields.io/badge/License-MIT-brightgreen.svg?style=flat-square" style="max-width:100%;"></a>
</p>
<h1 align="center">
<br>
<img src="https://raw.githubusercontent.com/xoco70/laravel-tournaments/master/resources/assets/images/laravel-tournaments.gif" alt="Laravel Tournaments Demo">
</h1>

* [Features](#features)
* [Installation](#installation)
* [Run the demo](#run-the-demo)
* [Usage](#usage)
* [Data Model](#data-model)
* [Models](#models)
* [Include views](#include-views)   
* [Limitations](#limitations)
* [Troubleshooting](#troubleshooting)
* [Changelog](#changelog)

## Features

- Single Elimination Trees Generation
- Single Elimination with Preliminary Round Generation
- Playoff Generation
- Third place fight
- List of Fights Generation
- Customize Preliminary Round Size
- Customize area number (1,2,4,8)
- Modify Single Elimination Tree generation on the fly
- Use teams instead of competitors


## Warning

This is still a work in progress. Things could change, things could break. Use it at your own risks in production

## Installation
> **NOTE**: Depending on your version of Laravel, you should install a different
> version of the package:
> 
> | Laravel Version | Laravel Tournament Version |
> |:---------------:|:--------------------------:|
> |       5.5       |            0.13            |
> |       5.4       |            0.11            |

First, you'll need to install the package via Composer:

```php
$ composer require "xoco70/laravel-tournaments":^0.13
```

If you use Laravel 5.4, update `config/app.php` by adding an entry for the service provider.

```php
'providers' => [
    // ...
    Xoco70\LaravelTournaments\TournamentsServiceProvider::class,
];
```


Finally, from the command line again, publish the default configuration file:

```php
php artisan vendor:publish --force --tag=laravel-tournaments
```

## Run the demo

To run the demo, you need to generate Tournaments, Championships, Users, Competitors and Settings

Run Migrations:
```php
php artisan migrate
composer dump-autoload
```

Seed dummy data:
```php
php artisan db:seed --class=LaravelTournamentSeeder

```
> **WARNING**: Don't do this in production, it would wipe all your data. Use this line for demo purpose only

You will be able to access the demo at `http://yourdomain.com/laravel-tournaments`

## Usage
```php
// Create a tournament

$tournament = factory(Tournament::class)->create(['user_id' => Auth::user()->id]);

$championsip = factory(Championship::class)->create(['$tournament_id' => $tournament->id]);

// Optional, if not defined, it will take default in ChampionshipSettings

$settings = factory(ChampionshipSettings::class)->create(['championship_id' => $championship->id]);

// Add competitors to championship

$competitors = factory(\App\Competitor::class,10)->create([
    'championship_id' => $championship->id,
     'user_id' => factory(User::class)->create()->id
]);

// Define strategy to generate

$generation = $championship->chooseGenerationStrategy();

// Generate everything

$generation->run();

// Just generate Tree

$this->generateAllTrees();

// Just generate Fight List

$this->generateAllFights();

```
## Data model
![Database Model](https://raw.githubusercontent.com/xoco70/laravel-tournaments/master/resources/assets/images/laravel-tournaments-database-model.png)

## Models
### Tournament

```php
$tournament->owner; // get owner
$tournament->venue; // get venue
$tournament->championships; // get championships 
```

Check tournament type: 
```php
$tournament->isOpen()
$tournament->needsInvitation()
```

Check tournament level:
```php
$tournament ->isInternational()
$tournament->isNational() 
$tournament->isRegional()
$tournament->isEstate()
$tournament->isMunicipal()
$tournament->isDistrictal()
$tournament->isLocal()
$tournament->hasNoLevel()
```
## Championship

```php
$championship->competitors; // Get competitors
$championship->teams; // Get teams
$championship->fighters; // Get fighters
$championship->category; // Get category
$championship->tournament; // Get tournament
$championship->users; // Get users
$championship->settings; // Get settings
$championship->fightersGroups; // Get groups 
$championship->groupsByRound($numRound = 1); // Get groups for a specific round
$championship->groupsFromRound($numRound = 1); // Get groups from a specific round
$championship->fights; // Get fights
$championship->firstRoundFights; // Get fights for the first round only ( Useful when has preliminary )
$championship->fights($numRound = 1); // Get fights for a specific round
```
> **NOTE**: $fighter can be an instance of `Team` or `Competitor`


Determine strategy:
```php
$championship->isPlayoffCompetitor()
$championship->isPlayoffTeam()
$championship->isSingleEliminationCompetitor()
$championship->isSingleEliminationTeam()
```

Determine group size:
```php
$championship->getGroupSize()
```

Determine championship type: 
```php
$championship->hasPreliminary()
$championship->isPlayOffType()
$championship->isSingleEliminationType()
```


### FightersGroup

```php
$group->championship; // Get championship
$group->fights; // Get fights
$group->fighters; // Get fighters
$group->teams; // Get teams
$group->competitors; // Get competitors
$group->users; // Get users
```
> **NOTE**: $fighter can be an instance of `Team` or `Competitor`

To get the instance name:
```php
$group->getFighterType() // Should return Team::class or Competitor::class
```

> **NOTE**: This plugin use <a href="https://github.com/lazychaser/laravel-nestedset">laravel-nestedset</a>. 
This means you can navigate with `$group->children()` or `$group->parent()` or use any methods available in this great plugin.  

### Competitor

```php
$competitor->user; // Get user
```

### Team
```php
// Create a team

$team = factory(Team::class)
    ->create([ championship_id' => $championship->id]);
```

```php
// Add competitor to team 

$team->competitors()->attach($competitor->id);

// Remove competitor from a team 

$team->competitors()->detach($competitor->id);

```

### Fight 

```php
$fight->group; // Get group
$fight->competitor1; // Get competitor1
$fight->competitor2; // Get competitor2
$fight->team1; // Get team1
$fight->team2; // Get team2
```

## Views
Preliminary tree
```php
@include('laravel-tournaments::partials.tree.preliminary') // Preliminary table
```

Single Elimination tree
```php
@include('laravel-tournaments::partials.tree.singleElimination', ['hasPreliminary' => 0]) 
```

Fight List
```php
@include('laravel-tournaments::partials.fights')
```

## Run Functional Tests

vendor/bin/phpunit tests

## Limitations

This is a work in progress, and tree creation might be very complex, so there is a bunch of things to achieve.  

- Seed fighter
- Manage more than 1 fighter out of preliminary round
- Modify Preliminary Round generation on the fly
- Use any number of area ( restricted to 1,2,4,8)
- Manage n+1 case : When for instance, there is 17 competitors in a direct elimination tree, there will have 15 BYES.
We can improve that making the first match with 3 competitors.
- Double elimination
 
## Troubleshooting

### Specified key was too long error
For those running MariaDB or older versions of MySQL you may hit this error when trying to run migrations:
As outlined in the <a href="https://laravel.com/docs/master/migrations#creating-indexes">Migrations guide</a> to fix this all you have to do is edit your AppServiceProvider.php file and inside the boot method set a default string length:
```php
use Illuminate\Support\Facades\Schema;

public function boot()
{
Schema::defaultStringLength(191);
}
```
### With this configuration, you must have at least...
This error means you don't have enough competitors / teams to create given tree
Try to increase competitor number, decrease areas or preliminary group size, if preliminary round is active 

## ChangeLog:

- v0.13: Manage third place fight
- v0.12: Laravel 5.5 version
- v0.11: Initial Version


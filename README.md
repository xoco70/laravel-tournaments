<h1 align="center">
  <br>
  <img src="https://raw.githubusercontent.com/xoco70/laravel-tournaments/master/resources/assets/images/logo.png" alt="Laravel Tournaments">
  <br>
  Laravel Tournaments
  <br>
</h1>

<h4 align="center">A Laravel plugin that generate tournaments out of the box</h4>


<p align="center">
    <a href="https://scrutinizer-ci.com/g/xoco70/laravel-tournaments/?branch=master"><img src="https://scrutinizer-ci.com/g/xoco70/laravel-tournaments/badges/quality-score.png?b=master" alt="Scrutinizer Code Quality" data-canonical-src="https://scrutinizer-ci.com/g/xoco70/laravel-tournaments/badges/quality-score.png?b=master" style="max-width:100%;"></a>
    <a href="https://scrutinizer-ci.com/g/xoco70/laravel-tournaments/badges/build.png?b=master"><img src="https://scrutinizer-ci.com/g/xoco70/laravel-tournaments/badges/build.png?b=master" alt="Build Status" data-canonical-src="https://scrutinizer-ci.com/g/xoco70/laravel-tournaments/badges/build.png?b=master" style="max-width:100%;"></a>
    <a href="https://scrutinizer-ci.com/g/xoco70/laravel-tournaments/?branch=master"><img src="https://scrutinizer-ci.com/g/xoco70/laravel-tournaments/badges/coverage.png?b=master" alt="Code Coverage" data-canonical-src="https://scrutinizer-ci.com/g/xoco70/laravel-tournaments/badges/coverage.png?b=master" style="max-width:100%;"></a>
    <a href="http://laravel.com"><img src="https://camo.githubusercontent.com/f0b5ac8a0947b27575c9f48844d9df6744d62d7b/68747470733a2f2f696d672e736869656c64732e696f2f62616467652f6275696c74253230666f722d6c61726176656c2d626c75652e737667" alt="Built For Laravel" data-canonical-src="https://img.shields.io/badge/built%20for-laravel-blue.svg" style="max-width:100%;"></a>
    <a href="https://opensource.org/licenses/MIT"><img src="https://camo.githubusercontent.com/28ddbec0801282129302d6a51a9dd09b4c09c438/68747470733a2f2f696d672e736869656c64732e696f2f62616467652f4c6963656e73652d4d49542d627269676874677265656e2e7376673f7374796c653d666c61742d737175617265" alt="License: MIT" data-canonical-src="https://img.shields.io/badge/License-MIT-brightgreen.svg?style=flat-square" style="max-width:100%;"></a>
</p>
<h1 align="center">
  <br>
  <img src="https://raw.githubusercontent.com/xoco70/laravel-tournaments/master/resources/assets/images/laravel-tournaments.gif" alt="Laravel Tournaments Demo">
</h1>


  * [Features](#features)
  * [Installation](#installation)
  * [Usage](#usage)
      * [Create a tournament] (#create-a-tournament)
  * [Run the demo](#run-the-demo)
  * [Limitations](#limitations)
  * [Troubleshooting](#troubleshooting)

Laravel Tournaments is A Laravel 5.4 Package that allows you to generate Tournaments tree   
## Features

- Direct Elimination Trees Generation
- Direct Elimination with Preliminary Round Generation
- List of Fights Generation
- Customize Preliminary Round Size
- Customize area number( 1,2,4,8 )
- Modify Direct Elimination Tree generation on the fly
- Use teams instead of competitors

## Warning

This is still a work in progress. Things could change, things could break. Use it at your own risks in production


## Installation

[comment]: <>(> **NOTE**: Depending on your version of Laravel, you should install a different)
[comment]: <>(> version of the package:)
[comment]: <>(> )
[comment]: <>(> | Laravel Version | Laravel Tournament Version |)
[comment]: <>(> |:---------------:|:--------------------------:|)
[comment]: <>(> |       5.4       |            0.11          |)
[comment]: <>(>)
 
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
#Data model

# Usage
```shell
// Create a tournament
$tournament = factory(Tournament::class)->create(['user_id' => Auth::user()->id]);

$championsip = factory(Championship::class)->create(['$tournament_id' => $tournament->id]);

// Optional, if not defined, it will take default in ChampionshipSettings
$settings = factory(ChampionshipSettings::class)->create(['championship_id' => $championship->id]);

// Add competitors to championship
$competitor = factory(\App\Competitor::class)->create([
    'championship_id' => $championship->id,
     'user_id' => factory(User::class)->create()->id
]);

// Create a team
$team = factory(Teams::class)->create();

// Add competitor to team 
$team->competitors()->attach($competitor->id);

// Remove competitor from a team 
$team->competitors()->detach($competitor->id);

// Define strategy to generate
$generation = $championship->chooseGenerationStrategy();

// Generate everything
$generation->run();

// Just generate Tree

$this->generateAllTrees();

// Just generate Fight List
$this->generateAllFights();
 
```

### Tournaments

Create a tournament
```shell
factory(Tournament::class)->create(['user_id' => Auth::user()->id]);

```

Get tournament owner 
```shell
$user = $tournament->owner;
```

Get tournament venue 
```shell
$user = $tournament->venue;
```

Get tournament championships
```shell
$user = $tournament->championships;
```

## Tournaments
Helpers

Check tournament type: 
```
$tournament->isOpen()
$tournament->needsInvitation()
 ```

Check tournament level: `$tournament->isInternational()`, `$tournament->isNational()`, `$tournament->isRegional()`, `$tournament->isEstate()`, `$tournament->isMunicipal()`, `$tournament->isDistrictal()`, `$tournament->isLocal()`, `$tournament->hasNoLevel()`

## Championship
 
 
 competitors
 teams
 fighters
 category
 tournament
 users
 settings
 fightersGroups
 groupsByRound
 groupsFromRound
 fights
 firstRoundFights
 fightsByRound
 
 isPlayoffCompetitor
 isPlayoffTeam
 isDirectEliminationCompetitor
 isDirectEliminationTeam
 
 getGroupSize
 
 
 hasPreliminary(),isPlayOffType(),isDirectEliminationType()
 
 
 ## FightersGroup
  
  championship
  fights
  fighters
  teams
  competitors
  getFighterType
  
  ## Competitor
  user
  fightersGroups
  getFullName
  defaultName
  
  #Fight 
  group
  competitor1
  competitor2
  team1
  team2
  shouldBeInFightList
  generateFightsId
  
  Navigate in the tree
  -> parents, children
  
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

## Limitations

This is a work in progress, and tree creation might be very complex, so there is a bunch of things to achieve.  

- Modify Preliminary Round generation on the fly
- Manage Winner and third place fight
- Manage more than 1 fighter out of preliminary round
- Manage n+1 case : When for instance, there is 17 competitors in a direct elimination tree, there will have 15 BYES.
 We can improve that making the first match with 3 competitors.
- Use any number of area ( restricted to 1,2,4,8) 
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

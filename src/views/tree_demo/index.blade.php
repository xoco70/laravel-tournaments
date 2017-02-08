<!DOCTYPE html>
<html>
<head>
    <title>Laravel Tournaments</title>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">


</head>
<body>
<?php
$championship = $tournament->championships[0];
$setting = $championship->settings
    ?? new \Xoco70\LaravelTournaments\Models\ChampionshipSettings(config('laravel-tournaments.default_settings'));

$treeType = $setting->treeType;
$hasPreliminary = $setting->hasPreliminary;
$hasEncho = $setting->hasEncho;
$teamSize = $setting->teamSize;
$enchoQty = $setting->enchoQty;
$fightingAreas = $setting->fightingAreas; // 0

$fightDuration = $setting->fightDuration;
$enchoDuration = $setting->enchoDuration;


$categoryId = $championship->category->id;
$disableEncho = $hasEncho ? "" : "disabled";
$disablePreliminary = $hasPreliminary ? "" : "disabled";

//$currency = Auth::user()->country->currency_code;

        ?>
<div class="container">
    <div class="content">
{{--        <h1 align="center">{{ $tournament->name }}</h1>--}}
        <div class="row">
            <div class="col-md-12">
                @include('laravel-tournaments::partials.settings')
            </div>
            {{--<div class="col-md-6">2</div>--}}
        </div>
    </div>
</div>
</body>
</html>
<!DOCTYPE html>
<html>
<head>
    <title>Laravel Tournaments</title>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" href="/vendor/laravel-tournaments/css/custom.css">
    <link rel="stylesheet" href="/vendor/laravel-tournaments/css/brackets.css">


</head>
<body>
<?php
$isTeam = session()->has('isTeam') ? session('isTeam') : 0;
$championship = $tournament->championships[$isTeam];
$setting = $championship->getSettings();


$treeType = $setting->treeType;
$hasPreliminary = $setting->hasPreliminary;
$fightingAreas = $setting->fightingAreas;
$fights = $championship->fights;
$numFighters = session()->has('numFighters') ? session('numFighters') : 5;

?>
@include('laravel-tournaments::partials.errors')

<div class="container">
    <div class="content">
        <h1 align="center">{{ $tournament->name }}</h1>
        @include('laravel-tournaments::partials.settings')
        @if ($championship->fightersGroups->count()>0)
            <h1>Tree</h1>
            <hr/>
            @if ($championship->hasPreliminary())
                @include('laravel-tournaments::partials.tree.preliminary')
                @include('laravel-tournaments::partials.tree.directElimination', ['hasPreliminary' => 1])
            @else
                @if ($championship->isDirectEliminationType())
                    @include('laravel-tournaments::partials.tree.directElimination', ['hasPreliminary' => 0])
                @elseif ($championship->isPlayOffType())
                    @include('laravel-tournaments::partials.tree.playOff')
                @else
                    Ooooops. Problem
                @endif
            @endif
            <br/>
            <h1>Fight List</h1>
            <hr/>
            <div align="center">
                @include('laravel-tournaments::partials.fights')
            </div>

        @endif
    </div>
</div>
</body>


<script src="/vendor/laravel-tournaments/js/jquery.js"></script>
<script src="/vendor/laravel-tournaments/js/bootstrap.js"></script>

</html>
<!DOCTYPE html>
<html>
<head>
    <title>Laravel Kendo Tournaments</title>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" href="/vendor/kendo-tournaments/css/bootstrap-switch.min.css">
    <link rel="stylesheet" href="/vendor/kendo-tournaments/css/jquery.timepicker.css">
    <link rel="stylesheet" href="/vendor/kendo-tournaments/css/custom.css">
    <link rel="stylesheet" href="/vendor/kendo-tournaments/css/brackets.css">


</head>
<body>
<?php

$setting = $championship->getSettings();
$treeType = $setting->treeType;
$hasPreliminary = $setting->hasPreliminary;
$fightingAreas = $setting->fightingAreas;
$fights = $championship->fights;
$numFighters = $numFighters ??  5;
$isTeam = $isTeam ??  5;;
?>
@include('kendo-tournaments::partials.errors')

<div class="container">
    <div class="content">
        <h1 align="center">{{ $tournament->name }}</h1>
        @include('kendo-tournaments::partials.settings')
        @if ($championship->fightersGroups->count()>0)
            <h1>Tree</h1>
            <hr/>
            @if ($championship->hasPreliminary())
                @include('kendo-tournaments::partials.tree.preliminary')
            @else
                @if ($championship->isDirectEliminationType())
                    @include('kendo-tournaments::partials.tree.directElimination', ['fromRound' => 1])
                @elseif ($championship->isPlayOffType())
                    @include('kendo-tournaments::partials.tree.playOff')
                @else
                    Ooooops. Problem
                @endif
            @endif
            <br/>
            @if ($championship->isDirectEliminationType() && !$championship->hasPreliminary() )
                <h1 style=" margin-top: {{ 65* sizeof($fights )*2 }}px; ">Fight List</h1>
            @else
                <h1>Fight List</h1>
            @endif

            <hr/>
            <div align="center">
                @include('kendo-tournaments::partials.fights')
            </div>

        @endif
    </div>
</div>
</body>


<script src="/vendor/kendo-tournaments/js/jquery.js"></script>
<script src="/vendor/kendo-tournaments/js/bootstrap.js"></script>
<script src="/vendor/kendo-tournaments/js/bootstrap-switch.min.js"></script>
<script src="/vendor/kendo-tournaments/js/jquery.timepicker.js"></script>
<script src="/vendor/kendo-tournaments/js/jquery.bracket.min.js"></script>

</html>
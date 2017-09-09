<!DOCTYPE html>
<html>
<head>
    <title>Laravel Tournaments</title>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" href="/vendor/laravel-tournaments/css/custom.css">
    <link rel="stylesheet" href="/vendor/laravel-tournaments/css/brackets.css">
    <script src="https://unpkg.com/vue@2.4.2/dist/vue.js"></script>

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

<div class="container" id="app">
    <div class="content">
        <br/>
        @include('laravel-tournaments::partials.settings')
        @if ($championship->fightersGroups->count()>0)
            <h1>Tree</h1>
            <hr/>
            @if ($championship->hasPreliminary())
                @include('laravel-tournaments::partials.tree.preliminary')
            @else
                @if ($championship->isSingleEliminationType())
                    @include('laravel-tournaments::partials.tree.singleElimination', ['hasPreliminary' => 0])
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


<script>
    var treeValue = {{$setting->treeType}};
    var preliminaryValue = {{$hasPreliminary}};
    new Vue({

        el: '#app',
        data: {
            isPrelimDisabled: false,
            isGroupSizeDisabled: false,
            isAreasDisabled: false,
            hasPrelim: 0,
            tree: 1,
        },
        methods: {
            prelim: function () {
                if (this.hasPrelim == 0) {
                    this.isGroupSizeDisabled = true;
                } else {
                    this.isGroupSizeDisabled = false;
                }
            },
            treeType: function () {
                if (this.tree == 0) {
                    this.isPrelimDisabled = true;
                    this.isAreaDisabled = true;
                } else {
                    this.isPrelimDisabled = false;
                    this.isAreaDisabled = false;
                }
            }

        },
        created() {
            this.tree = treeValue;
            this.hasPrelim = preliminaryValue;
            this.prelim();
            this.treeType();
        }
    })
</script>
</html>
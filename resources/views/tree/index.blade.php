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
    <style>
        .bg-grey {
            background-color: #CCC;
        }
    </style>

</head>
<body>
<?php
$championship = $tournament->championships[0];
$setting = $championship->settings
    ?? new \Xoco70\KendoTournaments\Models\ChampionshipSettings(\Xoco70\KendoTournaments\Models\ChampionshipSettings::DEFAULT_SETTINGS);

$treeType = $setting->treeType;
$hasPreliminary = $setting->hasPreliminary;
$hasEncho = $setting->hasEncho;
$teamSize = $setting->teamSize;
$enchoQty = $setting->enchoQty;
$fightingAreas = $setting->fightingAreas;

$fightDuration = $setting->fightDuration;
$enchoDuration = $setting->enchoDuration;


$categoryId = $championship->category->id;
$disableEncho = $hasEncho ? "" : "disabled";
$disablePreliminary = $hasPreliminary ? "" : "disabled";
$fights = $championship->fights;
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
                    @include('kendo-tournaments::partials.tree.directElimination')
                @elseif ($championship->isPlayOffType())
                    @include('kendo-tournaments::partials.tree.playOff')
                @else
                    Ooooops. Problem
                @endif
            @endif
            <br/>
            @if ($championship->isDirectEliminationType())
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

<script>
    $(".switch").bootstrapSwitch();
    $('input[name="hasEncho"]').on('switchChange.bootstrapSwitch', function (event, state) {
        let isChecked = $(this).is(':checked');
        $(this).closest('form').find('[name="enchoQty"]').prop('disabled', !isChecked);
        $(this).closest('form').find('[name="enchoDuration"]').prop('disabled', !isChecked);
        $(this).closest('form').find('[name="enchoTimeLimitless"]').prop('disabled', !isChecked);
    });
    $('input[name="hasPreliminary"]').on('switchChange.bootstrapSwitch', function (event, state) {
        let isChecked = $(this).is(':checked');
        $(this).closest('form').find('[name="preliminaryGroupSize"]').prop('disabled', !isChecked);
        $(this).closest('form').find('[name="preliminaryWinner"]').prop('disabled', !isChecked);

    });
    $('input[name="hasHantei"]').on('switchChange.bootstrapSwitch', function (event, state) {
        let isChecked = $(this).is(':checked');
        $(this).closest('form').find('[name="hanteiLimit"]').prop('disabled', !isChecked);
    });
    $('.fightDuration').timepicker(('option', {
        'minTime': '2:00',
        'maxTime': '10:00',
        'timeFormat': 'H:i',
        'step': '15'
    }));

    $('.enchoDuration').timepicker(('option', {
        'minTime': '1:00',
        'maxTime': '10:00',
        'timeFormat': 'H:i',
        'step': '15'
    }));
</script>
</html>
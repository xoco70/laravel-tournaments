<!DOCTYPE html>
<html>
<head>
    <title>Laravel Kendo Tournaments</title>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" href="/vendor/kendo-tournaments/css/bootstrap-switch.min.css">
    <link rel="stylesheet" href="/vendor/kendo-tournaments/css/jquery.timepicker.css">


</head>
<body>
<?php
$championship = $tournament->championships[0];
$setting = $championship->settings
    ?? new \Xoco70\KendoTournaments\Models\ChampionshipSettings(config('kendo-tournaments.default_settings'));

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
@if (isset($message))
    <div class="alert alert-info">
        <button type="button" class="close" data-dismiss="alert"><span>×</span><span class="sr-only">Close</span>
        </button>
        {{ $message }}</div>
@endif
@if (isset($error))
    <div class="alert alert-info">
        <button type="button" class="close" data-dismiss="alert"><span>×</span><span class="sr-only">Close</span>
        </button>
        {{ $error }}</div>
@endif

<div class="container">
    <div class="content">
        <h1 align="center">{{ $tournament->name }}</h1>
        @include('kendo-tournaments::partials.settings')
        @if ($championship->tree->count()>0)
            @if ($championship->hasPreliminary())
                Si Hay Arboles preliminarios
            @else
                @if ($championship->isDirectEliminationType())
                    Si Hay Arboles de Elimination directa
                @elseif ($championship->isRoundRobinType())
                    Si Hay Arboles de Round Robin
                @else
                    Si hay arboles de ningun tipo
                @endif
            @endif
        @endif
    </div>
</div>
</body>
<script src="/vendor/kendo-tournaments/js/jquery.js"></script>
<script src="/vendor/kendo-tournaments/js/bootstrap.js"></script>
<script src="/vendor/kendo-tournaments/js/bootstrap-switch.min.js"></script>
<script src="/vendor/kendo-tournaments/js/jquery.timepicker.js"></script>

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
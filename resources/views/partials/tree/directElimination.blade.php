<?php
use Xoco70\KendoTournaments\TreeGen\CreateDirectEliminationTree;

// Used to regenerate _lft and _rgt
foreach ($championship->fightersGroups as $group) {
    $group->fixTree();
}
$directEliminationTree = $championship->fightersGroups->where('round', '>=', $hasPreliminary +1 )->groupBy('round');

$treeGen = new CreateDirectEliminationTree($directEliminationTree, $championship, $hasPreliminary);
$treeGen->build();
?>
@if (Request::is('championships/'.$championship->id.'/pdf'))
    <h1> {{$championship->buildName()}}</h1>
@endif

{{--{!! Form::model(null, ["action" => ["TreeController@update", $championship->id]]) !!}--}}
<input type="hidden" id="activeTreeTab" name="activeTreeTab" value="{{$championship->id}}"/>
{{  $treeGen->printRoundTitles() }}

<div id="brackets-wrapper"
     style="padding-bottom: {{ ($championship->groupsByRound(1)->count() * 205 / 2) +100 }}px">
    @foreach ($treeGen->brackets as $roundNumber => $round)
        @foreach ($round as $matchNumber => $match)

            <div class="match-wrapper"
                 style="top:  {{ $match['matchWrapperTop'] }}px; left:  {{ $match['matchWrapperLeft']  }}px; width: {{   $treeGen->matchWrapperWidth  }}px;">
                <input type="text"
                       class="score"> @include('kendo-tournaments::partials.tree.brackets.playerList', ['selected' => $match['playerA']])
                <div class="match-divider"></div>
                <input type="text"
                       class="score"> @include('kendo-tournaments::partials.tree.brackets.playerList', ['selected' => $match['playerB']])
            </div>

            @if ($roundNumber != $treeGen->noRounds)


                <div class="vertical-connector"
                     style="top: {{  $match['vConnectorTop']  }}px; left: {{  $match['vConnectorLeft']  }}px; height: {{  $match['vConnectorHeight']  }}px;"></div>
                <div class="horizontal-connector"
                     style="top: {{  $match['hConnectorTop']  }}px; left: {{  $match['hConnectorLeft']  }}px;"></div>
                <div class="horizontal-connector"
                     style="top: {{  $match['hConnector2Top']  }}px; left: {{  $match['hConnector2Left']  }}px;"></div>
            @endif

        @endforeach

    @endforeach

</div>

{{--{!! Form::close() !!}--}}

<?php
use Xoco70\LaravelTournaments\TreeGen\CreateSingleEliminationTree;

$singleEliminationTree = $championship->fightersGroups->where('round', '>=', $hasPreliminary + 1)->groupBy('round');
if (sizeof($singleEliminationTree) > 0) {
    $treeGen = new CreateSingleEliminationTree($singleEliminationTree, $championship, $hasPreliminary);
    $treeGen->build();
}
?>
@if (sizeof($singleEliminationTree)>0)

    @if (Request::is('championships/'.$championship->id.'/pdf'))
        <h1> {{$championship->buildName()}}</h1>
    @endif
    <form method="POST" action="{{ route('tree.update', ['championship' => $championship->id])}}"
          accept-charset="UTF-8">
        {{ csrf_field() }}
        <input type="hidden" name="_method" value="PUT">
        <input type="hidden" id="activeTreeTab" name="activeTreeTab" value="{{$championship->id}}"/>
        {{  $treeGen->printRoundTitles() }}

        <div id="brackets-wrapper"
             style="padding-bottom: {{ ($championship->groupsByRound(1)->count() / 2 * 205) }}px">
            <!-- 205 px x 2 groups of 2-->
            @foreach ($treeGen->brackets as $roundNumber => $round)
                @foreach ($round as $matchNumber => $match)
                    <?php
                    $isAWinner = (optional($match['playerA'])->id == $match['winner_id'] && $match['winner_id'] != null) ? 'X' : null;
                    $isBWinner = (optional($match['playerB'])->id == $match['winner_id'] && $match['winner_id'] != null) ? 'X' : null;
                    ?>
                    <div class="match-wrapper"
                         style="top:  {{ $match['matchWrapperTop'] }}px; left:  {{ $match['matchWrapperLeft']  }}px; width: {{   $treeGen->matchWrapperWidth  }}px;">
                        <div {{ $isAWinner ? "id=success" : '' }}>
                            <input type="text" class="score" name="score[]" value="{{ $isAWinner }}" {{ $isAWinner ? "id=success" : '' }}>
                            @include('laravel-tournaments::partials.tree.brackets.playerList',
                                ['selected' => $match['playerA'],
                                'matchNumber' => $matchNumber,
                                'isSuccess' => $isAWinner
                                ])
                        </div>
                        <div class="match-divider"></div>
                        <div {{ $isBWinner ? "id=success" : '' }}>
                            <input type="text" class="score" name="score[]" value="{{ $isBWinner }}" {{ $isBWinner ? "id=success" : '' }}>
                            @include('laravel-tournaments::partials.tree.brackets.playerList',
                                ['selected' => $match['playerB'],
                                 'roundNumber'=>$roundNumber,
                                 'isSuccess' => $isBWinner
                                  ])
                        </div>
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
        @endif


        <div class="clearfix"></div>
        <div align="right">
            <button type="submit" class="btn btn-success" id="update">
                Update Tree
            </button>
        </div>


    </form>
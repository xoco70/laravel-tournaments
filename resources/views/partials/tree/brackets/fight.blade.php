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
            'roundNumber'=>$roundNumber,
            'isSuccess' => $isAWinner
            ])
    </div>
    <div class="match-divider"></div>
    <div {{ $isBWinner ? "id=success" : '' }}>
        <input type="text" class="score" name="score[]"
               value="{{ $isBWinner }}" {{ $isBWinner ? "bg-success-300" : "" }}>
        @include('laravel-tournaments::partials.tree.brackets.playerList',
            ['selected' => $match['playerB'],
             'roundNumber'=>$roundNumber,
             'isSuccess' => $isBWinner
              ])
    </div>
</div>
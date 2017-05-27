<?php
$topOffset = ($numRound == 1 ? 0 : (pow(2, $numRound-1)-1) * 51);
$top = \App\Bracket::getTopFight($numRound, $numFight) + $topOffset;

$left = \App\Bracket::getLeftFight($numRound);
if ($numRound== 3) dump(\App\Bracket::getTopFight($numRound, $numFight),$topOffset);
?>

<div class="match-wrapper" style="top: {{ $top }}px; left: {{$left}}px; width: 150px;">
    <input type="text" class="score">
    Name
    <div class="match-divider"></div>
    <input type="text" class="score">
    Name
</div>
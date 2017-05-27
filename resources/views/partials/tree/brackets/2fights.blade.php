<?php
$numFight1 = $numGroup + $numGroup - 1;
$numFight2 = 2 * $numGroup;

?>
@include('layouts.tree.brackets.fight', ['numFight' => $numFight1])
@include('layouts.tree.brackets.upper_bracket', ['numFight' => $numFight1, '$numGroup' => $numGroup, '$numRound' => $numRound])
@include('layouts.tree.brackets.fight', ['numFight' => $numFight2])
@include('layouts.tree.brackets.lower_bracket', ['numFight' => $numFight2, '$numGroup' => $numGroup, '$numRound' => $numRound])

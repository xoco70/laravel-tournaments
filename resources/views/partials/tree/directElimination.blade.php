<?php
use Xoco70\KendoTournaments\TreeGen\DirectEliminationTreeGen;

$directEliminationTree = $championship->fightersGroups->reverse()->groupBy('round');

?>
@if (Request::is('championships/'.$championship->id.'/pdf'))
    <h1> {{$championship->buildName()}}</h1>
@endif
<?php
$brackets = new DirectEliminationTreeGen($directEliminationTree, $championship);

$brackets->printBrackets();
?>
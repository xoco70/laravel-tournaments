<?php
use Xoco70\KendoTournaments\TreeGen\CreateDirectEliminationTree;

$directEliminationTree = $championship->fightersGroups->groupBy('round');

?>
@if (Request::is('championships/'.$championship->id.'/pdf'))
    <h1> {{$championship->buildName()}}</h1>
@endif
<?php
$brackets = new CreateDirectEliminationTree($directEliminationTree, $championship);

$brackets->printBrackets();
?>
<?php
use Xoco70\KendoTournaments\TreeGen\DirectEliminationTreeGen;

$directEliminationTree = $championship->fightersGroups->map(function ($item, $key) use ($championship) {
    if ($championship->category->isTeam()){

        $fighter1 = $item->team1 != null ? $item->team1->name : "Bye";
        $fighter2 = $item->team2 != null ? $item->team2->name : "Bye";
    }else{
        $fighter1 = $item->competitors->get(0) != null ? $item->competitors->get(0)->user->name : "Bye";
        $fighter2 = $item->competitors->get(1) != null ? $item->competitors->get(1)->user->name : "Bye";
    }
    return [$fighter1, $fighter2];
})->toArray();

$directEliminationTree = array_flatten($directEliminationTree);
?>
@if (Request::is('championships/'.$championship->id.'/pdf'))
    <h1> {{$championship->buildName()}}</h1>
@endif
<?php
$brackets = new DirectEliminationTreeGen($directEliminationTree, "", 1);

$brackets->printBrackets();
?>
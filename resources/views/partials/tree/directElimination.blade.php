<?php
$directEliminationTree = $championship->rounds->map(function ($item, $key) use ($championship) {
    if ($championship->category->isTeam()){

        $fighter1 = $item->team1 != null ? $item->team1->name : "Bye";
        $fighter2 = $item->team2 != null ? $item->team2->name : "Bye";
    }else{
        $fighter1 = $item->competitors->get(0) != null ? $item->competitors->get(0)->user->name : "Bye";
        $fighter2 = $item->competitors->get(1) != null ? $item->competitors->get(1)->user->name : "Bye";
    }
    return [$fighter1, $fighter2];
})->toArray();
?>
@if (Request::is('championships/'.$championship->id.'/pdf'))
    <h1> {{$championship->category->buildName()}}</h1>
@endif

<div id="brackets_{{ $championship->id }}"></div>
<script>
    var minimalData_{{ $championship->id }} = {!!     json_encode([ 'teams' => $directEliminationTree ] ) !!};
</script>

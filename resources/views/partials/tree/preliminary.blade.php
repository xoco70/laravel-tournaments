<?php
$settings = $championship->getSettings();
?>
@if (Request::is('championships/'.$championship->id.'/pdf'))
    <h1> {{$championship->buildName()}}</h1>
@endif
<br/>
<br/>

@foreach($championship->fightersGroups()->where('round',1)->get()->groupBy('area') as $groupsByArea)
    <table class="table-bordered" cellpadding="5" cellspacing="0">
        <tr>
            <th class="p-10">Area</th>
            <th class="p-10"></th>
            <th class="p-10">Competitor 1</th>
            <th class="p-10"></th>
            <th class="p-10">Competitor 2</th>
            <th class="p-10"></th>
            <th class="p-10">Competitor 3</th>
            @if ($settings!= null && $settings->preliminaryGroupSize > 3)
                <th class="p-10"></th>
                <th class="p-10">Competitor 4</th>
            @endif
            @if ($settings!= null && $settings->preliminaryGroupSize > 4)
                <th class="p-10"></th>
                <th class="p-10">Competitor 5</th>
            @endif
        </tr>

        @foreach($groupsByArea as $group)
            <?php
            $fighters = $group->getFightersWithBye();
            $letter = 'a';
            ?>
            <tr>
                <td class="p-10 text-center">{{$group->area}}</td>
                @foreach ($fighters as $numFighter => $fighter)
                    <td class="p-10">{{ $letter++ }}</td>
                    <td class="p-10">@include('laravel-tournaments::partials.tree.brackets.playerList',
                    [
                        'selected' => $fighter,
                        'roundNumber' => 1,
                        'isSuccess' => 0,
                     ])
                    </td>
                @endforeach
            </tr>
        @endforeach
    </table><br/>
@endforeach




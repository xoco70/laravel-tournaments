<?php
$settings = $championship->getSettings();
?>
<div class="row">
    <div class="col-lg-12">
        @if (Request::is('championships/'.$championship->id.'/pdf'))
            <h1> {{$championship->buildName()}}</h1>
        @endif
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
                    $names = $group->fightersWithBye()->map->fullName;
                    $letter = 'a';
                    ?>
                    <tr>
                        <td class="p-10">{{$group->area}}</td>
                        @foreach ($names as $name)
                            <td class="p-10">{{ $letter++ }}</td>
                            <td class="p-10">{{ $name }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </table><br/>
        @endforeach
    </div>
    <div class="col-lg-12">
        @include('kendo-tournaments::partials.tree.directElimination', ['fromRound' => 2])
    </div>
</div>


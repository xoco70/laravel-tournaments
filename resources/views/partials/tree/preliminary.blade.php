<?php
$settings = $championship->getSettings();
?>
<div align="center">
    @if (Request::is('championships/'.$championship->id.'/pdf'))
        <h1> {{$championship->buildName()}}</h1>
    @endif
    @foreach($championship->fightersGroups()->where('round',1)->get()->groupBy('area') as $groupsByArea)
        <table class="table-bordered" cellpadding="5" cellspacing="0">
            <tr>
                <th class="p-10">ID</th>
                <th class="p-10">Has DP</th>
                <th class="p-10">Areas</th>
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
                    <td class="p-10">{{$group->id}}</td>
                    <td class="p-10">{{$group->hasDeterminedParent()}}</td>
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


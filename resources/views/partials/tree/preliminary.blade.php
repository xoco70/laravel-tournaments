<div align="center">

    @if (Request::is('championships/'.$championship->id.'/pdf'))
        <h1> {{$championship->category->buildName()}}</h1>
    @endif
    @foreach($championship->tree->groupBy('area') as $roundsByArea)
        <table class="table-bordered" cellpadding="5" cellspacing="0">
            <tr>
                <th class="p-10">Areas</th>
                <th class="p-10"></th>
                <th class="p-10">Competitor 1</th>
                <th class="p-10"></th>
                <th class="p-10">Competitor 2</th>
                <th class="p-10"></th>
                <th class="p-10">Competitor 3</th>
                @if ($championship->settings!= null && $championship->settings->preliminaryGroupSize > 3)
                    <th class="p-10"></th>
                    <th class="p-10">{{trans_choice('core.competitor',1)}} 4</th>
                @endif
                @if ($championship->settings!= null && $championship->settings->preliminaryGroupSize > 4)
                    <th class="p-10"></th>
                    <th class="p-10">{{trans_choice('core.competitor',1)}} 5</th>
                @endif
            </tr>

            @foreach($roundsByArea as $round)
                <?php
                if ($championship->category->isTeam) {
                    $fighter1 = $round->team1 != null ? $round->team1->name : '';
                    $fighter2 = $round->team2 != null ? $round->team2->name : '';
                    $fighter3 = $round->team3 != null ? $round->team3->name : '';
                    $fighter4 = $round->team4 != null ? $round->team4->name : '';
                    $fighter5 = $round->team5 != null ? $round->team5->name : '';
                } else {
                    $fighter1 = $round->competitors->get(0) != null ? $round->competitors->get(0)->user->name : '';
                    $fighter2 = $round->competitors->get(1) != null ? $round->competitors->get(1)->user->name : '';
                    $fighter3 = $round->competitors->get(2) != null ? $round->competitors->get(2)->user->name : '';
                    $fighter4 = $round->competitors->get(3) != null ? $round->competitors->get(3)->user->name : '';
                    $fighter5 = $round->competitors->get(4) != null ? $round->competitors->get(4)->user->name : '';

                }
                ?>
                <tr>
                    <td class="p-10">{{$round->area}}</td>
                    <td class="p-10">a</td>
                    <td class="p-10">{{ $fighter1 }}</td>
                    <td class="p-10">b</td>
                    <td class="p-10">{{ $fighter2 }}</td>
                    <td class="p-10">c</td>
                    <td class="p-10">{{ $fighter3 }}</td>

                    @if ($championship->settings!= null && $championship->settings->preliminaryGroupSize > 3)
                        <td class="p-10">d</td>
                        <td class="p-10">{{ $fighter4 }}</td>
                    @endif
                    @if ($championship->settings!= null && $championship->settings->preliminaryGroupSize > 3)
                        <td class="p-10">e</td>
                        <td class="p-10">{{ $fighter5 }}</td>
                    @endif

                    {{--@if ($championship->settings!= null && $championship->settings->preliminaryGroupSize>3)--}}
                    {{--<td class="p-10">{{$round->user4!= null ? $pt->user4->name : ''}}</td>--}}
                    {{--@endif--}}
                    {{--@if ($championship->settings!= null && $championship->settings->preliminaryGroupSize==5)--}}
                    {{--<td class="p-10">{{$pt->user5!= null ? $pt->user5->name : ''}}</td>--}}
                    {{--@endif--}}
                </tr>
            @endforeach
        </table><br/>
    @endforeach
</div>

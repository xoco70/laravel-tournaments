@foreach($championship->fights()->get()->groupBy('area') as $fightsByArea)
    <h4>Area {{ $fightsByArea->get(0)->area }}</h4>
    <table class="table-bordered text-center" width="600">
        <th class="p-10 text-center" width="100">Id</th>
        <th class="p-10 text-center" width="250" >Competitor 1</th>
        <th class="p-10 text-center" width="250" >Competitor 2</th>

        <?php $fightId = 0; ?>
        @foreach($fightsByArea as $fight)
            @if ($fight->shouldBeInFightList(false))
                <?php
                if ($championship->category->isTeam) {
                    $fighter1 = optional($fight->team1)->name ?? "BYE";
                    $fighter2 = optional($fight->team2)->name ?? "BYE";
                } else {
                    $fighter1 = optional($fight->competitor1)->fullName ?? "BYE";
                    $fighter2 = optional($fight->competitor2)->fullName ?? "BYE";
                }
                $fightId++;
                ?>


                <tr>
                    <td class="p-10">{{$fightId}}</td>
                    <td class="p-10">{{ $fighter1 }}</td>
                    <td class="p-10">{{ $fighter2 }}</td>
                </tr>
            @endif
        @endforeach
    </table>
    <br/><br/>
@endforeach
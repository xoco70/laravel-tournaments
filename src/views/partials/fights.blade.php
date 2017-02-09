
@foreach($championship->fights->groupBy('area') as $fightsByArea)
    <table class="table-bordered text-center">
        <th class="p-10">Id</th>
        <th class="p-10">Competitor 1</th>
        <th class="p-10">Competitor 2</th>

        @foreach($fightsByArea->sortBy('id') as $id => $fight)

            <?php

            if ($championship->category->isTeam) {
                $user1 = $fight->team1->name;
                $user2 = $fight->team2->name;
            } else {
                $user1 = $fight->user1 != null ? $fight->user1->name : "BYE";
                $user2 = $fight->user2 != null ? $fight->user2->name : "BYE";
            }

            ?>


            <tr>
                <td class="p-10">{{$id + 1}}</td>
                <td class="p-10">{{ $user1 }}</td>
                <td class="p-10">{{ $user2 }}</td>
            </tr>
        @endforeach
    </table>
@endforeach
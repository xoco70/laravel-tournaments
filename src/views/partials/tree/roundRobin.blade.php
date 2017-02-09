<div align="center">
    <table class="table-bordered text-center">
        <th class="p-10"></th>
        <th class="p-10"></th>
        <?php
        if ($championship->category->isTeam) {
            $users = $championship->teams;
        } else {
            $users = $championship->users;
        }
        ?>
        @foreach ( $users as $id => $user)

            <th class="p-10">{{ $id+1 }}</th>
        @endforeach
        <th class="p-10">W</th>
        <th class="p-10">L</th>
        <th class="p-10">P</th>

        @foreach ( $users as $id =>$user1)
            <tr>
                <td class="p-10">{{ ($id +1) }}</td>
                <td class="p-10">{{$user1->name}}</td>
                @foreach ( $users as $user2)
                    @if ($user1 == $user2)
                        <td class="p-10 bg-grey"></td>
                    @else
                        <td class="p-10"></td>
                    @endif
                @endforeach
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
        @endforeach
    </table>
    <br/>
</div>
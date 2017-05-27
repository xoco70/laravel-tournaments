<select name="fights[]">
    <option {{ $selected == '' ? ' selected' : '' }} ></option>
        @foreach ($treeGen->championship->fighters as $fighter)

            @if ($fighter != null)
                <option {{ $selected != null && $selected->id == $fighter->id ? ' selected' : '' }}  value="{{$fighter->id ?? null }}">
                    {{  $fighter->fullName }}
                </option>
            @endif
        @endforeach
</select>


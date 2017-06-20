<?php
$className = "directElimination_select";
if ($championship->hasPreliminary() && $numRound ==1){
    $className = "preliminary_select";
}

?>
<select name="fighters[]" class={{$className}}>
    <option {{ $selected == '' ? ' selected' : '' }} ></option>
        @foreach ($championship->fighters as $fighter)

            @if ($fighter != null)
                <option {{ $selected != null && $selected->id == $fighter->id ? ' selected' : '' }}  value="{{$fighter->id ?? null }}">
                    {{  $fighter->fullName }}
                </option>
            @endif
        @endforeach
</select>


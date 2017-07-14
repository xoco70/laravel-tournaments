<?php
$prefix = "directElimination";
if ($championship->hasPreliminary() && $numRound ==1){
    $prefix = "preliminary";
}
$className = $prefix."_select";
$selectName = $prefix."_fighters[]";
?>
<select name="{{ $selectName }}" class={{$className}}>
    <option {{ $selected == '' ? ' selected' : '' }} ></option>
        @foreach ($championship->fighters as $fighter)

            @if ($fighter != null)
                <option {{ $selected != null && $selected->id == $fighter->id ? ' selected' : '' }}  value="{{$fighter->id ?? null }}">
                    {{  $fighter->fullName }}
                </option>
            @endif
        @endforeach
</select>


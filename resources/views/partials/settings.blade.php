
<form method="POST" action="{{ route('tree.store', ['championship' => $championship])}}" accept-charset="UTF-8"
      class="form-settings">
    {{ csrf_field() }}

    <div class="row">
        <div class="col-lg-2">
            <label for="hasPreliminary">Preliminary</label>
            <br/>
            <input name="hasPreliminary" type="hidden" value="0">
            <select class="form-control" id="hasPreliminary" name="hasPreliminary"
                    :disabled="isPrelimDisabled" v-model="hasPrelim" v-on:change="prelim()">

                <option value="0" {{ $hasPreliminary == 0 ? 'selected' :'' }}>NO</option>
                <option value="1" {{ $hasPreliminary == 1 ? 'selected' :'' }}>YES</option>

            </select>
        </div>

        <div class="col-lg-2">
            <div class="form-group">
                <label for="preliminaryGroupSize">{{trans('laravel-tournaments::core.preliminaryGroupSize')}}</label>
                <select class="form-control" id="preliminaryGroupSize" name="preliminaryGroupSize" :disabled="isGroupSizeDisabled">
                    <option value="3" @if ($setting->preliminaryGroupSize == 3) selected @endif>3</option>
                    <option value="4" @if ($setting->preliminaryGroupSize == 4) selected @endif>4</option>
                    <option value="5" @if ($setting->preliminaryGroupSize == 5) selected @endif>5</option>
                </select>
            </div>
        </div>
        <div class="col-lg-2">
            <div class="form-group">
                <label for="numFighters">Fighter Qty</label>
                <select class="form-control" id="numFighters" name="numFighters">
                    @for($i=1;$i<60;$i++)
                        <option value="{{$i}}" @if ($numFighters== $i) selected @endif >{{  $i }}</option>
                    @endfor
                </select>
            </div>
        </div>



    </div>

    <div class="row">
        <div class="col-lg-2">
            <label for="isTeam">Team?</label>
            <br/>
            <select class="form-control" id="isTeam" name="isTeam">
                <option value="0" {{ $isTeam == 0 ? 'selected' :'' }}>NO</option>
                <option value="1" {{ $isTeam == 1 ? 'selected' :'' }}>YES</option>

            </select>
        </div>
        <div class="col-lg-2">

            <label for="treeType">Tree Type</label>
            <select class="form-control" id="treeType" name="treeType"
                    v-model="tree" v-on:change="treeType()" >
                <option value="0"
                        @if ($setting->treeType == 0) selected @endif>{{ trans('laravel-tournaments::core.playoff') }}
                </option>
                <option value="1"
                        @if ($setting->treeType == 1) selected @endif>{{ trans('laravel-tournaments::core.single_elimination') }}
                </option>
            </select>
        </div>

        <div class="col-lg-2">

            <label for="fightingAreas">{{ trans_choice('laravel-tournaments::core.fightingArea',2) }}</label>
            <select class="form-control" id="fightingAreas" name="fightingAreas" :disabled="isAreaDisabled">
                <option value="1" @if ($setting->fightingAreas == 1) selected @endif>1</option>
                <option value="2" @if ($setting->fightingAreas == 2) selected @endif>2</option>
                <option value="4" @if ($setting->fightingAreas == 4) selected @endif>4</option>
                <option value="8" @if ($setting->fightingAreas == 8) selected @endif>8</option>
            </select>

        </div>


    </div>

    <div align="right">
        <button type="submit" class="btn btn-success save_category" id="save">
            Generate Tree
        </button>
    </div>

</form>
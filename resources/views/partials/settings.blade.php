<form method="POST" action="http://tournament-plugin.dev/kendo-tournaments/championships/1/trees"
      accept-charset="UTF-8"
      class="form-settings">
    {{ csrf_field() }}


    <div class="row">
        <div class="col-lg-2">
            <div class="checkbox-switch">

                <label for="hasPreliminary">Preliminary</label>
                <br/>

                <input id="hasPreliminary" name="hasPreliminary" type="hidden" value="0">
                <input class="switch" data-on-text="Si" data-off-text="No" id="hasPreliminary" checked="checked"
                       name="hasPreliminary" type="checkbox" value="{{ $setting->hasPreliminary }}">

            </div>
        </div>

        <div class="col-lg-3">
            <div class="form-group">
                <label for="preliminaryGroupSize">{{trans('kendo-tournaments::categories.preliminaryGroupSize')}}</label>
                <select class="form-control" id="preliminaryGroupSize" name="preliminaryGroupSize">
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                </select>
            </div>
        </div>
        <div class="col-lg-3">
            {!!  Form::label('numFighters', "Fighter Quantity") !!}

            <div class="input-group">
                <label for="preliminaryGroupSize">Fighter Qty</label>
                <select class="form-control" id="numFighters" name="numFighters">
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                    <option value="7">7</option>
                    <option value="8">8</option>
                </select>
            </div>
        </div>


    </div>
    <hr/>
    <div class="row">
        <div class="col-lg-3">

            <label for="treeType">Tree Type</label>
            <select class="form-control" id="treeType" name="treeType">
                <option value="0">{{ trans('kendo-tournaments::categories.roundRobin') }}</option>
                <option value="1"
                        selected="selected">{{ trans('kendo-tournaments::categories.direct_elimination') }}</option>
            </select>
        </div>

        <div class="col-lg-2">

            <label for="fightingAreas">{{ trans_choice('kendo-tournaments::categories.fightingArea',2) }}</label>
            <select class="form-control" id="fightingAreas" name="fightingAreas">
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="4">4</option>
                <option value="8">8</option>
            </select>

        </div>


    </div>

    <div align="right">
        <button type="submit" class="btn btn-success save_category" id="save">
            Generate Tree
        </button>
    </div>

</form>
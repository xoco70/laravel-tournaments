{!! Form::open([
             'id' => 'form_',
             'data-championship' => $championship->id,
             'class' => 'form-settings',
             'action' => ['\Xoco70\KendoTournaments\TreeController@store', $championship->id ]
]) !!}

<div class="row">

    {{--<div class="col-lg-2">--}}
        {{--{!!  Form::label('fightDuration', trans('kendo-tournaments::categories.fightDuration')) !!}--}}

        {{--<div class="input-group">--}}
            {{--{!!  Form::input('text','fightDuration',$fightDuration, ['class' => 'form-control fightDuration','id' => 'fightDuration']) !!}--}}
            {{--<span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span>--}}
        {{--</div>--}}
    {{--</div>--}}
    {{--<div class="col-lg-2">--}}
        {{--{!!  Form::label('numFighters', "Fighter Quantity") !!}--}}

        {{--<div class="input-group">--}}
            {{--{!!  Form::select('numFighters',[0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20],old('numFighters'), ['class' => 'form-control','id' => 'numFighters']) !!}--}}
        {{--</div>--}}
    {{--</div>--}}

    {{--<div class="col-lg-2">--}}
        {{--<div class="form-group">--}}
            {{--{!!  Form::label('cost', trans('kendo-tournaments::categories.cost'). ' ('. $currency  .')' ) !!}--}}
            {{--<i class="icon-help" data-popup="tooltip" title="" data-placement="right"--}}
               {{--data-original-title="{{trans('kendo-tournaments::categories.costTooltip')}}"></i>--}}
            {{--{!!  Form::input('number','cost',old('cost'), ['class' => 'form-control']) !!}--}}
        {{--</div>--}}
    {{--</div>--}}

    {{--@if ($tournament->championships->get($key)->category->isTeam())--}}

        {{--<div class="col-lg-3">--}}
            {{--{!!  Form::label('teamSize', trans('kendo-tournaments::categories.teamSize')) !!}--}}
            {{--{!!  Form::select('teamSize', config('kendo-tournaments.teamSize'),old('teamSize'), ['class' => 'form-control']) !!}--}}
        {{--</div>--}}
    {{--@endif--}}

</div>
<hr/>
<div class="row">
    <div class="col-lg-2">
        <div class="checkbox-switch">

            {!!  Form::label('hasPreliminary', trans('kendo-tournaments::categories.hasPreliminary')) !!}
            <br/>

            {!!   Form::hidden('hasPreliminary', 0,['id'=>'hasPreliminary' ]) !!}
            {!!   Form::checkbox('hasPreliminary', 1, $setting->hasPreliminary,
                                 ['class' => 'switch', 'data-on-text'=>"Si", 'data-off-text'=>"No", 'id'=>'hasPreliminary']) !!}

        </div>
    </div>

    <div class="col-lg-3">
        <div class="form-group">
            {!!  Form::label('preliminaryGroupSize', trans('kendo-tournaments::categories.preliminaryGroupSize')) !!}
            {!!  Form::select('preliminaryGroupSize', config('kendo-tournaments.preliminaryGroupSize'), old('preliminaryGroupSize'),['class' => 'form-control',1]) !!}
        </div>
    </div>
    {{--<div class="col-lg-4">--}}
        {{--<div class="form-group">--}}
            {{--{!!  Form::label('preliminaryWinner', trans('kendo-tournaments::categories.preliminaryWinner')) !!}--}}
            {{--{!!  Form::select('preliminaryWinner', config('kendo-tournaments.preliminaryWinner'), old('preliminaryWinner'),['class' => 'form-control',$disablePreliminary]) !!}--}}
        {{--</div>--}}
    {{--</div>--}}

</div>
<hr/>
<div class="row">
    <div class="col-lg-3">
        {!!  Form::label('treeType', trans('kendo-tournaments::categories.treeType')) !!}
        {!!  Form::select('treeType',
        [
        0 => trans('kendo-tournaments::categories.roundRobin'),
        1 => trans('kendo-tournaments::categories.direct_elimination')]
        , $treeType ,['class' => 'form-control']) !!}
    </div>

    <div class="col-lg-2">
        {!!  Form::label('fightingAreas', trans_choice('kendo-tournaments::categories.fightingArea',2)) !!}
        {!!  Form::select('fightingAreas', [1 => 1, 2 => 2,4 => 4,8 => 8], old('fightingAreas'),['class' => 'form-control']) !!}
    </div>

    {{--<div class="col-lg-3">--}}
        {{--<div class="form-group">--}}
            {{--{!!  Form::label('limitByEntity', trans('kendo-tournaments::categories.limitByEntity')) !!}--}}
            {{--{!!  Form::select('limitByEntity', config('kendo-tournaments.limitByEntity'), old('limitByEntity'),['class' => 'form-control', "disabled"]) !!}--}}
        {{--</div>--}}
    {{--</div>--}}

</div>

<div align="right">
    <button type="submit" class="btn btn-success save_category" id="save">
        Generate Tree
    </button>
</div>

{!! Form::close() !!}
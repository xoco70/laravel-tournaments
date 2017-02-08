@if (is_null($championship->settings))
    {!! Form::open([
                 'id' => 'form_',
                 'data-championship' => $championship->id,
                 'class' => 'form-settings',

]) !!}
@else
    {!! Form::model($setting,
                ['method'=>"PATCH",
                'class' => 'form-settings',
                 'id' => 'form_',
                 'data-championship' => $championship->id,
                 'data-setting' => $setting->id,
                               ]) !!}

@endif

{{--<div class="row">--}}

{{--<div class="col-lg-2">--}}
{{--{!!  Form::label('fightDuration', trans('laravel-tournaments::categories.fightDuration')) !!}--}}
{{--<i class="icon-help" data-popup="tooltip" title="" data-placement="right"--}}
{{--data-original-title="{{trans('laravel-tournaments::categories.fightDurationTooltip')}}"></i>--}}

{{--<div class="input-group">--}}
{{--{!!  Form::input('text','fightDuration',$fightDuration, ['class' => 'form-control fightDuration','id' => 'fightDuration']) !!}--}}
{{--<span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span>--}}
{{--</div>--}}
{{--</div>--}}
{{--<div class="col-lg-2">--}}
{{--<div class="form-group">--}}
{{--{!!  Form::label('cost', trans('laravel-tournaments::categories.cost'). ' ('. $currency  .')' ) !!}--}}
{{--<i class="icon-help" data-popup="tooltip" title="" data-placement="right"--}}
{{--data-original-title="{{trans('laravel-tournaments::categories.costTooltip')}}"></i>--}}
{{--{!!  Form::input('number','cost',old('cost'), ['class' => 'form-control']) !!}--}}
{{--</div>--}}
{{--</div>--}}

{{--@if ($tournament->championships->get($key)->category->isTeam())--}}

{{--<div class="col-lg-3">--}}
{{--{!!  Form::label('teamSize', trans('laravel-tournaments::categories.teamSize')) !!}--}}
{{--{!!  Form::select('teamSize', config('laravel-tournaments.teamSize'),old('teamSize'), ['class' => 'form-control']) !!}--}}
{{--</div>--}}
{{--@endif--}}

{{--</div>--}}
{{--<hr/>--}}
<div class="row">
    <div class="col-lg-2">
        <div class="checkbox-switch">

            {!!  Form::label('hasPreliminary', trans('laravel-tournaments::categories.hasPreliminary')) !!}
            <br/>

            {!!   Form::hidden('hasPreliminary', 0,['id'=>'hasPreliminary' ]) !!}
            {!!   Form::checkbox('hasPreliminary', 1, $setting->hasPreliminary,
                                 ['class' => 'switch', 'data-on-text'=>"Si", 'data-off-text'=>"No", 'id'=>'hasPreliminary']) !!}

        </div>
    </div>

    <div class="col-lg-3">
        <div class="form-group">
            {!!  Form::label('preliminaryGroupSize', trans('laravel-tournaments::categories.preliminaryGroupSize')) !!}
            {!!  Form::select('preliminaryGroupSize', config('laravel-tournaments.preliminaryGroupSize'), 3,['class' => 'form-control',1]) !!}
        </div>
    </div>
    <div class="col-lg-4">
        <div class="form-group">
            {!!  Form::label('preliminaryWinner', trans('laravel-tournaments::categories.preliminaryWinner')) !!}
            {!!  Form::select('preliminaryWinner', config('laravel-tournaments.preliminaryWinner'), old('preliminaryWinner'),['class' => 'form-control',$disablePreliminary]) !!}
        </div>
    </div>

</div>
<hr/>
<div class="row">
    <div class="col-lg-3">
        {!!  Form::label('treeType', trans('laravel-tournaments::categories.treeType')) !!}
        {!!  Form::select('treeType',
        [
        0 => trans('laravel-tournaments::categories.roundRobin'),
        1 => trans('laravel-tournaments::categories.direct_elimination')]
        , $treeType ,['class' => 'form-control']) !!}
    </div>

    <div class="col-lg-2">
        {!!  Form::label('fightingAreas', trans_choice('categories.fightingArea',2)) !!}
        {!!  Form::select('fightingAreas', [1 => 1, 2 => 2,4 => 4,8 => 8], old('fightingAreas'),['class' => 'form-control']) !!}
    </div>

    <div class="col-lg-3">
        <div class="form-group">
            {!!  Form::label('limitByEntity', trans('laravel-tournaments::categories.limitByEntity')) !!}
            {!!  Form::select('limitByEntity', config('laravel-tournaments.limitByEntity'), old('limitByEntity'),['class' => 'form-control', "disabled"]) !!}

        </div>
    </div>

</div>
<hr/>
<div class="row">
    <div class="col-lg-2">
        <div class="checkbox-switch">
            <label>

                {!!  Form::label('hasEncho', trans('laravel-tournaments::categories.hasEncho')) !!}
                <br/>
                {!!   Form::hidden('hasEncho', 0,['id'=>'hasEncho' ]) !!}
                {!!   Form::checkbox('hasEncho', 1, $setting->hasEncho, // $hasPreliminary
                ['class' => 'switch', 'data-on-text'=>"Si", 'data-off-text'=>"No", 'id'=>'hasEncho']) !!}

            </label>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="form-group">
            {!!  Form::label('enchoQty', trans('laravel-tournaments::categories.enchoQty')) !!}
            {!!  Form::select('enchoQty', config('laravel-tournaments.enchoQty'), old('enchoQty'),['class' => 'form-control',$disableEncho]) !!}
            <small class="display-block">{{ trans('laravel-tournaments::categories.encho_infinite') }}</small>
        </div>
    </div>
    <div class="col-lg-4">
        {!!  Form::label('enchoDuration', trans('laravel-tournaments::categories.enchoDuration')) !!}
        <div class="input-group ">
            {!!  Form::input('text','enchoDuration', $enchoDuration, ['class' => 'form-control enchoDuration','id' => 'enchoDuration', $disableEncho]) !!}
            <span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span>
        </div>
    </div>
    <div class="col-lg-2">
        <div class="form-group">
            {!!  Form::label('enchoGoldPoint', trans('laravel-tournaments::categories.enchoGoldPoint')) !!}
            {!!  Form::select('enchoGoldPoint', [0,1,2,3,4,5,6,7,8,9,10], old('enchoGoldPoint'),['class' => 'form-control',$disableEncho]) !!}
        </div>
    </div>
</div>
<hr/>
<div class="row">
    <div class="col-lg-2">
        <div class="checkbox-switch">
            <label>

                {!!  Form::label('hasHantei', trans('laravel-tournaments::categories.hasHantei')) !!}
                <br/>
                {!!   Form::hidden('hasHantei', 0,['id'=>'hasHantei' ]) !!}
                {!!   Form::checkbox('hasHantei', 1,$setting->hasHantei,
                ['class' => 'switch', 'data-on-text'=>"Si", 'data-off-text'=>"No", 'id'=>'hasHantei']) !!}
            </label>
        </div>
    </div>
    <div class="col-lg-3">
    <div class="checkbox-switch">
    <label>

    {{--{!!  Form::label('hanteiLimit', trans('laravel-tournaments::categories.hanteiLimit')) !!}--}}
    {{--{!!  Form::select('hanteiLimit', $hanteiLimit , old('hanteiLimit'),['class' => 'form-control']) !!}--}}

    </label>
    </div>
    </div>

    </div>

    <div align="right">
        <button type="submit" class="btn btn-success save_category" id="save">
            Generate Tree
        </button>
    </div>
</div>

{!! Form::close() !!}
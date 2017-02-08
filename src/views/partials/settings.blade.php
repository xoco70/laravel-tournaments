<div id="accordion-styled-group" class="panel-collapse collapse ">
    <div class="panel-body">
        <div class="tab-pane" id="category">
            <div class="row">


                {{--@if ($tournament->championship->category->isTeam())--}}
                    {{--<div class="col-lg-3">--}}
                        {{--{!!  Form::label('teamSize', trans('categories.teamSize')) !!}--}}
                        {{--{!!  Form::select('teamSize', config('options.teamSize'),old('teamSize'), ['class' => 'form-control']) !!}--}}
                        {{--{!!  Form::select('teamSize', 1 ,old('teamSize'), ['class' => 'form-control']) !!}--}}
                    {{--</div>--}}
                {{--@endif--}}

            </div>
            <hr/>
            <div class="row">
                <div class="col-lg-2">
                    <div class="checkbox-switch ">
                        <label>

                            {!!  Form::label('hasPreliminary', "Has Preliminary") !!}
                            <br/>

                            {!!   Form::hidden('hasPreliminary', 0,['id'=>'hasPreliminary' ]) !!}
                            {{--{!!   Form::checkbox('hasPreliminary', 1, $setting->hasPreliminary,--}}
                                                 {{--['class' => 'switch', 'data-on-text'=>"Si", 'data-off-text'=>"No", 'id'=>'hasPreliminary']) !!}--}}
                            {!!   Form::checkbox('hasPreliminary', 1, 1,
                                                 ['class' => 'switch', 'data-on-text'=>"Si", 'data-off-text'=>"No", 'id'=>'hasPreliminary']) !!}

                        </label>
                    </div>
                </div>

                <div class="col-lg-3">
                    <div class="form-group">
                        {!!  Form::label('preliminaryGroupSize', trans('categories.preliminaryGroupSize')) !!}
{{--                        {!!  Form::select('preliminaryGroupSize', config('options.preliminaryGroupSize'), old('preliminaryGroupSize'),['class' => 'form-control',$disablePreliminary]) !!}--}}
                        {!!  Form::select('preliminaryGroupSize', 3, old('preliminaryGroupSize'),['class' => 'form-control',$disablePreliminary = 1]) !!}

                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="form-group">
                        {!!  Form::label('preliminaryWinner', trans('categories.preliminaryWinner')) !!}
                        {{--{!!  Form::select('preliminaryWinner', config('options.preliminaryWinner'), old('preliminaryWinner'),['class' => 'form-control',$disablePreliminary = 1]) !!}--}}
                        {!!  Form::select('preliminaryWinner', 1, old('preliminaryWinner'),['class' => 'form-control',$disablePreliminary = 1]) !!}
                    </div>
                </div>

            </div>
            <hr/>
            {{--<div class="row">--}}
                {{--<div class="col-lg-3">--}}
                    {{--{!!  Form::label('treeType', trans('categories.treeType')) !!}--}}
                    {{--{!!  Form::select('treeType',--}}
                            {{--[--}}
                             {{--0 => trans('categories.roundRobin'),--}}
                             {{--1 => trans('categories.direct_elimination')]--}}
                             {{--, $treeType ,['class' => 'form-control']) !!}--}}
                {{--</div>--}}

                {{--<div class="col-lg-2">--}}
                    {{--{!!  Form::label('fightingAreas', trans_choice('categories.fightingArea',2)) !!}--}}
                    {{--<i class="icon-help" data-popup="tooltip" title="" data-placement="right"--}}
                       {{--data-original-title="{{trans('categories.fightingAreaTooltip')}}"></i>--}}
                    {{--{!!  Form::select('fightingAreas', [1 => 1, 2 => 2,4 => 4,8 => 8], old('fightingAreas'),['class' => 'form-control']) !!}--}}
                {{--</div>--}}

                {{--<div class="col-lg-3">--}}
                    {{--<div class="form-group">--}}
                        {{--{!!  Form::label('limitByEntity', trans('categories.limitByEntity')) !!}--}}
                        {{--<i class="icon-help" data-popup="tooltip" title="" data-placement="right"--}}
                           {{--data-original-title="{{trans('categories.limitByEntityTooltip')}}"></i>--}}
                        {{--{!!  Form::select('limitByEntity', config('options.limitByEntity'), old('limitByEntity'),['class' => 'form-control', "disabled"]) !!}--}}

                    {{--</div>--}}
                {{--</div>--}}

            {{--</div>--}}
            {{--<hr/>--}}


            {{--<div class="row">--}}
                {{--<div class="col-lg-2">--}}
                    {{--<div class="checkbox-switch">--}}
                        {{--<label>--}}

                            {{--{!!  Form::label('hasEncho', trans('categories.hasEncho')) !!}--}}
                            {{--<i class="icon-help" data-popup="tooltip" title="" data-placement="right"--}}
                               {{--data-original-title="{{trans('categories.hasEnchoTooltip')}}"></i>--}}
                            {{--<br/>--}}
                            {{--{!!   Form::hidden('hasEncho', 0,['id'=>'hasEncho' ]) !!}--}}
                            {{--{!!   Form::checkbox('hasEncho', 1, $setting->hasEncho, // $hasPreliminary--}}
                                                 {{--['class' => 'switch', 'data-on-text'=>"Si", 'data-off-text'=>"No", 'id'=>'hasEncho']) !!}--}}

                        {{--</label>--}}
                    {{--</div>--}}
                {{--</div>--}}
                {{--<div class="col-lg-4">--}}
                    {{--<div class="form-group">--}}
                        {{--{!!  Form::label('enchoQty', trans('categories.enchoQty')) !!}--}}
                        {{--<i class="icon-help" data-popup="tooltip" title="" data-placement="right"--}}
                           {{--data-original-title="{{trans('categories.enchoQtyTooltip')}}"></i>--}}

                        {{--{!!  Form::select('enchoQty', config('options.enchoQty'), old('enchoQty'),['class' => 'form-control',$disableEncho]) !!}--}}
                        {{--<small class="display-block">{{ trans('categories.encho_infinite') }}</small>--}}
                    {{--</div>--}}
                {{--</div>--}}
                {{--<div class="col-lg-4">--}}
                    {{--{!!  Form::label('enchoDuration', trans('categories.enchoDuration')) !!}--}}
                    {{--<i class="icon-help" data-popup="tooltip" title="" data-placement="right"--}}
                       {{--data-original-title="{{trans('categories.enchoDurationTooltip')}}"></i>--}}
                    {{--<div class="input-group ">--}}
                        {{--{!!  Form::input('text','enchoDuration', $enchoDuration, ['class' => 'form-control enchoDuration','id' => 'enchoDuration', $disableEncho]) !!}--}}
                        {{--<span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span>--}}
                    {{--</div>--}}
                {{--</div>--}}
                {{--<div class="col-lg-2">--}}
                    {{--<div class="form-group">--}}
                        {{--{!!  Form::label('enchoGoldPoint', trans('categories.enchoGoldPoint')) !!}--}}
                        {{--<i class="icon-help" data-popup="tooltip" title="" data-placement="right"--}}
                           {{--data-original-title="{{trans('categories.enchoGoldPointTooltip')}}"></i>--}}

                        {{--{!!  Form::select('enchoGoldPoint', [0,1,2,3,4,5,6,7,8,9,10], old('enchoGoldPoint'),['class' => 'form-control',$disableEncho]) !!}--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}
            {{--<hr/>--}}
            {{--<div class="row">--}}
                {{--<div class="col-lg-2">--}}
                    {{--<div class="checkbox-switch">--}}
                        {{--<label>--}}

                            {{--{!!  Form::label('hasHantei', trans('categories.hasHantei')) !!}--}}
                            {{--<i class="icon-help" data-popup="tooltip" title="" data-placement="right"--}}
                               {{--data-original-title="{{trans('categories.hasHanteiTooltip')}}"></i>--}}
                            {{--<br/>--}}
                            {{--{!!   Form::hidden('hasHantei', 0,['id'=>'hasHantei' ]) !!}--}}
                            {{--{!!   Form::checkbox('hasHantei', 1,$setting->hasHantei,--}}
                                                 {{--['class' => 'switch', 'data-on-text'=>"Si", 'data-off-text'=>"No", 'id'=>'hasHantei']) !!}--}}
                        {{--</label>--}}
                    {{--</div>--}}
                {{--</div>--}}
                {{--<div class="col-lg-3">--}}
                    {{--<div class="checkbox-switch">--}}
                        {{--<label>--}}

                            {{--{!!  Form::label('hanteiLimit', trans('categories.hanteiLimit')) !!}--}}
                            {{--<i class="icon-help" data-popup="tooltip" title="" data-placement="right"--}}
                               {{--data-original-title="{{trans('categories.hanteilimitTooltip')}}"></i><br/>--}}
                            {{--{!!  Form::select('hanteiLimit', $hanteiLimit , old('hanteiLimit'),['class' => 'form-control']) !!}--}}

                        {{--</label>--}}
                    {{--</div>--}}
                {{--</div>--}}

            {{--</div>--}}


            <div align="right">
                <button type="submit" class="btn btn-success save_category" id="save">
                    Generate
                </button>
            </div>
        </div>
    </div>

</div>
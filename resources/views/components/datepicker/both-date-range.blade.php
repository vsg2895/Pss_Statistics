{{--<div class="row justify-content-start w-100">--}}
<div
    class="col-xl-7 col-lg-9 col-md-12 input-daterange row align-items-center">
    <div class="col-md-6">
        <div class="form-group">
            <div class="input-group form-group">
                <label class="form-control-lable-compare" for="compare-s-start">{{__('Compare start')}}</label>
                <div class="input-group-prepend">
                                                <span class="input-group-text"><i
                                                        class="ni ni-calendar-grid-58"></i></span>
                </div>
                <input id="compare-s-start" class="form-control
                      @if(isset($attributes) && isset($attributes['class']))
                {{ $attributes['class'] }}
                @else
                    ps-datepicker
@endif"
                       form="{{ $form }}" name="s_start"
                       placeholder="{{__('Start date')}}" type="text"
                       autocomplete="off"
                       value="{{ old('s_start') ?? request()->s_start }}">
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <div class="input-group">
                <div class="input-group-prepend">
                                                <span class="input-group-text"><i
                                                        class="ni ni-calendar-grid-58"></i></span>
                </div>
                <input
                    id="compare-s-end"
                    class="form-control
                    @if(isset($attributes) && isset($attributes['class']))
                    {{ $attributes['class'] }}
                    @else
                        ps-datepicker
@endif"
                    form="{{ $form }}" name="s_end"
                    placeholder="{{__('End date')}}" type="text"
                    autocomplete="off"
                    value="{{ old('s_end') ?? request()->s_end }}">
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <div class="input-group form-group">
                <label class="form-control-lable-compare" for="compare-start">{{__('Compare end')}}</label>
                <div class="input-group-prepend">
                                                <span class="input-group-text"><i
                                                        class="ni ni-calendar-grid-58"></i></span>
                </div>
                <input id="compare-start"
                       class="form-control
                    @if(isset($attributes) && isset($attributes['class']))
                       {{ $attributes['class'] }}
                       @else
                           ps-datepicker
@endif"
                       form="{{ $form }}" name="start"
                       placeholder="{{__('Start date')}}" type="text"
                       autocomplete="off"
                       value="{{ old('start') ?? request()->start }}">
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <div class="input-group">
                <div class="input-group-prepend">
                                                <span class="input-group-text"><i
                                                        class="ni ni-calendar-grid-58"></i></span>
                </div>
                <input id="compare-end" class="form-control @if(isset($attributes) && isset($attributes['class']))
                {{ $attributes['class'] }}
                @else
                    ps-datepicker
@endif" form="{{ $form }}"
                       name="end"
                       placeholder="{{__('End date')}}" type="text"
                       autocomplete="off"
                       value="{{ old('end') ?? request()->end }}">
            </div>
        </div>
    </div>

</div>
<div class="col-xl-4 col-lg-3 col-md-9 d-flex justify-content-around align-items-end">
    <div class="form-group">
        <label class="calls_count_label" for="calls_count">{{ __('Calls min count') }}:</label>
        <input type="number" form="{{ $form }}" id="calls_count"
               value="{{ request()->calls_count ? request()->calls_count : '' }}" name="calls_count" class="" min="0"
               max="999">
    </div>
    <div class="form-group">
        <div class="input-group">
            <button type="submit" class="btn btn-primary" id="date_range_submit" form="{{ $form }}"
                    data-toggle="tooltip" data-placement="left" title="">{{__('Submit')}}</button>
        </div>
    </div>
</div>
{{--    <div class="col-md-2 submit-div d-flex align-items-end">--}}
{{--        <div class="col-sm-12 d-flex justify-content-start">--}}
{{--            --}}
{{--        </div>--}}
{{--    </div>--}}


{{--</div>--}}



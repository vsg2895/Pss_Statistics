<div class="input-daterange datepicker row align-items-center">
    <div class="col-md-5">
        <div class="form-group">
            <div class="input-group">
                <div class="input-group-prepend">
                                                <span class="input-group-text"><i
                                                        class="ni ni-calendar-grid-58"></i></span>
                </div>
                <input
                    class="form-control  @if(isset($attributes) && isset($attributes['class'])) {{ $attributes['class'] }}
                    @else ps-datepicker @endif" name="start"
                    placeholder="{{__('Start date')}}" type="text"
                    value="{{ old('start') ?? request()->start }}">
            </div>
        </div>
    </div>
    <div class="col-md-5">
        <div class="form-group">

            <div class="input-group">
                <div class="input-group-prepend">
                                                <span class="input-group-text"><i
                                                        class="ni ni-calendar-grid-58"></i></span>
                </div>
                <input class="form-control
                @if(isset($attributes) && isset($attributes['class']))
                    {{ $attributes['class'] }}
                @else
                    ps-datepicker
                @endif"
                       name="end"
                       placeholder="{{__('End date')}}" type="text"
                       value="{{ old('end') ?? request()->end }}">
            </div>
        </div>
    </div>
    <div class="col-md-2 col-sm-12 d-flex justify-content-end">
        <div class="form-group">
            <div class="input-group">
                <button class="btn btn-primary" id="date_range_submit"
                        data-toggle="tooltip" data-placement="left" title="{{$buttonTitle}}">{{__('Submit')}}</button>
            </div>
        </div>
    </div>
</div>

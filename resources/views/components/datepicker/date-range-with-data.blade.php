<div
    class="input-daterange datepicker row align-items-center">
    <div class="col-md-5">
        <div class="form-group">
            <div class="input-group">
                <div class="input-group-prepend">
                                                <span class="input-group-text"><i
                                                        class="ni ni-calendar-grid-58"></i></span>
                </div>
                <input
                    class="form-control current_change
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
    <div class="col-md-5">
        <div class="form-group">
            <div class="input-group">
                <div class="input-group-prepend">
                                                <span class="input-group-text"><i
                                                        class="ni ni-calendar-grid-58"></i></span>
                </div>
                <input class="form-control current_change
                @if(isset($attributes) && isset($attributes['class']))
                    {{ $attributes['class'] }}
                @else
                    ps-datepicker
                @endif" form="{{ $form }}" name="end"
                       placeholder="{{__('End date')}}" type="text"
                       {{--                       Check which route set data-url depend update type by company or provider--}}
                       {{--                       @if($object instanceof ('App\\Models\\Company'))--}}
                       {{--                       data-url="{{route('admin.company.currentDb',['company' => $object])}}"--}}
                       {{--                       @else--}}
                       {{--                       data-url="{{route('admin.provider.currentDb',['provider' => $object])}}"--}}
                       {{--                       @endif--}}
                       autocomplete="off"
                       value="{{ old('end') ?? request()->end }}">
            </div>
        </div>
    </div>

</div>


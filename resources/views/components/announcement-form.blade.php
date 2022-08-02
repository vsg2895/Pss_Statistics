<form action="{{ route($routeName,[$routeParam => $object->id]) }}"
      method="post" class="w-50">
@csrf

<!-- Textarea with class .w-50 -->

    <div class="@if($routeParam == 'company') col-11 pl-0 d-flex mt-2 flex-column justify-content-end form-outline w-100 mb-4 @endif">
        <input type="hidden" name="start" value="{{ request()->start }}">
        <input type="hidden" name="end" value="{{ request()->end }}">
        <div class="col-11 @if($routeParam !== 'company') p-0 @endif">
            <label class="form-label font-weight-bold"
                   for="announcement">{{ __('Note') }}</label>
            <textarea class="form-control" name="announcement" id="announcement">{{ $text }}</textarea>
        </div>
        <div class="col-11 d-flex justify-content-end">
            <button type="submit"
                    class="mt-2  btn btn-sm btn-success">{{ __('Add') }}</button>
        </div>
    </div>

</form>

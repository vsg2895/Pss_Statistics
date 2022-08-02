<div class="col-xl-7 col-lg-12 col-md-12 col-sm-12 ">
    @php
        if (isset(request()->start) && isset(request()->start))
            {
               $start = \Carbon\Carbon::createFromFormat('Y-m-d',request()->start);
               $end = \Carbon\Carbon::createFromFormat('Y-m-d',request()->end);
            }
    @endphp

    <button type="button" data-start="{{date('Y-m-d')}}" data-end="{{date('Y-m-d')}}"
            class="mt-md-1 default-date-buttons filters-form btn
{{ !isset(request()->start) || request()->start == request()->end ? 'btn-success' : 'btn-primary' }}">
        {{__('Today')}}</button>
    <button type="button" data-start="{{$thisMonday}}" data-end="{{date('Y-m-d')}}"
            class="mt-md-1 default-date-buttons filters-form btn ml-sm-0 ml-md-2 mt-3 mt-md-0
{{isset(request()->start) && request()->start == $thisMonday ? 'btn-success' : 'btn-primary'}}">
        {{__('Current Week')}}</button>
    <button type="button" data-start="{{$thisMonthStart}}" data-end="{{date('Y-m-d')}}"
            class="mt-md-1 default-date-buttons filters-form btn ml-sm-0 ml-md-2 mt-3 mt-md-0
{{isset(request()->start) && request()->start == $thisMonthStart ? 'btn-success' : 'btn-primary'}}">
        {{__('Current Month')}}</button>
    <button type="button" data-start="{{$lastMonday}}" data-end="{{$lastFriday->format('Y-m-d')}}"
            class="mt-md-1 default-date-buttons filters-form btn ml-sm-0 ml-md-2 mt-3 mt-md-0
{{isset(request()->start) && request()->start == $lastMonday && request()->end == $lastFriday->format('Y-m-d') ? 'btn-success' : 'btn-primary'}}">
        {{__('Last Week')}}</button>
    <button type="button" data-start="{{$lastMonthStart}}" data-end="{{$lastMonthEnd}}"
            class="mt-md-1 default-date-buttons filters-form btn ml-sm-0 mt-3 mt-md-0
{{isset(request()->start) && request()->start == $lastMonthStart && request()->end == $lastMonthEnd ? 'btn-success' : 'btn-primary'}}">
        {{__('Last Month')}}</button>
</div>

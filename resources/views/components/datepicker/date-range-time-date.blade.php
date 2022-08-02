<div class="col-12 col-sm-12 col-md-7">

    <a href="{{route($currentRoute, [$servitUser->servit_id])}}"
       class="btn btn-primary {{ request()->start_date == request()->end_date ? 'btn-success' : 'btn-primary' }}">{{__('Today')}}</a>
    <a href="{{route($currentRoute, [$servitUser->servit_id, 'start_date' => $thisMonday, 'end_date' => date('Y-m-d')])}}"
       class="btn btn-primary ml-sm-0 ml-md-2 {{isset(request()->start_date) && request()->start_date == $thisMonday ? 'btn-success' : 'btn-primary'}}">{{__('Current Week')}}</a>
    <a href="{{route($currentRoute, [$servitUser->servit_id, 'start_date' => $thisMonthStart, 'end_date' => date('Y-m-d')])}}"
       class="btn btn-primary ml-sm-0 ml-md-2 mt-3 mt-md-0 {{isset(request()->start_date) && request()->start_date == $thisMonthStart ? 'btn-success' : 'btn-primary'}}">{{__('Current Month')}}</a>
    <a href="{{route($currentRoute, [$servitUser->servit_id, 'start_date' => $lastMonday, 'end_date' => $lastFriday->format('Y-m-d')])}}"
       class="btn btn-primary ml-sm-0 ml-md-2 mt-3 mt-md-0 {{isset(request()->start_date) && request()->start_date == $lastMonday && request()->end_date == $lastFriday->format('Y-m-d') ? 'btn-success' : 'btn-primary'}}">{{__('Last Week')}}</a>
    <a href="{{route($currentRoute, [$servitUser->servit_id, 'start_date' => $lastMonthStart, 'end_date' => $lastMonthEnd])}}"
       class="btn btn-primary ml-sm-0 mt-3 mt-md-0 {{isset(request()->start_date) && request()->start_date == $lastMonthStart && request()->end_date == $lastMonthEnd ? 'btn-success' : 'btn-primary'}}">{{__('Last Month')}}</a>
</div>

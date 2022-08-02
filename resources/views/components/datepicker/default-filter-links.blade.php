<div class="col-12 col-sm-12 col-md-7">
{{--@dd($lastMonthStart,$lastMonthEnd)--}}
    <a href="{{route($routeName, [$routeParam => $object->id])}}"
       class="btn btn-primary {{ request()->start == request()->end ? 'btn-success' : 'btn-primary' }}">
        {{__('Today')}}</a>
    <a href="{{route($routeName, [$routeParam => $object->id, 'start' => $thisMonday, 'end' => date('Y-m-d')])}}"
       class="btn btn-primary ml-sm-0 ml-md-2 {{isset(request()->start) && request()->start == $thisMonday ? 'btn-success' : 'btn-primary'}}">
        {{__('Current Week')}}</a>
    <a href="{{route($routeName, [$routeParam => $object->id, 'start' => $thisMonthStart, 'end' => date('Y-m-d')])}}"
       class="btn btn-primary ml-sm-0 ml-md-2 mt-3 mt-md-0 {{isset(request()->start) && request()->start == $thisMonthStart ? 'btn-success' : 'btn-primary'}}">
        {{__('Current Month')}}</a>
    <a href="{{route($routeName, [$routeParam => $object->id, 'start' => $lastMonday, 'end' => $lastFriday->format('Y-m-d')])}}"
       class="btn btn-primary ml-sm-0 ml-md-2 mt-3 mt-md-0 {{isset(request()->start) && request()->start == $lastMonday && request()->end == $lastFriday->format('Y-m-d') ? 'btn-success' : 'btn-primary'}}">
        {{__('Last Week')}}</a>
    <a href="{{route($routeName, [$routeParam => $object->id, 'start' => $lastMonthStart, 'end' => $lastMonthEnd])}}"
       class="btn btn-primary ml-sm-0 mt-3 mt-md-0 {{isset(request()->start) && request()->start == $lastMonthStart && request()->end == $lastMonthEnd ? 'btn-success' : 'btn-primary'}}">
        {{__('Last Month')}}</a>
</div>


@php
    $inRangeFilterPage = request()->get('date_range') === 'true';
@endphp

{{--1st line boxes--}}
@foreach($todayAgentsData as $agentId => $datum)
<h3>{{$datum['name']}}</h3>
<div class="pt-3">
    <div class="card">
        <div>
            <p>{{__('Calls')}} <b><span>{{$datum['calls']}}</span></b></p>
        </div>
    </div>
    <div class="card">
        <div>
            <p>{{__('Bookings')}} <b><span>{{$datum['bookings']}}</span></b></p>
        </div>
    </div>
    <div class="card">
        <div>
            <p>{{__('Chats')}} <b><span>{{$datum['chats']}}</span></b></p>
        </div>
    </div>
    <div class="card">
        <div>
            <p>{{__('Login Time')}} <b><span>{{$datum['login_time']}}</span></b></p>
        </div>
    </div>
</div>
{{--2st line boxes--}}
<div class="pt-1">
    <div class="card">
        <div>
            <p>{{__('Avg Pickup Time')}} <b><span>{{$datum['avg_pickup_time']}}</span></b></p>
        </div>
    </div>
    <div class="card">
        <div>
            <p>{{__('Avg Talk Time')}} <b><span>{{$datum['avg_talk_time']}}</span></b></p>
        </div>
    </div>
    <div class="card">
        <div>
            <p>{{__('Pause Time')}} <b><span>{{$datum['pause_time']}}</span></b></p>
        </div>
    </div>
    <div class="card">
        <div>
            <p>{{__('Reply Busy')}} <b><span>{{$datum['repbusy']}}</span></b></p>
        </div>
    </div>
</div>
@endforeach()

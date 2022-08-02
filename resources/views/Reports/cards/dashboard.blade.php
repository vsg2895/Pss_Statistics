@php
    $inRangeFilterPage = request()->get('date_range') === 'true';
@endphp
{{--1st line boxes--}}
<div class="pt-3">
    {{--    style="display: flex;justify-content: space-between;align-items: center;"--}}
    <div class="card">
        <div class="planing-card-flex" style="width: 100%">
            <div style="width: 60%">
                <p>{{__('Calls')}}</p>

            </div>

        </div>

        @if(!$inRangeFilterPage && !isset($monthly))
            @php
            $progress = 0;
            if ($dailyStats['last_week']['daily_calls']) {
                $progress = ($dailyStats['today']['daily_calls'] / $dailyStats['last_week']['daily_calls'] * 100);
                $progress = round($progress - 100, 2);
            }
            @endphp
            <div class="text-muted text-sm">
            <span class="{{$progress >= 0 ? 'text-success' : 'text-danger'}}">
                <i class="mt-1 fa {{$progress >= 0 ? 'fa-arrow-up' : 'fa-arrow-down'}}"></i> {{abs($progress)}}%
                  </span>
                <span class="text-nowrap">{{__('Compared week ago')}}</span>
            </div>
        @endif
        <div>
            <div style="width: 70%">
                <b><span>{{$dailyStats['today']['daily_calls']}}</span></b>
            </div>
            <div class="text-muted text-sm planing-card-right" style="width: 30%">
                <i class="fas fa-mobile-android-alt green-icon @if(!$inRangeFilterPage) icon-size-with-progress @else icon-size @endif"
                   style="float: right"></i>
            </div>
        </div>


    </div>

    <div class="card">
        <div class="planing-card-flex" style="width: 100%">
            <div style="width: 60%">
                <p>{{__('Bookings')}}</p>
            </div>

        </div>
        @if(!$inRangeFilterPage && !isset($monthly))
            @php
                $progress = 0;
                if ($dailyStats['last_week']['daily_bookings']) {
                    $progress = ($dailyStats['today']['daily_bookings'] / $dailyStats['last_week']['daily_bookings'] * 100);
                    $progress = round($progress - 100, 2);
                }
            @endphp
            <div class="text-muted text-sm">
                    <span class="{{$progress >= 0 ? 'text-success' : 'text-danger'}}">
                        <i class="mt-1 fa {{$progress >= 0 ? 'fa-arrow-up' : 'fa-arrow-down'}}"></i> {{abs($progress)}}%
                    </span>
                <span class="text-nowrap">{{__('Compared week ago')}}</span>
            </div>
        @endif
        <div>
            <div style="width: 70%">
                <b><span>{{$dailyStats['today']['daily_bookings']}}</span></b>
            </div>
            <div class="text-muted text-sm planing-card-right" style="width: 30%">
                <i class="fas fa-book-alt orange-icon @if(!$inRangeFilterPage) icon-size-with-progress @else icon-size @endif"
                   style="float: right"></i>
            </div>
        </div>

    </div>
    <div class="card">
        <div class="planing-card-flex" style="width: 100%">
            <div style="width: 60%">
                <p>{{__('Chats')}}</p>
            </div>

        </div>
        @if(!$inRangeFilterPage && !isset($monthly))
            @php
                $progress = 0;
                if ($dailyStats['last_week']['daily_chats']) {
                    $progress = ($dailyStats['today']['daily_chats'] / $dailyStats['last_week']['daily_chats'] * 100);
                    $progress = round($progress - 100, 2);
                }
            @endphp
            <div class="text-muted text-sm">
                    <span class="{{$progress >= 0 ? 'text-success' : 'text-danger'}}">
                        <i class="mt-1 fa {{$progress >= 0 ? 'fa-arrow-up' : 'fa-arrow-down'}}"></i> {{abs($progress)}}%
                    </span>
                <span class="text-nowrap">{{__('Compared week ago')}}</span>
            </div>
        @endif
        <div>
            <div style="width: 70%">
                <b><span>{{$dailyStats['today']['daily_chats']}}</span></b>
            </div>
            <div class="text-muted text-sm planing-card-right" style="width: 30%">
                <i class="fas fa-comment blue-icon @if(!$inRangeFilterPage) icon-size-with-progress @else icon-size @endif"
                   style="float: right"></i>
            </div>
        </div>

    </div>
    <div class="card">
        <div class="planing-card-flex" style="width: 100%">
            <div style="width: 60%">
                <p>{{__('Median')}}</p>
            </div>

        </div>
        @if(!$inRangeFilterPage && !isset($monthly))
            @php
                $progress = 0;
                if ($dailyStats['last_week']['median_value']) {
                    $progress = ($dailyStats['today']['median_value'] / $dailyStats['last_week']['median_value'] * 100);
                    $progress = round($progress - 100, 2);
                }
            @endphp
            <div class="text-muted text-sm">
                    <span class="{{$progress >= 0 ? 'text-success' : 'text-danger'}}">
                        <i class="mt-1 fa {{$progress >= 0 ? 'fa-arrow-up' : 'fa-arrow-down'}}"></i> {{abs($progress)}}%
                    </span>
                <span class="text-nowrap">{{__('Compared week ago')}}</span>
            </div>
        @endif
        <div>
            <div style="width: 70%">
                <b><span>{{$dailyStats['today']['median_value']}}</span></b>
            </div>
            <div class="text-muted text-sm planing-card-right" style="width: 30%">
                <i class="fas fa-user-friends yellow-icon @if(!$inRangeFilterPage) icon-size-with-progress @else icon-size @endif"
                   style="float: right"></i>
            </div>
        </div>

    </div>
</div>

{{--2st line boxes--}}
<div class="pt-1">
    <div class="card">
        <div class="planing-card-flex" style="width: 100%">
            <div>
                <p>{{__('Lost Calls')}}</p>
            </div>

        </div>
        @if(!$inRangeFilterPage && !isset($monthly))
            @php
                $progress = 0;
                if ($dailyStats['last_week']['daily_missed']) {
                    $progress = ($dailyStats['today']['daily_missed'] / $dailyStats['last_week']['daily_missed'] * 100);
                    $progress = round($progress - 100, 2);
                }
            @endphp
            <div class="text-muted text-sm">
                    <span class="{{$progress >= 0 ? 'text-success' : 'text-danger'}}">
                        <i class="mt-1 fa {{$progress >= 0 ? 'fa-arrow-up' : 'fa-arrow-down'}}"></i> {{abs($progress)}}%
                    </span>
                <span class="text-nowrap">{{__('Compared week ago')}}</span>
            </div>
        @endif
        <div>
            <div style="width: 70%">
                <b><span>{{$dailyStats['today']['daily_missed']}}</span></b>
            </div>
            <div class="text-muted text-sm planing-card-right" style="width: 30%">
                <i class="fas fa-mobile-android-alt red-icon @if(!$inRangeFilterPage) icon-size-with-progress @else icon-size @endif"
                   style="float: right"></i>
            </div>
        </div>

    </div>
    <div class="card">
        <div class="planing-card-flex" style="width: 100%">
            <div>
                <p>{{__('Avg Waiting Time')}}</p>
            </div>

        </div>
        @if(!$inRangeFilterPage && !isset($monthly))
            @php
                $progress = 0;
                if ($dailyStats['last_week']['avg_waiting_time']) {
                    $progress = ($dailyStats['today']['avg_waiting_time'] / $dailyStats['last_week']['avg_waiting_time'] * 100);
                    $progress = round($progress - 100, 2);
                }
            @endphp
            <div class="text-muted text-sm">
                    <span class="{{$progress >= 0 ? 'text-success' : 'text-danger'}}">
                        <i class="mt-1 fa {{$progress >= 0 ? 'fa-arrow-up' : 'fa-arrow-down'}}"></i> {{abs($progress)}}%
                    </span>
                <span class="text-nowrap">{{__('Compared week ago')}}</span>
            </div>
        @endif
        <div>
            <div style="width: 70%">
                <b><span>{{gmdate("i:s", $dailyStats['today']['avg_waiting_time'])}}</span></b>
            </div>
            <div class="text-muted text-sm planing-card-right" style="width: 30%">
                <i class="fas fa-mobile-android-alt green-icon @if(!$inRangeFilterPage) icon-size-with-progress @else icon-size @endif"
                   style="float: right"></i>
            </div>
        </div>

    </div>
    <div class="card">
        <div class="planing-card-flex" style="width: 100%">
            <div style="width: 100%">
                <p>{{__('Avg Talk Time')}}</p>
            </div>
        </div>
        @if(!$inRangeFilterPage && !isset($monthly))
            @php
                $progress = 0;
                if ($dailyStats['last_week']['avg_talk_time']) {
                    $progress = ($dailyStats['today']['avg_talk_time'] / $dailyStats['last_week']['avg_talk_time'] * 100);
                    $progress = round($progress - 100, 2);
                }
            @endphp
            <div class="text-muted text-sm">
                    <span class="{{$progress >= 0 ? 'text-success' : 'text-danger'}}">
                        <i class="mt-1 fa {{$progress >= 0 ? 'fa-arrow-up' : 'fa-arrow-down'}}"></i> {{abs($progress)}}%
                    </span>
                <span class="text-nowrap">{{__('Compared week ago')}}</span>
            </div>
        @endif
        <div>
            <div style="width: 70%">
                <b><span>{{gmdate("i:s", $dailyStats['today']['avg_talk_time'])}}</span></b>
            </div>
            <div class="text-muted text-sm planing-card-right" style="width: 30%!important;">
                <i class="fas fa-mobile-android-alt green-icon @if(!$inRangeFilterPage) icon-size-with-progress @else icon-size @endif"
                   style="float: right"></i>
            </div>
        </div>


    </div>
    <div class="card">
        <div class="planing-card-flex" style="width: 100%">
            <div>
                <p>{{__('Time Above 60')}}</p>
            </div>

        </div>
        @if(!$inRangeFilterPage && !isset($monthly))
            @php
                $progress = 0;
                if ($dailyStats['last_week']['above_sixteen_money']) {
                    $progress = ($dailyStats['today']['above_sixteen_money'] / $dailyStats['last_week']['above_sixteen_money'] * 100);
                    $progress = round($progress - 100, 2);
                }
            @endphp
            <div class="text-muted text-sm">
                    <span class="{{$progress >= 0 ? 'text-success' : 'text-danger'}}">
                        <i class="mt-1 fa {{$progress >= 0 ? 'fa-arrow-up' : 'fa-arrow-down'}}"></i> {{abs($progress)}}%
                    </span>
                <span class="text-nowrap">{{__('Compared week ago')}}</span>
            </div>
        @endif
        <div>
            <div style="width: 70%">
                <b><span>{{$dailyStats['today']['above_sixteen_money']}} kr</span></b>
            </div>
            <div class="text-muted text-sm planing-card-right" style="width: 30%">
                <i class="fas fa-mobile-android-alt green-icon @if(!$inRangeFilterPage) icon-size-with-progress @else icon-size @endif"
                   style="float: right"></i>
            </div>
        </div>

    </div>
</div>

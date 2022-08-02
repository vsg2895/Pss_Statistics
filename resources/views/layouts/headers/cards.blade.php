@php
    $inRangeFilterPage = request()->get('date_range') === 'true';
@endphp
<div class="header pt-4 all-cards">
    <div class="container-fluid">
        <div class="header-body">
            @if(request()->route()->getName() === 'home')
                <ul class="nav nav-tabs dashboard-general-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="range-tab" data-toggle="tab" href="#range" role="tab"
                           aria-controls="range" aria-selected="false">{{__('Date Filter')}}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="compare-tab" data-toggle="tab" href="#compare" role="tab"
                           aria-controls="compare" aria-selected="true">{{__('Compare')}}</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade mt-4 show active" id="range" role="tabpanel" aria-labelledby="range-tab">
                        <form action="{{route('home')}}" method="get" autocomplete="off">
                            <input type="hidden" name="date_range" value="true">
                            <x-datepicker.date-range/>
                        </form>
                        <x-datepicker.default-filters route="home"/>
                    </div>
                    <div class="tab-pane fade mt-4" id="compare" role="tabpanel" aria-labelledby="compare-tab">
                        <form action="{{route('home')}}" method="get" autocomplete="off">
                            <div class="row align-items-center">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i
                                                        class="ni ni-calendar-grid-58"></i></span>
                                            </div>
                                            <input class="form-control datepicker ps-datepicker" name="start_date"
                                                   placeholder="{{__('Select date')}}" type="text"
                                                   value="{{ old('start_date') ?? request()->start_date }}">
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
                                            <input class="form-control datepicker ps-datepicker" name="compare_date"
                                                   placeholder="{{__('Compare with')}}" type="text"
                                                   value="{{ old('compare_date') ?? request()->compare_date }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-12 d-flex justify-content-end">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <button class="btn btn-primary">{{__('Compare')}}</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

        <!-- Card stats -->
            <div class="row mt-4">
                <div class="col-xl-3 col-lg-6">
                    <div class="card card-stats mb-4 mb-xl-0">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h5 class="card-title text-uppercase text-muted mb-0">{{__('Calls')}}</h5>
                                    <span
                                        class="h2 font-weight-bold mb-0">{{$dailyStats['today']['daily_calls']}}</span>
                                </div>
                                <div class="col-auto">
                                    <div class="icon icon-shape bg-gradient-green text-white rounded-circle shadow">
                                        <i class="fa-solid fa-phone-flip"></i>
                                        {{--                                        <i class="ni ni-mobile-button"></i>--}}
                                    </div>
                                </div>
                            </div>
                            @if(!$inRangeFilterPage)
                                @php
                                    $progress = 0;
                                    if ($dailyStats['last_week']['daily_calls']) {
                                        $progress = ($dailyStats['today']['daily_calls'] / $dailyStats['last_week']['daily_calls'] * 100);
                                        $progress = round($progress - 100, 2);
                                    }
                                @endphp
                                <p class="mt-3 mb-0 text-muted text-sm">
                                <span class="{{$progress >= 0 ? 'text-success' : 'text-danger'}} mr-2">
                                    <i class="fa {{$progress >= 0 ? 'fa-arrow-up' : 'fa-arrow-down'}}"></i> {{abs($progress)}}%
                                </span>
                                    <span class="text-nowrap">{{__('Compared week ago')}}</span>
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6">
                    <div class="card card-stats mb-4 mb-xl-0">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h5 class="card-title text-uppercase text-muted mb-0">{{__('Bookings')}}</h5>
                                    <span
                                        class="h2 font-weight-bold mb-0">{{$dailyStats['today']['daily_bookings']}}</span>
                                </div>
                                <div class="col-auto">
                                    <div class="icon icon-shape bg-gradient-orange text-white rounded-circle shadow">
                                        <i class="fa-solid fa-book-bookmark"></i>
                                        {{--                                        <i class="ni ni-book-bookmark"></i>--}}
                                    </div>
                                </div>
                            </div>
                            @if(!$inRangeFilterPage)
                                @php
                                    $progress = 0;
                                    if ($dailyStats['last_week']['daily_bookings']) {
                                        $progress = ($dailyStats['today']['daily_bookings'] / $dailyStats['last_week']['daily_bookings'] * 100);
                                        $progress = round($progress - 100, 2);
                                    }
                                @endphp
                                <p class="mt-3 mb-0 text-muted text-sm">
                                <span class="{{$progress >= 0 ? 'text-success' : 'text-danger'}} mr-2">
                                    <i class="fa {{$progress >= 0 ? 'fa-arrow-up' : 'fa-arrow-down'}}"></i> {{abs($progress)}}%
                                </span>
                                    <span class="text-nowrap">{{__('Compared week ago')}}</span>
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6">
                    <div class="card card-stats mb-4 mb-xl-0">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h5 class="card-title text-uppercase text-muted mb-0">{{__('Chats')}}</h5>
                                    <span
                                        class="h2 font-weight-bold mb-0">{{$dailyStats['today']['daily_chats']}}</span>
                                </div>
                                <div class="col-auto">
                                    <div class="icon icon-shape bg-gradient-blue text-white rounded-circle shadow">
                                        <i class="fa-solid fa-comment-dots"></i>
{{--                                        <i class="ni ni-chat-round"></i>--}}

                                    </div>
                                </div>
                            </div>
                            @if(!$inRangeFilterPage)
                                @php
                                    $progress = 0;
                                    if ($dailyStats['last_week']['daily_chats']) {
                                        $progress = ($dailyStats['today']['daily_chats'] / $dailyStats['last_week']['daily_chats'] * 100);
                                        $progress = round($progress - 100, 2);
                                    }
                                @endphp
                                <p class="mt-3 mb-0 text-muted text-sm">
                                <span class="{{$progress >= 0 ? 'text-success' : 'text-danger'}} mr-2">
                                    <i class="fa {{$progress >= 0 ? 'fa-arrow-up' : 'fa-arrow-down'}}"></i> {{abs($progress)}}%
                                </span>
                                    <span class="text-nowrap">{{__('Compared week ago')}}</span>
                                </p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-6">
                    <div class="card card-stats mb-4 mb-xl-0">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h5 class="card-title text-uppercase text-muted mb-0">{{__('Median')}}</h5>
                                    <span
                                        class="h2 font-weight-bold mb-0">{{$dailyStats['today']['median_value']}}%</span>
                                </div>
                                <div class="col-auto">
                                    <div class="icon icon-shape bg-gradient-yellow text-white rounded-circle shadow">
                                        <i class="fa-solid fa-users-gear"></i>
{{--                                        <i class="fas fa-users"></i>--}}
                                    </div>
                                </div>
                            </div>
                            @if(!$inRangeFilterPage)
                                @php
                                    $progress = 0;
                                    if ($dailyStats['last_week']['median_value']) {
                                        $progress = ($dailyStats['today']['median_value'] / $dailyStats['last_week']['median_value'] * 100);
                                        $progress = round($progress - 100, 2);
                                    }
                                @endphp
                                <p class="mt-3 mb-0 text-muted text-sm">
                                <span class="{{$progress >= 0 ? 'text-success' : 'text-danger'}} mr-2">
                                    <i class="fa {{$progress >= 0 ? 'fa-arrow-up' : 'fa-arrow-down'}}"></i> {{abs($progress)}}%
                                </span>
                                    <span class="text-nowrap">{{__('Compared week ago')}}</span>
                                </p>
                            @endif
                        </div>
                    </div>
                </div>

            </div>

            <div class="row mt-3">
                @if(auth()->guard('web')->check())
                    <div class="col-xl-3 col-lg-6">
                        <div class="card card-stats mb-4 mb-xl-0">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col">
                                        <h5 class="card-title text-uppercase text-muted mb-0">{{__('Lost Calls')}}</h5>
                                        <span
                                            class="h2 font-weight-bold mb-0">{{$dailyStats['today']['daily_missed']}}</span>
                                    </div>
                                    <div class="col-auto">
                                        <div class="icon icon-shape bg-gradient-red text-white rounded-circle shadow">
                                            <i class="fa-solid fa-phone-slash"></i>
{{--                                            <i class="ni ni-mobile-button"></i>--}}
                                        </div>
                                    </div>
                                </div>
                                @if(!$inRangeFilterPage)
                                    @php
                                        $progress = 0;
                                        if ($dailyStats['last_week']['daily_missed']) {
                                            $progress = ($dailyStats['today']['daily_missed'] / $dailyStats['last_week']['daily_missed'] * 100);
                                            $progress = round($progress - 100, 2);
                                        }
                                    @endphp
                                    <p class="mt-3 mb-0 text-muted text-sm">
                                <span class="{{$progress >= 0 ? 'text-success' : 'text-danger'}} mr-2">
                                    <i class="fa {{$progress >= 0 ? 'fa-arrow-up' : 'fa-arrow-down'}}"></i> {{abs($progress)}}%
                                </span>
                                        <span class="text-nowrap">{{__('Compared week ago')}}</span>
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                <div class="col-xl-3 col-lg-6">
                    <div class="card card-stats mb-4 mb-xl-0">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h5 class="card-title text-uppercase text-muted mb-0">{{__('Avg Waiting Time')}}</h5>
                                    <span
                                        class="h2 font-weight-bold mb-0">{{gmdate("i:s", $dailyStats['today']['avg_waiting_time'])}}</span>
                                </div>
                                <div class="col-auto">
                                    <div class="icon icon-shape bg-gradient-purple text-white rounded-circle shadow">
                                        <i class="fa-solid fa-blender-phone"></i>
{{--                                        <i class="ni ni-mobile-button"></i>--}}
                                    </div>
                                </div>
                            </div>
                            @if(!$inRangeFilterPage)
                                @php
                                    $progress = 0;
                                    if ($dailyStats['last_week']['avg_waiting_time']) {
                                        $progress = ($dailyStats['today']['avg_waiting_time'] / $dailyStats['last_week']['avg_waiting_time'] * 100);
                                        $progress = round($progress - 100, 2);
                                    }
                                @endphp
                                <p class="mt-3 mb-0 text-muted text-sm">
                                <span class="{{$progress >= 0 ? 'text-success' : 'text-danger'}} mr-2">
                                    <i class="fa {{$progress >= 0 ? 'fa-arrow-up' : 'fa-arrow-down'}}"></i> {{abs($progress)}}%
                                </span>
                                    <span class="text-nowrap">{{__('Compared week ago')}}</span>
                                </p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-6">
                    <div class="card card-stats mb-4 mb-xl-0">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h5 class="card-title text-uppercase text-muted mb-0">{{__('Avg Talk Time')}}</h5>
                                    <span
                                        class="h2 font-weight-bold mb-0">{{gmdate("i:s", $dailyStats['today']['avg_talk_time'])}}</span>
                                </div>
                                <div class="col-auto">
                                    <div class="icon icon-shape bg-gradient-info text-white rounded-circle shadow">
                                        <i class="fa-solid fa-phone-volume"></i>
{{--                                        <i class="ni ni-mobile-button"></i>--}}
                                    </div>
                                </div>
                            </div>
                            @if(!$inRangeFilterPage)
                                @php
                                    $progress = 0;
                                    if ($dailyStats['last_week']['avg_talk_time']) {
                                        $progress = ($dailyStats['today']['avg_talk_time'] / $dailyStats['last_week']['avg_talk_time'] * 100);
                                        $progress = round($progress - 100, 2);
                                    }
                                @endphp
                                <p class="mt-3 mb-0 text-muted text-sm">
                                <span class="{{$progress >= 0 ? 'text-success' : 'text-danger'}} mr-2">
                                    <i class="fa {{$progress >= 0 ? 'fa-arrow-up' : 'fa-arrow-down'}}"></i> {{abs($progress)}}%
                                </span>
                                    <span class="text-nowrap">{{__('Compared week ago')}}</span>
                                </p>
                            @endif
                        </div>
                    </div>
                </div>

                @if(auth()->guard('web')->check())
                    <div class="col-xl-3 col-lg-6">
                        <div class="card card-stats mb-4 mb-xl-0">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col">
                                        <h5 class="card-title text-uppercase text-muted mb-0">{{__('Time Above')}}</h5>
                                        <span class="h2 font-weight-bold mb-0">
                                            {{$dailyStats['today']['above_sixteen_money']}} kr
                                        </span>
                                    </div>
                                    <div class="col-auto">
                                        <div class="icon icon-shape bg-gradient-success text-white rounded-circle shadow">
                                            <i class="fa-brands fa-get-pocket"></i>
{{--                                            <i class="ni ni-mobile-button"></i>--}}
                                        </div>
                                    </div>
                                </div>
                                @if(!$inRangeFilterPage)
                                    @php
                                        $progress = 0;
                                        if ($dailyStats['last_week']['above_sixteen_money']) {
                                            $progress = ($dailyStats['today']['above_sixteen_money'] / $dailyStats['last_week']['above_sixteen_money'] * 100);
                                            $progress = round($progress - 100, 2);
                                        }
                                    @endphp
                                    <p class="mt-3 mb-0 text-muted text-sm">
                                    <span class="{{$progress >= 0 ? 'text-success' : 'text-danger'}} mr-2">
                                        <i class="fa {{$progress >= 0 ? 'fa-arrow-up' : 'fa-arrow-down'}}"></i> {{abs($progress)}}%
                                    </span>
                                        <span class="text-nowrap">{{__('Compared week ago')}}</span>
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            @if(auth()->guard('web')->check())
                <div class="row mt-3">

                    <div class="col-xl-3 col-lg-6">
                        <div class="card card-stats mb-4 mb-xl-0">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col">
                                        <h5 class="card-title text-uppercase text-muted mb-0">{{__('Max Calls')}}</h5>
                                        <span
                                            class="h2 font-weight-bold mb-0">{{isset($maxData['calls']) && isset($maxData['calls']['max_answered']) ? $maxData['calls']['max_answered']['count'] : '-'}}</span>
                                    </div>
                                    <div class="col-auto">
                                        <div class="icon icon-shape bg-gradient-indigo text-white rounded-circle shadow">
                                            <i class="fa-solid fa-phone-flip"></i>
{{--                                            <i class="ni ni-mobile-button"></i>--}}
                                        </div>
                                    </div>
                                </div>
                                <p class="mt-3 mb-0 text-muted text-sm">
                                    <span class="mr-2">
                                        <i class="fa fa-calendar-alt"></i>
                                    </span>
                                    <span
                                        class="text-nowrap">{{isset($maxData['calls']) && isset($maxData['calls']['max_answered']) ? $maxData['calls']['max_answered']['date'] : '-'}}</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-lg-6">
                        <div class="card card-stats mb-4 mb-xl-0">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col">
                                        <h5 class="card-title text-uppercase text-muted mb-0">{{__('Max Bookings')}}</h5>
                                        <span
                                            class="h2 font-weight-bold mb-0">{{isset($maxData['bookings']) && isset($maxData['bookings']['max']) ? $maxData['bookings']['max']['count'] : '-'}}</span>
                                    </div>
                                    <div class="col-auto">
                                        <div class="icon icon-shape bg-gradient-pink text-white rounded-circle shadow">
                                            <i class="fa-solid fa-book-bookmark"></i>
                                        </div>
                                    </div>
                                </div>
                                <p class="mt-3 mb-0 text-muted text-sm">
                                    <span class="mr-2">
                                        <i class="fa fa-calendar-alt"></i>
                                    </span>
                                    <span
                                        class="text-nowrap">{{isset($maxData['bookings']) && isset($maxData['bookings']['max']) ? $maxData['bookings']['max']['date'] : '-'}}</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-lg-6">
                        <div class="card card-stats mb-4 mb-xl-0">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col">
                                        <h5 class="card-title text-uppercase text-muted mb-0">{{__('Max Chats')}}</h5>
                                        <span
                                            class="h2 font-weight-bold mb-0">{{isset($maxData['chats']) && isset($maxData['chats']['max']) ? $maxData['chats']['max']['count'] : '-'}}</span>
                                    </div>
                                    <div class="col-auto">
                                        <div class="icon icon-shape bg-gradient-cyan text-white rounded-circle shadow">
                                            <i class="fa-solid fa-comment-dots"></i>
                                        </div>
                                    </div>
                                </div>
                                <p class="mt-3 mb-0 text-muted text-sm">
                                    <span class="mr-2">
                                        <i class="fa fa-calendar-alt"></i>
                                    </span>
                                    <span
                                        class="text-nowrap">{{isset($maxData['chats']) && isset($maxData['chats']['max']) ? $maxData['chats']['max']['date'] : '-'}}</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-lg-6">
                        <div class="card card-stats mb-4 mb-xl-0">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col">
                                        <h5 class="card-title text-uppercase text-muted mb-0">{{__('Login Time')}}</h5>
                                        <span
                                            class="h2 font-weight-bold mb-0">{{$dailyStats['today']['daily_login_time_show']}}</span>
                                    </div>
                                    <div class="col-auto">
                                        <div class="icon icon-shape bg-gradient-gray-dark text-white rounded-circle shadow">
                                            <i class="fas fa-user-clock"></i>
                                        </div>
                                    </div>
                                </div>
                                @if(!$inRangeFilterPage)
                                    @php
                                        $progress = 0;
                                        if ($dailyStats['last_week']['daily_login_time']) {
                                            $progress = ($dailyStats['today']['daily_login_time'] / $dailyStats['last_week']['daily_login_time'] * 100);
                                            $progress = round($progress - 100, 2);
                                        }
                                    @endphp
                                    <p class="mt-3 mb-0 text-muted text-sm">
                                        <span class="{{$progress >= 0 ? 'text-success' : 'text-danger'}} mr-2">
                                            <i class="fa {{$progress >= 0 ? 'fa-arrow-up' : 'fa-arrow-down'}}"></i> {{abs($progress)}}%
                                        </span>
                                        <span class="text-nowrap">{{__('Compared week ago')}}</span>
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>
            @endif

        </div>
    </div>
</div>

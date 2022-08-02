<div class="header pb-8 pt-4">
    <div class="container-fluid">
        <div class="header-body">
            @php
                $currentRoute = auth()->guard('web')->check()
                    ? 'admin.employee_statistics'
                    : 'employee.user_statistics'
            @endphp
            <form action="{{route($currentRoute, [$servitUser->servit_id])}}" method="get" autocomplete="off">
                <div class="input-daterange datepicker row align-items-center">
                    <div class="col-md-5">
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                                <span class="input-group-text"><i
                                                        class="ni ni-calendar-grid-58"></i></span>
                                </div>
                                <input class="form-control employee-datepicker" name="start_date"
                                       placeholder="{{__('Start date')}}" type="text"
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
                                <input class="form-control employee-datepicker" name="end_date"
                                       placeholder="{{__('End date')}}" type="text"
                                       value="{{ old('end_date') ?? request()->end_date }}">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-12 d-flex justify-content-end">
                        <div class="form-group">
                            <div class="input-group">
                                <button class="btn btn-primary">Submit</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <div class="row align-items-baseline">
                <x-datepicker.date-range-time-date :currentRoute="$currentRoute" :servitUser="$servitUser"
                                                   :thisMonday="$thisMonday" :thisMonthStart="$thisMonthStart"
                                                   :lastMonday="$lastMonday" :lastFriday="$lastFriday"
                                                   :lastMonthStart="$lastMonthStart" :lastMonthEnd="$lastMonthEnd"/>
                <div class="col-12 col-sm-12 col-md-3 mt-md-0 d-flex align-items-center">
                    <div class="card card-stats mb-4 mb-xl-0">
                        <div class="card-body">
                            <div class="row">
                                <div class="col d-flex align-items-center">
                                    <span class="mr-2">{{$dailyStats['progress']}}%</span>
                                    <div class="progress m-0" style="min-width: 150px;">
                                        <div
                                            class="progress-bar bg-gradient-{{$dailyStats['progress'] < 80 ? 'danger' : ($dailyStats['progress'] >=100 ? 'green' : 'yellow')}}"
                                            role="progressbar"
                                            aria-valuenow="{{$dailyStats['progress']}}" aria-valuemin="0"
                                            aria-valuemax="120"
                                            style="width: {{$dailyStats['progress']}}%;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-12 col-md-2 mt-md-0 d-flex align-items-center justify-content-end">
                    <button type="button" class="btn btn-sm btn-primary mr-3" onclick="generateEmployeePDF()"
                            id="export_employee_statistics_pdf">{{__('Export PDF')}}</button>
                </div>
            </div>
            <!-- Card stats -->
            <div id="cards-block">
                <div class="row pt-3">
                    <div class="col-xl-3 col-lg-6">
                        <div class="card card-stats mb-4 mb-xl-0">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col">
                                        <h5 class="card-title text-uppercase text-muted mb-0">{{__('Calls')}}</h5>
                                        <span
                                            class="h2 font-weight-bold mb-0">{{$dailyStats['calls']}}</span>
                                    </div>
                                    <div class="col-auto">
                                        <div class="icon icon-shape bg-green text-white rounded-circle shadow">
                                            <i class="fas fa-chart-bar"></i>
                                        </div>
                                    </div>
                                </div>
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
                                            class="h2 font-weight-bold mb-0">{{$dailyStats['bookings']}}</span>
                                    </div>
                                    <div class="col-auto">
                                        <div
                                            class="icon icon-shape bg-warning text-white rounded-circle shadow">
                                            <i class="fas fa-chart-pie"></i>
                                        </div>
                                    </div>
                                </div>
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
                                            class="h2 font-weight-bold mb-0">{{$dailyStats['chats']}}</span>
                                    </div>
                                    <div class="col-auto">
                                        <div class="icon icon-shape bg-blue text-white rounded-circle shadow">
                                            <i class="ni ni-chat-round"></i>

                                        </div>
                                    </div>
                                </div>
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
                                            class="h2 font-weight-bold mb-0">{{get_hour_format($dailyStats['login_time'])}}</span>
                                    </div>
                                    <div class="col-auto">
                                        <div class="icon icon-shape bg-blue text-white rounded-circle shadow">
                                            <i class="ni ni-watch-time"></i>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-xl-3 col-lg-6">
                        <div class="card card-stats mb-4 mb-xl-0">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col">
                                        <h5 class="card-title text-uppercase text-muted mb-0">{{__('Avg Pickup Time')}}</h5>
                                        <span
                                            class="h2 font-weight-bold mb-0">{{$dailyStats['avg_pickup_time'] }} sec</span>
                                    </div>
                                    <div class="col-auto">
                                        <div class="icon icon-shape bg-blue text-white rounded-circle shadow">
                                            <i class="ni ni-mobile-button"></i>
                                        </div>
                                    </div>
                                </div>
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
                                            class="h2 font-weight-bold mb-0">{{gmdate("i:s", $dailyStats['avg_talk_time'])}}</span>
                                    </div>
                                    <div class="col-auto">
                                        <div class="icon icon-shape bg-green text-white rounded-circle shadow">
                                            <i class="ni ni-mobile-button"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-lg-6">
                        <div class="card card-stats mb-4 mb-xl-0">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col">
                                        <h5 class="card-title text-uppercase text-muted mb-0">{{__('Pause Time')}}</h5>
                                        <span
                                            class="h2 font-weight-bold mb-0">{{get_hour_format($dailyStats['pause_time'])}}</span>
                                    </div>
                                    <div class="col-auto">
                                        <div class="icon icon-shape bg-orange text-white rounded-circle shadow">
                                            <i class="ni ni-mobile-button"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-lg-6">
                        <div class="card card-stats mb-4 mb-xl-0">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col">
                                        <h5 class="card-title text-uppercase text-muted mb-0">{{__('Reply Busy')}}</h5>
                                        <span
                                            class="h2 font-weight-bold mb-0">{{$dailyStats['repbusy']}}</span>
                                    </div>
                                    <div class="col-auto">
                                        <div class="icon icon-shape bg-orange text-white rounded-circle shadow">
                                            <i class="ni ni-briefcase-24"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-xl-3 col-lg-6">
                        <div class="card card-stats mb-4 mb-xl-0">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col">
                                        <h5 class="card-title text-uppercase text-muted mb-0">{{__('No Reply')}}</h5>
                                        <span
                                            class="h2 font-weight-bold mb-0">{{$dailyStats['repnorep']}}</span>
                                    </div>
                                    <div class="col-auto">
                                        <div class="icon icon-shape bg-orange text-white rounded-circle shadow">
                                            <i class="ni ni-atom"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('js')
    <script src="{{asset('assets/js/pages/employee-statistics.js')}}"></script>
@endpush


<div class="container-fluid">
    <div class="header-body">
        <!-- Card stats -->
        <div class="row">
            <div class="col-12 col-sm-3">
                <div class="card card-stats mb-4 mb-xl-0">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <h5 class="card-title text-uppercase text-muted mb-0">{{__('Daily Calls')}}</h5>
                                <span
                                    class="h2 font-weight-bold mb-0">{{$dailyStats['today']['daily_calls'] + $dailyStats['today']['daily_missed']}}</span>

                                <h5 class="card-title text-uppercase text-muted mb-0">{{__('Answered')}}</h5>
                                <span
                                    class="h2 font-weight-bold mb-0">{{$dailyStats['today']['daily_calls']}} | {{round($dailyStats['today']['daily_calls'] * 100 / max(($dailyStats['today']['daily_calls'] + $dailyStats['today']['daily_missed']), 1))}}%</span>

                                <h5 class="card-title text-uppercase text-muted mb-0">{{__('Missed')}}</h5>
                                <span
                                    class="h2 font-weight-bold mb-0">{{$dailyStats['today']['daily_missed']}}</span>
                            </div>
                            <div class="col-auto">
                                <div class="icon icon-shape bg-gradient-green text-white rounded-circle shadow">
                                    <i class="fas fa-chart-bar"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-3">
                <div class="card card-stats mb-4 mb-xl-0">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <h5 class="card-title text-uppercase text-muted mb-0">{{__('Calls in queue')}}</h5>
                                <span class="h2 font-weight-bold mb-0" id="calls-in-qu">{{isset($liveData['calls']) ? $liveData['calls'] : ''}}</span>
                                <h5 class="card-title text-uppercase text-muted mb-0">{{__('Agents')}}</h5>
                                <span class="h2 font-weight-bold mb-0" id="agents">{{isset($liveData['agents']) ? $liveData['agents'] : ''}}</span>
                                <h5 class="card-title text-uppercase text-muted mb-0">{{__('Agents Ready')}}</h5>
                                <span class="h2 font-weight-bold mb-0" id="agents_ready">{{isset($liveData['agentsReady']) ? $liveData['agentsReady'] : ''}}</span>
                            </div>
                            <div class="col-auto">
                                <div class="icon icon-shape bg-gradient-blue text-white rounded-circle shadow">
{{--                                    <i class="fa-solid fa-mobile-retro"></i>--}}
                                    <i class="ni ni-mobile-button"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6">
                <div class="row">
                    <div class="col-6">
                        <div class="card card-stats mb-4 mb-xl-0">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col">
                                        <h5 class="card-title text-uppercase text-muted mb-0">{{__('Daily Bookings')}}</h5>
                                        <span
                                            class="h2 font-weight-bold mb-0">{{$dailyStats['today']['daily_bookings']}}</span>
                                    </div>
                                    <div class="col-auto">
                                        <div class="icon icon-shape bg-gradient-warning text-white rounded-circle shadow">
                                            <i class="fas fa-chart-pie"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card card-stats mb-4 mb-xl-0">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col">
                                        <h5 class="card-title text-uppercase text-muted mb-0">{{__('Daily Chats')}}</h5>
                                        <span
                                            class="h2 font-weight-bold mb-0">{{$dailyStats['today']['daily_chats']}}</span>
                                    </div>
                                    <div class="col-auto">
                                        <div class="icon icon-shape bg-gradient-cyan text-white rounded-circle shadow">
                                            <i class="ni ni-chat-round"></i>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="row mt-3">
                    <div class="col-6">
                        <div class="card card-stats mb-4 mb-xl-0">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col">
                                        <h5 class="card-title text-uppercase text-muted mb-0">{{__('Daily Median Value')}}</h5>
                                        <span
                                            class="h2 font-weight-bold mb-0">{{$dailyStats['today']['median_value']}}%</span>
                                    </div>
                                    <div class="col-auto">
                                        <div class="icon icon-shape bg-gradient-yellow text-white rounded-circle shadow">
                                            <i class="fas fa-users"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="card card-stats mb-4 mb-xl-0">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col">
                                        <h5 class="card-title text-uppercase text-muted mb-0">{{__('Avg Waiting Time')}}</h5>
                                        <span
                                            {{--                                        class="h2 font-weight-bold mb-0">{{\Illuminate\Support\Carbon::seconds($dailyStats['today']['avg_waiting_time'])->cascade()}}</span>--}}
                                            class="h2 font-weight-bold mb-0">{{gmdate("i:s", $dailyStats['today']['avg_waiting_time'])}}</span>
                                    </div>
                                    <div class="col-auto">
                                        <div class="icon icon-shape bg-gradient-purple text-white rounded-circle shadow">
                                            <i class="ni ni-mobile-button"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>

            {{--<div class="col-xl-3 col-lg-6">
                <div class="card card-stats mb-4 mb-xl-0">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <h5 class="card-title text-uppercase text-muted mb-0">{{__('Avg Talk Time')}}</h5>
                                <span
                                    class="h2 font-weight-bold mb-0">{{gmdate("i:s", $dailyStats['today']['avg_talk_time'])}}</span>
                            </div>
                            <div class="col-auto">
                                <div class="icon icon-shape bg-blue text-white rounded-circle shadow">
                                    <i class="ni ni-mobile-button"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>--}}
        </div>
    </div>
</div>

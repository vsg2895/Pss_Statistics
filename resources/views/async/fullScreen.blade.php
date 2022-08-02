@include('layouts.headers.cards-fullScreen')

{{--<div class="box-canvas">
    <div class="scene-wrapper">
        <div class="ant-wrapper one">
            <div class="body-left"></div>
            <div class="body-middle"></div>
            <div class="body-right"></div>
        </div>

        <div class="ant-wrapper two">
            <div class="body-left"></div>
            <div class="body-middle"></div>
            <div class="body-right"></div>
        </div>

        <div class="ant-wrapper three">
            <div class="body-left"></div>
            <div class="body-middle"></div>
            <div class="body-right"></div>
        </div>
    </div>
</div>--}}

<div class="container-fluid">
    <div class="row mt-4">
        <div class="col-xl-12">
            <div class="card shadow">
                <div class="table-responsive d-none d-sm-block">
                    <!-- Projects table -->
                    <table class="table align-items-center table-flush table-striped" id="user_stat_table">
                        <thead class="thead-light">
                        <tr>
                            <th scope="col">{{__('Username')}}</th>
                            <th scope="col">{{__('Daily Calls')}}</th>
                            <th scope="col">{{__('Daily Bookings')}}</th>
                            <th scope="col">{{__('Daily Chats')}}</th>
                            <th scope="col">{{__('Daily Points')}}</th>
                            <th scope="col">{{__('Daily Login Time')}}</th>
                            <th scope="col">{{__('Progress')}}</th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach($userStats as $data)
                            <tr>
                                <th scope="row">
                                    <div class="d-flex justify-content-start">
                                        <div class="d-flex w-100">
                                            <div class="d-flex align-items-center mr-2">
                                                <div class="avatar avatar-sm rounded-circle overflow-hidden">
                                                    <img class="h-100" alt="" src="{{ $data['profile_pic']}}">
                                                </div>
                                            </div>

                                            <div class="d-flex align-items-center justify-content-start">
                                                <span class="">{{$data['username'] ?: $data['servit_user_id']}}</span>
                                            </div>
                                        </div>

                                        <div class="{{auth()->guard('web')->check() ? 'w-25' : 'w-50'}} pl-4 d-flex align-items-center justify-content-start">
                                            <div class="status_{{$data['servit_user_id']}}">
                                                <div class="icon-sm icon-shape bg-gray text-white rounded-circle shadow ml-1">
                                                    <i class="ni ni-fat-delete"></i>
                                                </div>
                                            </div>
                                        </div>

                                        @if(auth()->guard('web')->check())
                                            <div class="w-25 pl-4 d-flex align-items-center justify-content-start">
                                                <div class="icon-sm icon-shape bg-light text-white rounded-circle shadow ml-1">
                                                    @if($data['from_office'] === false)
                                                        <i class="fas fa-home"></i>
                                                    @elseif($data['from_office'] === true)
                                                        <i class="fas fa-wifi"></i>
                                                    @else
                                                        <i class="fas fa-question"></i>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </th>
                                <td>{{$data['daily_calls']}}</td>
                                <td>{{$data['daily_bookings']}}</td>
                                <td>{{$data['daily_chats']}}</td>
                                <td>{{$data['points']}}</td>
                                <td>{{$data['daily_login_time']}}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="mr-2"><span class="user-progress">{{$data['progress']}}</span>%</span>
                                        <div>
                                            <div class="progress">
                                                <div
                                                    class="progress-bar bg-gradient-{{$data['progress'] < 80 ? 'danger' : ($data['progress'] >=100 ? 'green' : 'yellow')}}"
                                                    role="progressbar"
                                                    aria-valuenow="{{$data['progress']}}" aria-valuemin="0"
                                                    aria-valuemax="120"
                                                    style="width: {{$data['progress']}}%;"></div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-block d-sm-none row">
                    @foreach($userStats as $data)
                        <div class="col">
                            <div class="card card-stats mb-4 mb-xl-0">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col">
                                            <div class="d-flex justify-content-between">
                                                <h5 class="card-title text-uppercase mb-0">{{$data['username'] ?: $data['servit_user_id']}}</h5>
                                                <div class="col d-flex align-items-center justify-content-end">
                                                    <span class="mr-2">{{$data['progress']}}%</span>
                                                    <div class="progress m-0" style="min-width: 90px;">
                                                        <div
                                                            class="progress-bar bg-gradient-{{$data['progress'] < 80 ? 'danger' : ($data['progress'] >=100 ? 'green' : 'yellow')}}"
                                                            role="progressbar"
                                                            aria-valuenow="{{$data['progress']}}" aria-valuemin="0"
                                                            aria-valuemax="120"
                                                            style="width: {{$data['progress']}}%;">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row d-flex justify-content-between">
                                                <div class="w-75">
                                                    <div>{{__('Daily Calls')}}: {{$data['daily_calls']}}</div>
                                                    <div>{{__('Daily Bookings')}}: {{$data['daily_bookings']}}</div>
                                                    <div>{{__('Daily Login Time')}}: {{$data['daily_login_time']}}</div>
                                                    <div>{{__('Daily Chats')}}: {{$data['daily_chats']}}</div>
                                                </div>
                                                <div class="w-25 d-flex align-items-center">
                                                    <div class="status_{{$data['servit_user_id']}} w-50">
                                                        <div class="icon-sm icon-shape bg-gray text-white rounded-circle shadow ml-1">
                                                            <i class="ni ni-fat-delete"></i>
                                                        </div>
                                                    </div>
                                                    <div class="w-50">
                                                        <div class="icon-sm icon-shape bg-light text-white rounded-circle shadow ml-1">
                                                            @if($data['from_office'] === false)
                                                                <i class="fas fa-home"></i>
                                                            @elseif($data['from_office'] === true)
                                                                <i class="fas fa-wifi"></i>
                                                            @else
                                                                <i class="fas fa-question"></i>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <a class="btn btn-primary ml-3"
           href="{{auth()->guard('web')->check() ? route('home') : route('employee.user_statistics')}}">
            <i class="ni ni-bold-left"></i>
        </a>
    </div>
</div>


@php
    $inRangeFilterPage = request()->get('date_range') === 'true';
@endphp
{{--@dd($totals)--}}
<div class="header pt-4">
    <div class="container-fluid">
        <div class="header-body all-cards">
            <!-- Card stats -->
            <div class="row mt-4">
                <div class="col-xl-3 col-lg-6">
                    <div class="card card-stats mb-4 mb-xl-0">
                        <div class="card-body">

                            <div class="row">
                                <div class="col">
                                    <h5 class="card-title text-uppercase text-muted mb-0">{{__('Calls')}}</h5>
                                    <span
                                        class="h2 font-weight-bold mb-0">{{$totals['calls'] ? $totals['calls'] : 0}}</span>
                                </div>
                                <div class="col-auto">
                                    <div class="icon icon-shape bg-gradient-green text-white rounded-circle shadow">
                                        <i class="fa-solid fa-phone-flip"></i>
                                        {{--                                        <i class="ni ni-mobile-button"></i>--}}
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
                                        class="h2 font-weight-bold mb-0">{{$totals['bookings'] ? $totals['bookings'] : 0}}</span>
                                </div>
                                <div class="col-auto">
                                    <div class="icon icon-shape bg-gradient-orange text-white rounded-circle shadow">
                                        <i class="fa-solid fa-book-bookmark"></i>
                                        {{--                                        <i class="ni ni-book-bookmark"></i>--}}
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
                                        class="h2 font-weight-bold mb-0">{{$totals['chats']}}</span>
                                </div>
                                <div class="col-auto">
                                    <div class="icon icon-shape bg-gradient-blue text-white rounded-circle shadow">
                                        <i class="fa-solid fa-comment-dots"></i>
                                        {{--                                        <i class="ni ni-chat-round"></i>--}}
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
                                    <h5 class="card-title text-uppercase text-muted mb-0">{{__('Cold Transfers')}}</h5>
                                    <span class="h2 font-weight-bold mb-0">
                                            {{$totals['cold_transferred']}}
                                        </span>
                                </div>
                                <div class="col-auto">
                                    <div class="icon icon-shape bg-gradient-cyan text-white rounded-circle shadow">
                                        <i class="fa-solid fa-icicles"></i>
                                        {{--                                        <i class="ni ni-mobile-button"></i>--}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="row mt-sm-3 mt-0">
                @if(isset($totals['avg_waiting_time']))
                    <div class="col-xl-3 col-lg-6">
                        <div class="card card-stats mb-4 mb-xl-0">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col">
                                        <h5 class="card-title text-uppercase text-muted mb-0">{{__('Avg Waiting Time')}}</h5>
                                        <span
                                            class="h2 font-weight-bold mb-0">{{gmdate("i:s", $totals['avg_waiting_time'])}}</span>
                                    </div>
                                    <div class="col-auto">
                                        <div class="icon icon-shape bg-gradient-info text-white rounded-circle shadow">
                                            <i class="ni ni-mobile-button"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="col-xl-3 col-lg-6">
                    <div class="card card-stats mb-4 mb-xl-0">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h5 class="card-title text-uppercase text-muted mb-0">{{__('Avg Talk Time')}}</h5>
                                    <span
                                        {{--                                        @dd(is_string($totals['avg_talk_time']))--}}
                                        class="h2 font-weight-bold mb-0">{{
                                         !is_string($totals['avg_talk_time'])
                                         ? gmdate("i:s", $totals['avg_talk_time'])
                                         : $totals['avg_talk_time']
                                         }}</span>
                                </div>
                                <div class="col-auto">
                                    <div class="icon icon-shape bg-gradient-info text-white rounded-circle shadow">
                                        <i class="fa-solid fa-phone-volume"></i>
                                        {{--                                        <i class="ni ni-mobile-button"></i>--}}
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
                                    <h5 class="card-title text-uppercase text-muted mb-0">{{__('Time Above')}}</h5>
                                    <span class="h2 font-weight-bold mb-0">
                                            {{$totals['timeAboveMoney']}} kr
                                        </span>
                                </div>
                                <div class="col-auto">
                                    <div class="icon icon-shape bg-gradient-success text-white rounded-circle shadow">
                                        <i class="fa-brands fa-get-pocket"></i>
                                        {{--                                                                                <i class="ni ni-mobile-button"></i>--}}
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
                                    <h5 class="card-title text-uppercase text-muted mb-0">{{__('Warm Transfers')}}</h5>
                                    <span class="h2 font-weight-bold mb-0">
                                            {{$totals['warm_transferred']}}
                                        </span>
                                </div>
                                <div class="col-auto">
                                    <div class="icon icon-shape bg-gradient-yellow text-white rounded-circle shadow">
                                        <i class="fa-solid fa-meteor"></i>
                                        {{--                                        <i class="ni ni-mobile-button"></i>--}}
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

<div class="table-responsive mt-4">
    <!-- Projects table -->
    <table class="table align-items-center table-flush table-striped">
        <thead class="thead-light">
        <tr>
            <th scope="col">{{__('Title')}}</th>
            <th scope="col">{{__('Value')}}</th>
            <th scope="col">{{__('Our Income')}}</th>
            @if(!isset($company) || !is_null($company->service_provider_id))
                <th scope="col">{{__('Provider Income')}}</th>
            @endif
        </tr>
        </thead>
        <tbody>
        @foreach($cdrStatistics as $key => $cdrStatistic)
            @if($key !== "missed_cals")
                <tr class="{{($key == 'total_income') ? 'total_tr' : ''}}">
                    <th>
                        {{$cdrStatistic['name']}}
                    </th>
                    @if(isset($cdrStatistic['p_count']) && (!isset($company) || !is_null($company->service_provider_id)))
                        <td>{{$cdrStatistic['count'] . ' / ' . $cdrStatistic['p_count']}}</td>
                    @else
                        <td>{{$cdrStatistic['count']}}</td>
                    @endif
                    <td>{{$cdrStatistic['fee'] != '-' && $cdrStatistic['fee']['price'] != '-' ? $cdrStatistic['fee']['price'] . ' kr' : '-'}}</td>
                    @if(!isset($company) || (isset($company) && !is_null($company->service_provider_id)))
                        <td>{{$cdrStatistic['fee'] != '-' && $cdrStatistic['fee']['p_price'] != '-' ? $cdrStatistic['fee']['p_price'] . ' kr' : '-'}}</td>
                    @endif
                </tr>
            @endif
        @endforeach

        </tbody>
    </table>
</div>

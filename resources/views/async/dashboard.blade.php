@include('layouts.headers.cards')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-xl-12 mb-5 mb-xl-0">
            <div class="card shadow">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">{{__('User Statistics')}}</h3>
                        </div>
                        @if(auth()->guard('web')->check())
                        <div class="col text-right">
                            <form action="{{route('admin.export_pdf')}}" method="get" id="export_form">
                                <input type="hidden" id="export_start_date" name="start_date" value="">
                                <input type="hidden" id="export_compare_date" name="compare_date" value="">
                                <input type="hidden" id="export_start" name="start" value="">
                                <input type="hidden" id="export_end" name="end" value="">
                                <input type="hidden" id="export_date_range" name="date_range" value="true">
                                <button type="button" class="btn btn-sm btn-primary" id="export_pdf">{{__('Export PDF')}}</button>
                            </form>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="table-responsive">
                    <!-- Projects table -->
                    <table class="table align-items-center table-flush table-striped" id="user_stat_table">
                        <thead class="thead-light">
                        <tr>
                            <th scope="col">{{__('Username')}}</th>
                            <th scope="col">{{__('Calls')}}</th>
                            <th scope="col">{{__('Talk')}}</th>
                            <th scope="col">{{__('Pause')}}</th>
                            <th scope="col">{{__('Bookings')}}</th>
                            <th scope="col">{{__('Login Time')}}</th>
                            <th scope="col">{{__('Chats')}}</th>
                            <th scope="col">{{__('Progress')}}</th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach($userStats as $data)
                            <tr>
                                <th scope="row" >
                                    <a href="{{route('admin.employee_statistics', [$data['servit_user_id']])}}">
                                        {{$data['username'] ?: $data['servit_user_id']}}
                                    </a>
                                </th>
                                <td>{{$data['daily_calls']}}</td>
                                <td>{{get_hour_format($data['talk_time'])}}</td>
                                <td>{{get_hour_format($data['pause_time'])}}</td>
                                <td>{{$data['daily_bookings']}}</td>
                                <td>{{$data['daily_login_time']}}</td>
                                <td>{{$data['daily_chats']}}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="mr-2">{{$data['progress']}}%</span>
                                        <div>
                                            <div class="progress">
                                                <div class="progress-bar bg-gradient-{{$data['progress'] < 80 ? 'danger' : ($data['progress'] >= 100 ? 'green' : 'yellow')}}" role="progressbar"
                                                     aria-valuenow="{{$data['progress']}}" aria-valuemin="0" aria-valuemax="120"
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
            </div>
        </div>
    </div>

    @include('layouts.footers.auth')
</div>

@push('js')
{{--    <script id="chat_script_990088" referrerpolicy="no-referrer-when-downgrade" onload='chatlayer()' async></script>
    <script>
        if (window.innerWidth >= 525) {
            var elem = document.getElementById('chat_script_990088');
            elem.setAttribute('src', 'https://chatbox.prod.europe-west1.gc.chatlayer.ai/sdk/60a4d343982d2bb9b25cb062');
        }
    </script>
    <style>
        .chatlayer-chatbox-wrapper {
            z-index: 9999999999;
        }
        .chatlayer-chatbox-button {
            cursor: pointer;
        }
    </style>
    <script>
        setTimeout(function () {
            if(!localStorage.getItem('visited') || new Date().getTime() - localStorage.getItem('visited') > 3000) {
                $('.chatlayer-chatbox-button').trigger("click");
            }

            window.onunload = function() {
                localStorage.setItem('visited', new Date().getTime());
                return null;
            }
        }, 1000);
    </script>--}}
    <script src="{{ asset('assets/js/pages/dashboard.js') }}"></script>
@endpush

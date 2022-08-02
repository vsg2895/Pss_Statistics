{{--User stats--}}
<div class="table-responsive m-1">
    <table class="table table-striped table-flush">
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
{{--        @if(!isset($monthly))--}}
            @foreach($userStats as $data)
                <tr class="text-center">
                    <th scope="row" class="p-1">
                        <a target="_blank" href="{{route('admin.employee_statistics', [$data['servit_user_id']])}}">
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
                        <div>
                            {{--                        <span style="margin-top:12px" class="progress">--}}
                            {{--                            <div class="progress-bar bg-{{$data['progress'] < 80 ? 'danger' : ($data['progress'] >= 100 ? 'green' : 'yellow')}}"--}}
                            {{--                                 style="width: {{$data['progress']}}%;"></div>--}}
                            {{--                        </span>--}}
                            <span
                                class="{{$data['progress'] < 80 ? 'danger' : ($data['progress'] >= 100 ? 'green' : 'yellow')}}">{{$data['progress']}}%</span>
                        </div>
                    </td>
                </tr>
            @endforeach
{{--        @endif--}}
        </tbody>
    </table>
</div>

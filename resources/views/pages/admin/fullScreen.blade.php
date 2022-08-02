<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Start your development with a Dashboard for Bootstrap 4.">
    <meta name="author" content="VH">
    <title>{{config('app.name')}}</title>
    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700">
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/datatables.net-bs4/css/dataTables.bootstrap4.min.css">
    <!-- Icons -->
    <link rel="stylesheet" href="../assets/vendor/nucleo/css/nucleo.css" type="text/css">
    <link rel="stylesheet" href="../assets/vendor/@fortawesome/fontawesome-free/css/all.min.css" type="text/css">
    <!-- Page plugins -->
    <!-- Argon CSS -->
    <link rel="stylesheet" href="../assets/css/argon.css?v=1.2.0" type="text/css">
    <link type="text/css" href="{{ asset('assets') }}/css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/pages/full-screen.css') }}">
    <script src="{{ asset('js/app.js') }}"></script>
    {{--    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>--}}

</head>

<body>

<script>

{{--    -------}}
//     var pusher = new Pusher('aaaaa', {
//         cluster: 'ap2',
//         wsHost: window.location.hostname,
//         wsPort: 6001,
//         forceTLS: true
//     });
//     var channel = pusher.subscribe('private-websockets-dashboard-statistics');
//     channel.bind('pusher:pong', function (members) {
//         console.log('successfully subscribed! - pinggg', members);
//     });
    //---------------


    // channel.bind('historical-event', function (data) {
    //
    //
    // })
    // var pusher = new Pusher('3eea21e8b04eb016f40b', {
    //     authEndpoint : 'http://127.0.0.1:8000/broadcasting/auth',
    //     cluster: 'ap2'
    // });
    // var channel = pusher.subscribe('private-websockets-dashboard-statistics');
    // channel.bind('pusher:subscribe', function (members) {
    //     console.log('successfully subscribed!');
    // });
    // channel.bind('pusher:pong', function (data) {
    //
    //     console.log(data, ' - socket')
    // })
    // window.Echo.channel('wallboard-channel')
    //     .listen('GetWallboardEvent', (e) => {
    //         console.log(e, 'socket')
    //     });
    // window.Echo.private('private-websockets-dashboard-statistics')
    //     .listen('pusher:pong', (e) => {
    //         console.log(e, 'socket')
    //     });
</script>
{{--@include('snowing')--}}
{{--<audio>--}}
{{--    <source id="bestAgentChangedSound" src="{{asset('audio/beep.mp3')}}" type="audio/mpeg">--}}
{{--</audio>--}}
{{--<audio id="bestAgentChangedSound" src="{{asset('audio/beep.mp3')}}" type="audio/mpeg" >--}}
{{--</audio>--}}
<div id="dashboard_content">
    @include('async.fullScreen')
</div>

<script src="{{ asset('argon') }}/vendor/jquery/dist/jquery.min.js"></script>
<script src="{{ asset('argon') }}/vendor/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('assets') }}/vendor/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="{{ asset('assets') }}/vendor/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="{{ asset('assets/js/pages/full-screen.js') }}"></script>

</body>

</html>

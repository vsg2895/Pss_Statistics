<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title : config('app.name', 'PS Admin') }}</title>
{{--        <title>{{ isset($title) ? $title : config('app.name', 'PS Admin') }}</title>--}}
<!-- Favicon -->
{{--        <link href="{{ asset('argon') }}/img/brand/favicon.png" rel="icon" type="image/png">--}}
<!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
    <!-- Extra details for Live View on GitHub Pages -->

    @stack('css')
    <link rel="stylesheet" href="{{ asset('assets/vendor/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}">
    <!-- Icons -->
    <link href="{{ asset('argon/vendor/nucleo/css/nucleo.css') }}" rel="stylesheet">
    {{--    <link href="{{ asset('argon/vendor/@fortawesome/fontawesome-free/css/all.min.css') }}" rel="stylesheet">--}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" rel="stylesheet">
    <!-- Argon CSS -->
    <link type="text/css" href="{{ asset('argon/css/argon.css?v=1.0.0') }}" rel="stylesheet">
    <link type="text/css" href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>

    @stack('after-styles')
    <script>
        var receiverId = '';
        window.user = {!! Auth::id() !!}

        // console.log(window.user)//todo:check senc baner petq chi toghel
        // Enable pusher logging - don't include this in production
        // Pusher.logToConsole = true;
        window.Echo.channel(`historical-channel`)
            .listen('.historical-event', (data) => {
                if (data.receiverId !== null) {
                    receiverId = data.receiverId;
                }
                console.log(receiverId, window.user)
                if (receiverId === window.user) {
                    if ($('.custom-alert').hasClass('show') && !$('.custom-alert').hasClass('update-realtime-alert')) {
                        $('.custom-alert').fadeOut(2000);
                    }
                    if (data.route !== null) {
                        var route = data.route;
                        $('.update-realtime-alert').attr('data-route', route)
                    }
                    let text = data.text;
                    console.log(text,data,'esaaa')
                    let percent = data.percent;
                    $('.full-content').append("<div class='full-content main'> " +
                        "<div class='alert custom-alert update-realtime-alert alert-success alert-dismissible fade show mt-3 mr-2' role='alert'>" +
                        "<div class='text-check-message-block d-flex flex-column align-items-center'>" +
                        "<div class='text-icon-line'>" +
                        "<span class='alert-icon'><i class='ni ni-like-2'></i></span>" +
                        "<span class='alert-text-realtime'></span>" +
                        "</div>" +
                        "<a href='' style='color: white' class='redirect-job-success'></a>" +
                        "</div>" +
                        "<span style='display: block; background-color: white;height: 0.3rem' class='alert-line-realtime'></span>" +
                        "<button type='button' class='close' data-dismiss='alert' aria-label='Close'>" +
                        "<span aria-hidden='true'></span>" +
                        "</button>" +
                        "</div>" +
                        "<div>")
                    if (percent !== null) {
                        $('.alert-text-realtime').text(text + percent)
                        $('.alert-line-realtime').css('width', percent)
                    } else {
                        $('.alert-text-realtime').text(text)
                        $('.redirect-job-success').attr('href', $('.update-realtime-alert').attr('data-route'))
                        $('.redirect-job-success').text('Check Update Result')
                        $('.alert-line-realtime').remove();
                        setTimeout(function () {
                            $('.update-realtime-alert').fadeOut('slow');
                        }, 5000);

                    }
                }

            });

        window.Echo.channel(`excelImport-channel`)
            .listen('.excel-import-event', (data) => {
                console.log(data, 'importData')
                if ($('.custom-alert').hasClass('show') && !$('.custom-alert').hasClass('update-realtime-alert')) {
                    $('.custom-alert').fadeOut(2000);
                }
                let time = 7000;
                let text = data.message;
                let start = data.start;
                let end = data.end;
                let company = data.company;
                let checkRedirect = data.checkRedirect;
                console.log(checkRedirect, company, start, end)
                if (text !== null) {
                    $('.full-content').append("<div class='full-content main'> " +
                        "<div class='alert custom-alert update-realtime-alert alert-success alert-dismissible fade show mt-3 mr-2' role='alert'>" +
                        "<div class='text-check-message-block d-flex flex-column align-items-center'>" +
                        "<div class='text-icon-line'>" +
                        "<span class='alert-icon'><i class='ni ni-like-2'></i></span>" +
                        "<span class='alert-text-realtime'></span>" +
                        "</div>" +
                        "<a href='' style='color: white' class='redirect-job-success ml-3'></a>" +
                        "</div>" +
                        "<span style='display: block; background-color: white;height: 0.3rem' class='alert-line-realtime'></span>" +
                        "<button type='button' class='close' data-dismiss='alert' aria-label='Close'>" +
                        "<span aria-hidden='true'></span>" +
                        "</button>" +
                        "</div>" +
                        "<div>")
                }

                $('.alert-text-realtime').text(text)
                if (checkRedirect !== null) {
                    $('.redirect-job-success').text('Check Imported Changes')
                }
                if (company !== null) {
                    let redirect = window.location.origin + '/admin/companies/' + company + '?start=' + start + '&end=' + end
                    $('.redirect-job-success').attr('href', redirect)
                    time = 50000;
                }

                $('.alert-line-realtime').remove();
                setTimeout(function () {
                    $('.update-realtime-alert').fadeOut('slow');
                }, time);


            })

        window.Echo.channel(`excelExport-channel`)
            .listen('.excel-export-event', (data) => {
                console.log(data)
                if ($('.custom-alert').hasClass('show') && !$('.custom-alert').hasClass('update-realtime-alert')) {
                    $('.custom-alert').fadeOut(2000);
                }
                let time = 7000;
                let text = data.message;
                let checkRedirect = data.checkRedirect;
                $('.full-content').append("<div class='full-content main'> " +
                    "<div class='alert custom-alert update-realtime-alert alert-success alert-dismissible fade show mt-3 mr-2' role='alert'>" +
                    "<div class='text-check-message-block d-flex flex-column align-items-center'>" +
                    "<div class='text-icon-line'>" +
                    "<span class='alert-icon'><i class='ni ni-like-2'></i></span>" +
                    "<span class='alert-text-realtime'></span>" +
                    "</div>" +
                    "<a href='' style='color: white' class='redirect-job-success'></a>" +
                    "</div>" +
                    "<span style='display: block; background-color: white;height: 0.3rem' class='alert-line-realtime'></span>" +
                    "<button type='button' class='close' data-dismiss='alert' aria-label='Close'>" +
                    "<span aria-hidden='true'></span>" +
                    "</button>" +
                    "</div>" +
                    "<div>")

                $('.alert-text-realtime').text(text)
                if (checkRedirect !== null) {
                    $('.redirect-job-success').attr('href', checkRedirect)
                    $('.redirect-job-success').text('See Exported File')
                    time = 50000;
                }

                $('.alert-line-realtime').remove();
                setTimeout(function () {
                    $('.update-realtime-alert').fadeOut('slow');
                }, time);


            });

    </script>
</head>
<body class="{{ $class ?? '' }}">

@auth()
    @if(Auth::guard('web')->check())
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    @elseif(Auth::guard('employee')->check())
        <form id="logout-form" action="{{ route('employee.logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    @endif
@endauth
<div class="d-flex">

    @auth()
        <div class="sidebar">
            @include('layouts.navbars.sidebar', ['activePage' => isset($activePage) ? $activePage : 'dashboard'])
        </div>
    @endauth
    <div class="full-content @auth() main @else main-guest @endauth">
        @include('layouts.navbars.navbar')
        @include('alerts.alerts')
        @yield('content')
    </div>
</div>

@guest()
    @include('layouts.footers.guest')
@endguest

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/js/all.min.js"></script>
<script src="{{ asset('argon/vendor/jquery/dist/jquery.min.js') }}"></script>
<script src="{{ asset('argon/vendor/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/vendor/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/vendor/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/vendor/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.5.3/jspdf.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.27.1/axios.min.js"></script>


<script src="{{ asset('assets/js/script.js') }}"></script>
@stack('js')


<!-- Argon JS -->
<script src="{{ asset('argon/js/argon.js?v=1.0.0') }}"></script>
</body>
</html>

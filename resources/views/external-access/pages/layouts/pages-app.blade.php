<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ isset($title) ? $title : config('app.name', 'PS Admin') }}</title>
    <!-- Favicon -->
{{--        <link href="{{ asset('argon') }}/img/brand/favicon.png" rel="icon" type="image/png">--}}
<!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
    <!-- Extra details for Live View on GitHub Pages -->

    @stack('css')
    <link rel="stylesheet" href="{{ asset('assets/vendor/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}">
    <!-- Argon CSS -->
    <link type="text/css" href="{{ asset('argon/css/argon.css?v=1.0.0') }}" rel="stylesheet">
    <link type="text/css" href="{{ asset('assets/css/pages/pages.css') }}" rel="stylesheet">
</head>
<body >
    @include('external-access.pages.layouts.pages-header')

    @yield('content')

    @include('external-access.pages.layouts.pages-footer')

<script src="{{ asset('argon/vendor/jquery/dist/jquery.min.js') }}"></script>
<script src="{{ asset('argon/vendor/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
@stack('js')


<!-- Argon JS -->
<script src="{{ asset('argon/js/argon.js?v=1.0.0') }}"></script>
</body>
</html>

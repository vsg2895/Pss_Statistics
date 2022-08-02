@extends('layouts.app', [
    "activePage" => "userStatistics",
    "headerName" => "Statistics: $servitUser->servit_username"
])

@section('content')
    @include('layouts.headers.cards-employeeStatistics')
    @include('layouts.footers.pages')
@endsection

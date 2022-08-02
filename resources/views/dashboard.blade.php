@extends('layouts.app', [
    "activePage" => "dashboard",
    "headerName" => "Dashboard",
])

@push('css')
@endpush
@section('content')
    <div id="dashboard_content">
        @include('async.dashboard')
    </div>
@endsection

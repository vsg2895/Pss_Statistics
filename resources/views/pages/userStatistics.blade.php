@extends('layouts.app', [
    "activePage" => "userStatistics",
    'headerName' => 'Statistics'
])

@section('content')
@include('layouts.headers.cards-employeeStatistics')
<div class="container-fluid mt-4">

</div>
@include('layouts.footers.pages')
@endsection

@push('js')

@endpush

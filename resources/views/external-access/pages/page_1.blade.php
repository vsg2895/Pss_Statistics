@extends('external-access.pages.layouts.pages-app')

@section('content')
    <div class="px-5 page-content">
        <div class="d-flex justify-content-center align-items-center h-100 text-center">
            <h1 class="big-h1">
                {{$variable}}
            </h1>
        </div>
    </div>

@endsection

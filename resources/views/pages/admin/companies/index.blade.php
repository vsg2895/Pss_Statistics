@extends('layouts.app', [
    "activePage" => "companies",
    "headerName" => "Companies",
])

@push('css')
    <link rel="stylesheet" href="{{asset('assets/vendor/select2/dist/css/select2.min.css')}}">
@endpush
{{--@dd('this page')--}}
@section('content')
    <div class="container-fluid main-container">
        <form class="mt-4" action="{{ route('admin.companies.index') }}" method="get" autocomplete="off">
            <x-datepicker.date-range  button-title="{{__('Submit Filters')}}"/>
            <div class="row">
                <x-datepicker.default-filters-form/>
                <div class="col-12 col-sm-12 col-md-5 mt-3 mt-sm-0">
                    <div class="row">
                        <div class="col-3 d-flex align-items-center">
                            {{__('Select Tags') . ':'}}
                        </div>
                        <div class="col-9">
                            <select class="tags-select w-100" name="tags[]" multiple="multiple">
                                @foreach($tags as $tag)
                                    <option value="{{$tag->id}}"
                                    @if(request()->tags){{in_array($tag->id, request()->tags) ? 'selected' : ''}}@endif
                                    >{{$tag->name}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <div class="row mt-2">
            <div class="col-12 mb-5 mb-xl-0">
                <div class="card shadow">
                    <div class="card-header border-0">
                        <div class="row align-items-center">
                            <div class="col">
                                <h3 class="mb-0">{{__('Companies')}}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <!-- Projects table -->
                        <table class="table align-items-center table-flush table-striped border" id="companies_table">
                            <thead class="thead-light">
                            <tr>
                                <th>{{__('Name')}}</th>
                                <th>{{__('Address')}}</th>
                                <th>{{__('Answered Calls')}}</th>
                                <th>{{__('Missed Calls')}}</th>
                                <th>{{__('Bookings')}}</th>
                                <th>{{__('Time Above 60')}}</th>
                                <th>{{__('Earned')}}</th>
                                <th>{{__('Added At')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php
                                $start = request()->start
                                         ?  request()->start
                                         :\Illuminate\Support\Carbon::now()->startOfMonth()->format('Y-m-d');//month start
                                $end = request()->end
                                         ?  request()->end
                                         : \Illuminate\Support\Carbon::now()->format('Y-m-d');//today
                            @endphp
                            @foreach($companies as $company)
                                <tr>
                                    <td>
                                        <a href="{{route('admin.companies.show', ['company' => $company->id,'start' => $start,'end' => $end])}}">
                                            {{$company->name}}
                                        </a>
                                    </td>
                                    <td>{{$company->city . ' | ' . substr(unicode_decode($company->street), 1, -1)}}</td>
                                    <td>{{$company->answered_calls}}</td>
                                    <td>{{$company->missed_calls}}</td>
                                    <td>{{$company->bookings_count}}</td>
                                    <td>{{get_hour_format($company->time_above)}}</td>
                                    <td>{{$company->time_above * $moneyForSeconds}}</td>
                                    <td>{{$company->added_at}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('layouts.footers.pages')
@endsection

@push('js')
    <script src="{{asset('assets/vendor/select2/dist/js/select2.full.min.js')}}"></script>
    <script src="{{asset('assets/js/pages/companies.js')}}"></script>
@endpush

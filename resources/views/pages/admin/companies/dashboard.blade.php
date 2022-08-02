@extends('layouts.app', [
    "activePage" => "company_dashboard",
    "headerName" => "Companies Dashboard",
])

@push('css')
    <link rel="stylesheet" href="{{asset('assets/vendor/select2/dist/css/select2.min.css')}}">
    <style>
        div.dataTables_wrapper div.dataTables_filter label {
            padding-right: 0.5rem;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid main-container export-all-company">
        <ul class="nav nav-tabs companies-providers-general-tabs mt-1" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="statistics-companies-tab" data-toggle="tab"
                   href="#statistics-all-companies" role="tab"
                   aria-controls="statistics-all-companies" aria-selected="false">{{__('Companies')}}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="date-companies-update-tab" data-toggle="tab" href="#date-update-all-companies"
                   role="tab"
                   aria-controls="date-update-all-companies" aria-selected="false">{{__('Update Billing')}}</a>
            </li>
            {{--            <li class="nav-item">--}}
            {{--                <a class="nav-link" id="companies-compare-date-range-tab" data-toggle="tab"--}}
            {{--                   href="#companies-compare-date-range"--}}
            {{--                   role="tab"--}}
            {{--                   aria-controls="companies-compare-date-range" aria-selected="false">{{__('Compare')}}</a>--}}
            {{--            </li>--}}
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade mt-4 show active" id="statistics-all-companies" role="tabpanel"
                 aria-labelledby="range-tab">

                <form class="mt-4" action="{{ route('admin.companies.dashboard') }}" method="get" autocomplete="off">
                    <x-datepicker.date-range button-title="{{__('Submit Filters')}}"/>

                    <div class="row default-filters">
                        <x-datepicker.default-filters-form/>
                        <div
                            class="col-xl-5 col-lg-8 col-md-10 col-sm-12 mt-3 mt-lg-3 ml-lg-0 ml-xl-0 mt-md-3 ml-md-3 mt-sm-0">
                            <div class="row mobile-w-100 align-items-baseline">
                                <div
                                    class="col-xl-2 col-lg-2 col-md-2 col-sm-12 ml-xl-0 ml-lg-0 ml-md-0 ml-5 d-flex align-items-center p-0 mobile-p-0">
                                    {{__('Select Tags')}}
                                </div>
                                <div
                                    class="col-xl-7 col-lg-7 col-md-7 col-sm-12 ml-xl-0 ml-lg-0 ml-md-0 ml-4 select-tags-block-mobile">
                                    <select class="tags-select w-100" name="tags[]" multiple="multiple">
                                        @foreach($tags as $tag)
                                            <option value="{{$tag->id}}"
                                            @if(request()->tags){{in_array($tag->id, request()->tags) ? 'selected' : ''}}@endif
                                            >{{$tag->name}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div
                                    class="col-xl-3 col-lg-3 col-md-3 col-sm-12 mt-xl-0 mt-lg-0 mt-md-0 mt-2 d-flex justify-content-end export-block">
                                    <button type="button" class="btn btn-sm btn-primary"
                                            id="export_companies_pdf">{{__('Export PDF')}}</button>
                                    <i class="loading-icon-company-pdf fa-lg fas fa-spinner d-none fa-spin hide"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3 provider-filters justify-content-start align-items-baseline">
                        <div
                            class="col-12 col-xl-5 col-lg-5 col-md-5 provider-filter-options d-flex justify-content-center">
                            <div
                                class="col-6 col-lg-7 col-md-10  ml-0 ml-xl-5 ml-lg-5 ml-md-0 d-flex align-items-baseline justify-content-center">
                                <input type="checkbox" name="provider" id="in_provider" class="provider_filter"
                                       value="{{true}}"
                                       @if(request()->has('provider') && !is_null(request()->provider)) checked @endif>
                                <label class="mb-0 ml-2 font-weight-bold"
                                       for="in_provider">{{ __('With Provider') }}</label>
                            </div>

                            <div
                                class="col-6 col-lg-7 col-md-10 mr-xl-3 mr-lg-3 mr-0 mr-md-0 d-flex align-items-baseline justify-content-start">
                                <input type="checkbox" name="provider" id="no_provider" class="provider_filter"
                                       value="{{false}}"
                                       @if(request()->has('provider') && is_null(request()->provider)) checked @endif>
                                <label class="mb-0 ml-2 font-weight-bold"
                                       for="no_provider">{{ __('Without Provider') }}</label>
                            </div>
                        </div>

                    </div>
                </form>

                <div id="statistics-all-companies-pdf">

                    @include('layouts.headers.cards-company-dashboard')

                    <div class="row mt-4">
                        <div class="col-xl-12 mb-5 mb-xl-0">
                            <div class="card shadow">
                                <div class="card-header border-0">
                                    <div class="row align-items-center">
                                        <div class="col d-flex align-items-baseline">
                                            <h3 class="mb-0">{{__('Statistics')}}
                                                <h3 class="dashboard_companies_count">
                                                    &nbsp; {{ "- " . count($companies)}}
                                                </h3>
                                            </h3>
                                        </div>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <!-- Projects table -->
                                    <table class="table align-items-center table-flush table-striped"
                                           id="companies_table">
                                        <thead class="thead-light">
                                        <tr>
                                            <th>{{__('Name')}}</th>
                                            <th>{{__('Id')}}</th>
                                            <th>{{__('Address')}}</th>
                                            <th>{{__('Answered Calls')}}</th>
                                            <th>{{__('Bookings')}}</th>
                                            <th>{{__('Chats')}}</th>
                                            <th>{{__('Time Above')}}</th>
                                        </tr>
                                        </thead>
                                        <tbody>

                                        @foreach($companies as $company)
                                            <tr>
                                                <td>
                                                    <a href="{{route('admin.companies.show', ['company' => $company->c_id,'start' => $start, 'end' => $end])}}">
                                                        {{$company->name}}
                                                    </a>
                                                </td>
                                                <td class="available-company-id">{{$company->company_id}}</td>
                                                <td>{{$company->city . ' | ' . substr(unicode_decode($company->street), 1, -1)}}</td>
                                                <td>{{$company->answered_calls}}</td>
                                                <td>{{$company->bookings}}</td>
                                                <td>{{$company->chats_count}}</td>
                                                <td>{{get_hour_format($company->time_above)}}</td>
                                            </tr>
                                        @endforeach

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade mt-4" id="date-update-all-companies" role="tabpanel"
                 aria-labelledby="range-tab">
                <x-datepicker.date-range-with-data class="billing-datapicker"
                                                   form="update-fees-form"/>
                {{--                admin.company.updateFeesByDateAll--}}
                <form action=""
                      id="update-fees-form-all" method="post">
                    {{ method_field('PUT') }}
                    @csrf
                    <div class="row">
                        <div class="col-sm-8 col-12 pt-3">
                            <div class="card-header border-0">
                                <div class="row align-items-center">
                                    <div class="col d-flex align-items-center ">
                                        <h3 class="mb-0">{{__('Updates Fees')}}</h3>
                                        <div>
                                            <i class="ml-2 mt-1 ni ni-air-baloon cursor-pointer"
                                               data-toggle="tooltip" data-placement="bottom" data-html="true"
                                               title="Update Fees, that PS charge from company (OUR INCOME). Don't depend on having provider."></i>
                                        </div>
                                    </div>
                                    <div class="col text-right">
                                        <button type="submit"
                                                class="btn btn-sm btn-success update-all">{{__('Update')}}</button>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table align-items-center table-flush table-striped border">
                                    <thead class="thead-light">
                                    <tr>
                                        <th>{{__('Name')}}</th>
                                        <th>{{__('Value')}}</th>
                                        {{--                                                <th style="padding-left: 0px!important;">{{__('Db Value')}}</th>--}}
                                        {{--                                        <th>{{__('Update')}}</th>--}}
                                    </tr>
                                    </thead>
                                    <tbody class="db_values_td">
                                    @include('components.db_fees')
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

        </div>

    </div>
    @include('layouts.footers.pages')

@endsection

@push('js')
    @routes
    <script src="{{asset('assets/vendor/select2/dist/js/select2.full.min.js')}}"></script>
    <script src="{{asset('assets/js/pages/companiesAll.js')}}"></script>
@endpush


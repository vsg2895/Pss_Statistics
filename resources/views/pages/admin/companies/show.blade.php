@extends('layouts.app', [
    "activePage" => "companies",
    "headerName" => $company->name,
])
@push('after-styles')
    <link rel="stylesheet" href="{{asset('assets/vendor/select2/dist/css/select2.min.css')}}">
    <style>
        .select2-container--default {
            width: 100% !important;
        }
    </style>
@endpush


@section('content')
    <div class="header pt-4 main-container">
        <div class="container-fluid">
            <div class="header-body">
                <ul class="nav nav-tabs companies-providers-general-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="statistics-tab" data-toggle="tab" href="#statistics" role="tab"
                           aria-controls="statistics" aria-selected="false">{{__('Billing')}}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="date-update-tab" data-toggle="tab" href="#date-update" role="tab"
                           aria-controls="date-update" aria-selected="false">{{__('Update Billing')}}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="configs-tab" data-toggle="tab" href="#configs" role="tab"
                           aria-controls="configs" aria-selected="true">{{__('Configs')}}</a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane fade mt-4 show active" id="statistics" role="tabpanel"
                         aria-labelledby="range-tab">
                        <form action="{{route('admin.companies.show', ['company' => $company->id])}}" method="get"
                              autocomplete="off">
                            <input type="hidden" name="date_range" value="true">
                            <x-datepicker.date-range class="billing-datapicker"/>
                        </form>
                        <div class="row default-buttons justify-content-between align-items-baseline">
                            <x-datepicker.default-filter-links routeName="admin.companies.show" routeParam="company"
                                                               :object="$company"/>
                            <div
                                class="col-xl-5 col-lg-5 col-md-5 col-8 d-flex mt-2 pr-xl-1 pr-lg-1 pr-md-1 pr-2 justify-content-end">
                                <button type="button" class="btn btn-sm btn-primary mr-3"
                                        id="export_current_companies_pdf">{{__('Export PDF')}}</button>
                                <button type="button" class="btn btn-sm btn-primary mr-3"
                                        id="export_current_companies_pdf_by_company">{{__('Export PDF Company')}}</button>
                                <form
                                    action="{{ route('admin.export.excel',['company' => $company, 'start' => $todayStart,'end' => $todayEnd]) }}"
                                    method="post"
                                    id="export_excel_billingfees">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-primary mr-2" id="export_excel">
                                        {{__('Export Excel')}}</button>
                                </form>
                                <button type="button" class="btn btn-sm btn-primary mr-2" id="import_excel"
                                        data-toggle="modal"
                                        data-target="#companies_import_upload">
                                    {{__('Import Excel')}}</button>
                            </div>

                        </div>

                        <div id="current-company-export-block">
                            @include('layouts.headers.cards-company-dashboard')
                            {{--       Table Component    --}}
                            <x-billing.billing-data-table :company="$company" :fixed="$fixed" :cdrStatistics="$cdrStatistics"/>
                        </div>

                        <div id="current-company-export-block-without">
                            @include('Reports.Pdf.in-company')
                        </div>

                        <div id="include-company-more">
                            @include('pages.admin.companies.moreInfo')
                        </div>

                    </div>
                    {{-- Date Update Content--}}
                    <div class="tab-pane fade mt-4" id="date-update" role="tabpanel"
                         aria-labelledby="range-tab">
                        <x-datepicker.date-range-with-data class="billing-datapicker" :object="$company"
                                                           form="update-fees-form"/>
                        <form action="{{route('admin.company.updateFeesByDate', ['company' => $company->id])}}"
                              id="update-fees-form" method="post">
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
                                                       title="@if($company->service_provider_id)
                                                           Update Fees, that provider charge from company (PROVIDER INCOME).
                                                           @else
                                                           Update Fees, that PS charge from company (OUR INCOME).
                                                           @endif"></i>
                                                </div>
                                            </div>
                                            <div class="col text-right">
                                                <button type="submit"
                                                        class="btn btn-sm btn-success">{{__('Update')}}</button>
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
                                                <th>{{__('Update')}}</th>
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
                    {{-- End Date Update Content --}}
                    <div class="tab-pane fade mt-4" id="configs" role="tabpanel" aria-labelledby="range-tab">


                        <form action="{{url('admin/companies/' . $company->id)}}" method="post">
                            @csrf
                            <input type="hidden" name="_method" value="patch">
                            <div class="row d-flex">
                                <div class="col-12 col-sm-5">
                                    <div class="card shadow">
                                        <div class="card-header border-0">
                                            <div class="row align-items-center">
                                                <div class="col">
                                                    <h3 class="mb-0">{{__('Company Tags')}}</h3>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="table-responsive">
                                            <!-- Projects table -->
                                            <table class="table align-items-center table-flush table-striped border"
                                                   id="">
                                                <thead class="thead-light">
                                                <tr>
                                                    <th>{{__('Name')}}</th>
                                                    <th></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($company->tags as $tag)
                                                    <tr>
                                                        <td>{{$tag->name}}</td>
                                                        <td class="d-flex justify-content-end">
                                                            <input type="hidden" name="tags[]" value="{{$tag->id}}">
                                                            <span class="cursor-pointer delete-data">&times;</span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-5 pt-3 pt-sm-0">
                                    <div class="card shadow">
                                        <div class="card-header border-0">
                                            <div class="row align-items-center">
                                                <div class="col">
                                                    <h3 class="mb-0">{{__('Service Provider')}}</h3>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table align-items-center table-flush border" id="">
                                                <thead class="thead-light">
                                                <tr>
                                                    <th>{{__('Name')}}</th>
                                                    <th></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @if($company->serviceProvider)
                                                    <tr>
                                                        <td>
                                                            <a href="{{route('admin.service-providers.show', ['service_provider' => $company->serviceProvider->id])}}">
                                                                {{$company->serviceProvider->name}}
                                                            </a>
                                                        </td>
                                                        <td class="d-flex justify-content-end">
                                                            <input type="hidden" name="service_provider_id"
                                                                   value="{{$company->serviceProvider->id}}">
                                                            <span class="cursor-pointer delete-data provider-delete">&times;</span>
                                                        </td>
                                                    </tr>
                                                @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-2 pt-3 pt-sm-0">
                                    <button type="button" data-id="{{$company->id}}"
                                            class="btn btn-primary companies-edit"
                                            data-toggle="modal" data-target="#companies_edit">
                                        {{__('Edit')}}
                                    </button>
                                </div>
                            </div>
                        </form>
                        <div class="row">
                            <x-announcement-form routeName="admin.company.announcement.store"
                                                 routeParam="company"
                                                 :text="$text" :object="$company"/>
                            <div class="col-3 d-flex align-items-center justify-content-start">
                                <form action="{{ route('admin.update.compare.excluding',['company' => $company]) }}"
                                      method="post">
                                    @csrf
                                    <input type="checkbox" name="exclude_compare" @if($company->exclude_compare) checked
                                           @endif  id="exclude_compare">
                                    <label class="mb-0 ml-2 font-weight-bold"
                                           for="exclude_compare">{{ __('Exclude from compare') }}</label>
                                    <button type="submit"
                                            class="mt-1 ml-2  btn btn-sm btn-success">{{ __('Save') }}</button>

                                </form>

                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('layouts.footers.pages')
    @include('modals.companies.companies-edit')
    @include('modals.excel.import-companies')
@endsection

@push('js')
    <script src="{{asset('assets/vendor/select2/dist/js/select2.full.min.js')}}"></script>
    @routes
    <script src="{{asset('assets/js/pages/companies.js')}}"></script>
@endpush

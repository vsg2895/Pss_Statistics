@extends('layouts.app', [
    "activePage" => "providers",
    "headerName" => $provider->name,
])

@push('css')
    <link rel="stylesheet" href="{{asset('assets/css/pages/service-provider.css')}}">
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
                    <li class="nav-item">
                        <a class="nav-link" id="files-tab" data-toggle="tab" href="#files" role="tab"
                           aria-controls="files" aria-selected="true">{{__('Documents')}}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="medias-tab" data-toggle="tab" href="#medias" role="tab"
                           aria-controls="medias" aria-selected="true">{{__('Media')}}</a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane fade mt-4 show active" id="statistics" role="tabpanel"
                         aria-labelledby="range-tab">
                        <form action="{{route('admin.service-providers.show', ['service_provider' => $provider->id])}}"
                              method="get"
                              autocomplete="off">
                            <input type="hidden" name="date_range" value="true">
                            <x-datepicker.date-range class="billing-datapicker"/>
                        </form>
                        <div class="row default-buttons">
                            <x-datepicker.default-filter-links routeName="admin.service-providers.show"
                                                               routeParam="service_provider" :object="$provider"/>
                        </div>
                        {{--                               Table Component--}}
                        <x-billing.billing-data-table :cdrStatistics="$cdrStatistics" :fixed="$fixed"/>

                    </div>

                    {{-- Date Update Content--}}
                    <div class="tab-pane fade mt-4" id="date-update" role="tabpanel"
                         aria-labelledby="range-tab">
                        <x-datepicker.date-range-with-data class="billing-datapicker" :object="$provider"
                                                           form="update-fees-form"/>
                        <form
                            action="{{route('admin.provider.updateFeesByDate', ['provider' => $provider->id])}}"
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
                                                       title="Update Fees, that PS charge from provider.
                                                        (OUR INCOME)"></i>
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
                        <div class="container-fluid main-container mt-4">
                            @csrf
                            <input type="hidden" name="_method" value="patch">
                            <div class="row d-flex">
                                <div class="col-12 col-sm-5">
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
                                            <table class="table align-items-center table-flush table-striped border"
                                                   id="">
                                                <thead class="thead-light">
                                                <tr>
                                                    <th>{{__('Name')}}</th>
                                                    <th></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($provider->companies as $company)
                                                    <tr>
                                                        <td>
                                                            <a href="{{route('admin.companies.show', ['company' => $company->id, 'start' => $currStart, 'end' => $currEnd])}}">{{$company->name}}</a>
                                                        </td>
                                                        <td class="d-flex justify-content-end">
                                                            <form
                                                                action="{{route('admin.companies.update', [$company->id])}}"
                                                                method="post">
                                                                @csrf
                                                                <input type="hidden" name="_method" value="patch">
                                                                <input type="hidden" class="update-from-provider"
                                                                       name="service_provider_id"
                                                                       value="{{$provider->id}}">
                                                                <span class="cursor-pointer delete-data">&times;</span>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-5 pt-3 pt-sm-0">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="card-header border-0">
                                                <div class="row align-items-center">
                                                    <div class="col">
                                                        <h3 class="mb-0">{{__('Provider Users')}}</h3>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="table-responsive">
                                                <table class="table align-items-center table-flush table-striped border"
                                                       id="">
                                                    <thead class="thead-light">
                                                    <tr>
                                                        <th>{{__('Name')}}</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($provider->serviceProviderUsers as $providerUser)
                                                        <tr>
                                                            <td>{{$providerUser->name}}</td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <x-announcement-form routeName="admin.provider.announcement.store"
                                                         routeParam="provider"
                                                         :text="$text" :object="$provider"/>
                                </div>
                                <div class="col-12 col-sm-2 pt-3 pt-sm-0">
                                    <button type="button" class="btn btn-primary edit-provider"
                                            data-id="{{$provider->id}}"
                                            data-toggle="modal" data-target="#provider_update">{{__('Edit')}}</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade mt-4" id="files" role="tabpanel" aria-labelledby="range-tab">
                        <div class="row mt-4">
                            <div class="col-xs-12 col-md-4 mb-5 mb-xl-0">
                                <div class="card shadow">
                                    <div class="card-header border-0">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                <h3 class="mb-0">{{__('Files')}}</h3>
                                            </div>
                                            <div class="col text-right">
                                                <button type="button" class="btn btn-sm btn-success" id=""
                                                        data-toggle="modal"
                                                        data-target="#provider_file_upload">{{__('Add')}}</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <!-- Projects table -->
                                        <table class="table align-items-center table-flush table-striped border">
                                            <thead class="thead-light">
                                            <tr>
                                                <th>{{__('Name')}}</th>
                                                <th>{{__('Actions')}}</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($attachments as $attachment)
                                                <tr>
                                                    <td>
                                                        <form action="{{route('admin.reports.download')}}" method="get">
                                                            <input type="hidden" name="path"
                                                                   value="{{$attachment->path}}">
                                                            <a href="#" class="submit_download">
                                                                {{$attachment->name}}
                                                            </a>
                                                        </form>
                                                    </td>
                                                    <td>
                                                        <button type="button"
                                                                class="btn btn-sm btn-danger ml-2 delete-provider-file"
                                                                data-id="{{$attachment->id}}" data-toggle="modal"
                                                                data-target="#provider_file_delete">{{__('Delete')}}</button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{--                Media Part    --}}
                    <div class="tab-pane fade mt-4" id="medias" role="tabpanel"
                         aria-labelledby="range-tab">
                        <div class="row ml-2">
                            <button type="button" class="btn btn-md btn-success p-2" id=""
                                    data-toggle="modal"
                                    data-target="#provider_media_upload">{{__('Add')}}</button>
                        </div>
                        <div class="row mt-2 ml-2 flex-wrap flex-column">
                            @if(count($medias))
                                <h3 class="mb-0">{{__('Media')}}</h3>
                            @endif
                            <div class="row mt-2">
                                @foreach($medias as $attachment)
                                    <div class="col-3 embed-block ml-2">
                                        <div class="embed-responsive embed-responsive-1by1 embed-video">
                                            <iframe class="embed-responsive-item" allowfullscreen
                                                    src="{{ $attachment->path }}">

                                            </iframe>
                                        </div>
                                        <i class="fa-solid fa-circle-minus delete-provider-media"
                                           data-id="{{$attachment->id}}" data-toggle="modal"
                                           data-target="#provider_media_delete">

                                        </i>
                                    </div>
                                @endforeach
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    @include('layouts.footers.pages')
    @include('modals.providers.update')
    @include('modals.providers.file-upload')
    @include('modals.providers.media-upload')
    @include('modals.providers.file-delete')
    @include('modals.providers.media-delete')
@endsection

@push('js')
    <script src="{{asset('assets/js/pages/providers.js')}}"></script>
@endpush

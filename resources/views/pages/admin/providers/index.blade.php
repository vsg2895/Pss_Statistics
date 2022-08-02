@extends('layouts.app', [
    "activePage" => "providers",
    "headerName" => "Providers",
])
@push('css')
    <link rel="stylesheet" href="{{asset('assets/css/pages/service-provider.css')}}">
@endpush
@section('content')
    <div class="container-fluid main-container">

        <ul class="nav nav-tabs companies-providers-general-tabs mt-1" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="providers-list-tab" data-toggle="tab"
                   href="#providers-list" role="tab"
                   aria-controls="providers-list" aria-selected="false">{{__('List')}}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="providers-files-tab" data-toggle="tab" href="#providers-files"
                   role="tab"
                   aria-controls="providers-files" aria-selected="false">{{__('Documents')}}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="providers-medias-tab" data-toggle="tab" href="#providers-medias"
                   role="tab"
                   aria-controls="providers-medias" aria-selected="false">{{__('Media')}}</a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade mt-4 show active" id="providers-list" role="tabpanel"
                 aria-labelledby="range-tab">
                <div class="row mt-4">
                    <div class="col-xl-8 col-md-12 col-lg-8 col-12 mb-5 mb-xl-0">
                        <div class="card shadow">
                            <div class="card-header border-0">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h3 class="mb-0">{{__('Service Providers')}}</h3>
                                    </div>
                                    <div class="col text-right">
                                        <button type="button" class="btn btn-sm btn-success" id="" data-toggle="modal"
                                                data-target="#provider_store">{{__('Add')}}</button>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <!-- Projects table -->
                                <table class="table align-items-center table-flush table-striped border" id="">
                                    <thead class="thead-light">
                                    <tr>
                                        <th>{{__('Name')}}</th>
                                        <th>{{__('Email')}}</th>
                                        <th>{{__('Actions')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($providers as $provider)
                                        <tr>
                                            <td>
                                                <a href="{{route('admin.service-providers.show', ['service_provider' => $provider->id,'start' => $start, 'end' => $end])}}">
                                                    {{$provider->name}}
                                                </a>
                                            </td>
                                            <td>{{$provider->email}}</td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-primary edit-provider"
                                                        data-id="{{$provider->id}}" data-toggle="modal"
                                                        data-target="#provider_update">{{__('Edit')}}</button>
                                                <button type="button" class="btn btn-sm btn-danger ml-2 delete-provider"
                                                        data-id="{{$provider->id}}" data-toggle="modal"
                                                        data-target="#provider_delete">{{__('Delete')}}</button>
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
            {{--            Documents Part--}}
            <div class="tab-pane fade mt-4" id="providers-files" role="tabpanel"
                 aria-labelledby="range-tab">
                <div class="row mt-4">
                    <div class="col-xl-8 col-md-12 col-lg-8 col-12 mb-5 mb-xl-0">
                        <div class="card shadow">
                            <div class="card-header border-0">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h3 class="mb-0">{{__('Files For All Providers')}}</h3>
                                    </div>
                                    <div class="col text-right">
                                        <button type="button" class="btn btn-sm btn-success" id="" data-toggle="modal"
                                                data-target="#provider_all_files">{{__('Add')}}</button>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <!-- Projects table -->
                                <table class="table align-items-center table-flush table-striped border" id="">
                                    <thead class="thead-light">
                                    <tr>
                                        <th>{{__('Name')}}</th>
                                        <th>{{__('Action')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($providersFiles as $attachment)
                                        <tr>
                                            <td>
                                                <form action="{{route('admin.reports.download')}}" method="get">
                                                    <input type="hidden" name="path" value="{{$attachment->path}}">
                                                    <a href="#" class="submit_download">
                                                        {{$attachment->name}}
                                                    </a>
                                                </form>
                                            </td>
                                            <td>
                                                <button type="button"
                                                        class="btn btn-sm btn-danger ml-2 delete-provider-file-all"
                                                        {{--                                                        data-ids="{{ json_encode($providersIds[$attachment->name]->pluck('id')->toArray()) }} "--}}
                                                        data-ids="{{ json_encode($providersIds[$attachment->name]->pluck('id')->toArray()) }}"
                                                        data-toggle="modal"
                                                        data-target="#provider_file_all_delete">{{__('Delete')}}</button>
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
            {{--            Media Part--}}
            <div class="tab-pane fade mt-4" id="providers-medias" role="tabpanel"
                 aria-labelledby="range-tab">
                <div class="row ml-2">
                    <button type="button" class="btn btn-md btn-success p-2" id=""
                            data-toggle="modal"
                            data-target="#provider_all_medias">{{__('Add')}}</button>
                </div>
                <div class="row mt-2 ml-2 flex-wrap flex-column">
                    @if(count($providersMedias))
                        <h3 class="mb-0">{{__('Media For All Providers')}}</h3>
                    @endif
                    <div class="row mt-2">
                        @foreach($providersMedias as $attachment)
                            <div class="col-xl-3 col-lg-3 col-5 embed-block ml-2">
                                <div class="embed-responsive embed-responsive-1by1 embed-video">
                                    <iframe class="embed-responsive-item" allowfullscreen
                                            src="{{ $attachment->path }}">
                                    </iframe>
                                </div>
                                <i class="fa-solid fa-circle-minus delete-provider-media-all"
                                   data-ids="{{ json_encode($providersIdsMedia[$attachment->name]->pluck('id')->toArray()) }}"
                                   data-toggle="modal"
                                   data-target="#provider_media_all_delete">

                                </i>
                            </div>
                        @endforeach
                    </div>

                </div>

            </div>

        </div>

    </div>
    @include('layouts.footers.pages')
    @include('modals.providers.create')
    @include('modals.providers.update')
    @include('modals.providers.delete')
    @include('modals.providers.file-for-all')
    @include('modals.providers.media-for-all')
    @include('modals.providers.file-delete-all')
    @include('modals.providers.media-delete-all')
@endsection

@push('js')
    <script src="{{asset('assets/js/pages/providers.js')}}"></script>
@endpush

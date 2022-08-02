@extends('layouts.app', [
    "activePage" => "users",
    "headerName" => "Users",
])

@push('css')
    <link rel="stylesheet" href="{{asset('assets/vendor/select2/dist/css/select2.min.css')}}">
@endpush
@section('content')

    <div class="container-fluid main-container">
        <ul class="nav nav-tabs pt-4" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="admin-users-tab" data-toggle="tab" href="#admin-users" role="tab"
                   aria-controls="admin-users" aria-selected="false">{{__('Admin Users')}}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link border-right border-left" id="agents-tab" data-toggle="tab" href="#agents" role="tab"
                   aria-controls="agents" aria-selected="true">{{__('Agents')}}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="provider-users-tab" data-toggle="tab" href="#service-provider-users" role="tab"
                   aria-controls="service-provider-users" aria-selected="true">{{__('Service Provider Users')}}</a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade mt-4 show active" id="admin-users" role="tabpanel"
                 aria-labelledby="admin-users-tab">
                <div class="row mt-4">
                    <div class="col-xs-12 col-lg-12 col-md-12 col-12 mb-5 mb-xl-0">
                        <div class="card shadow">
                            <div class="card-header border-0">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h3 class="mb-0">{{__('Users')}}</h3>
                                    </div>
                                    <div class="col text-right">
                                        <button type="button" class="btn btn-sm btn-success" id="" data-toggle="modal"
                                                data-target="#user_store">{{__('Add')}}</button>
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
                                        <th>{{__('Roles & Permissions')}}</th>
                                        <th>{{__('Actions')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($users as $user)
                                        <tr>
                                            <td>{{$user->name}}</td>
                                            <td>{{$user->email}}</td>
                                            <td>
                                                @if(!empty($user->getRoleNames()))
                                                    @foreach($user->getRoleNames() as $v)
                                                        <label data-toggle="tooltip" data-placement="bottom"
                                                               data-html="true"
                                                               title="{{__('Role')}}"
                                                               class="badge badge-success cursor-pointer color-white">{{ $v }}
                                                        </label>
                                                    @endforeach
                                                @endif
                                                @if(!is_null($user->permissions))
                                                    @foreach($user->permissions as $p)
                                                        <label data-toggle="tooltip" data-placement="bottom"
                                                               data-html="true"
                                                               title="{{__('Permission')}}"
                                                               class="badge badge-default cursor-pointer color-white">{{ $p->name }}
                                                        </label>
                                                    @endforeach
                                                @endif
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-primary edit-user"
                                                        data-id="{{$user->id}}" data-toggle="modal"
                                                        data-target="#user_update">{{__('Edit')}}</button>
                                                <button type="button" class="btn btn-sm btn-danger ml-2 delete-user"
                                                        data-id="{{$user->id}}" data-toggle="modal"
                                                        data-target="#user_delete">{{__('Delete')}}</button>
                                                <button type="button" class="btn btn-sm btn-dark ml-2 attach-role"
                                                        data-id="{{$user->id}}">{{__('Priority')}}</button>
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
            <div class="tab-pane fade mt-4" id="agents" role="tabpanel" aria-labelledby="agents-tab">
                <div class="row mt-4">
                    <div class="col-xs-12 col-lg-12 col-md-12 col-12 mb-5 mb-xl-0">
                        <div class="card shadow">
                            <div class="card-header border-0">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h3 class="mb-0">{{__('Agents')}}</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <!-- Projects table -->
                                <table class="table align-items-center table-flush table-striped border" id="">
                                    <thead class="thead-light">
                                    <tr>
                                        <th>{{__('ID')}}</th>
                                        <th>{{__('Name')}}</th>
                                        <th>{{__('Email')}}</th>
                                        <th>{{__('Agent Point')}}</th>
                                        <th>{{__('Actions')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($agents as $agent)
                                        <tr>
                                            <td>{{$agent->servit_id}}</td>
                                            <td>{{$agent->servit_username}}</td>
                                            <td>{{$agent->email}}</td>
                                            <td>{{$agent->agent_point ?? $mainPoint}}</td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-primary edit-agent"
                                                        data-id="{{$agent->id}}" data-toggle="modal"
                                                        data-target="#agent_update">{{__('Edit')}}</button>
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
            <div class="tab-pane fade mt-4" id="service-provider-users" role="tabpanel"
                 aria-labelledby="service-provider-users-tab">

                <div class="row mt-4">
                    <div class="col-xs-12 col-lg-12 col-md-12 col-12 mb-5 mb-xl-0">
                        <div class="card shadow">
                            <div class="card-header border-0">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h3 class="mb-0">{{__('Service Provider Users')}}</h3>
                                    </div>
                                    <div class="col text-right">
                                        <button type="button" class="btn btn-sm btn-success" id="" data-toggle="modal"
                                                data-target="#provider_user_store">{{__('Add')}}</button>
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
                                        <th>{{__('Provider Name')}}</th>
                                        <th>{{__('Actions')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($providerUsers as $providerUser)
                                        <tr>
                                            <td>{{$providerUser->name}}</td>
                                            <td>{{$providerUser->email}}</td>
                                            <td>
                                                <a href="{{route('admin.service-providers.show', ['service_provider' => $providerUser->serviceProvider->id, 'start' => $start, 'end' => $end])}}">
                                                    {{ $providerUser->serviceProvider->name }}
                                                </a>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-primary edit-provider-user"
                                                        data-id="{{$providerUser->id}}" data-toggle="modal"
                                                        data-target="#provider_user_update">{{__('Edit')}}</button>
                                                <button type="button"
                                                        class="btn btn-sm btn-danger ml-2 delete-provider-user"
                                                        data-id="{{$providerUser->id}}" data-toggle="modal"
                                                        data-target="#provider_user_delete">{{__('Delete')}}</button>
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
        </div>
    </div>
    <div id="attachModal">
        @include('modals.users.attachRole')
    </div>

    @include('modals.users.create')
    @include('modals.users.update')
    @include('modals.users.delete')
    @include('modals.agents.update')
    @include('modals.provider-users.create')
    @include('modals.provider-users.update')
    @include('modals.provider-users.delete')
    @include('layouts.footers.pages')
@endsection

@push('js')
    <script src="{{asset('assets/vendor/select2/dist/js/select2.full.min.js')}}"></script>
    <script src="{{asset('assets/js/pages/users.js')}}"></script>

    <script>
        $(document).ready(function () {
            $('.modal-select').select2();
        });
    </script>
@endpush

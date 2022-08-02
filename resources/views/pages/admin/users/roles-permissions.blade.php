@extends('layouts.app', [
    "activePage" => "roles & permissions",
    "headerName" => "Roles & Permissions",
])
@push('css')
    <link rel="stylesheet" href="{{asset('assets/vendor/select2/dist/css/select2.min.css')}}">
@endpush
@section('content')
    <div class="container-fluid main-container">
        <ul class="nav nav-tabs pt-4 roles-general-tabs" role="tablist">
            @foreach($res['guards'] as $guard => $guardName)
                <li class="nav-item">
                    <a class="nav-link @if($guardName == 'Admins') active show @endif" id="{{ $guard . '-tab' }}"
                       data-toggle="tab" href="{{ '#'.$guard }}"
                       role="tab"
                       aria-controls="{{ $guard }}" aria-selected="false">{{$guardName}}</a>
                </li>
            @endforeach
        </ul>
        <div class="tab-content">
            @foreach($res['guards'] as $guard => $guardName)
                <div class="tab-pane fade mt-4 @if($guardName == 'Admins') show active @endif" id="{{ $guard }}"
                     role="tabpanel"
                     aria-labelledby="{{ $guard . '-tab' }}">
                    <div class="row mt-4 parent-tables-row">
                        <div class="col-xs-12 col-lg-12 col-md-12 col-12 mb-1 mb-xl-0 p-2">
                            <div class="card shadow">
                                <div class="card-header border-0">
                                    <div class="row align-items-center">
                                        <div class="col-xl-8 col-lg-8 col-6">
                                            <h3 class="mb-0">{{__('Roles & Permissions')}}</h3>
                                        </div>
                                        <div class="col-xl-2 col-lg-8 col-2 justify-content-end text-right">
                                            <button type="button" class="btn btn-sm btn-success storeRolePermission"
                                                    data-toggle="modal"
                                                    data-target="#role_store">{{__('Add')}}</button>
                                        </div>
                                        @if(count($res['permissions']))
                                            <div
                                                class="col-xl-2 col-lg-8 col-4 d-flex justify-content-end text-right align-items-baseline permission-toggle">
                                                <button type="button"
                                                        class="btn btn-sm btn-info m-0 d-flex align-items-center">
                                                    {{__('Permissions')}}
                                                    <i class="ni ni-fat-add permission-part-icon"></i>
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <!-- Roles table -->
                                    <table class="table align-items-center table-flush table-striped border" id="">
                                        <thead class="thead-light">
                                        <tr>
                                            <th>{{__('Roles')}}</th>
                                            <th>{{__('Related Permissions')}}</th>
                                            <th>{{__('Actions')}}</th>
                                            <th>{{__('Delete')}}</th>
                                            {{--                                            <th class="permission-part">{{__('Permission')}}</th>--}}
                                            {{--                                            <th class="permission-part">{{__('Delete')}}</th>--}}
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($res['roles'][$guard] as $key => $roles)
                                            <tr>
                                                <td>{{$roles->name}}</td>
                                                <td>
                                                    @if(count($roles->permissions))
                                                        <h5 class="font-weight-400 d-flex mb-0">
                                                            @foreach($roles->permissions as $key => $permission)
                                                                {{ $key !== 0 ? ' | ' . $permission->name : $permission->name }}
                                                            @endforeach
                                                        </h5>
                                                    @else
                                                        <h5 class="font-weight-400 d-flex mb-0">{{__('Dont Attached Permissions')}}</h5>
                                                    @endif
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-primary action-role"
                                                            data-id="{{$roles->id}}"
                                                            data-toggle="modal"
                                                            data-target="#role-action">{{__('Actions')}}</button>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-danger ml-2 delete-role"
                                                            data-id="{{$roles->id}}"
                                                            data-toggle="modal"
                                                            data-target="#role-delete">{{__('Delete')}}</button>
                                                </td>
                                            </tr>
                                        @endforeach
                                        {{--                                        @dd($res)--}}
                                        {{--                                                    <button type="button" class="btn btn-sm btn-dark ml-2 attach-role"--}}
                                        {{--                                                            data-id="{{$user->id}}">{{__('Priority')}}</button>--}}
                                        </tbody>

                                    </table>
                                </div>
                            </div>
                        </div>

                        {{--                         Permission Table--}}
                        @include('components.permissions-part')

                    </div>
                </div>
            @endforeach
        </div>
    </div>

    @include('modals.users.roleStore')
    @include('modals.users.roleDelete')
    @include('modals.users.actionRole')
@endsection

@push('js')
    <script src="{{asset('assets/vendor/select2/dist/js/select2.full.min.js')}}"></script>
    @routes
    <script src="{{asset('assets/js/pages/users.js')}}"></script>

    <script>
        $(document).ready(function () {
            $('.modal-select-create').select2();
            var anchor = window.location.href.substring(window.location.href.indexOf("#") + 1);
            if (anchor == 'create-role' || anchor == 'create-permission' || anchor == 'detach-permission-role' || anchor == 'attach-permission-role') {
                window.location.href = window.location.origin + window.location.pathname + window.location.search

                // $('#createRole').removeClass('show')
                // $('#createRole').removeClass('active')
                // $('#createPermission').removeClass('show')
                // $('#createPermission').removeClass('active')

            }
        });


    </script>


@endpush

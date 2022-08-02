<div class="modal fade" id="role_attach" tabindex="-1" role="dialog" aria-labelledby="user_attach_role"
     aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header pl-3 pb-2">
                <h5 class="modal-title" id="user_attach_role">
                    {{ __('Roles & Permissions')  . ' - ' . $user->name}}
                </h5>
                <button type="button" class="close close-role" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body pl-3 pt-0">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item attach-li-mobile-50">
                        <a class="nav-link active tabModal" id="attachRole" data-toggle="tab"
                           href="#attach-role" role="tab"
                           aria-controls="attach-role">{{__('Attach Role')}}</a>
                    </li>
                    <li class="nav-item attach-li-mobile-50">
                        <a class="nav-link border-right border-left tabModal" id="deleteRole" data-toggle="tab"
                           href="#delete-role" role="tab"
                           aria-controls="delete-role">{{__('Detach Role')}}</a>
                    </li>
                    <li class="nav-item attach-li-mobile-50 attach-li-mobile-border">
                        <a class="nav-link tabModal" id="attachPermission" data-toggle="tab"
                           href="#attach-permission" role="tab"
                           aria-controls="attach-permission">{{__('Attach Permission')}}</a>
                    </li>
                    <li class="nav-item attach-li-mobile-50 attach-li-mobile-border">
                        <a class="nav-link border-right border-left tabModal" id="deletePermission" data-toggle="tab"
                           href="#delete-permission" role="tab" data-text="{{__('Dont have Permissions')}}"
                           aria-controls="delete-permission">{{__('Detach Permission')}}</a>
                    </li>
                </ul>
                <div class="tab-content">
                    {{--     Attach Role Part --}}
                    <div class="tab-pane fade mt-4 show active" id="attach-role" role="tabpanel"
                         aria-labelledby="attachRole">
                        <form action="{{url('admin/users/roles/store/:user')}}" id="user_attach_role_form" method="post"
                              autocomplete="off">
                            @csrf
                            @if(isset($dontBelongRoles) && count($dontBelongRoles))
                                <div class="row">
                                    <div class="col-2 d-flex align-items-center">
                                        {{__('Roles')}}
                                    </div>
                                    <div class="col-6">
                                        <select class="modal-select w-100" name="role[]" multiple="multiple">
                                            @foreach($dontBelongRoles as $role)
                                                <option value="{{ $role->id }}">
                                                    {{ $role->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @else
                                <p>{{__('User Have All Roles')}}</p>
                            @endif
                        </form>
                    </div>
                    {{--     Delete Role Part --}}
                    <div class="tab-pane fade mt-4" id="delete-role" role="tabpanel"
                         aria-labelledby="deleteRole">
                        <form action="{{url('admin/users/roles/delete/:user')}}" id="user_delete_role_form"
                              method="post"
                              autocomplete="off">
                            @csrf
                            @if(!is_null($user->roles))
                                <div class="row">
                                    <div class="col-2 d-flex align-items-center">
                                        {{__('Roles')}}
                                    </div>
                                    <div class="col-6">
                                        <select class="modal-select w-100" name="role[]" multiple="multiple">
                                            @foreach($user->roles as $role)
                                                <option value="{{ $role->id }}">
                                                    {{ $role->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @else
                                <p>{{__('User Dont Have Roles')}}</p>
                            @endif

                        </form>
                    </div>
                    {{-- Attach Permission part--}}
                    <div class="tab-pane fade mt-4" id="attach-permission" role="tabpanel"
                         aria-labelledby="attachPermission">
                        <form action="{{url('admin/users/permission/store/:user')}}" id="user_attach_permission_form"
                              method="post"
                              autocomplete="off">
                            @csrf
                            @if(isset($dontBelongPermissions) && count($dontBelongPermissions))
                                <div class="row">
                                    <div class="col-2 d-flex align-items-center">
                                        {{__('Permissions')}}
                                    </div>
                                    <div class="col-6">
                                        <select class="w-100 modal-select" name="permission[]" multiple="multiple">
                                            @foreach($dontBelongPermissions as $permission)
                                                <option value="{{ $permission->id }}">
                                                    {{ $permission->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @else
                                <p>{{__('User Have All Permissions')}}</p>
                            @endif

                        </form>
                    </div>
                    {{--Delete Permission part--}}
                    <div class="tab-pane fade mt-4" id="delete-permission" role="tabpanel"
                         aria-labelledby="deletePermission">
                        <form action="{{url('admin/users/permission/delete/:user')}}" id="user_delete_permission_form"
                              method="post"
                              autocomplete="off">
                            @csrf
                            @if(!is_null($user->permissions))
                                <div class="row">
                                    <div class="col-2 d-flex align-items-center">
                                        {{__('Permissions')}}
                                    </div>
                                    <div class="col-6">
                                        <select class="w-100 modal-select" name="permission[]" multiple="multiple">
                                            @foreach($user->permissions as $permission)
                                                <option value="{{ $permission->id }}">
                                                    {{ $permission->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @else
                                <p>{{__('User Dont Have Permissions')}}</p>
                            @endif

                        </form>
                    </div>
                </div>

            </div>
            <div class="modal-footer">

                <button type="submit" form="user_attach_role_form"
                        class="btn btn-sm btn-primary submit-modal-forms">{{__('Save')}}</button>
            </div>
        </div>
    </div>
</div>

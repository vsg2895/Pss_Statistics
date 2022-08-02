<!-- Modal -->

<div class="modal fade" id="role_store" tabindex="-1" role="dialog" aria-labelledby="role_storeLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header p-2">
                {{--                    <h5 class="modal-title" id="role_storeLabel">{{__('Create')}}</h5>--}}
                <button type="button" class="close close-role-permission-store" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body pt-1">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link tabModalCreate active show" id="createRole" data-toggle="tab"
                           href="#create-role" role="tab"
                           aria-controls="create-role">{{__('Create Role')}}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link border-right border-left tabModalCreate" id="createPermission"
                           data-toggle="tab"
                           href="#create-permission" role="tab"
                           aria-controls="create-permission">{{__('Create Permission')}}</a>
                    </li>
                </ul>
                <div class="tab-content">
                    {{--     Create Role Part --}}
                    <div class="tab-pane fade mt-4 active show" id="create-role" role="tabpanel"
                         aria-labelledby="createRole">
                        <form action="{{route('admin.roles.store')}}" id="role_store_form" method="post"
                              autocomplete="off">
                            @csrf
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">{{__('Role')}}</span>
                                    </div>
                                    <input type="text" name="role" value="{{old('name')}}"
                                           class="form-control border-left pl-2"
                                           required>
                                </div>
                                <div class="input-group flex-column">
                                    <div class="input-group-prepend w-100 mt-5 flex-column">
                                <span
                                    class="input-group-text p-1 d-contents">{{__('Attach Permissions To Role')}}</span>
                                        <select class="w-100 modal-select-create" name="permission[]"
                                                multiple="multiple">
                                            @foreach($res['permissions'] as $permission)
                                                <option value="{{ $permission->id }}">
                                                    {{ $permission->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                </div>
                            </div>
                        </form>
                    </div>
                    {{--     Create Permission Part --}}
                    <div class="tab-pane fade mt-4" id="create-permission" role="tabpanel"
                         aria-labelledby="createPermission">
                        <form action="{{route('admin.permission.store')}}" id="permission_store_form" method="post"
                              autocomplete="off">
                            @csrf
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">{{__('Permission')}}</span>
                                    </div>
                                    <input type="text" name="permission" value="{{old('name')}}"
                                           class="form-control border-left pl-2"
                                           required>
                                </div>
                                <div class="form-check mt-2 d-flex w-50 pl-0">
                                    @foreach($res['guards'] as $guard => $guardName)
                                        <div class="form-check ml-1 d-flex">
                                            <input class="form-check-input checkRoleGuard" type="radio" value="{{ $guard }}"
                                                   name="guard"
                                                   id="select-guard">
                                            <label class="form-check-label" for="flexRadioDefault1">
                                                {{ $guardName }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="input-group flex-column">
                                    <div class="input-group-prepend w-100 mt-2 flex-column" id="guardsRoles">
                                        @include('modals.users.guardRoles')
{{--                                        <select class="w-100 modal-select-create modal-create-permission" name="role[]"--}}
{{--                                                multiple="multiple" id="guardsRoles">--}}
{{--                                            @foreach($res['roles'] as $key => $roles)--}}
{{--                                                <optgroup label="{{ $key }}" class="{{ $key }} roleType">--}}
{{--                                                    @foreach($roles as $role)--}}
{{--                                                        <option value="{{ $role->id }}" class="{{ $key }} roleType">--}}
{{--                                                            {{ $role->name }}--}}
{{--                                                        </option>--}}
{{--                                                    @endforeach--}}
{{--                                                </optgroup>--}}

{{--                                            @endforeach--}}
{{--                                        </select>--}}
                                    </div>

                                </div>
                            </div>
                        </form>
                    </div>

                </div>

            </div>
            <div class="modal-footer">
                <button type="submit" form="role_store_form"
                        class="btn btn-sm btn-primary submit-create-modal-forms">{{__('Save')}}</button>
            </div>
        </div>
    </div>
</div>


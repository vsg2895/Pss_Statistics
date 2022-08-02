<div class="modal fade" id="role-action" tabindex="-1" role="dialog" aria-labelledby="action_role"
     aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header pl-3 pb-2">
                <h5 class="modal-title" id="action_role" data-text="{{ __('Action Roles')}}">
                    {{--                    {{ __('Roles & Permissions')  . ' - ' . $user->name}}--}}
                </h5>
                <button type="button" class="close close-role-permission" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body pl-3 pt-0">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item attach-li-mobile-50">
                        <a class="nav-link active show tabModalRoleAction" id="attachPermissionRole" data-toggle="tab"
                           href="#attach-permission-role" role="tab"
                           aria-controls="attach-permission-role">{{__('Attach Permission')}}</a>
                    </li>
                    <li class="nav-item attach-li-mobile-50">
                        <a class="nav-link border-right border-left tabModalRoleAction" id="detachPermissionRole"
                           data-toggle="tab"
                           href="#detach-permission-role" role="tab"
                           aria-controls="detach-permission-role">{{__('Detach Permission')}}</a>
                    </li>
                </ul>
                <div class="tab-content">
                    {{--     Attach Permission Part --}}
                    <div class="tab-pane fade mt-4 show active" id="attach-permission-role" role="tabpanel"
                         aria-labelledby="attachPermissionRole">
                        <form action="{{url('admin/attach-permission/:role')}}" id="action_attach_permission_form"
                              method="post"
                              autocomplete="off">
                            @csrf
                            {{--                            @if(isset($dontBelongRoles) && count($dontBelongRoles))--}}
                            <div class="row">
                                <div class="col-2 d-flex align-items-center">
                                    {{__('Roles')}}
                                </div>
                                <div class="col-6">
                                    <select class="modal-select-action w-100" name="permission[]"
                                            id="attachPermissionSelect" multiple="multiple">
                                        {{--                                        @foreach($dontBelongRoles as $role)--}}
                                        {{--                                            <option value="{{ $role->id }}">--}}
                                        {{--                                                {{ $role->name }}--}}
                                        {{--                                            </option>--}}
                                        {{--                                        @endforeach--}}
                                    </select>
                                </div>
                            </div>
                            {{--                            @else--}}
                            {{--                                <p>{{__('User Have All Roles')}}</p>--}}
                            {{--                            @endif--}}
                        </form>
                    </div>
                    {{--     Delete Permission Part --}}
                    <div class="tab-pane fade mt-4" id="detach-permission-role" role="tabpanel"
                         aria-labelledby="detachPermissionRole">
                        <form action="{{url('admin/detach-permission/:role')}}" id="action_dettach_permission_form"
                              method="post"
                              autocomplete="off">
                            @csrf
                            {{--                            @if(!is_null($user->roles))--}}
                            <div class="row align-items-baseline">
                                <div class="col-xl-2 col-lg-2 col-2 d-flex align-items-center">
                                    {{__('Roles')}}
                                </div>
                                <div class="col-xl-6 col-lg-6 col-6">
                                    <select class="modal-select-action w-100" name="permission[]"
                                            id="detachPermissionSelect" multiple="multiple">
                                        {{--                                        @foreach($user->roles as $role)--}}
                                        {{--                                            <option value="{{ $role->id }}">--}}
                                        {{--                                                {{ $role->name }}--}}
                                        {{--                                            </option>--}}
                                        {{--                                        @endforeach--}}
                                    </select>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-8 mobile-flex-tab">
                                    <div class="form-check ml-1 d-flex">
                                        <input class="form-check-input checkRoleGuard" type="radio"
                                               name="all">
                                        <label class="form-check-label font-weight-800" for="flexRadioDefault1">
                                            {{ __('Stash All Permissions') }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                            {{--                            @else--}}
                            {{--                                <p>{{__('User Dont Have Roles')}}</p>--}}
                            {{--                            @endif--}}

                        </form>
                    </div>

                </div>
            </div>
            <div class="modal-footer">

                <button type="submit" form="action_attach_permission_form"
                        class="btn btn-sm btn-primary submit-modal-forms">{{__('Save')}}</button>
            </div>
        </div>
    </div>
</div>
